<?php

namespace App\Controllers;

use App\Core\Controller;

class PaymentController extends Controller
{
    private $paymentModel;
    private $playerModel;
    private $campaignModel;
    private $ticketService;
    private $revenueService;
    private $smsService;

    public function __construct()
    {
        $this->paymentModel = $this->model('Payment');
        $this->playerModel = $this->model('Player');
        $this->campaignModel = $this->model('Campaign');
        
        require_once '../app/services/TicketGeneratorService.php';
        require_once '../app/services/RevenueAllocationService.php';
        require_once '../app/services/SMS/HubtelSmsService.php';
        
        $this->ticketService = new \App\Services\TicketGeneratorService();
        $this->revenueService = new \App\Services\RevenueAllocationService();
        $this->smsService = new \App\Services\SMS\HubtelSmsService();
    }

    public function index()
    {
        $this->requireAuth();

        // Check permission
        if (!can('view_payments') && !hasRole(['super_admin', 'station_admin', 'programme_manager', 'auditor'])) {
            flash('error', 'You do not have permission to view payments');
            $this->redirect('home');
        }

        $payments = $this->paymentModel->getSuccessfulPayments();

        $data = [
            'title' => 'Payments',
            'payments' => $payments
        ];

        $this->view('payments/index', $data);
    }

    public function show($id)
    {
        $this->requireAuth();

        // Get payment with related data
        $payment = $this->paymentModel->getWithDetails($id);

        if (!$payment) {
            flash('error', 'Payment not found');
            $this->redirect('payment');
        }

        $ticketModel = $this->model('Ticket');
        $tickets = $ticketModel->getByPayment($id);

        $data = [
            'title' => 'Payment Details',
            'payment' => $payment,
            'tickets' => $tickets
        ];

        $this->view('payments/view', $data);
    }

    public function initiate()
    {
        // Public endpoint for payment initiation
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            verify_csrf();

            $campaignId = $_POST['campaign_id'];
            $phone = sanitize($_POST['phone']);
            $amount = floatval($_POST['amount']);
            $gateway = $_POST['gateway'] ?? 'mtn';
            $stationId = $_POST['station_id'];
            $programmeId = $_POST['programme_id'];

            // Get or create player
            $player = $this->playerModel->findOrCreate($phone);
            $campaign = $this->campaignModel->findById($campaignId);

            if (!$campaign || $campaign->status !== 'active') {
                flash('error', 'Campaign not available');
                $this->redirect('public/campaign/' . $campaignId);
            }

            // Create payment record
            $reference = strtoupper($gateway) . '-' . time() . '-' . rand(1000, 9999);
            
            $paymentData = [
                'campaign_id' => $campaignId,
                'player_id' => $player->id,
                'station_id' => $stationId,
                'programme_id' => $programmeId,
                'amount' => $amount,
                'currency' => $campaign->currency,
                'payment_method' => $gateway,
                'payment_reference' => $reference,
                'status' => 'pending'
            ];

            $paymentId = $this->paymentModel->create($paymentData);

            if ($paymentId) {
                // Initiate payment with gateway
                $gatewayService = $this->getGatewayService($gateway);
                
                $result = $gatewayService->initiatePayment([
                    'amount' => $amount,
                    'phone' => $phone,
                    'campaign_id' => $campaignId,
                    'campaign_name' => $campaign->name,
                    'player_name' => $player->name,
                    'email' => $player->email ?? 'player@raffle.com'
                ]);

                if ($result['success']) {
                    // Update payment with gateway reference
                    $this->paymentModel->update($paymentId, [
                        'gateway_reference' => $result['reference'],
                        'gateway_response_json' => json_encode($result['gateway_response'])
                    ]);

                    // For Paystack, redirect to authorization URL
                    if ($gateway === 'paystack' && isset($result['authorization_url'])) {
                        header('Location: ' . $result['authorization_url']);
                        exit;
                    }

                    flash('success', $result['message']);
                    $_SESSION['pending_payment_id'] = $paymentId;
                    $this->redirect('payment/verify/' . $paymentId);
                } else {
                    flash('error', 'Payment initiation failed');
                    $this->redirect('public/campaign/' . $campaignId);
                }
            }
        }
    }

    public function verify($id)
    {
        $payment = $this->paymentModel->findById($id);

        if (!$payment) {
            flash('error', 'Payment not found');
            $this->redirect('public');
        }

        $data = [
            'title' => 'Verify Payment',
            'payment' => $payment
        ];

        $this->view('payments/verify', $data);
    }

    public function processSuccess($id)
    {
        $payment = $this->paymentModel->findById($id);

        if (!$payment || $payment->status === 'success') {
            $this->redirect('public');
        }

        // Update payment status
        $this->paymentModel->updateStatus($id, 'success');

        // Generate tickets
        $paymentData = [
            'payment_id' => $id,
            'campaign_id' => $payment->campaign_id,
            'player_id' => $payment->player_id,
            'station_id' => $payment->station_id,
            'programme_id' => $payment->programme_id,
            'amount' => $payment->amount
        ];

        $ticketResult = $this->ticketService->generateTickets($paymentData);

        if ($ticketResult) {
            // Allocate revenue
            $this->revenueService->allocate($paymentData);

            // Update player loyalty
            $this->playerModel->updateLoyaltyLevel($payment->player_id);

            // Send SMS notification
            $campaign = $this->campaignModel->findById($payment->campaign_id);
            $player = $this->playerModel->findById($payment->player_id);
            
            $this->smsService->sendTicketNotification(
                $player->phone,
                $ticketResult['tickets'],
                $campaign->name,
                $payment->amount,
                $campaign->currency
            );

            flash('success', 'Payment successful! ' . $ticketResult['ticket_count'] . ' tickets generated.');
        }

        $this->redirect('payment/success/' . $id);
    }

    public function success($id)
    {
        $payment = $this->paymentModel->findById($id);
        $ticketModel = $this->model('Ticket');
        $tickets = $ticketModel->getByPayment($id);

        $data = [
            'title' => 'Payment Successful',
            'payment' => $payment,
            'tickets' => $tickets
        ];

        $this->view('payments/success', $data);
    }

    public function manual()
    {
        $this->requireAuth();

        $data = [
            'title' => 'Manual Payment',
            'campaigns' => $this->model('Campaign')->getActive(),
            'stations' => $this->model('Station')->findAll(),
            'programmes' => $this->model('Programme')->findAll()
        ];

        $this->view('payments/manual', $data);
    }

    public function processManual()
    {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('payment/manual');
        }

        verify_csrf();

        try {
            require_once '../app/services/TicketGeneratorService.php';
            require_once '../app/services/RevenueAllocationService.php';

            $ticketService = new \App\Services\TicketGeneratorService();
            $revenueService = new \App\Services\RevenueAllocationService();

            // Get or create player
            $playerModel = $this->model('Player');
            $phone = sanitize($_POST['phone']);
            $player = $playerModel->findByPhone($phone);

            if (!$player) {
                $playerId = $playerModel->create([
                    'phone' => $phone,
                    'name' => sanitize($_POST['player_name'] ?? 'Test Player'),
                    'loyalty_level' => 'bronze',
                    'loyalty_points' => 0
                ]);
                $player = $playerModel->findById($playerId);
            }

            // Create payment record
            $paymentData = [
                'campaign_id' => $_POST['campaign_id'],
                'player_id' => $player->id,
                'station_id' => $_POST['station_id'],
                'programme_id' => !empty($_POST['programme_id']) ? $_POST['programme_id'] : null,
                'amount' => floatval($_POST['amount']),
                'currency' => 'GHS',
                'payment_method' => 'manual',
                'payment_reference' => 'MANUAL-' . time() . '-' . rand(1000, 9999),
                'status' => 'success',
                'payment_completed_at' => date('Y-m-d H:i:s')
            ];

            $paymentId = $this->paymentModel->create($paymentData);

            if ($paymentId) {
                // Generate tickets
                $campaign = $this->model('Campaign')->findById($_POST['campaign_id']);
                $ticketCount = floor($paymentData['amount'] / $campaign->ticket_price);

                $tickets = $ticketService->generateTickets(
                    $paymentId,
                    $player->id,
                    $_POST['campaign_id'],
                    $_POST['station_id'],
                    $_POST['programme_id'] ?? null,
                    $ticketCount
                );

                // Allocate revenue
                $revenueService->allocateRevenue($paymentId, $paymentData, $campaign);

                flash('success', "Manual payment processed! Generated {$ticketCount} ticket(s). Reference: {$paymentData['payment_reference']}");
                $this->redirect('payment');
            } else {
                flash('error', 'Failed to create payment');
                $this->redirect('payment/manual');
            }
        } catch (\Exception $e) {
            flash('error', 'Error: ' . $e->getMessage());
            $this->redirect('payment/manual');
        }
    }

    public function reconcile()
    {
        $this->requireAuth();

        $startDate = $_GET['start_date'] ?? date('Y-m-01');
        $endDate = $_GET['end_date'] ?? date('Y-m-d');
        $gateway = $_GET['gateway'] ?? null;

        $payments = $this->paymentModel->getForReconciliation($startDate, $endDate, $gateway);

        // Calculate totals
        $totalSuccess = 0;
        $totalPending = 0;
        $totalFailed = 0;
        $countSuccess = 0;
        $countPending = 0;
        $countFailed = 0;

        foreach ($payments as $payment) {
            if ($payment->status == 'success') {
                $totalSuccess += $payment->amount;
                $countSuccess++;
            } elseif ($payment->status == 'pending') {
                $totalPending += $payment->amount;
                $countPending++;
            } else {
                $totalFailed += $payment->amount;
                $countFailed++;
            }
        }

        $data = [
            'title' => 'Payment Reconciliation',
            'payments' => $payments,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'gateway' => $gateway,
            'total_success' => $totalSuccess,
            'total_pending' => $totalPending,
            'total_failed' => $totalFailed,
            'count_success' => $countSuccess,
            'count_pending' => $countPending,
            'count_failed' => $countFailed
        ];

        $this->view('payments/reconcile', $data);
    }

    private function getGatewayService($gateway)
    {
        switch ($gateway) {
            case 'manual':
                require_once '../app/services/PaymentGateway/ManualPaymentService.php';
                return new \App\Services\PaymentGateway\ManualPaymentService();
            
            case 'mtn':
                require_once '../app/services/PaymentGateway/MtnMomoService.php';
                return new \App\Services\PaymentGateway\MtnMomoService();
            
            case 'hubtel':
                require_once '../app/services/PaymentGateway/HubtelService.php';
                return new \App\Services\PaymentGateway\HubtelService();
            
            case 'paystack':
                require_once '../app/services/PaymentGateway/PaystackService.php';
                return new \App\Services\PaymentGateway\PaystackService();
            
            default:
                require_once '../app/services/PaymentGateway/ManualPaymentService.php';
                return new \App\Services\PaymentGateway\ManualPaymentService();
        }
    }
}
