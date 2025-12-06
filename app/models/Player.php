<?php

namespace App\Models;

use App\Core\Model;

class Player extends Model
{
    protected $table = 'players';

    public function findByPhone($phone)
    {
        $this->db->query("SELECT * FROM {$this->table} WHERE phone = :phone");
        $this->db->bind(':phone', $phone);
        return $this->db->single();
    }

    public function getByPhone($phone)
    {
        return $this->findByPhone($phone);
    }

    public function getWithStats()
    {
        $this->db->query("SELECT p.*, 
                         COUNT(DISTINCT t.id) as total_tickets,
                         COALESCE(SUM(pay.amount), 0) as total_spent,
                         0 as total_wins,
                         COALESCE(p.loyalty_level, 'bronze') as loyalty_level
                         FROM {$this->table} p
                         LEFT JOIN tickets t ON p.id = t.player_id
                         LEFT JOIN payments pay ON p.id = pay.player_id AND pay.status = 'success'
                         GROUP BY p.id
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
        
        // Determine loyalty level
        $level = 'bronze';
        if ($totalSpent >= 1000) $level = 'platinum';
        elseif ($totalSpent >= 500) $level = 'gold';
        elseif ($totalSpent >= 100) $level = 'silver';
        
        // Calculate loyalty points (1 point per GHS spent)
        $points = floor($totalSpent);
        
        return $this->update($playerId, [
            'loyalty_level' => $level,
            'loyalty_points' => $points
        ]);
    }

    public function findOrCreate($phone, $name = null)
    {
        $player = $this->findByPhone($phone);
        
        if ($player) {
            return $player;
        }
        
        // Create new player
        $data = [
            'phone' => $phone,
            'name' => $name ?? 'Player ' . substr($phone, -4)
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
