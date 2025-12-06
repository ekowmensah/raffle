<?php

namespace App\Models;

use App\Core\Model;

class Payment extends Model
{
    protected $table = 'payments';

    public function findByReference($reference)
    {
        $this->db->query("SELECT * FROM {$this->table} WHERE internal_reference = :reference OR gateway_reference = :reference");
        $this->db->bind(':reference', $reference);
        return $this->db->single();
    }

    public function getByCampaign($campaignId, $status = null)
    {
        $sql = "SELECT p.*, pl.phone as player_phone, pl.name as player_name,
                pr.name as programme_name, s.name as station_name
                FROM {$this->table} p
                LEFT JOIN players pl ON p.player_id = pl.id
                LEFT JOIN programmes pr ON p.programme_id = pr.id
                LEFT JOIN stations s ON p.station_id = s.id
                WHERE p.campaign_id = :campaign_id";
        
        if ($status) {
            $sql .= " AND p.status = :status";
        }
        
        $sql .= " ORDER BY p.created_at DESC";
        
        $this->db->query($sql);
        $this->db->bind(':campaign_id', $campaignId);
        
        if ($status) {
            $this->db->bind(':status', $status);
        }
        
        return $this->db->resultSet();
    }

    public function getByPlayer($playerId)
    {
        $this->db->query("SELECT p.*, c.name as campaign_name
                         FROM {$this->table} p
                         LEFT JOIN raffle_campaigns c ON p.campaign_id = c.id
                         WHERE p.player_id = :player_id
                         ORDER BY p.created_at DESC");
        $this->db->bind(':player_id', $playerId);
        return $this->db->resultSet();
    }

    public function updateStatus($id, $status, $gatewayResponse = null)
    {
        $data = [
            'status' => $status,
            'payment_completed_at' => ($status === 'success') ? date('Y-m-d H:i:s') : null
        ];

        if ($gatewayResponse) {
            $data['gateway_response_json'] = json_encode($gatewayResponse);
        }

        return $this->update($id, $data);
    }

    public function getSuccessfulPayments($campaignId = null)
    {
        $sql = "SELECT p.*, 
                COUNT(t.id) as ticket_count,
                pl.phone as player_phone,
                pl.name as player_name,
                c.name as campaign_name
                FROM {$this->table} p
                LEFT JOIN tickets t ON p.id = t.payment_id
                LEFT JOIN players pl ON p.player_id = pl.id
                LEFT JOIN raffle_campaigns c ON p.campaign_id = c.id
                WHERE p.status = 'success'";
        
        if ($campaignId) {
            $sql .= " AND p.campaign_id = :campaign_id";
        }
        
        $sql .= " GROUP BY p.id ORDER BY p.created_at DESC";
        
        $this->db->query($sql);
        
        if ($campaignId) {
            $this->db->bind(':campaign_id', $campaignId);
        }
        
        return $this->db->resultSet();
    }

    public function getWithDetails($id)
    {
        $this->db->query("SELECT p.*, 
                         pl.phone as player_phone,
                         pl.name as player_name,
                         c.name as campaign_name,
                         s.name as station_name,
                         pr.name as programme_name
                         FROM {$this->table} p
                         LEFT JOIN players pl ON p.player_id = pl.id
                         LEFT JOIN raffle_campaigns c ON p.campaign_id = c.id
                         LEFT JOIN stations s ON p.station_id = s.id
                         LEFT JOIN programmes pr ON p.programme_id = pr.id
                         WHERE p.id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function getTotalRevenue($campaignId = null)
    {
        $sql = "SELECT COALESCE(SUM(amount), 0) as total
                FROM {$this->table}
                WHERE status = 'success'";
        
        if ($campaignId) {
            $sql .= " AND campaign_id = :campaign_id";
        }
        
        $this->db->query($sql);
        
        if ($campaignId) {
            $this->db->bind(':campaign_id', $campaignId);
        }
        
        $result = $this->db->single();
        return $result->total ?? 0;
    }

    public function getForReconciliation($startDate, $endDate, $gateway = null)
    {
        $sql = "SELECT p.*, 
                c.name as campaign_name,
                pl.phone as player_phone,
                pl.name as player_name,
                s.name as station_name,
                pr.name as programme_name
                FROM {$this->table} p
                LEFT JOIN raffle_campaigns c ON p.campaign_id = c.id
                LEFT JOIN players pl ON p.player_id = pl.id
                LEFT JOIN stations s ON p.station_id = s.id
                LEFT JOIN programmes pr ON p.programme_id = pr.id
                WHERE DATE(p.created_at) BETWEEN :start_date AND :end_date";
        
        if ($gateway) {
            $sql .= " AND p.payment_method = :gateway";
        }
        
        $sql .= " ORDER BY p.created_at DESC";
        
        $this->db->query($sql);
        $this->db->bind(':start_date', $startDate);
        $this->db->bind(':end_date', $endDate);
        
        if ($gateway) {
            $this->db->bind(':gateway', $gateway);
        }
        
        return $this->db->resultSet();
    }

    public function getRecent($limit = 5)
    {
        $this->db->query("SELECT * FROM {$this->table} 
                         WHERE status = 'success'
                         ORDER BY created_at DESC 
                         LIMIT :limit");
        $this->db->bind(':limit', $limit);
        return $this->db->resultSet();
    }

    public function getGatewaySummary($startDate, $endDate, $gateway = null)
    {
        $sql = "SELECT 
                gateway,
                COUNT(*) as payment_count,
                SUM(amount) as total_amount,
                SUM(CASE WHEN status = 'success' THEN amount ELSE 0 END) as successful_amount,
                SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed_count
                FROM {$this->table}
                WHERE DATE(created_at) BETWEEN :start_date AND :end_date";
        
        if ($gateway) {
            $sql .= " AND gateway = :gateway";
        }
        
        $sql .= " GROUP BY gateway ORDER BY total_amount DESC";
        
        $this->db->query($sql);
        $this->db->bind(':start_date', $startDate);
        $this->db->bind(':end_date', $endDate);
        
        if ($gateway) {
            $this->db->bind(':gateway', $gateway);
        }
        
        return $this->db->resultSet();
    }

    public function getPaymentsWithoutAllocation($startDate, $endDate)
    {
        $this->db->query("SELECT p.* 
                         FROM {$this->table} p
                         LEFT JOIN revenue_allocations ra ON p.id = ra.payment_id
                         WHERE p.status = 'success'
                         AND DATE(p.created_at) BETWEEN :start_date AND :end_date
                         AND ra.id IS NULL");
        $this->db->bind(':start_date', $startDate);
        $this->db->bind(':end_date', $endDate);
        return $this->db->resultSet();
    }
}
