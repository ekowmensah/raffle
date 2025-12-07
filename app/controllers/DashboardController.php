<?php

namespace App\Controllers;

use App\Core\Controller;

class DashboardController extends Controller
{
    /**
     * Main dashboard - routes to role-specific dashboard
     */
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
        // Use existing home dashboard
        $this->redirect('home');
    }

    /**
     * Station Admin Dashboard - Station-scoped
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
        
        $data = [
            'title' => 'Station Dashboard',
            'station' => $stationModel->findById($stationId),
            'programmes' => $programmeModel->getByStation($stationId),
            'campaigns' => $campaignModel->getByStation($stationId),
            'users' => $userModel->getByStation($stationId),
            'stats' => [
                'total_programmes' => $programmeModel->countByStation($stationId),
                'active_campaigns' => $campaignModel->countActiveByStation($stationId),
                'total_users' => $userModel->countByStation($stationId),
                'station_revenue' => $paymentModel->getTotalByStation($stationId)
            ]
        ];
        
        $this->view('dashboards/station_admin', $data);
    }

    /**
     * Programme Manager Dashboard - Programme-scoped with draw focus
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
                'tickets_sold_today' => $ticketModel->countTodayByProgramme($programmeId)
            ]
        ];
        
        $this->view('dashboards/programme_manager', $data);
    }

    /**
     * Finance Dashboard - Financial operations
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
     * Auditor Dashboard - Audit & compliance focus
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
