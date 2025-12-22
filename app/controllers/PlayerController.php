<?php

namespace App\Controllers;

use App\Core\Controller;

class PlayerController extends Controller
{
    private $playerModel;

    public function __construct()
    {
        $this->playerModel = $this->model('Player');
    }

    public function index()
    {
        $this->requireAuth();

        // Check permission
        if (!can('view_players') && !hasRole(['super_admin', 'station_admin', 'programme_manager', 'auditor'])) {
            flash('error', 'You do not have permission to view players');
            $this->redirect('home');
        }

        // Get players based on role
        $user = $_SESSION['user'];
        $role = $user->role_name ?? '';
        
        if ($role === 'super_admin' || $role === 'auditor') {
            $players = $this->playerModel->getWithStats();
            $station = null;
        } elseif ($role === 'station_admin') {
            $players = $this->playerModel->getByStation($user->station_id);
            $stationModel = $this->model('Station');
            $station = $stationModel->findById($user->station_id);
        } elseif ($role === 'programme_manager') {
            $players = $this->playerModel->getByProgramme($user->programme_id);
            $station = null;
        } else {
            $players = [];
            $station = null;
        }

        // Calculate stats
        $totalPlayers = count($players);
        $totalTickets = 0;
        $totalSpent = 0;
        $totalWinners = 0;
        
        foreach ($players as $player) {
            $totalTickets += ($player->total_tickets ?? 0);
            $totalSpent += ($player->total_spent ?? 0);
            if (($player->total_wins ?? 0) > 0) {
                $totalWinners++;
            }
        }

        $data = [
            'title' => 'Players',
            'players' => $players,
            'station' => $station,
            'stats' => [
                'total_players' => $totalPlayers,
                'total_tickets' => $totalTickets,
                'total_spent' => $totalSpent,
                'total_winners' => $totalWinners
            ]
        ];

        $this->view('players/index', $data);
    }

    public function show($id)
    {
        $this->requireAuth();

        $player = $this->playerModel->findById($id);

        if (!$player) {
            flash('error', 'Player not found');
            $this->redirect('player');
        }

        $tickets = $this->playerModel->getTickets($id);
        
        // Get player stats
        $ticketModel = $this->model('Ticket');
        $paymentModel = $this->model('Payment');
        $winnerModel = $this->model('DrawWinner');
        
        $totalTickets = count($tickets);
        
        // Get total spent
        $payments = $paymentModel->getByPlayer($id);
        $totalSpent = array_sum(array_map(function($p) {
            return $p->status === 'success' ? $p->amount : 0;
        }, $payments));
        
        // Get wins
        $wins = $winnerModel->getByPlayer($id);
        $totalWins = count($wins);
        $totalWinnings = array_sum(array_column($wins, 'prize_amount'));

        $data = [
            'title' => 'Player Details',
            'player' => $player,
            'tickets' => $tickets,
            'wins' => $wins,
            'stats' => [
                'total_tickets' => $totalTickets,
                'total_spent' => $totalSpent,
                'total_wins' => $totalWins,
                'total_winnings' => $totalWinnings
            ]
        ];

        $this->view('players/view', $data);
    }

    // Alias for show() method to support /player/viewDetails/{id} URLs
    public function viewDetails($id)
    {
        return $this->show($id);
    }
}
