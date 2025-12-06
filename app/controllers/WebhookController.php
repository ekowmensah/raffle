<?php

namespace App\Controllers;

use App\Core\Controller;

class WebhookController extends Controller
{
    private $paymentModel;
    private $ticketService;
    private $revenueService;
    private $smsService;

    public function __construct()
    {
        $this->paymentModel = $this->model('Payment');
        
        require_once '../app/services/TicketGeneratorService.php';
        require_once '../app/services/RevenueAllocationService.php';
        require_once '../app/services/SmsNotificationService.php';
        
        $this->ticketService = new \App\Services\TicketGeneratorService();
        $this->revenueService = new \App\Services\RevenueAllocationService();
        $this->smsService = new \App\Services\SmsNotificationService();
    }

    public function mtn()
    {
        // Handle MTN MoMo webhook
        $payload = json_decode(file_get_contents('php://input'), true);
        
        require_once '../app/services/PaymentGateway/MtnMomoService.php';
        $service = new \App\Services\PaymentGateway\MtnMomoService();
        
        $result = $service->handleWebhook($payload);
        
        if ($result['reference']) {
            $this->processWebhook($result);
        }

        http_response_code(200);
        echo json_encode(['status' => 'received']);
    }

    public function hubtel()
    {
        // Log webhook received
        error_log('Hubtel webhook received: ' . file_get_contents('php://input'));
        
        // Handle Hubtel webhook
        $payload = json_decode(file_get_contents('php://input'), true);
        
        if (!$payload) {
            error_log('Hubtel webhook: Invalid JSON payload');
            http_response_code(400);
            echo json_encode(['error' => 'Invalid payload']);
            return;
        }
        
        require_once '../app/services/PaymentGateway/HubtelService.php';
        $service = new \App\Services\PaymentGateway\HubtelService();
        
        $result = $service->handleWebhook($payload);
        
        if ($result['success'] && isset($result['reference'])) {
            $this->processWebhook($result);
            
            http_response_code(200);
            echo json_encode(['status' => 'received', 'reference' => $result['reference']]);
        } else {
            error_log('Hubtel webhook processing failed: ' . ($result['message'] ?? 'Unknown error'));
            http_response_code(400);
            echo json_encode(['error' => $result['message'] ?? 'Processing failed']);
        }
    }

    public function paystack()
    {
        // Handle Paystack webhook
        $payload = json_decode(file_get_contents('php://input'), true);
        
        require_once '../app/services/PaymentGateway/PaystackService.php';
        $service = new \App\Services\PaymentGateway\PaystackService();
        
        $result = $service->handleWebhook($payload);
        
        if ($result['reference']) {
            $this->processWebhook($result);
        }

        http_response_code(200);
        echo json_encode(['status' => 'received']);
    }

    private function processWebhook($webhookData)
    {
        error_log("Processing webhook for reference: " . $webhookData['reference']);
        
        // Find payment by reference
        $payment = $this->paymentModel->findByReference($webhookData['reference']);
        
        if (!$payment) {
            error_log("Payment not found for reference: " . $webhookData['reference']);
            return;
        }
        
        if ($payment->status === 'success') {
            error_log("Payment already processed: " . $payment->id);
            return; // Already processed
        }
        
        error_log("Updating payment {$payment->id} to status: {$webhookData['status']}");

        if ($webhookData['status'] === 'success') {
            error_log("Payment successful - updating status");
            
            // Update payment status
            $updateResult = $this->paymentModel->updateStatus($payment->id, 'success', $webhookData);
            
            if (!$updateResult) {
                error_log("ERROR: Failed to update payment status!");
                return;
            }
            
            error_log("Payment status updated successfully - generating tickets");

            // Generate tickets
            $paymentData = [
                'payment_id' => $payment->id,
                'campaign_id' => $payment->campaign_id,
                'player_id' => $payment->player_id,
                'station_id' => $payment->station_id,
                'programme_id' => $payment->programme_id,
                'amount' => $payment->amount
            ];

            try {
                $ticketResult = $this->ticketService->generateTickets($paymentData);
                error_log("Tickets generated: " . ($ticketResult ? 'YES' : 'NO'));

                if ($ticketResult) {
                    // Allocate revenue
                    error_log("Allocating revenue");
                    $this->revenueService->allocate($paymentData);

                    // Update player loyalty
                    error_log("Updating player loyalty");
                    $playerModel = $this->model('Player');
                    $playerModel->updateLoyaltyLevel($payment->player_id);

                    // Send SMS
                    error_log("Sending SMS notification");
                    $campaignModel = $this->model('Campaign');
                    $campaign = $campaignModel->findById($payment->campaign_id);
                    $player = $playerModel->findById($payment->player_id);
                    
                    $this->smsService->sendTicketNotification(
                        $player->phone,
                        $ticketResult['tickets'],
                        $campaign->name
                    );
                    
                    error_log("Webhook processing completed successfully");
                }
            } catch (\Exception $e) {
                error_log("ERROR in ticket generation: " . $e->getMessage());
            }
        } else {
            error_log("Payment failed - updating status to failed");
            // Payment failed
            $this->paymentModel->updateStatus($payment->id, 'failed', $webhookData);
        }
    }
}
