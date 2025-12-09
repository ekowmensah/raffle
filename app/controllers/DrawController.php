<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Services\AuditService;

class DrawController extends Controller
{
    private $drawModel;
    private $winnerModel;
    private $campaignModel;
    private $drawService;
    private $auditService;

    public function __construct()
    {
        $this->drawModel = $this->model('Draw');
        $this->winnerModel = $this->model('DrawWinner');
        $this->campaignModel = $this->model('Campaign');
        
        require_once '../app/services/DrawService.php';
        $this->drawService = new \App\Services\DrawService();
        $this->auditService = new AuditService();
    }

    public function index()
    {
        $this->requireAuth();

        $user = $_SESSION['user'];
        $role = $user->role_name ?? '';
        
        $campaignId = $_GET['campaign'] ?? null;
        
        // Get draws based on role
        if ($role === 'super_admin' || $role === 'auditor') {
            $draws = $campaignId ? $this->drawModel->getByCampaign($campaignId) : $this->drawModel->getCompletedDraws();
            $campaigns = $this->campaignModel->findAll();
        } elseif ($role === 'station_admin') {
            $campaigns = $this->campaignModel->getByStation($user->station_id);
            if ($campaignId) {
                $campaign = $this->campaignModel->findById($campaignId);
                if ($campaign && canAccessCampaign($campaign)) {
                    $draws = $this->drawModel->getByCampaign($campaignId);
                } else {
                    $draws = [];
                }
            } else {
                $draws = $this->drawModel->getCompletedDraws();
            }
        } elseif ($role === 'programme_manager') {
            $campaigns = $this->campaignModel->getByProgramme($user->programme_id);
            if ($campaignId) {
                $campaign = $this->campaignModel->findById($campaignId);
                if ($campaign && canAccessCampaign($campaign)) {
                    $draws = $this->drawModel->getByCampaign($campaignId);
                } else {
                    $draws = [];
                }
            } else {
                $draws = $this->drawModel->getCompletedDraws();
            }
        } else {
            $draws = [];
            $campaigns = [];
        }

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

        // Check access
        if (!canAccessDraw($draw)) {
            flash('error', 'You do not have permission to view this draw');
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
        
        // Programme managers cannot schedule draws, only conduct them
        if (hasRole('programme_manager')) {
            flash('error', 'Programme managers can only conduct scheduled draws, not create new schedules');
            $this->redirect('draw/pending');
        }
        
        // Only super admin and station admin can schedule draws
        if (!hasRole(['super_admin', 'station_admin'])) {
            flash('error', 'You do not have permission to schedule draws');
            $this->redirect('draw');
        }

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

        // Get campaigns based on role
        $user = $_SESSION['user'];
        $role = $user->role_name ?? '';
        
        if ($role === 'super_admin' || $role === 'auditor') {
            $campaigns = $this->campaignModel->getActive();
        } elseif ($role === 'station_admin') {
            $campaigns = $this->campaignModel->getByStation($user->station_id);
        } elseif ($role === 'programme_manager') {
            $campaigns = $this->campaignModel->getByProgramme($user->programme_id);
        } else {
            $campaigns = [];
        }

        $data = [
            'title' => 'Schedule Draw',
            'campaigns' => $campaigns
        ];

        $this->view('draws/schedule', $data);
    }

    public function conduct($id)
    {
        $this->requireAuth();

        $draw = $this->drawModel->findById($id);

        if (!$draw) {
            flash('error', 'Draw not found');
            $this->redirect('draw');
        }

        if ($draw->status !== 'pending') {
            flash('error', 'Draw already completed or cancelled');
            $this->redirect('draw/show/' . $id);
        }

        // Role-based access control
        $user = $_SESSION['user'];
        $role = $user->role_name ?? '';
        
        if ($role === 'programme_manager') {
            // Programme managers can only conduct draws on the scheduled date
            $drawDate = date('Y-m-d', strtotime($draw->draw_date));
            $today = date('Y-m-d');
            
            if ($drawDate !== $today) {
                flash('error', 'Programme managers can only conduct draws on the scheduled date. Draw date: ' . date('M d, Y', strtotime($draw->draw_date)));
                $this->redirect('draw/pending');
            }
            
            // Check if draw belongs to their programme
            if (!canAccessDraw($draw)) {
                flash('error', 'You do not have permission to conduct this draw');
                $this->redirect('draw/pending');
            }
        } elseif (!hasRole(['super_admin', 'station_admin'])) {
            flash('error', 'You do not have permission to conduct draws');
            $this->redirect('draw');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            verify_csrf();

            // Return JSON for AJAX requests
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                header('Content-Type: application/json');
                
                try {
                    $result = $this->drawService->conductDraw($id, $_SESSION['user_id']);
                    
                    if ($result['success']) {
                        // Log draw conducted
                        $draw = $this->drawModel->findById($id);
                        $campaign = $this->campaignModel->findById($draw->campaign_id);
                        $this->auditService->logDrawConducted(
                            $_SESSION['user_id'],
                            $id,
                            $campaign->name ?? 'Unknown',
                            $result['winner_count']
                        );
                        // Get winners with ticket details
                        $winners = $this->winnerModel->getByDraw($id);
                        $ticketModel = $this->model('Ticket');
                        
                        $winnersData = array_map(function($winner) use ($ticketModel) {
                            $ticket = $ticketModel->findById($winner->ticket_id);
                            $playerModel = $this->model('Player');
                            $player = $playerModel->findById($winner->player_id);
                            
                            // Mask last 3 digits of phone number
                            $phone = $player->phone ?? 'N/A';
                            $maskedPhone = strlen($phone) > 3 ? substr($phone, 0, -3) . 'XXX' : $phone;
                            
                            return [
                                'prize_rank' => $winner->prize_rank,
                                'ticket_code' => $ticket->ticket_code ?? 'N/A',
                                'prize_amount' => $winner->prize_amount,
                                'player_phone' => $maskedPhone
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
                } catch (\Exception $e) {
                    error_log("Draw conduct error: " . $e->getMessage());
                    echo json_encode([
                        'success' => false,
                        'message' => 'Draw failed: ' . $e->getMessage()
                    ]);
                }
                exit;
            }
            
            // Non-AJAX request (standard form submission)
            $result = $this->drawService->conductDraw($id, $_SESSION['user_id']);

            if ($result['success']) {
                // Log draw conducted
                $draw = $this->drawModel->findById($id);
                $campaign = $this->campaignModel->findById($draw->campaign_id);
                $this->auditService->logDrawConducted(
                    $_SESSION['user_id'],
                    $id,
                    $campaign->name ?? 'Unknown',
                    $result['winner_count']
                );
                flash('success', $result['message'] . ' - ' . $result['winner_count'] . ' winner(s) selected');
                $this->redirect('draw/show/' . $id);
            } else {
                flash('error', $result['message']);
                $this->redirect('draw/show/' . $id);
            }
        }

        // Get campaign details
        $campaign = $this->campaignModel->findById($draw->campaign_id);
        
        $data = [
            'title' => 'Conduct Draw',
            'draw' => $draw,
            'campaign' => $campaign
        ];

        $this->view('draws/conduct', $data);
    }
    
    /**
     * Live draw page with animated display
     */
    public function live($id)
    {
        $this->requireAuth();
        
        // Check permission
        if (!can('conduct_draw')) {
            flash('error', 'You do not have permission to conduct draws');
            $this->redirect('draw');
        }

        $draw = $this->drawModel->findById($id);

        if (!$draw) {
            flash('error', 'Draw not found');
            $this->redirect('draw');
        }

        // Check access to this draw
        if (!canAccessDraw($draw)) {
            flash('error', 'You do not have permission to conduct this draw');
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

    public function edit($id)
    {
        $this->requireAuth();

        // Only super admin and station admin can edit draws
        if (!hasRole(['super_admin', 'station_admin'])) {
            flash('error', 'You do not have permission to edit draws');
            $this->redirect('draw');
        }

        $draw = $this->drawModel->findById($id);

        if (!$draw) {
            flash('error', 'Draw not found');
            $this->redirect('draw');
        }

        // Only pending draws can be edited
        if ($draw->status !== 'pending') {
            flash('error', 'Only pending draws can be edited');
            $this->redirect('draw/show/' . $id);
        }

        // Station admin can only edit their station's draws
        if (hasRole('station_admin') && !canAccessDraw($draw)) {
            flash('error', 'You do not have permission to edit this draw');
            $this->redirect('draw');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            verify_csrf();

            $drawDate = $_POST['draw_date'];
            $drawType = $_POST['draw_type'];
            $winnerCount = intval($_POST['winner_count'] ?? 1);

            // Validation
            if (empty($drawDate) || empty($drawType)) {
                flash('error', 'Please fill in all required fields');
                $this->redirect('draw/edit/' . $id);
                return;
            }

            $data = [
                'draw_date' => $drawDate,
                'draw_type' => $drawType,
                'winner_count' => $winnerCount
            ];

            if ($this->drawModel->update($id, $data)) {
                flash('success', 'Draw updated successfully');
                $this->redirect('draw/show/' . $id);
            } else {
                flash('error', 'Failed to update draw');
                $this->redirect('draw/edit/' . $id);
            }
        }

        $campaign = $this->campaignModel->findById($draw->campaign_id);

        $data = [
            'title' => 'Edit Draw',
            'draw' => $draw,
            'campaign' => $campaign
        ];

        $this->view('draws/edit', $data);
    }

    public function pending()
    {
        $this->requireAuth();

        $user = $_SESSION['user'];
        $role = $user->role_name ?? '';
        
        // Get pending draws based on role
        if ($role === 'super_admin' || $role === 'auditor') {
            $draws = $this->drawModel->getPendingDraws();
        } elseif ($role === 'station_admin') {
            // Get pending draws for station admin's station
            $allDraws = $this->drawModel->getPendingDraws();
            $filteredDraws = [];
            foreach ($allDraws as $draw) {
                if (canAccessDraw($draw)) {
                    $filteredDraws[] = $draw;
                }
            }
            $draws = $filteredDraws;
        } elseif ($role === 'programme_manager') {
            // Get pending draws for programme manager's programme
            if (!$user->programme_id) {
                // Programme manager without programme assigned
                $draws = [];
            } else {
                $allDraws = $this->drawModel->getPendingDraws();
                $filteredDraws = [];
                foreach ($allDraws as $draw) {
                    if (canAccessDraw($draw)) {
                        $filteredDraws[] = $draw;
                    }
                }
                $draws = $filteredDraws;
            }
        } else {
            $draws = [];
        }

        $data = [
            'title' => 'Pending Draws',
            'draws' => $draws
        ];

        $this->view('draws/pending', $data);
    }

    public function winners()
    {
        $this->requireAuth();

        // Check permission
        if (!hasRole(['super_admin', 'station_admin', 'programme_manager', 'auditor'])) {
            flash('error', 'You do not have permission to view winners');
            $this->redirect('home');
        }

        $user = $_SESSION['user'];
        $role = $user->role_name ?? '';
        
        $campaignId = $_GET['campaign'] ?? null;
        
        // Get campaigns based on role
        if ($role === 'super_admin' || $role === 'auditor') {
            $campaigns = $this->campaignModel->findAll();
        } elseif ($role === 'station_admin') {
            $campaigns = $this->campaignModel->getByStation($user->station_id);
        } elseif ($role === 'programme_manager') {
            $campaigns = $this->campaignModel->getByProgramme($user->programme_id);
        } else {
            $campaigns = [];
        }
        
        $winners = $campaignId ? $this->winnerModel->getByCampaign($campaignId) : [];
        
        // Get campaign details if selected
        $selectedCampaignData = null;
        if ($campaignId) {
            $selectedCampaignData = $this->campaignModel->findById($campaignId);
        }

        $data = [
            'title' => 'All Winners',
            'winners' => $winners,
            'campaigns' => $campaigns,
            'selected_campaign' => $campaignId,
            'campaign' => $selectedCampaignData
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
                // Log prize payment
                $winner = $this->winnerModel->findById($winnerId);
                $this->auditService->logPrizePayment(
                    $_SESSION['user_id'],
                    $winnerId,
                    [
                        'status' => $status,
                        'prize_amount' => $winner->prize_amount ?? 0,
                        'player_id' => $winner->player_id ?? null
                    ]
                );
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
                        require_once '../app/services/SMS/HubtelSmsService.php';
                        $smsService = new \App\Services\SMS\HubtelSmsService();
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
    public function verify($drawId = null)
    {
        if (!$drawId) {
            echo json_encode(['error' => 'Draw ID is required']);
            exit;
        }
        
        $verificationController = new \App\Controllers\DrawVerificationController();
        $verificationController->verify($drawId);
    }
    
    /**
     * Show draw transparency page
     */
    public function transparency($drawId = null)
    {
        if (!$drawId) {
            flash('error', 'Draw ID is required');
            $this->redirect('draw');
            return;
        }
        
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
