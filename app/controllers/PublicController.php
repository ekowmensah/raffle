<?php

namespace App\Controllers;

use App\Core\Controller;

class PublicController extends Controller
{
    private $campaignModel;
    private $stationModel;
    private $programmeModel;

    public function __construct()
    {
        $this->campaignModel = $this->model('Campaign');
        $this->stationModel = $this->model('Station');
        $this->programmeModel = $this->model('Programme');
    }

    public function index()
    {
        // Get all active stations
        $stations = $this->stationModel->getAll();
        
        // Get all active campaigns with details
        $campaigns = $this->campaignModel->getAllWithDetails();

        $data = [
            'title' => 'Play & Win',
            'campaigns' => $campaigns,
            'stations' => $stations
        ];

        $this->view('public/index', $data);
    }

    public function campaign($id)
    {
        $campaign = $this->campaignModel->findById($id);

        if (!$campaign || $campaign->status !== 'active') {
            flash('error', 'Campaign not available');
            $this->redirect('public');
        }

        $stats = $this->campaignModel->getStats($id);

        $data = [
            'title' => $campaign->name,
            'campaign' => $campaign,
            'stats' => $stats
        ];

        $this->view('public/campaign', $data);
    }

    public function buyTicket()
    {
        // Get all active stations
        $stations = $this->stationModel->getAll();
        
        $data = [
            'title' => 'Buy Tickets',
            'stations' => $stations
        ];
        
        $this->view('public/buy-ticket', $data);
    }

    public function howToPlay()
    {
        $data = ['title' => 'How to Play'];
        $this->view('public/how-to-play', $data);
    }

    public function winners()
    {
        $winnerModel = $this->model('DrawWinner');
        $drawModel = $this->model('Draw');
        $campaignModel = $this->model('Campaign');
        
        // Get recent winners (last 50)
        $winners = $winnerModel->getRecentWinners(50);
        
        // Get total stats
        $totalWinners = $winnerModel->count();
        $totalPrizes = $winnerModel->getTotalPrizesAwarded();
        
        // Get active campaigns count
        $activeCampaigns = $campaignModel->getActive();
        
        $data = [
            'title' => 'Recent Winners',
            'winners' => $winners,
            'total_winners' => $totalWinners,
            'total_prizes' => $totalPrizes,
            'active_campaigns' => count($activeCampaigns)
        ];
        
        $this->view('public/winners', $data);
    }

    public function getProgrammesByStation($stationId)
    {
        header('Content-Type: application/json');
        
        $programmeModel = $this->model('Programme');
        $programmes = $programmeModel->getByStation($stationId);
        
        echo json_encode([
            'success' => true,
            'programmes' => $programmes
        ]);
        exit;
    }

    public function getCampaignsByStation($stationId)
    {
        header('Content-Type: application/json');
        
        // Get station-wide campaigns only
        $campaigns = $this->campaignModel->getActiveByStation($stationId);
        
        echo json_encode([
            'success' => true,
            'campaigns' => $campaigns
        ]);
        exit;
    }

    public function getCampaignsByProgramme($programmeId)
    {
        header('Content-Type: application/json');
        
        // Get programme-specific campaigns only (NOT station-wide)
        $campaigns = $this->campaignModel->getActiveByProgramme($programmeId);
        
        echo json_encode([
            'success' => true,
            'campaigns' => $campaigns
        ]);
        exit;
    }

    public function processPayment()
    {
        // Debug: Log that we reached this method
        error_log('ProcessPayment called - Method: ' . $_SERVER['REQUEST_METHOD']);
        error_log('POST data: ' . print_r($_POST, true));
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            flash('error', 'Invalid request method');
            $this->redirect('public');
            return;
        }

        // Basic validation
        if (empty($_POST['campaign_id']) || empty($_POST['phone']) || empty($_POST['ticket_count']) || empty($_POST['station_id'])) {
            flash('error', 'Please fill in all required fields: ' . 
                (!empty($_POST['campaign_id']) ? '' : 'campaign_id ') .
                (!empty($_POST['phone']) ? '' : 'phone ') .
                (!empty($_POST['ticket_count']) ? '' : 'ticket_count ') .
                (!empty($_POST['station_id']) ? '' : 'station_id'));
            $this->redirect('public/campaign/' . ($_POST['campaign_id'] ?? ''));
            return;
        }

        try {
            // Check if services exist
            $ticketServicePath = '../app/services/TicketGeneratorService.php';
            $revenueServicePath = '../app/services/RevenueAllocationService.php';
            
            if (!file_exists($ticketServicePath)) {
                throw new \Exception('TicketGeneratorService not found at: ' . $ticketServicePath);
            }
            if (!file_exists($revenueServicePath)) {
                throw new \Exception('RevenueAllocationService not found at: ' . $revenueServicePath);
            }
            
            require_once $ticketServicePath;
            require_once $revenueServicePath;

            $ticketService = new \App\Services\TicketGeneratorService();
            $revenueService = new \App\Services\RevenueAllocationService();
            $paymentModel = $this->model('Payment');
            $playerModel = $this->model('Player');

            // Get or create player (with normalized phone number)
            $phone = sanitize($_POST['phone']);
            $playerName = sanitize($_POST['player_name'] ?? null);
            $player = $playerModel->findOrCreate($phone, $playerName);

            $paymentMethod = $_POST['payment_method'] ?? 'manual';
            $ticketCount = intval($_POST['ticket_count']);
            $campaignId = $_POST['campaign_id'];
            $stationId = $_POST['station_id'];
            $programmeId = !empty($_POST['programme_id']) ? $_POST['programme_id'] : null;

            // Validate campaign exists
            $campaign = $this->campaignModel->findById($campaignId);
            if (!$campaign) {
                flash('error', 'Campaign not found');
                $this->redirect('public');
                return;
            }

            // Calculate amount based on ticket count (round to 2 decimal places)
            $amount = round($ticketCount * $campaign->ticket_price, 2);

            // For manual payment, process immediately
            if ($paymentMethod === 'manual') {
                $reference = 'MANUAL-' . time() . '-' . rand(1000, 9999);
                
                // Create payment record
                $paymentData = [
                    'campaign_id' => $campaignId,
                    'player_id' => $player->id,
                    'station_id' => $stationId,
                    'programme_id' => $programmeId, // Can be null for station-wide campaigns
                    'amount' => $amount,
                    'currency' => 'GHS',
                    'gateway' => 'manual',
                    'gateway_reference' => $reference,
                    'internal_reference' => $reference,
                    'status' => 'success',
                    'channel' => 'WEB',
                    'paid_at' => date('Y-m-d H:i:s')
                ];

                $paymentId = $paymentModel->create($paymentData);

                if ($paymentId) {
                    // Add payment_id to payment data for services
                    $paymentData['payment_id'] = $paymentId;
                    
                    // Generate tickets
                    $ticketCount = floor($amount / $campaign->ticket_price);

                    if ($ticketCount > 0) {
                        $tickets = $ticketService->generateTickets($paymentData);

                        // Allocate revenue
                        $revenueService->allocate($paymentData);

                        // Redirect to success page
                        $this->redirect('public/paymentSuccess/' . $paymentId);
                    } else {
                        flash('error', 'Amount too low. Minimum amount is ' . $campaign->currency . ' ' . $campaign->ticket_price);
                        $this->redirect('public/campaign/' . $campaignId);
                    }
                } else {
                    flash('error', 'Payment processing failed');
                    $this->redirect('public/campaign/' . $campaignId);
                }
            } else {
                // Handle Hubtel mobile money payment
                if ($paymentMethod === 'momo' || $paymentMethod === 'hubtel') {
                    require_once '../app/services/PaymentGateway/HubtelService.php';
                    $hubtelService = new \App\Services\PaymentGateway\HubtelService();
                    
                    // Create pending payment record
                    $reference = 'WEB-' . time() . '-' . rand(1000, 9999);
                    
                    $paymentData = [
                        'campaign_id' => $campaignId,
                        'player_id' => $player->id,
                        'station_id' => $stationId,
                        'programme_id' => $programmeId,
                        'amount' => $amount,
                        'currency' => 'GHS',
                        'gateway' => 'hubtel',
                        'internal_reference' => $reference,
                        'status' => 'pending',
                        'channel' => 'WEB'
                    ];
                    
                    $paymentId = $paymentModel->create($paymentData);
                    
                    if (!$paymentId) {
                        flash('error', 'Failed to create payment record');
                        $this->redirect('public/campaign/' . $campaignId);
                        return;
                    }
                    
                    // Initiate Hubtel payment
                    $result = $hubtelService->initiatePayment([
                        'amount' => $amount,
                        'phone' => $phone,
                        'reference' => $reference,
                        'description' => "Tickets for {$campaign->name}",
                        'customer_name' => $player->name ?? 'Player',
                        'customer_email' => $player->email ?? null
                    ]);
                    
                    if ($result['success']) {
                        // Update payment with gateway reference
                        $paymentModel->update($paymentId, [
                            'gateway_reference' => $result['transaction_id'] ?? null
                        ]);
                        
                        flash('success', 'Payment initiated! Please approve the mobile money prompt on your phone.');
                        $this->redirect('public/paymentPending/' . $paymentId);
                    } else {
                        // Update payment status to failed
                        $paymentModel->update($paymentId, [
                            'status' => 'failed',
                            'gateway_response' => json_encode($result)
                        ]);
                        
                        flash('error', 'Payment failed: ' . ($result['message'] ?? 'Unknown error'));
                        $this->redirect('public/campaign/' . $campaignId);
                    }
                } else {
                    flash('info', 'Payment method not supported yet');
                    $this->redirect('public/campaign/' . $campaignId);
                }
            }
        } catch (\Exception $e) {
            // Log the error for debugging
            error_log('Payment processing error: ' . $e->getMessage());
            error_log('Stack trace: ' . $e->getTraceAsString());
            
            flash('error', 'Payment processing failed: ' . $e->getMessage());
            $this->redirect('public/campaign/' . ($_POST['campaign_id'] ?? ''));
        }
    }

    public function paymentPending($paymentId)
    {
        $paymentModel = $this->model('Payment');
        $payment = $paymentModel->findById($paymentId);
        
        if (!$payment) {
            flash('error', 'Payment not found');
            $this->redirect('public');
            return;
        }

        $campaign = $this->campaignModel->findById($payment->campaign_id);

        $data = [
            'title' => 'Payment Pending',
            'payment' => $payment,
            'campaign' => $campaign
        ];

        $this->view('public/payment-pending', $data);
    }

    public function paymentSuccess($paymentId)
    {
        $paymentModel = $this->model('Payment');
        $ticketModel = $this->model('Ticket');

        $payment = $paymentModel->findById($paymentId);
        
        if (!$payment) {
            flash('error', 'Payment not found');
            $this->redirect('public');
            return;
        }

        $tickets = $ticketModel->getByPayment($paymentId);
        $campaign = $this->campaignModel->findById($payment->campaign_id);

        $data = [
            'title' => 'Payment Successful',
            'payment' => $payment,
            'tickets' => $tickets,
            'campaign' => $campaign
        ];

        $this->view('public/payment-success', $data);
    }
    
    public function checkPaymentStatus($paymentId)
    {
        header('Content-Type: application/json');
        
        $paymentModel = $this->model('Payment');
        $payment = $paymentModel->findById($paymentId);
        
        if (!$payment) {
            echo json_encode(['success' => false, 'message' => 'Payment not found']);
            exit;
        }
        
        echo json_encode([
            'success' => true,
            'status' => $payment->status,
            'payment_id' => $payment->id
        ]);
        exit;
    }
}
