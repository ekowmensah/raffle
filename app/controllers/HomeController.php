<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Services\CacheService;

class HomeController extends Controller
{
    private $cache;
    private $dashboardController;

    public function __construct()
    {
        $this->cache = new CacheService();
    }

    public function index()
    {
        $this->requireAuth();
        
        $user = $_SESSION['user'];
        $role = $user->role_name ?? '';
        
        // Route to role-specific dashboard
        switch ($role) {
            case 'super_admin':
                return $this->superAdminDashboard();
            
            case 'station_admin':
                return $this->stationAdminDashboard();
            
            case 'programme_manager':
                return $this->programmeManagerDashboard();
            
            case 'finance':
                return $this->financeDashboard();
            
            case 'auditor':
                return $this->auditorDashboard();
            
            default:
                return $this->defaultDashboard();
        }
    }

    /**
     * Super Admin Dashboard - Global overview
     */
    private function superAdminDashboard()
    {
        // Get models
        $campaignModel = $this->model('Campaign');
        $playerModel = $this->model('Player');
        $paymentModel = $this->model('Payment');
        $ticketModel = $this->model('Ticket');
        $drawModel = $this->model('Draw');
        $stationModel = $this->model('Station');
        $revenueModel = $this->model('RevenueAllocation');
        $withdrawalModel = $this->model('Withdrawal');
        
        // Get revenue breakdown
        $revenueData = $revenueModel->getGlobalRevenue();
        $revenue = [
            'gross_revenue' => floatval($revenueData->gross_revenue ?? 0),
            'platform_revenue' => floatval($revenueData->platform_revenue ?? 0),
            'station_revenue' => floatval($revenueData->station_revenue ?? 0),
            'programme_revenue' => floatval($revenueData->programme_revenue ?? 0),
            'prize_pool' => floatval($revenueData->prize_pool ?? 0),
            'platform_percent' => 30,
            'station_percent' => 20,
            'programme_percent' => 0,
            'prize_pool_percent' => 50
        ];
        
        // Get prize pool breakdown
        $prizePoolData = $revenueModel->getPrizePoolBreakdown();
        $prizePool = [
            'total' => floatval($prizePoolData->total ?? 0),
            'daily' => floatval($prizePoolData->daily ?? 0),
            'final' => floatval($prizePoolData->final ?? 0),
            'bonus' => floatval($prizePoolData->bonus ?? 0),
            'paid_out' => floatval($prizePoolData->paid_out ?? 0),
            'remaining' => floatval($prizePoolData->remaining ?? 0),
            'daily_percent' => floatval($prizePoolData->daily_percent ?? 0),
            'final_percent' => floatval($prizePoolData->final_percent ?? 0),
            'bonus_percent' => floatval($prizePoolData->bonus_percent ?? 0)
        ];
        
        // Get statistics
        $stats = [
            'total_stations' => $stationModel->count(),
            'active_stations' => $stationModel->countActive(),
            'total_campaigns' => $campaignModel->count(),
            'active_campaigns' => $campaignModel->countByStatus('active'),
            'total_tickets' => $ticketModel->count(),
            'tickets_today' => $ticketModel->countToday(),
            'total_players' => $playerModel->count(),
            'new_players_today' => $playerModel->countToday(),
            'completed_draws' => $drawModel->countByStatus('completed'),
            'pending_draws' => $drawModel->countByStatus('pending'),
            'pending_withdrawals' => $withdrawalModel->countPending()
        ];
        
        // Get top performers
        $topStations = $stationModel->getTopByRevenue(5);
        $topCampaigns = $campaignModel->getTopByRevenue(5);
        $topPlayers = $playerModel->getTopBySpending(5);
        
        // Get recent activity
        $recentPayments = $paymentModel->getSuccessfulPayments();
        $pendingWithdrawals = $withdrawalModel->getPending();
        
        $data = [
            'title' => 'Super Admin Dashboard',
            'user' => $this->getUser(),
            'revenue' => $revenue,
            'prizePool' => $prizePool,
            'stats' => $stats,
            'topStations' => $topStations,
            'topCampaigns' => $topCampaigns,
            'topPlayers' => $topPlayers,
            'recentPayments' => $recentPayments,
            'pendingWithdrawals' => $pendingWithdrawals
        ];
        
        $this->view('dashboard/super_admin', $data);
    }

    /**
     * Station Admin Dashboard
     */
    private function stationAdminDashboard()
    {
        $stationId = $_SESSION['user']->station_id;
        
        if (!$stationId) {
            flash('error', 'No station assigned to your account');
            $this->redirect('home');
        }
        
        $stationModel = $this->model('Station');
        $programmeModel = $this->model('Programme');
        $campaignModel = $this->model('Campaign');
        $userModel = $this->model('User');
        $paymentModel = $this->model('Payment');
        $walletModel = $this->model('StationWallet');
        $ticketModel = $this->model('Ticket');
        $playerModel = $this->model('Player');
        $drawModel = $this->model('Draw');
        $withdrawalModel = $this->model('Withdrawal');
        $revenueModel = $this->model('RevenueAllocation');
        
        // Get or create wallet
        $wallet = $walletModel->getOrCreate($stationId);
        
        // Get station revenue from revenue allocations
        $revenueData = $revenueModel->getStationRevenue($stationId);
        $stationRevenue = ($revenueData->total_station ?? 0) + ($revenueData->total_programme ?? 0);
        
        // Get total withdrawn
        $totalWithdrawn = $withdrawalModel->getTotalCompletedByStation($stationId);
        
        $data = [
            'title' => 'Station Dashboard',
            'station' => $stationModel->findById($stationId),
            'wallet' => $wallet,
            'programmes' => $programmeModel->getByStation($stationId),
            'campaigns' => $campaignModel->getByStation($stationId),
            'users' => $userModel->getByStation($stationId),
            'programme_revenue' => $paymentModel->getRevenuePerProgrammeByStation($stationId),
            'stats' => [
                'total_programmes' => $programmeModel->countByStation($stationId),
                'active_campaigns' => $campaignModel->countActiveByStation($stationId),
                'total_users' => $userModel->countByStation($stationId),
                'station_revenue' => $stationRevenue,
                'total_tickets' => $ticketModel->countByStation($stationId),
                'total_players' => $playerModel->countByStation($stationId),
                'pending_draws' => $drawModel->countPendingByStation($stationId),
                'total_withdrawn' => $totalWithdrawn
            ]
        ];
        
        $this->view('dashboards/station_admin', $data);
    }

    /**
     * Programme Manager Dashboard
     */
    private function programmeManagerDashboard()
    {
        $programmeId = $_SESSION['user']->programme_id;
        
        if (!$programmeId) {
            flash('error', 'No programme assigned to your account');
            $this->redirect('home');
        }
        
        $programmeModel = $this->model('Programme');
        $drawModel = $this->model('Draw');
        $campaignModel = $this->model('Campaign');
        $ticketModel = $this->model('Ticket');
        $paymentModel = $this->model('Payment');
        
        $data = [
            'title' => 'Programme Dashboard',
            'programme' => $programmeModel->findById($programmeId),
            'pending_draws' => $drawModel->getPendingByProgramme($programmeId),
            'today_draws' => $drawModel->getTodayByProgramme($programmeId),
            'campaigns' => $campaignModel->getByProgramme($programmeId),
            'stats' => [
                'pending_draws' => $drawModel->countPendingByProgramme($programmeId),
                'completed_today' => $drawModel->countCompletedTodayByProgramme($programmeId),
                'active_campaigns' => $campaignModel->countActiveByProgramme($programmeId),
                'tickets_sold_today' => $ticketModel->countTodayByProgramme($programmeId),
                'total_revenue' => $paymentModel->getRevenueByProgramme($programmeId)
            ]
        ];
        
        $this->view('dashboards/programme_manager', $data);
    }

    /**
     * Finance Dashboard
     */
    private function financeDashboard()
    {
        $paymentModel = $this->model('Payment');
        
        $data = [
            'title' => 'Finance Dashboard',
            'recent_payments' => $paymentModel->getRecent(20),
            'stats' => [
                'today_revenue' => $paymentModel->getTodayRevenue(),
                'pending_payments' => $paymentModel->countByStatus('pending'),
                'successful_today' => $paymentModel->countTodayByStatus('success'),
                'total_revenue' => $paymentModel->getTotalRevenue()
            ]
        ];
        
        $this->view('dashboards/finance', $data);
    }

    /**
     * Auditor Dashboard
     */
    private function auditorDashboard()
    {
        $auditModel = $this->model('AuditLog');
        $securityModel = $this->model('SecurityLog');
        
        $data = [
            'title' => 'Auditor Dashboard',
            'recent_audits' => $auditModel->getWithFilters(['limit' => 50]),
            'recent_security' => $securityModel->getRecentEvents(20),
            'stats' => [
                'total_logs_today' => $auditModel->countToday(),
                'critical_actions' => count($auditModel->getCriticalActions(10)),
                'security_events' => $securityModel->countToday()
            ]
        ];
        
        $this->view('dashboards/auditor', $data);
    }

    /**
     * Default dashboard for unknown roles
     */
    private function defaultDashboard()
    {
        $data = [
            'title' => 'Dashboard',
            'user' => $_SESSION['user']
        ];
        
        $this->view('dashboards/default', $data);
    }
}
