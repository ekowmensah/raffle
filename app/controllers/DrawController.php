<?php

namespace App\Controllers;

use App\Core\Controller;

class DrawController extends Controller
{
    private $drawModel;
    private $winnerModel;
    private $campaignModel;
    private $drawService;

    public function __construct()
    {
        $this->drawModel = $this->model('Draw');
        $this->winnerModel = $this->model('DrawWinner');
        $this->campaignModel = $this->model('Campaign');
        
        require_once '../app/services/DrawService.php';
        $this->drawService = new \App\Services\DrawService();
    }

    public function index()
    {
        $this->requireAuth();

        $campaignId = $_GET['campaign'] ?? null;
        $draws = $campaignId ? $this->drawModel->getByCampaign($campaignId) : $this->drawModel->getCompletedDraws();
        $campaigns = $this->campaignModel->findAll();

        $data = [
            'title' => 'Draws',
            'draws' => $draws,
            'campaigns' => $campaigns,
            'selected_campaign' => $campaignId
        ];

        $this->view('draws/index', $data);
    }

    public function show($id)
    {
        $this->requireAuth();

        $draw = $this->drawModel->getWithWinners($id);

        if (!$draw) {
            flash('error', 'Draw not found');
            $this->redirect('draw');
        }

        $winners = $this->winnerModel->getByDraw($id);

        $data = [
            'title' => 'Draw Details',
            'draw' => $draw,
            'winners' => $winners
        ];

        $this->view('draws/view', $data);
    }

    public function schedule()
    {
        $this->requireAuth();
        $this->requireRole('super_admin');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            verify_csrf();

            $campaignId = $_POST['campaign_id'];
            $scheduleType = $_POST['schedule_type'] ?? 'single';

            if ($scheduleType === 'auto_daily') {
                // Auto-schedule daily draws for entire campaign
                $result = $this->drawService->scheduleAutoDailyDraws($campaignId);
                
                if ($result['success']) {
                    flash('success', $result['message']);
                    $this->redirect('draw/pending');
                } else {
                    flash('error', $result['message']);
                }
            } else {
                // Single draw scheduling
                $stationId = $_POST['station_id'] ?? null;
                $programmeId = !empty($_POST['programme_id']) ? $_POST['programme_id'] : null;
                $drawType = $_POST['draw_type'];
                $drawDate = $_POST['draw_date'];
                $winnerCount = intval($_POST['winner_count'] ?? 1);

                // Debug logging
                error_log("Schedule Draw - Station: {$stationId}, Programme: " . ($programmeId ?? 'NULL') . ", Campaign: {$campaignId}");

                $result = $this->drawService->scheduleDraw($campaignId, $drawType, $drawDate, false, $winnerCount, $stationId, $programmeId);

                if (is_array($result) && isset($result['error'])) {
                    flash('error', $result['message']);
                    $this->redirect('draw/schedule');
                    return;
                } elseif ($result) {
                    flash('success', 'Draw scheduled successfully');
                    $this->redirect('draw/show/' . $result);
                    return;
                } else {
                    flash('error', 'Failed to schedule draw.');
                    $this->redirect('draw/schedule');
                    return;
                }
            }
        }

        $campaigns = $this->campaignModel->getActive();

        $data = [
            'title' => 'Schedule Draw',
            'campaigns' => $campaigns
        ];

        $this->view('draws/schedule', $data);
    }

    public function conduct($id)
    {
        $this->requireAuth();
        $this->requireRole('super_admin');

        $draw = $this->drawModel->findById($id);

        if (!$draw) {
            flash('error', 'Draw not found');
            $this->redirect('draw');
        }

        if ($draw->status !== 'pending') {
            flash('error', 'Draw already completed or cancelled');
            $this->redirect('draw/show/' . $id);
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            verify_csrf();

            $result = $this->drawService->conductDraw($id, $_SESSION['user_id']);

            // Return JSON for AJAX requests
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                header('Content-Type: application/json');
                
                if ($result['success']) {
                    // Get winners with ticket details
                    $winners = $this->winnerModel->getByDraw($id);
                    $ticketModel = $this->model('Ticket');
                    
                    $winnersData = array_map(function($winner) use ($ticketModel) {
                        $ticket = $ticketModel->findById($winner->ticket_id);
                        return [
                            'prize_rank' => $winner->prize_rank,
                            'ticket_code' => $ticket->ticket_code ?? 'N/A',
                            'prize_amount' => $winner->prize_amount
                        ];
                    }, $winners);
                    
                    echo json_encode([
                        'success' => true,
                        'message' => $result['message'],
                        'winners' => $winnersData
                    ]);
                } else {
                    echo json_encode($result);
                }
                exit;
            }

            if ($result['success']) {
                flash('success', $result['message'] . ' - ' . $result['winner_count'] . ' winner(s) selected');
                $this->redirect('draw/show/' . $id);
            } else {
                flash('error', $result['message']);
                $this->redirect('draw/show/' . $id);
            }
        }

        $data = [
            'title' => 'Conduct Draw',
            'draw' => $draw
        ];

        $this->view('draws/conduct', $data);
    }
    
    /**
     * Live draw page with animated display
     */
    public function live($id)
    {
        $this->requireAuth();
        $this->requireRole('super_admin');

        $draw = $this->drawModel->findById($id);

        if (!$draw) {
            flash('error', 'Draw not found');
            $this->redirect('draw');
        }

        $campaign = $this->campaignModel->findById($draw->campaign_id);

        $data = [
            'draw_id' => $id,
            'draw' => $draw,
            'campaign' => $campaign
        ];

        $this->view('draws/live', $data);
    }

    public function pending()
    {
        $this->requireAuth();

        $draws = $this->drawModel->getPendingDraws();

        $data = [
            'title' => 'Pending Draws',
            'draws' => $draws
        ];

        $this->view('draws/pending', $data);
    }

    public function winners()
    {
        $this->requireAuth();

        $campaignId = $_GET['campaign'] ?? null;
        $winners = $campaignId ? $this->winnerModel->getByCampaign($campaignId) : [];
        $campaigns = $this->campaignModel->findAll();

        $data = [
            'title' => 'All Winners',
            'winners' => $winners,
            'campaigns' => $campaigns,
            'selected_campaign' => $campaignId
        ];

        $this->view('draws/winners', $data);
    }

    public function updatePrizeStatus($winnerId)
    {
        $this->requireAuth();
        $this->requireRole('super_admin');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            verify_csrf();

            $status = $_POST['status'] ?? 'paid';
            
            $result = $this->winnerModel->update($winnerId, [
                'prize_paid_status' => $status,
                'prize_paid_at' => date('Y-m-d H:i:s')
            ]);

            if ($result) {
                // Send SMS notification
                $winner = $this->winnerModel->findById($winnerId);
                if ($winner && $status === 'paid') {
                    $ticketModel = $this->model('Ticket');
                    $playerModel = $this->model('Player');
                    $campaignModel = $this->model('Campaign');
                    
                    $ticket = $ticketModel->findById($winner->ticket_id);
                    $player = $playerModel->findById($winner->player_id);
                    $draw = $this->drawModel->findById($winner->draw_id);
                    $campaign = $campaignModel->findById($draw->campaign_id);
                    
                    if ($player && $ticket && $campaign) {
                        $smsService = new \App\Services\HubtelSmsService();
                        $smsService->sendPrizePaidNotification(
                            $player->phone,
                            $ticket->ticket_code,
                            $winner->prize_amount,
                            $campaign->name
                        );
                    }
                }
                
                flash('success', 'Prize status updated successfully');
            } else {
                flash('error', 'Failed to update prize status');
            }

            $this->redirect('draw/winners');
        }
    }
    
    /**
     * Verify draw results using cryptographic seed
     * Public endpoint for transparency
     */
    public function verify($drawId)
    {
        $verificationController = new \App\Controllers\DrawVerificationController();
        $verificationController->verify($drawId);
    }
    
    /**
     * Show draw transparency page
     */
    public function transparency($drawId)
    {
        $verificationController = new \App\Controllers\DrawVerificationController();
        $verificationController->transparency($drawId);
        $this->redirect('draw/winners');
    }

    public function analytics()
    {
        $this->requireAuth();

        $campaignId = $_GET['campaign'] ?? null;

        // Get statistics
        $stats = [
            'total_draws' => 0,
            'completed_draws' => 0,
            'pending_draws' => 0,
            'total_winners' => 0,
            'total_prizes' => 0
        ];

        if ($campaignId) {
            $draws = $this->drawModel->getByCampaign($campaignId);
            $stats['total_draws'] = count($draws);
            $stats['completed_draws'] = count(array_filter($draws, fn($d) => $d->status == 'completed'));
            $stats['pending_draws'] = count(array_filter($draws, fn($d) => $d->status == 'pending'));
            
            $winners = $this->winnerModel->getByCampaign($campaignId);
            $stats['total_winners'] = count($winners);
            $stats['total_prizes'] = array_sum(array_column($winners, 'prize_amount'));
        }

        $data = [
            'title' => 'Draw Analytics',
            'stats' => $stats,
            'campaigns' => $this->campaignModel->findAll(),
            'selected_campaign' => $campaignId
        ];

        $this->view('draws/analytics', $data);
    }
}
