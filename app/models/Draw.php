<?php

namespace App\Models;

use App\Core\Model;

class Draw extends Model
{
    protected $table = 'draws';

    public function getByCampaign($campaignId)
    {
        $this->db->query("SELECT d.*, u.name as started_by_name
                         FROM {$this->table} d
                         LEFT JOIN users u ON d.started_by_user_id = u.id
                         WHERE d.campaign_id = :campaign_id
                         ORDER BY d.draw_date DESC");
        $this->db->bind(':campaign_id', $campaignId);
        return $this->db->resultSet();
    }

    public function getWithWinners($drawId)
    {
        $this->db->query("SELECT d.*, 
                         COUNT(dw.id) as winner_count,
                         SUM(dw.prize_amount) as total_prizes
                         FROM {$this->table} d
                         LEFT JOIN draw_winners dw ON d.id = dw.draw_id
                         WHERE d.id = :draw_id
                         GROUP BY d.id");
        $this->db->bind(':draw_id', $drawId);
        return $this->db->single();
    }

    public function getPendingDraws()
    {
        $this->db->query("SELECT d.*, 
                         c.name as campaign_name,
                         (SELECT s.name FROM stations s 
                          INNER JOIN campaign_programme_access cpa ON cpa.programme_id IN (SELECT id FROM programmes WHERE station_id = s.id)
                          WHERE cpa.campaign_id = d.campaign_id LIMIT 1) as station_name,
                         (SELECT pr.name FROM programmes pr 
                          INNER JOIN campaign_programme_access cpa ON cpa.programme_id = pr.id
                          WHERE cpa.campaign_id = d.campaign_id LIMIT 1) as programme_name
                         FROM {$this->table} d
                         LEFT JOIN raffle_campaigns c ON d.campaign_id = c.id
                         WHERE d.status = 'pending'
                         ORDER BY d.draw_date ASC");
        return $this->db->resultSet();
    }

    public function getCompletedDraws($campaignId = null)
    {
        $sql = "SELECT d.*, 
                c.name as campaign_name,
                (SELECT s.name FROM stations s 
                 INNER JOIN campaign_programme_access cpa ON cpa.programme_id IN (SELECT id FROM programmes WHERE station_id = s.id)
                 WHERE cpa.campaign_id = d.campaign_id LIMIT 1) as station_name,
                (SELECT pr.name FROM programmes pr 
                 INNER JOIN campaign_programme_access cpa ON cpa.programme_id = pr.id
                 WHERE cpa.campaign_id = d.campaign_id LIMIT 1) as programme_name,
                COUNT(dw.id) as winner_count
                FROM {$this->table} d
                LEFT JOIN raffle_campaigns c ON d.campaign_id = c.id
                LEFT JOIN draw_winners dw ON d.id = dw.draw_id
                WHERE d.status = 'completed'";
        
        if ($campaignId) {
            $sql .= " AND d.campaign_id = :campaign_id";
        }
        
        $sql .= " GROUP BY d.id ORDER BY d.draw_date DESC";
        
        $this->db->query($sql);
        
        if ($campaignId) {
            $this->db->bind(':campaign_id', $campaignId);
        }
        
        return $this->db->resultSet();
    }

    public function updateStatus($drawId, $status)
    {
        $data = ['status' => $status];
        
        if ($status === 'completed') {
            $data['updated_at'] = date('Y-m-d H:i:s');
        }
        
        return $this->update($drawId, $data);
    }

    public function calculatePrizePool($campaignId, $drawType, $drawDate = null, $stationId = null, $programmeId = null)
    {
        // Get total prize pool from revenue allocations up to draw date
        // Filtered by station and programme if provided
        $sql = "SELECT 
                SUM(ra.winner_pool_amount_total) as total_pool,
                SUM(ra.winner_pool_amount_daily) as daily_pool,
                SUM(ra.winner_pool_amount_final) as final_pool,
                SUM(ra.winner_pool_amount_bonus) as bonus_pool
                FROM revenue_allocations ra
                INNER JOIN payments p ON ra.payment_id = p.id
                WHERE ra.campaign_id = :campaign_id";
        
        // Filter by station if provided
        if ($stationId) {
            $sql .= " AND ra.station_id = :station_id";
        }
        
        // Filter by programme if provided
        if ($programmeId) {
            $sql .= " AND ra.programme_id = :programme_id";
        }
        
        // Only include payments up to the draw date
        if ($drawDate) {
            $sql .= " AND DATE(p.created_at) <= :draw_date";
        }
        
        $this->db->query($sql);
        $this->db->bind(':campaign_id', $campaignId);
        
        if ($stationId) {
            $this->db->bind(':station_id', $stationId);
        }
        
        if ($programmeId) {
            $this->db->bind(':programme_id', $programmeId);
        }
        
        if ($drawDate) {
            $this->db->bind(':draw_date', $drawDate);
        }
        
        $result = $this->db->single();
        
        // Get already distributed prizes for this draw type (filtered by station/programme)
        $distributedSql = "SELECT COALESCE(SUM(dw.prize_amount), 0) as distributed
                          FROM draw_winners dw
                          INNER JOIN draws d ON dw.draw_id = d.id
                          WHERE d.campaign_id = :campaign_id 
                          AND d.draw_type = :draw_type";
        
        if ($stationId) {
            $distributedSql .= " AND d.station_id = :station_id";
        }
        
        if ($programmeId) {
            $distributedSql .= " AND d.programme_id = :programme_id";
        }
        
        if ($drawDate) {
            $distributedSql .= " AND d.draw_date < :draw_date";
        }
        
        $this->db->query($distributedSql);
        $this->db->bind(':campaign_id', $campaignId);
        $this->db->bind(':draw_type', $drawType);
        
        if ($stationId) {
            $this->db->bind(':station_id', $stationId);
        }
        
        if ($programmeId) {
            $this->db->bind(':programme_id', $programmeId);
        }
        
        if ($drawDate) {
            $this->db->bind(':draw_date', $drawDate);
        }
        
        $distributed = $this->db->single();
        $alreadyDistributed = $distributed->distributed ?? 0;
        
        // Calculate available pool based on draw type
        if ($drawType === 'daily') {
            $totalPool = ($result->daily_pool ?? 0) - $alreadyDistributed;
        } elseif ($drawType === 'final') {
            $totalPool = ($result->final_pool ?? 0) - $alreadyDistributed;
        } elseif ($drawType === 'bonus') {
            // Bonus draws use the dedicated bonus pool (overflow from daily+final)
            $totalPool = ($result->bonus_pool ?? 0) - $alreadyDistributed;
        } else {
            // Default to total pool
            $totalPool = ($result->total_pool ?? 0) - $alreadyDistributed;
        }
        
        return max(0, $totalPool); // Ensure non-negative
    }

    public function countByStatus($status)
    {
        $this->db->query("SELECT COUNT(*) as count FROM {$this->table} WHERE status = :status");
        $this->db->bind(':status', $status);
        $result = $this->db->single();
        return $result->count ?? 0;
    }

    public function getUpcoming($limit = 5)
    {
        $this->db->query("SELECT d.*, c.name as campaign_name
                         FROM {$this->table} d
                         LEFT JOIN raffle_campaigns c ON d.campaign_id = c.id
                         WHERE d.status = 'pending' AND d.draw_date >= CURDATE()
                         ORDER BY d.draw_date ASC
                         LIMIT :limit");
        $this->db->bind(':limit', $limit);
        return $this->db->resultSet();
    }
}
