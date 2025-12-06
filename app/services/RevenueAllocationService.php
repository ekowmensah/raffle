<?php

namespace App\Services;

class RevenueAllocationService
{
    private $revenueModel;
    private $campaignModel;

    public function __construct()
    {
        require_once '../app/models/RevenueAllocation.php';
        require_once '../app/models/Campaign.php';
        
        $this->revenueModel = new \App\Models\RevenueAllocation();
        $this->campaignModel = new \App\Models\Campaign();
    }

    public function allocate($paymentData)
    {
        $campaign = $this->campaignModel->findById($paymentData['campaign_id']);
        
        if (!$campaign) {
            return false;
        }

        return $this->revenueModel->allocateRevenue($paymentData, $campaign);
    }

    public function getCampaignBreakdown($campaignId)
    {
        $allocations = $this->revenueModel->getByCampaign($campaignId);
        
        $breakdown = [
            'total_revenue' => 0,
            'platform_total' => 0,
            'station_total' => 0,
            'programme_total' => 0,
            'prize_pool_total' => 0,
            'daily_pool_total' => 0,
            'final_pool_total' => 0,
            'allocation_count' => count($allocations)
        ];

        foreach ($allocations as $allocation) {
            $breakdown['total_revenue'] += $allocation->gross_amount;
            $breakdown['platform_total'] += $allocation->platform_amount;
            $breakdown['station_total'] += $allocation->station_amount;
            $breakdown['programme_total'] += $allocation->programme_amount;
            $breakdown['prize_pool_total'] += $allocation->winner_pool_amount_total;
            $breakdown['daily_pool_total'] += $allocation->winner_pool_amount_daily;
            $breakdown['final_pool_total'] += $allocation->winner_pool_amount_final;
        }

        return $breakdown;
    }

    public function getStationBreakdown($stationId, $campaignId = null)
    {
        return $this->revenueModel->getStationRevenue($stationId, $campaignId);
    }
}
