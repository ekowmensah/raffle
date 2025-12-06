<?php

namespace App\Models;

use App\Core\Model;

class Ticket extends Model
{
    protected $table = 'tickets';

    /**
     * Get ticket sales trend
     */
    public function getSalesTrend($days = 30)
    {
        $this->db->query("
            SELECT DATE(created_at) as date, 
                   COUNT(*) as ticket_count,
                   COALESCE(SUM(quantity), 0) as total_quantity
            FROM {$this->table}
            WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL :days DAY)
            GROUP BY DATE(created_at)
            ORDER BY date ASC
        ");
        
        $this->db->bind(':days', $days);
        return $this->db->resultSet();
    }

    public function findByCode($ticketCode)
    {
        $this->db->query("SELECT t.*, c.name as campaign_name, p.phone as player_phone
                         FROM {$this->table} t
                         LEFT JOIN raffle_campaigns c ON t.campaign_id = c.id
                         LEFT JOIN players p ON t.player_id = p.id
                         WHERE t.ticket_code = :ticket_code");
        $this->db->bind(':ticket_code', $ticketCode);
        return $this->db->single();
    }

    public function getByPayment($paymentId)
    {
        $this->db->query("SELECT * FROM {$this->table} WHERE payment_id = :payment_id ORDER BY ticket_code");
        $this->db->bind(':payment_id', $paymentId);
        return $this->db->resultSet();
    }

    public function getByPlayer($playerId, $campaignId = null)
    {
        $sql = "SELECT t.*, c.name as campaign_name
                FROM {$this->table} t
                LEFT JOIN raffle_campaigns c ON t.campaign_id = c.id
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

    public function getByCampaign($campaignId)
    {
        $this->db->query("SELECT t.*, 
                         p.phone as player_phone, 
                         p.name as player_name,
                         c.name as campaign_name,
                         c.ticket_price,
                         s.name as station_name,
                         pr.name as programme_name
                         FROM {$this->table} t
                         LEFT JOIN players p ON t.player_id = p.id
                         LEFT JOIN raffle_campaigns c ON t.campaign_id = c.id
                         LEFT JOIN stations s ON t.station_id = s.id
                         LEFT JOIN programmes pr ON t.programme_id = pr.id
                         WHERE t.campaign_id = :campaign_id
                         ORDER BY t.created_at DESC");
        $this->db->bind(':campaign_id', $campaignId);
        return $this->db->resultSet();
    }

    public function generateTicketCode($campaignCode, $stationCode, $sequence = null)
    {
        // Format: 10 random digits only (e.g., 3847562019)
        // Generate 10 random digits that cannot be guessed
        return $this->generateUniqueRandomDigits();
    }
    
    private function generateUniqueRandomDigits($length = 10)
    {
        $maxAttempts = 100;
        $attempts = 0;
        
        do {
            // Generate random digits
            $digits = '';
            for ($i = 0; $i < $length; $i++) {
                $digits .= mt_rand(0, 9);
            }
            
            // Check if this code already exists
            $this->db->query("SELECT COUNT(*) as count FROM {$this->table} WHERE ticket_code = :code");
            $this->db->bind(':code', $digits);
            $result = $this->db->single();
            
            $attempts++;
            
            if ($result->count == 0) {
                return $digits;
            }
        } while ($attempts < $maxAttempts);
        
        // Fallback: add timestamp to ensure uniqueness
        return $digits . substr(time(), -2);
    }

    public function getNextSequence($campaignId, $stationId)
    {
        // This method is deprecated as we now use random digits
        // Kept for backward compatibility but not used
        return 1;
    }

    public function bulkCreate($tickets)
    {
        $this->db->beginTransaction();
        
        try {
            foreach ($tickets as $ticket) {
                $this->create($ticket);
            }
            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollback();
            return false;
        }
    }

    public function markAsWinner($ticketId, $drawId, $prizeAmount)
    {
        // Winner information is stored in draw_winners table, not in tickets table
        // This method is deprecated and should not be used
        // Winners are tracked via DrawWinner model
        return true;
    }

    public function getEligibleForDraw($campaignId, $drawDate = null)
    {
        // Get tickets that haven't won yet (not in draw_winners table)
        $sql = "SELECT t.* FROM {$this->table} t
                WHERE t.campaign_id = :campaign_id
                AND t.id NOT IN (SELECT ticket_id FROM draw_winners)";
        
        if ($drawDate) {
            $sql .= " AND DATE(t.created_at) <= :draw_date";
        }
        
        $this->db->query($sql);
        $this->db->bind(':campaign_id', $campaignId);
        
        if ($drawDate) {
            $this->db->bind(':draw_date', $drawDate);
        }
        
        return $this->db->resultSet();
    }

    public function count()
    {
        $this->db->query("SELECT COUNT(*) as count FROM {$this->table}");
        $result = $this->db->single();
        return $result->count ?? 0;
    }

    public function getRecent($limit = 5)
    {
        $this->db->query("SELECT t.*, 
                         p.phone as player_phone,
                         c.name as campaign_name
                         FROM {$this->table} t
                         LEFT JOIN players p ON t.player_id = p.id
                         LEFT JOIN raffle_campaigns c ON t.campaign_id = c.id
                         ORDER BY t.created_at DESC 
                         LIMIT :limit");
        $this->db->bind(':limit', $limit);
        return $this->db->resultSet();
    }

    public function countByCampaign($campaignId)
    {
        $this->db->query("SELECT COUNT(*) as count FROM {$this->table} WHERE campaign_id = :campaign_id");
        $this->db->bind(':campaign_id', $campaignId);
        $result = $this->db->single();
        return $result->count ?? 0;
    }
}
