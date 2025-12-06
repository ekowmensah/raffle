<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Services\CacheService;

class HomeController extends Controller
{
    private $cache;

    public function __construct()
    {
        $this->cache = new CacheService();
    }

    public function index()
    {
        $this->requireAuth();
        
        // Get models
        $campaignModel = $this->model('Campaign');
        $playerModel = $this->model('Player');
        $paymentModel = $this->model('Payment');
        $ticketModel = $this->model('Ticket');
        $drawModel = $this->model('Draw');
        $stationModel = $this->model('Station');
        
        // Get statistics with caching (5 minutes TTL)
        $stats = $this->cache->remember('dashboard_stats', function() use ($campaignModel, $playerModel, $paymentModel, $ticketModel, $drawModel, $stationModel) {
            return [
                'active_campaigns' => $campaignModel->countByStatus('active'),
                'total_players' => $playerModel->count(),
                'total_tickets' => $ticketModel->count(),
                'total_revenue' => $paymentModel->getTotalRevenue(),
                'pending_draws' => $drawModel->countByStatus('pending'),
                'completed_draws' => $drawModel->countByStatus('completed'),
                'active_stations' => $stationModel->countActive(),
                'total_campaigns' => $campaignModel->count()
            ];
        }, 300); // 5 minutes
        
        // Get recent activity with caching (2 minutes TTL)
        $recentPayments = $this->cache->remember('recent_payments', function() use ($paymentModel) {
            return $paymentModel->getRecent(5);
        }, 120);
        
        $recentTickets = $this->cache->remember('recent_tickets', function() use ($ticketModel) {
            return $ticketModel->getRecent(5);
        }, 120);
        
        $upcomingDraws = $this->cache->remember('upcoming_draws', function() use ($drawModel) {
            return $drawModel->getUpcoming(5);
        }, 120);
        
        $data = [
            'title' => 'Dashboard',
            'user' => $this->getUser(),
            'stats' => $stats,
            'recentPayments' => $recentPayments,
            'recentTickets' => $recentTickets,
            'upcomingDraws' => $upcomingDraws
        ];
        
        $this->view('dashboard/index', $data);
    }
}
