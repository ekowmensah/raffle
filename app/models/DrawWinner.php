<?php

namespace App\Models;

use App\Core\Model;

class DrawWinner extends Model
{
    protected $table = 'draw_winners';

    public function getByDraw($drawId)
    {
        $this->db->query("SELECT dw.*, 
                         t.ticket_code,
                         p.phone as player_phone,
                         p.name as player_name,
                         c.name as campaign_name
                         FROM {$this->table} dw
                         LEFT JOIN tickets t ON dw.ticket_id = t.id
                         LEFT JOIN players p ON dw.player_id = p.id
                         LEFT JOIN draws d ON dw.draw_id = d.id
                         LEFT JOIN raffle_campaigns c ON d.campaign_id = c.id
                         WHERE dw.draw_id = :draw_id
                         ORDER BY dw.prize_rank ASC");
        $this->db->bind(':draw_id', $drawId);
        return $this->db->resultSet();
    }

    public function getByPlayer($playerId)
    {
        $this->db->query("SELECT dw.*, 
                         t.ticket_code,
                         c.name as campaign_name,
                         d.draw_date,
                         d.draw_type
                         FROM {$this->table} dw
                         LEFT JOIN tickets t ON dw.ticket_id = t.id
                         LEFT JOIN draws d ON dw.draw_id = d.id
                         LEFT JOIN raffle_campaigns c ON d.campaign_id = c.id
                         WHERE dw.player_id = :player_id
                         ORDER BY dw.created_at DESC");
        $this->db->bind(':player_id', $playerId);
        return $this->db->resultSet();
    }

    public function findByTicket($ticketId)
    {
        $this->db->query("SELECT * FROM {$this->table} WHERE ticket_id = :ticket_id LIMIT 1");
        $this->db->bind(':ticket_id', $ticketId);
        return $this->db->single();
    }

    public function getByCampaign($campaignId)
    {
        $this->db->query("SELECT dw.*, 
                         t.ticket_code,
                         p.phone as player_phone,
                         p.name as player_name,
                         d.draw_date,
                         d.draw_type
                         FROM {$this->table} dw
                         LEFT JOIN tickets t ON dw.ticket_id = t.id
                         LEFT JOIN players p ON dw.player_id = p.id
                         LEFT JOIN draws d ON dw.draw_id = d.id
                         WHERE d.campaign_id = :campaign_id
                         ORDER BY dw.created_at DESC");
        $this->db->bind(':campaign_id', $campaignId);
        return $this->db->resultSet();
    }

    public function updatePrizeStatus($winnerId, $status)
    {
        return $this->update($winnerId, [
            'prize_paid_status' => $status,
            'prize_paid_at' => $status === 'paid' ? date('Y-m-d H:i:s') : null
        ]);
    }

    public function getPayoutReport($startDate, $endDate, $campaignId = null, $status = null)
    {
        $sql = "SELECT dw.*, 
                d.draw_date,
                d.draw_type,
                c.name as campaign_name,
                p.name as player_name,
                p.phone as player_phone,
                t.ticket_code
                FROM {$this->table} dw
                INNER JOIN draws d ON dw.draw_id = d.id
                LEFT JOIN raffle_campaigns c ON d.campaign_id = c.id
                LEFT JOIN players p ON dw.player_id = p.id
                LEFT JOIN tickets t ON dw.ticket_id = t.id
                WHERE DATE(d.draw_date) BETWEEN :start_date AND :end_date";

        if ($campaignId) {
            $sql .= " AND d.campaign_id = :campaign_id";
        }

        if ($status) {
            $sql .= " AND dw.prize_paid_status = :status";
        }

        $sql .= " ORDER BY d.draw_date DESC, dw.prize_rank ASC";

        $this->db->query($sql);
        $this->db->bind(':start_date', $startDate);
        $this->db->bind(':end_date', $endDate);

        if ($campaignId) {
            $this->db->bind(':campaign_id', $campaignId);
        }

        if ($status) {
            $this->db->bind(':status', $status);
        }

        return $this->db->resultSet();
    }

    public function getTotalPrizesAwarded($campaignId = null)
    {
        $sql = "SELECT COALESCE(SUM(dw.prize_amount), 0) as total
                FROM {$this->table} dw";
        
        if ($campaignId) {
            $sql .= " LEFT JOIN draws d ON dw.draw_id = d.id
                     WHERE d.campaign_id = :campaign_id";
        }
        
        $this->db->query($sql);
        
        if ($campaignId) {
            $this->db->bind(':campaign_id', $campaignId);
        }
        
        $result = $this->db->single();
        return $result->total ?? 0;
    }
    
    public function getRecentWinners($limit = 50)
    {
        $this->db->query("SELECT 
                            dw.*,
                            d.draw_date,
                            d.draw_type,
                            c.name as campaign_name,
                            s.name as station_name,
                            pr.name as programme_name,
                            t.ticket_code,
                            p.phone as phone_number
                         FROM {$this->table} dw
                         INNER JOIN draws d ON dw.draw_id = d.id
                         INNER JOIN raffle_campaigns c ON d.campaign_id = c.id
                         LEFT JOIN stations s ON c.station_id = s.id
                         LEFT JOIN campaign_programme_access cpa ON c.id = cpa.campaign_id
                         LEFT JOIN programmes pr ON cpa.programme_id = pr.id
                         INNER JOIN tickets t ON dw.ticket_id = t.id
                         INNER JOIN players p ON dw.player_id = p.id
                         WHERE d.status = 'completed'
                         ORDER BY d.draw_date DESC, dw.prize_rank ASC
                         LIMIT :limit");
        
        $this->db->bind(':limit', $limit);
        return $this->db->resultSet();
    }
    
    public function count()
    {
        $this->db->query("SELECT COUNT(*) as count FROM {$this->table}");
        $result = $this->db->single();
        return $result->count ?? 0;
    }
}
