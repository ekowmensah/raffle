<?php

namespace App\Controllers\Api;

class TicketController extends ApiController
{
    private $ticketModel;
    private $paymentModel;
    private $campaignModel;
    
    public function __construct()
    {
        parent::__construct();
        require_once '../app/models/Ticket.php';
        require_once '../app/models/Payment.php';
        require_once '../app/models/Campaign.php';
        $this->ticketModel = new \App\Models\Ticket();
        $this->paymentModel = new \App\Models\Payment();
        $this->campaignModel = new \App\Models\Campaign();
    }
    
    /**
     * Get player's tickets
     * GET /api/tickets
     */
    public function index()
    {
        $player = $this->requireAuth();
        
        $campaignId = $_GET['campaign_id'] ?? null;
        $page = $_GET['page'] ?? 1;
        $perPage = $_GET['per_page'] ?? 20;
        
        $tickets = $this->ticketModel->getByPlayer($player->id, $campaignId);
        
        $data = [];
        foreach ($tickets as $ticket) {
            $data[] = $this->formatTicket($ticket);
        }
        
        $this->success($data);
    }
    
    /**
     * Get ticket by code
     * GET /api/tickets/{code}
     */
    public function show($code)
    {
        $player = $this->requireAuth();
        
        $ticket = $this->ticketModel->getByCode($code);
        
        if (!$ticket) {
            $this->error('Ticket not found', 404);
        }
        
        // Verify ownership
        if ($ticket->player_id != $player->id) {
            $this->error('Unauthorized', 403);
        }
        
        $this->success($this->formatTicketDetail($ticket));
    }
    
    /**
     * Purchase tickets
     * POST /api/tickets/purchase
     */
    public function purchase()
    {
        $player = $this->requireAuth();
        $input = $this->getJsonInput();
        
        $errors = $this->validate($input, [
            'campaign_id' => 'required|numeric',
            'station_id' => 'required|numeric',
            'programme_id' => 'required|numeric',
            'quantity' => 'required|numeric',
            'payment_method' => 'required'
        ]);
        
        if (!empty($errors)) {
            $this->error('Validation failed', 422, $errors);
        }
        
        // Validate campaign
        $campaign = $this->campaignModel->find($input['campaign_id']);
        
        if (!$campaign || !$campaign->is_active) {
            $this->error('Campaign not available', 400);
        }
        
        $quantity = (int)$input['quantity'];
        if ($quantity < 1 || $quantity > 10) {
            $this->error('Quantity must be between 1 and 10', 400);
        }
        
        $totalAmount = $quantity * $campaign->ticket_price;
        
        // Create payment record
        $reference = 'APP' . time() . rand(1000, 9999);
        
        $paymentData = [
            'player_id' => $player->id,
            'campaign_id' => $input['campaign_id'],
            'station_id' => $input['station_id'],
            'programme_id' => $input['programme_id'],
            'amount' => $totalAmount,
            'gateway' => $input['payment_method'],
            'gateway_reference' => $reference,
            'internal_reference' => $reference,
            'status' => 'pending',
            'channel' => 'mobile_app'
        ];
        
        $paymentId = $this->paymentModel->create($paymentData);
        
        $this->success([
            'payment_id' => $paymentId,
            'reference' => $reference,
            'amount' => $totalAmount,
            'quantity' => $quantity,
            'status' => 'pending',
            'message' => 'Payment initiated. Complete payment to receive tickets.'
        ], 'Payment created successfully', 201);
    }
    
    /**
     * Verify ticket code
     * POST /api/tickets/verify
     */
    public function verify()
    {
        $input = $this->getJsonInput();
        
        $errors = $this->validate($input, [
            'ticket_code' => 'required'
        ]);
        
        if (!empty($errors)) {
            $this->error('Validation failed', 422, $errors);
        }
        
        $ticket = $this->ticketModel->getByCode($input['ticket_code']);
        
        if (!$ticket) {
            $this->error('Invalid ticket code', 404);
        }
        
        $this->success($this->formatTicketDetail($ticket), 'Ticket verified');
    }
    
    /**
     * Get ticket statistics for player
     * GET /api/tickets/stats
     */
    public function stats()
    {
        $player = $this->requireAuth();
        
        $db = new \App\Core\Database();
        
        // Total tickets
        $db->query("SELECT COUNT(*) as total FROM tickets WHERE player_id = :player_id");
        $db->bind(':player_id', $player->id);
        $totalTickets = $db->single();
        
        // Active tickets (in active campaigns)
        $db->query("SELECT COUNT(*) as total 
                   FROM tickets t
                   INNER JOIN raffle_campaigns rc ON t.campaign_id = rc.id
                   WHERE t.player_id = :player_id 
                   AND rc.is_active = 1 
                   AND rc.end_date >= CURDATE()");
        $db->bind(':player_id', $player->id);
        $activeTickets = $db->single();
        
        // Total spent
        $db->query("SELECT SUM(amount) as total 
                   FROM payments 
                   WHERE player_id = :player_id 
                   AND status = 'success'");
        $db->bind(':player_id', $player->id);
        $totalSpent = $db->single();
        
        // Total winnings
        $db->query("SELECT COUNT(*) as win_count, SUM(prize_amount) as total_winnings
                   FROM draw_winners dw
                   INNER JOIN tickets t ON dw.ticket_id = t.id
                   WHERE t.player_id = :player_id");
        $db->bind(':player_id', $player->id);
        $winnings = $db->single();
        
        $this->success([
            'total_tickets' => (int)$totalTickets->total,
            'active_tickets' => (int)$activeTickets->total,
            'total_spent' => (float)($totalSpent->total ?? 0),
            'total_wins' => (int)($winnings->win_count ?? 0),
            'total_winnings' => (float)($winnings->total_winnings ?? 0),
            'loyalty_points' => (int)$player->loyalty_points
        ]);
    }
    
    /**
     * Format ticket for list view
     */
    private function formatTicket($ticket)
    {
        return [
            'id' => $ticket->id,
            'ticket_code' => $ticket->ticket_code,
            'campaign_id' => $ticket->campaign_id,
            'campaign_name' => $ticket->campaign_name ?? null,
            'purchase_date' => $ticket->created_at,
            'status' => $this->getTicketStatus($ticket)
        ];
    }
    
    /**
     * Format ticket detail view
     */
    private function formatTicketDetail($ticket)
    {
        $db = new \App\Core\Database();
        
        // Check if won
        $db->query("SELECT dw.*, d.draw_date, d.draw_type 
                   FROM draw_winners dw
                   INNER JOIN draws d ON dw.draw_id = d.id
                   WHERE dw.ticket_id = :ticket_id");
        $db->bind(':ticket_id', $ticket->id);
        $winner = $db->single();
        
        return [
            'id' => $ticket->id,
            'ticket_code' => $ticket->ticket_code,
            'campaign' => [
                'id' => $ticket->campaign_id,
                'name' => $ticket->campaign_name ?? null
            ],
            'station' => [
                'id' => $ticket->station_id,
                'name' => $ticket->station_name ?? null
            ],
            'programme' => [
                'id' => $ticket->programme_id,
                'name' => $ticket->programme_name ?? null
            ],
            'purchase_date' => $ticket->created_at,
            'status' => $this->getTicketStatus($ticket),
            'is_winner' => $winner ? true : false,
            'prize' => $winner ? [
                'amount' => (float)$winner->prize_amount,
                'rank' => $winner->prize_rank,
                'draw_date' => $winner->draw_date,
                'draw_type' => $winner->draw_type,
                'paid_status' => $winner->prize_paid_status
            ] : null
        ];
    }
    
    /**
     * Get ticket status
     */
    private function getTicketStatus($ticket)
    {
        $db = new \App\Core\Database();
        
        $db->query("SELECT * FROM raffle_campaigns WHERE id = :id");
        $db->bind(':id', $ticket->campaign_id);
        $campaign = $db->single();
        
        if (!$campaign) {
            return 'expired';
        }
        
        if ($campaign->is_active && strtotime($campaign->end_date) >= time()) {
            return 'active';
        }
        
        return 'expired';
    }
}
