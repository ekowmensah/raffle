<?php

namespace App\Models;

use App\Core\Model;

class Player extends Model
{
    protected $table = 'players';
    
    /**
     * Normalize phone number to standard format
     * Converts 0545644749 or 233545644749 to 233545644749
     */
    private function normalizePhone($phone)
    {
        // Remove spaces, dashes, and other non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // If starts with 0, replace with 233
        if (substr($phone, 0, 1) === '0') {
            $phone = '233' . substr($phone, 1);
        }
        
        // If doesn't start with 233, add it
        if (substr($phone, 0, 3) !== '233') {
            $phone = '233' . $phone;
        }
        
        return $phone;
    }

    public function findByPhone($phone)
    {
        // Normalize phone number
        $normalizedPhone = $this->normalizePhone($phone);
        
        // Try to find with normalized phone
        $this->db->query("SELECT * FROM {$this->table} WHERE phone = :phone");
        $this->db->bind(':phone', $normalizedPhone);
        $player = $this->db->single();
        
        // If not found, try with original phone (for backward compatibility)
        if (!$player) {
            $this->db->query("SELECT * FROM {$this->table} WHERE phone = :phone");
            $this->db->bind(':phone', $phone);
            $player = $this->db->single();
        }
        
        return $player;
    }

    public function getByPhone($phone)
    {
        return $this->findByPhone($phone);
    }

    public function getWithStats()
    {
        $this->db->query("SELECT p.*, 
                         COALESCE(t.total_tickets, 0) as total_tickets,
                         COALESCE(pay.total_spent, 0) as total_spent,
                         COALESCE(dw.total_wins, 0) as total_wins,
                         COALESCE(dw.total_winnings, 0) as total_winnings,
                         COALESCE(p.loyalty_level, 'bronze') as loyalty_level
                         FROM {$this->table} p
                         LEFT JOIN (
                             SELECT player_id, COUNT(*) as total_tickets
                             FROM tickets
                             GROUP BY player_id
                         ) t ON p.id = t.player_id
                         LEFT JOIN (
                             SELECT player_id, SUM(amount) as total_spent
                             FROM payments
                             WHERE status = 'success'
                             GROUP BY player_id
                         ) pay ON p.id = pay.player_id
                         LEFT JOIN (
                             SELECT player_id, COUNT(*) as total_wins, SUM(prize_amount) as total_winnings
                             FROM draw_winners
                             GROUP BY player_id
                         ) dw ON p.id = dw.player_id
                         ORDER BY p.created_at DESC");
        return $this->db->resultSet();
    }

    public function updateLoyaltyLevel($playerId)
    {
        // Get total spent
        $this->db->query("SELECT COALESCE(SUM(amount), 0) as total
                         FROM payments
                         WHERE player_id = :player_id AND status = 'success'");
        $this->db->bind(':player_id', $playerId);
        $result = $this->db->single();
        $totalSpent = $result->total ?? 0;
        
        // Get total tickets
        $this->db->query("SELECT COALESCE(SUM(quantity), 0) as total
                         FROM tickets
                         WHERE player_id = :player_id");
        $this->db->bind(':player_id', $playerId);
        $ticketResult = $this->db->single();
        $totalTickets = $ticketResult->total ?? 0;
        
        // Determine loyalty level
        $level = 'bronze';
        if ($totalSpent >= 1000) $level = 'platinum';
        elseif ($totalSpent >= 500) $level = 'gold';
        elseif ($totalSpent >= 100) $level = 'silver';
        
        // Calculate loyalty points (1 point per GHS spent)
        $points = floor($totalSpent);
        
        return $this->update($playerId, [
            'total_spent' => $totalSpent,
            'total_tickets' => $totalTickets,
            'loyalty_level' => $level,
            'loyalty_points' => $points
        ]);
    }

    public function findOrCreate($phone, $name = null)
    {
        // Normalize phone number before searching/creating
        $normalizedPhone = $this->normalizePhone($phone);
        
        $player = $this->findByPhone($normalizedPhone);
        
        if ($player) {
            return $player;
        }
        
        // Create new player with normalized phone
        $data = [
            'phone' => $normalizedPhone,
            'name' => $name ?? 'Player ' . substr($normalizedPhone, -4)
        ];
        
        $playerId = $this->create($data);
        return $this->findById($playerId);
    }

    public function getTickets($playerId, $campaignId = null)
    {
        $sql = "SELECT t.*, 
                c.name as campaign_name, 
                s.name as station_name,
                p.name as programme_name
                FROM tickets t
                LEFT JOIN raffle_campaigns c ON t.campaign_id = c.id
                LEFT JOIN stations s ON t.station_id = s.id
                LEFT JOIN programmes p ON t.programme_id = p.id
                WHERE t.player_id = :player_id";
        
        if ($campaignId) {
            $sql .= " AND t.campaign_id = :campaign_id";
        }
        
        $sql .= " ORDER BY t.created_at DESC";
        
        $this->db->query($sql);
        $this->db->bind(':player_id', $playerId);
        
        if ($campaignId) {
            $this->db->bind(':campaign_id', $campaignId);
        }
        
        return $this->db->resultSet();
    }

    public function count()
    {
        $this->db->query("SELECT COUNT(*) as count FROM {$this->table}");
        $result = $this->db->single();
        return $result->count ?? 0;
    }
}
