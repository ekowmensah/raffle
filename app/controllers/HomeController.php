<?php

namespace App\Controllers;

use App\Core\Controller;

class HomeController extends Controller
{
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
        
        // Get statistics
        $stats = [
            'active_campaigns' => $campaignModel->countByStatus('active'),
            'total_players' => $playerModel->count(),
            'total_tickets' => $ticketModel->count(),
            'total_revenue' => $paymentModel->getTotalRevenue(),
            'pending_draws' => $drawModel->countByStatus('pending'),
            'completed_draws' => $drawModel->countByStatus('completed'),
            'active_stations' => $stationModel->countActive(),
            'total_campaigns' => $campaignModel->count()
        ];
        
        // Get recent activity
        $recentPayments = $paymentModel->getRecent(5);
        $recentTickets = $ticketModel->getRecent(5);
        $upcomingDraws = $drawModel->getUpcoming(5);
        
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
