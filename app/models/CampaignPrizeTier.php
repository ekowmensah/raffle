<?php

namespace App\Models;

use App\Core\Model;

class CampaignPrizeTier extends Model
{
    protected $table = 'raffle_campaign_prize_tiers';
    
    /**
     * Get all tiers for a campaign, ordered by rank
     */
    public function getByCampaign($campaignId)
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE campaign_id = ? 
                ORDER BY tier_rank ASC";
        
        return $this->query($sql, [$campaignId]);
    }
    
    /**
     * Get a specific tier by campaign and rank
     */
    public function getTier($campaignId, $tierRank)
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE campaign_id = ? AND tier_rank = ? 
                LIMIT 1";
        
        $result = $this->query($sql, [$campaignId, $tierRank]);
        return $result[0] ?? null;
    }
    
    /**
     * Create or update tier
     */
    public function saveTier($data)
    {
        // Check if tier already exists
        $existing = $this->getTier($data['campaign_id'], $data['tier_rank']);
        
        if ($existing) {
            // Update existing tier
            return $this->update($existing->id, $data);
        } else {
            // Create new tier
            return $this->create($data);
        }
    }
    
    /**
     * Delete all tiers for a campaign
     */
    public function deleteByCampaign($campaignId)
    {
        $sql = "DELETE FROM {$this->table} WHERE campaign_id = ?";
        return $this->execute($sql, [$campaignId]);
    }
    
    /**
     * Get tier count for a campaign
     */
    public function countByCampaign($campaignId)
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE campaign_id = ?";
        $result = $this->query($sql, [$campaignId]);
        return $result[0]->count ?? 0;
    }
    
    /**
     * Validate tier data
     */
    public function validateTier($data)
    {
        $errors = [];
        
        if (empty($data['campaign_id'])) {
            $errors[] = 'Campaign ID is required';
        }
        
        if (empty($data['tier_rank']) || $data['tier_rank'] < 1) {
            $errors[] = 'Valid tier rank is required';
        }
        
        if (empty($data['tier_name'])) {
            $errors[] = 'Tier name is required';
        }
        
        if (empty($data['item_name'])) {
            $errors[] = 'Item name is required';
        }
        
        if (!isset($data['item_value']) || $data['item_value'] < 0) {
            $errors[] = 'Valid item value is required';
        }
        
        return $errors;
    }
}
