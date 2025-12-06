<?php

namespace App\Controllers;

use App\Core\Controller;

class TicketController extends Controller
{
    private $ticketModel;
    private $ticketService;

    public function __construct()
    {
        $this->ticketModel = $this->model('Ticket');
        
        require_once '../app/services/TicketGeneratorService.php';
        $this->ticketService = new \App\Services\TicketGeneratorService();
    }

    public function index()
    {
        $this->requireAuth();

        $campaignModel = $this->model('Campaign');
        $campaigns = $campaignModel->getActive();

        $campaignId = $_GET['campaign'] ?? null;
        $tickets = $campaignId ? $this->ticketModel->getByCampaign($campaignId) : [];

        $data = [
            'title' => 'Tickets',
            'campaigns' => $campaigns,
            'tickets' => $tickets,
            'selected_campaign' => $campaignId
        ];

        $this->view('tickets/index', $data);
    }

    public function verify()
    {
        $data = ['title' => 'Verify Ticket'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $ticketCode = strtoupper(sanitize($_POST['ticket_code']));
            
            $result = $this->ticketService->verifyTicket($ticketCode);
            $data['result'] = $result;
        }

        $this->view('tickets/verify', $data);
    }

    public function show($id)
    {
        $this->requireAuth();

        $ticket = $this->ticketModel->findById($id);

        if (!$ticket) {
            flash('error', 'Ticket not found');
            $this->redirect('ticket');
        }

        $data = [
            'title' => 'Ticket Details',
            'ticket' => $ticket
        ];

        $this->view('tickets/view', $data);
    }

    public function myTickets()
    {
        // Public endpoint for players to check their tickets
        $data = ['title' => 'My Tickets'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $phone = sanitize($_POST['phone']);
            
            $playerModel = $this->model('Player');
            $player = $playerModel->findByPhone($phone);

            if ($player) {
                $tickets = $this->ticketModel->getByPlayer($player->id);
                $data['tickets'] = $tickets;
                $data['player'] = $player;
            } else {
                $data['error'] = 'No tickets found for this phone number';
            }
        }

        $this->view('tickets/my-tickets', $data);
    }

    public function bulkGenerate()
    {
        $this->requireAuth();
        $this->requireRole('super_admin');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            verify_csrf();

            $campaignId = $_POST['campaign_id'];
            $stationId = $_POST['station_id'];
            $programmeId = $_POST['programme_id'];
            $count = intval($_POST['count']);
            
            // Create test player
            $playerModel = $this->model('Player');
            $player = $playerModel->findOrCreate('+233200000000', 'Test Player');

            $result = $this->ticketService->generateBulkTickets(
                $campaignId,
                $stationId,
                $programmeId,
                $player->id,
                $count
            );

            if ($result) {
                flash('success', $count . ' tickets generated successfully');
            } else {
                flash('error', 'Failed to generate tickets');
            }

            $this->redirect('ticket');
        }

        $campaignModel = $this->model('Campaign');
        $stationModel = $this->model('Station');
        $programmeModel = $this->model('Programme');

        $data = [
            'title' => 'Bulk Generate Tickets',
            'campaigns' => $campaignModel->getActive(),
            'stations' => $stationModel->findAll(),
            'programmes' => $programmeModel->findAll()
        ];

        $this->view('tickets/bulk-generate', $data);
    }
}
