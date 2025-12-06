<?php

namespace App\Controllers\Api;

class DrawController extends ApiController
{
    private $drawModel;
    private $drawWinnerModel;
    
    public function __construct()
    {
        parent::__construct();
        require_once '../app/models/Draw.php';
        require_once '../app/models/DrawWinner.php';
        $this->drawModel = new \App\Models\Draw();
        $this->drawWinnerModel = new \App\Models\DrawWinner();
    }
    
    /**
     * Get draws
     * GET /api/draws
     */
    public function index()
    {
        $campaignId = $_GET['campaign_id'] ?? null;
        $drawType = $_GET['draw_type'] ?? null;
        $status = $_GET['status'] ?? 'completed';
        
        $draws = $this->drawModel->getDraws($campaignId, $drawType, $status);
        
        $data = [];
        foreach ($draws as $draw) {
            $data[] = $this->formatDraw($draw);
        }
        
        $this->success($data);
    }
    
    /**
     * Get draw by ID
     * GET /api/draws/{id}
     */
    public function show($id)
    {
        $draw = $this->drawModel->find($id);
        
        if (!$draw) {
            $this->error('Draw not found', 404);
        }
        
        $this->success($this->formatDrawDetail($draw));
    }
    
    /**
     * Get draw winners
     * GET /api/draws/{id}/winners
     */
    public function winners($id)
    {
        $draw = $this->drawModel->find($id);
        
        if (!$draw) {
            $this->error('Draw not found', 404);
        }
        
        $winners = $this->drawWinnerModel->getByDraw($id);
        
        $data = [];
        foreach ($winners as $winner) {
            $data[] = $this->formatWinner($winner);
        }
        
        $this->success($data);
    }
    
    /**
     * Get upcoming draws
     * GET /api/draws/upcoming
     */
    public function upcoming()
    {
        $campaignId = $_GET['campaign_id'] ?? null;
        
        $db = new \App\Core\Database();
        
        $sql = "SELECT d.*, rc.name as campaign_name 
                FROM draws d
                INNER JOIN raffle_campaigns rc ON d.campaign_id = rc.id
                WHERE d.draw_date >= CURDATE() 
                AND d.status = 'pending'";
        
        if ($campaignId) {
            $sql .= " AND d.campaign_id = :campaign_id";
        }
        
        $sql .= " ORDER BY d.draw_date ASC LIMIT 10";
        
        $db->query($sql);
        
        if ($campaignId) {
            $db->bind(':campaign_id', $campaignId);
        }
        
        $draws = $db->resultSet();
        
        $data = [];
        foreach ($draws as $draw) {
            $data[] = $this->formatDraw($draw);
        }
        
        $this->success($data);
    }
    
    /**
     * Get recent winners
     * GET /api/draws/recent-winners
     */
    public function recentWinners()
    {
        $limit = $_GET['limit'] ?? 20;
        $campaignId = $_GET['campaign_id'] ?? null;
        
        $db = new \App\Core\Database();
        
        $sql = "SELECT dw.*, d.draw_date, d.draw_type, 
                       rc.name as campaign_name,
                       t.ticket_code,
                       p.name as player_name, p.phone as player_phone
                FROM draw_winners dw
                INNER JOIN draws d ON dw.draw_id = d.id
                INNER JOIN raffle_campaigns rc ON d.campaign_id = rc.id
                INNER JOIN tickets t ON dw.ticket_id = t.id
                INNER JOIN players p ON t.player_id = p.id
                WHERE d.status = 'completed'";
        
        if ($campaignId) {
            $sql .= " AND d.campaign_id = :campaign_id";
        }
        
        $sql .= " ORDER BY d.draw_date DESC, dw.prize_rank ASC LIMIT :limit";
        
        $db->query($sql);
        
        if ($campaignId) {
            $db->bind(':campaign_id', $campaignId);
        }
        
        $db->bind(':limit', (int)$limit);
        
        $winners = $db->resultSet();
        
        $data = [];
        foreach ($winners as $winner) {
            $data[] = [
                'id' => $winner->id,
                'campaign_name' => $winner->campaign_name,
                'draw_date' => $winner->draw_date,
                'draw_type' => $winner->draw_type,
                'prize_rank' => $winner->prize_rank,
                'prize_amount' => (float)$winner->prize_amount,
                'ticket_code' => $winner->ticket_code,
                'player_name' => $this->maskName($winner->player_name),
                'player_phone' => $this->maskPhone($winner->player_phone)
            ];
        }
        
        $this->success($data);
    }
    
    /**
     * Check if player won
     * GET /api/draws/my-wins
     */
    public function myWins()
    {
        $player = $this->requireAuth();
        
        $db = new \App\Core\Database();
        
        $db->query("SELECT dw.*, d.draw_date, d.draw_type, 
                           rc.name as campaign_name,
                           t.ticket_code
                   FROM draw_winners dw
                   INNER JOIN draws d ON dw.draw_id = d.id
                   INNER JOIN raffle_campaigns rc ON d.campaign_id = rc.id
                   INNER JOIN tickets t ON dw.ticket_id = t.id
                   WHERE t.player_id = :player_id
                   ORDER BY d.draw_date DESC");
        $db->bind(':player_id', $player->id);
        
        $wins = $db->resultSet();
        
        $data = [];
        foreach ($wins as $win) {
            $data[] = [
                'id' => $win->id,
                'campaign_name' => $win->campaign_name,
                'draw_date' => $win->draw_date,
                'draw_type' => $win->draw_type,
                'prize_rank' => $win->prize_rank,
                'prize_amount' => (float)$win->prize_amount,
                'ticket_code' => $win->ticket_code,
                'paid_status' => $win->prize_paid_status,
                'paid_at' => $win->prize_paid_at
            ];
        }
        
        $this->success($data);
    }
    
    /**
     * Format draw for list view
     */
    private function formatDraw($draw)
    {
        return [
            'id' => $draw->id,
            'campaign_id' => $draw->campaign_id,
            'campaign_name' => $draw->campaign_name ?? null,
            'draw_date' => $draw->draw_date,
            'draw_type' => $draw->draw_type,
            'status' => $draw->status,
            'winner_count' => (int)$draw->winner_count
        ];
    }
    
    /**
     * Format draw detail view
     */
    private function formatDrawDetail($draw)
    {
        $winners = $this->drawWinnerModel->getByDraw($draw->id);
        
        return [
            'id' => $draw->id,
            'campaign_id' => $draw->campaign_id,
            'campaign_name' => $draw->campaign_name ?? null,
            'draw_date' => $draw->draw_date,
            'draw_type' => $draw->draw_type,
            'status' => $draw->status,
            'winner_count' => (int)$draw->winner_count,
            'prize_pool' => (float)$draw->prize_pool,
            'winners' => array_map(function($winner) {
                return $this->formatWinner($winner);
            }, $winners)
        ];
    }
    
    /**
     * Format winner
     */
    private function formatWinner($winner)
    {
        return [
            'id' => $winner->id,
            'prize_rank' => $winner->prize_rank,
            'prize_amount' => (float)$winner->prize_amount,
            'ticket_code' => $winner->ticket_code ?? null,
            'player_name' => isset($winner->player_name) ? $this->maskName($winner->player_name) : null,
            'paid_status' => $winner->prize_paid_status
        ];
    }
    
    /**
     * Mask player name for privacy
     */
    private function maskName($name)
    {
        if (strlen($name) <= 3) {
            return $name;
        }
        
        return substr($name, 0, 1) . str_repeat('*', strlen($name) - 2) . substr($name, -1);
    }
    
    /**
     * Mask phone number for privacy
     */
    private function maskPhone($phone)
    {
        if (strlen($phone) <= 6) {
            return $phone;
        }
        
        return substr($phone, 0, 3) . str_repeat('*', strlen($phone) - 6) . substr($phone, -3);
    }
}
