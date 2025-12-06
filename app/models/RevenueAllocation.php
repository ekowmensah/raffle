<?php

namespace App\Models;

use App\Core\Model;

class RevenueAllocation extends Model
{
    protected $table = 'revenue_allocations';

    public function getByPayment($paymentId)
    {
        $this->db->query("SELECT * FROM {$this->table} WHERE payment_id = :payment_id");
        $this->db->bind(':payment_id', $paymentId);
        return $this->db->single();
    }

    public function getByCampaign($campaignId)
    {
        $this->db->query("SELECT ra.*, p.payment_reference, p.amount as payment_amount
                         FROM {$this->table} ra
                         LEFT JOIN payments p ON ra.payment_id = p.id
                         WHERE ra.campaign_id = :campaign_id
                         ORDER BY ra.created_at DESC");
        $this->db->bind(':campaign_id', $campaignId);
        return $this->db->resultSet();
    }

    public function getStationRevenue($stationId, $campaignId = null)
    {
        $sql = "SELECT 
                COALESCE(SUM(station_amount), 0) as total_station,
                COALESCE(SUM(programme_amount), 0) as total_programme,
                COALESCE(SUM(winner_pool_amount_total), 0) as total_prize_pool
                FROM {$this->table}
                WHERE station_id = :station_id";
        
        if ($campaignId) {
            $sql .= " AND campaign_id = :campaign_id";
        }
        
        $this->db->query($sql);
        $this->db->bind(':station_id', $stationId);
        
        if ($campaignId) {
            $this->db->bind(':campaign_id', $campaignId);
        }
        
        return $this->db->single();
    }

    public function getPlatformRevenue($campaignId = null)
    {
        $sql = "SELECT COALESCE(SUM(platform_amount), 0) as total
                FROM {$this->table}";
        
        if ($campaignId) {
            $sql .= " WHERE campaign_id = :campaign_id";
        }
        
        $this->db->query($sql);
        
        if ($campaignId) {
            $this->db->bind(':campaign_id', $campaignId);
        }
        
        $result = $this->db->single();
        return $result->total ?? 0;
    }

    public function allocateRevenue($paymentData, $campaign)
    {
        error_log("RevenueAllocation: Starting allocation for payment " . $paymentData['payment_id']);
        
        $grossAmount = $paymentData['amount'];
        error_log("RevenueAllocation: Gross amount: {$grossAmount}");
        
        // Calculate allocations based on campaign percentages
        $platformAmount = ($grossAmount * $campaign->platform_percent) / 100;
        $stationAmount = ($grossAmount * $campaign->station_percent) / 100;
        $programmeAmount = ($grossAmount * $campaign->programme_percent) / 100;
        $winnerPoolTotal = ($grossAmount * $campaign->prize_pool_percent) / 100;
        
        error_log("RevenueAllocation: Platform: {$platformAmount}, Station: {$stationAmount}, Programme: {$programmeAmount}, Prize Pool: {$winnerPoolTotal}");
        
        // Split prize pool between daily, final, and bonus
        $dailyPoolAmount = ($winnerPoolTotal * $campaign->daily_share_percent_of_pool) / 100;
        $finalPoolAmount = ($winnerPoolTotal * $campaign->final_share_percent_of_pool) / 100;
        
        // Bonus pool is the remainder (overflow) after daily and final allocation
        // Example: if daily=40% and final=40%, bonus=20%
        $bonusPoolAmount = $winnerPoolTotal - $dailyPoolAmount - $finalPoolAmount;
        
        $allocation = [
            'payment_id' => $paymentData['payment_id'],
            'campaign_id' => $paymentData['campaign_id'],
            'station_id' => $paymentData['station_id'],
            'programme_id' => $paymentData['programme_id'],
            'gross_amount' => $grossAmount,
            'platform_amount' => round($platformAmount, 2),
            'station_amount' => round($stationAmount, 2),
            'programme_amount' => round($programmeAmount, 2),
            'winner_pool_amount_total' => round($winnerPoolTotal, 2),
            'winner_pool_amount_daily' => round($dailyPoolAmount, 2),
            'winner_pool_amount_final' => round($finalPoolAmount, 2),
            'winner_pool_amount_bonus' => round($bonusPoolAmount, 2)
        ];
        
        $allocationId = $this->create($allocation);
        error_log("RevenueAllocation: Allocation created with ID: " . ($allocationId ?: 'FAILED'));
        
        // Credit station wallet if allocation was successful
        if ($allocationId && $stationAmount > 0) {
            error_log("RevenueAllocation: Crediting station wallet for station {$paymentData['station_id']} with amount {$stationAmount}");
            require_once '../app/models/StationWallet.php';
            require_once '../app/models/StationWalletTransaction.php';
            
            $walletModel = new \App\Models\StationWallet();
            $transactionModel = new \App\Models\StationWalletTransaction();
            
            // Get or create station wallet
            $wallet = $walletModel->getOrCreate($paymentData['station_id']);
            
            // Credit the wallet
            $walletModel->credit($wallet->id, round($stationAmount, 2));
            
            // Record transaction
            $transactionModel->recordCredit(
                $wallet->id,
                round($stationAmount, 2),
                $paymentData['campaign_id'],
                $paymentData['payment_id'],
                'Commission from ticket sale - ' . $campaign->name
            );
        }
        
        return $allocationId;
    }

    public function getRevenueReport($startDate, $endDate, $campaignId = null, $stationId = null, $programmeId = null)
    {
        $sql = "SELECT 
                c.name as campaign_name,
                s.name as station_name,
                p.name as programme_name,
                SUM(ra.platform_amount) as platform_total,
                SUM(ra.station_amount) as station_total,
                SUM(ra.programme_amount) as programme_total,
                SUM(ra.winner_pool_amount_total) as prize_pool_total,
                SUM(ra.winner_pool_amount_daily) as daily_pool,
                SUM(ra.winner_pool_amount_final) as final_pool,
                SUM(ra.winner_pool_amount_bonus) as bonus_pool,
                COUNT(DISTINCT ra.payment_id) as payment_count
                FROM {$this->table} ra
                LEFT JOIN raffle_campaigns c ON ra.campaign_id = c.id
                LEFT JOIN stations s ON ra.station_id = s.id
                LEFT JOIN programmes p ON ra.programme_id = p.id
                WHERE DATE(ra.created_at) BETWEEN :start_date AND :end_date";
        
        if ($campaignId) {
            $sql .= " AND ra.campaign_id = :campaign_id";
        }
        
        if ($stationId) {
            $sql .= " AND ra.station_id = :station_id";
        }
        
        if ($programmeId) {
            $sql .= " AND ra.programme_id = :programme_id";
        }
        
        $sql .= " GROUP BY ra.campaign_id, ra.station_id, ra.programme_id
                 ORDER BY c.name, s.name, p.name";
        
        $this->db->query($sql);
        $this->db->bind(':start_date', $startDate);
        $this->db->bind(':end_date', $endDate);
        
        if ($campaignId) {
            $this->db->bind(':campaign_id', $campaignId);
        }
        
        if ($stationId) {
            $this->db->bind(':station_id', $stationId);
        }
        
        if ($programmeId) {
            $this->db->bind(':programme_id', $programmeId);
        }
        
        return $this->db->resultSet();
    }

    public function getStationPerformance($startDate, $endDate)
    {
        $sql = "SELECT 
                s.id as station_id,
                s.name as station_name,
                s.code as station_code,
                COUNT(DISTINCT ra.payment_id) as total_payments,
                COUNT(DISTINCT ra.campaign_id) as campaigns_count,
                SUM(ra.gross_amount) as total_revenue,
                SUM(ra.station_amount) as total_commission,
                SUM(ra.winner_pool_amount_total) as total_prize_pool,
                (SUM(ra.station_amount) / SUM(ra.gross_amount) * 100) as commission_percentage
                FROM stations s
                LEFT JOIN {$this->table} ra ON s.id = ra.station_id
                    AND DATE(ra.created_at) BETWEEN :start_date AND :end_date
                GROUP BY s.id, s.name, s.code
                HAVING total_payments > 0
                ORDER BY total_revenue DESC";
        
        $this->db->query($sql);
        $this->db->bind(':start_date', $startDate);
        $this->db->bind(':end_date', $endDate);
        
        return $this->db->resultSet();
    }

    public function getReconciliationSummary($startDate, $endDate)
    {
        $this->db->query("SELECT 
                         COUNT(*) as allocation_count,
                         SUM(gross_amount) as total_allocated,
                         SUM(platform_amount) as total_platform,
                         SUM(station_amount) as total_station,
                         SUM(programme_amount) as total_programme,
                         SUM(winner_pool_amount_total) as total_prize_pool
                         FROM {$this->table}
                         WHERE DATE(created_at) BETWEEN :start_date AND :end_date");
        $this->db->bind(':start_date', $startDate);
        $this->db->bind(':end_date', $endDate);
        return $this->db->single();
    }

    public function getAllocationsWithoutPayment($startDate, $endDate)
    {
        $this->db->query("SELECT ra.* 
                         FROM {$this->table} ra
                         LEFT JOIN payments p ON ra.payment_id = p.id
                         WHERE DATE(ra.created_at) BETWEEN :start_date AND :end_date
                         AND (p.id IS NULL OR p.status != 'success')");
        $this->db->bind(':start_date', $startDate);
        $this->db->bind(':end_date', $endDate);
        return $this->db->resultSet();
    }
}
