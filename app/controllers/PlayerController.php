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

        $players = $this->playerModel->getWithStats();

        $data = [
            'title' => 'Players',
            'players' => $players
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

        $data = [
            'title' => 'Player Details',
            'player' => $player,
            'tickets' => $tickets
        ];

        $this->view('players/view', $data);
    }

    // Alias for show() method to support /player/viewDetails/{id} URLs
    public function viewDetails($id)
    {
        return $this->show($id);
    }
}
