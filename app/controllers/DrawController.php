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
                $programmeId = $_POST['programme_id'] ?? null;
                $drawType = $_POST['draw_type'];
                $drawDate = $_POST['draw_date'];
                $winnerCount = intval($_POST['winner_count'] ?? 1);

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

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            verify_csrf();

            $status = $_POST['status'];
            
            if ($this->winnerModel->updatePrizeStatus($winnerId, $status)) {
                flash('success', 'Prize status updated');
            } else {
                flash('error', 'Failed to update status');
            }
            
            // Redirect back to the referring page
            if (isset($_SERVER['HTTP_REFERER'])) {
                header('Location: ' . $_SERVER['HTTP_REFERER']);
                exit;
            }
        }

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
