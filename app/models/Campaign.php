<?php

namespace App\Models;

use App\Core\Model;

class Campaign extends Model
{
    protected $table = 'raffle_campaigns';

    public function findByCode($code)
    {
        $this->db->query("SELECT * FROM {$this->table} WHERE code = :code");
        $this->db->bind(':code', $code);
        return $this->db->single();
    }

    public function getActive()
    {
        $this->db->query("SELECT c.*, s.name as sponsor_name 
                         FROM {$this->table} c
                         LEFT JOIN sponsors s ON c.sponsor_id = s.id
                         WHERE c.status = 'active' 
                         ORDER BY c.start_date DESC");
        return $this->db->resultSet();
    }

    public function getActiveByStation($stationId)
    {
        $this->db->query("SELECT c.*, s.name as sponsor_name
                         FROM {$this->table} c
                         LEFT JOIN sponsors s ON c.sponsor_id = s.id
                         WHERE c.status = 'active' 
                         AND c.station_id = :station_id
                         ORDER BY c.start_date DESC");
        $this->db->bind(':station_id', $stationId);
        return $this->db->resultSet();
    }

    public function getActiveByProgramme($programmeId)
    {
        // Get programme's station first
        $this->db->query("SELECT station_id FROM programmes WHERE id = :programme_id");
        $this->db->bind(':programme_id', $programmeId);
        $programme = $this->db->single();
        
        if (!$programme) {
            return [];
        }
        
        // Get ONLY programme-specific campaigns (NOT station-wide)
        $this->db->query("SELECT DISTINCT c.id, c.name, c.code, c.ticket_price, c.currency, c.status, c.station_id
                         FROM {$this->table} c
                         INNER JOIN campaign_programme_access cpa ON c.id = cpa.campaign_id
                         WHERE c.status = 'active' 
                         AND c.station_id = :station_id
                         AND cpa.programme_id = :programme_id
                         ORDER BY c.start_date DESC");
        $this->db->bind(':station_id', $programme->station_id);
        $this->db->bind(':programme_id', $programmeId);
        return $this->db->resultSet();
    }

    public function getAllWithDetails()
    {
        $this->db->query("SELECT c.*, 
                         s.name as sponsor_name,
                         st.name as station_name,
                         u.name as created_by_name,
                         (SELECT COUNT(*) FROM tickets WHERE campaign_id = c.id) as total_tickets,
                         (SELECT COALESCE(SUM(amount), 0) FROM payments WHERE campaign_id = c.id AND status = 'success') as total_revenue,
                         (SELECT COUNT(*) FROM campaign_programme_access WHERE campaign_id = c.id) as programme_count,
                         (SELECT COALESCE(SUM(winner_pool_amount_total), 0) FROM revenue_allocations WHERE campaign_id = c.id) as prize_pool_allocated
                         FROM {$this->table} c
                         LEFT JOIN sponsors s ON c.sponsor_id = s.id
                         LEFT JOIN stations st ON c.station_id = st.id
                         LEFT JOIN users u ON c.created_by_user_id = u.id
                         ORDER BY c.created_at DESC");
        return $this->db->resultSet();
    }

    public function getStats($campaignId)
    {
        // Get campaign to check type
        $campaign = $this->findById($campaignId);
        
        $this->db->query("SELECT 
                         (SELECT COUNT(*) FROM tickets WHERE campaign_id = :cid1) as total_tickets,
                         (SELECT COUNT(DISTINCT player_id) FROM tickets WHERE campaign_id = :cid2) as total_players,
                         (SELECT COALESCE(SUM(amount), 0) FROM payments WHERE campaign_id = :cid3 AND status = 'success') as total_revenue,
                         (SELECT COALESCE(SUM(winner_pool_amount_total), 0) FROM revenue_allocations WHERE campaign_id = :cid4) as total_prize_pool,
                         (SELECT COUNT(*) FROM draws WHERE campaign_id = :cid5) as total_draws");
        $this->db->bind(':cid1', $campaignId);
        $this->db->bind(':cid2', $campaignId);
        $this->db->bind(':cid3', $campaignId);
        $this->db->bind(':cid4', $campaignId);
        $this->db->bind(':cid5', $campaignId);
        
        $stats = $this->db->single();
        
        // For item campaigns, set total_prize_pool to item_value instead of 0
        if ($campaign && $campaign->campaign_type === 'item') {
            $stats->total_prize_pool = $campaign->item_value ?? 0;
        }
        
        return $stats;
    }

    public function lockConfiguration($campaignId)
    {
        return $this->update($campaignId, ['is_config_locked' => 1]);
    }

    public function unlockConfiguration($campaignId)
    {
        return $this->update($campaignId, ['is_config_locked' => 0]);
    }

    public function cloneCampaign($campaignId, $newName, $newCode)
    {
        $campaign = $this->findById($campaignId);
        if (!$campaign) {
            return false;
        }

        $newData = [
            'name' => $newName,
            'code' => $newCode,
            'description' => $campaign->description,
            'sponsor_id' => $campaign->sponsor_id,
            'ticket_price' => $campaign->ticket_price,
            'currency' => $campaign->currency,
            'platform_percent' => $campaign->platform_percent,
            'station_percent' => $campaign->station_percent,
            'programme_percent' => $campaign->programme_percent,
            'prize_pool_percent' => $campaign->prize_pool_percent,
            'daily_share_percent_of_pool' => $campaign->daily_share_percent_of_pool,
            'final_share_percent_of_pool' => $campaign->final_share_percent_of_pool,
            'daily_draw_enabled' => $campaign->daily_draw_enabled,
            'status' => 'draft',
            'created_by_user_id' => $_SESSION['user_id']
        ];

        return $this->create($newData);
    }

    public function getByStatus($status)
    {
        $this->db->query("SELECT c.*, s.name as sponsor_name
                         FROM {$this->table} c
                         LEFT JOIN sponsors s ON c.sponsor_id = s.id
                         WHERE c.status = :status
                         ORDER BY c.created_at DESC");
        $this->db->bind(':status', $status);
        return $this->db->resultSet();
    }

    public function updateStatus($campaignId, $status)
    {
        return $this->update($campaignId, ['status' => $status]);
    }

    public function countByStatus($status)
    {
        $this->db->query("SELECT COUNT(*) as count FROM {$this->table} WHERE status = :status");
        $this->db->bind(':status', $status);
        $result = $this->db->single();
        return $result->count ?? 0;
    }

    public function count()
    {
        $this->db->query("SELECT COUNT(*) as count FROM {$this->table}");
        $result = $this->db->single();
        return $result->count ?? 0;
    }

    public function getByStation($stationId)
    {
        $this->db->query("SELECT c.*, 
                         s.name as sponsor_name,
                         st.name as station_name,
                         (SELECT COUNT(*) FROM tickets WHERE campaign_id = c.id) as total_tickets,
                         (SELECT COALESCE(SUM(amount), 0) FROM payments WHERE campaign_id = c.id AND status = 'success') as total_revenue,
                         (SELECT COALESCE(SUM(station_amount + programme_amount), 0) FROM revenue_allocations WHERE campaign_id = c.id) as station_allocated_revenue,
                         (SELECT COALESCE(SUM(winner_pool_amount_total), 0) FROM revenue_allocations WHERE campaign_id = c.id) as prize_pool_allocated
                         FROM {$this->table} c
                         LEFT JOIN sponsors s ON c.sponsor_id = s.id
                         LEFT JOIN stations st ON c.station_id = st.id
                         WHERE c.station_id = :station_id
                         ORDER BY c.created_at DESC");
        $this->db->bind(':station_id', $stationId);
        return $this->db->resultSet();
    }

    public function getByProgramme($programmeId)
    {
        $this->db->query("SELECT c.*, 
                         s.name as sponsor_name,
                         st.name as station_name,
                         (SELECT COUNT(*) FROM tickets WHERE campaign_id = c.id) as total_tickets,
                         (SELECT COALESCE(SUM(amount), 0) FROM payments WHERE campaign_id = c.id AND status = 'success') as total_revenue
                         FROM {$this->table} c
                         LEFT JOIN sponsors s ON c.sponsor_id = s.id
                         LEFT JOIN stations st ON c.station_id = st.id
                         LEFT JOIN campaign_programme_access cpa ON c.id = cpa.campaign_id
                         WHERE cpa.programme_id = :programme_id
                         ORDER BY c.created_at DESC");
        $this->db->bind(':programme_id', $programmeId);
        return $this->db->resultSet();
    }

    public function countActiveByStation($stationId)
    {
        $this->db->query("SELECT COUNT(*) as count FROM {$this->table} 
                         WHERE station_id = :station_id AND status = 'active'");
        $this->db->bind(':station_id', $stationId);
        $result = $this->db->single();
        return $result->count ?? 0;
    }

    public function countActiveByProgramme($programmeId)
    {
        $this->db->query("SELECT COUNT(DISTINCT c.id) as count 
                         FROM {$this->table} c
                         LEFT JOIN campaign_programme_access cpa ON c.id = cpa.campaign_id
                         WHERE cpa.programme_id = :programme_id AND c.status = 'active'");
        $this->db->bind(':programme_id', $programmeId);
        $result = $this->db->single();
        return $result->count ?? 0;
    }

    // ============================================
    // ITEM CAMPAIGN METHODS
    // ============================================

    /**
     * Check if campaign is item-based
     */
    public function isItemCampaign($campaign = null)
    {
        if ($campaign === null) {
            return false;
        }
        return isset($campaign->campaign_type) && $campaign->campaign_type === 'item';
    }

    /**
     * Get display prize (item name or cash amount)
     */
    public function getDisplayPrize($campaign)
    {
        if ($this->isItemCampaign($campaign)) {
            $value = number_format($campaign->item_value ?? 0, 2);
            return $campaign->item_name . ' (Worth ' . $campaign->currency . ' ' . $value . ')';
        }
        
        $stats = $this->getStats($campaign->id);
        $prizePool = $stats->total_prize_pool ?? 0;
        return $campaign->currency . ' ' . number_format($prizePool, 2);
    }

    /**
     * Get prize structure for display
     */
    public function getPrizeStructure($campaignId)
    {
        $campaign = $this->findById($campaignId);
        
        if ($this->isItemCampaign($campaign)) {
            $structure = [
                'type' => 'item',
                'main_prize' => $campaign->item_name,
                'value' => $campaign->item_value,
                'quantity' => $campaign->item_quantity ?? 1,
                'selection_type' => $campaign->winner_selection_type ?? 'single'
            ];
            
            // Get tiered prizes if applicable
            if ($campaign->winner_selection_type === 'tiered') {
                $structure['prizes'] = $this->getItemPrizes($campaignId);
            }
            
            return $structure;
        }
        
        // Cash campaign structure
        $stats = $this->getStats($campaignId);
        $totalRevenue = $stats->total_revenue ?? 0;
        $prizePool = $totalRevenue * ($campaign->prize_pool_percent / 100);
        
        return [
            'type' => 'cash',
            'prize_pool' => $prizePool,
            'first_prize' => $prizePool * 0.50,
            'second_prize' => $prizePool * 0.30,
            'third_prize' => $prizePool * 0.20
        ];
    }

    /**
     * Get item prizes for tiered campaigns
     */
    public function getItemPrizes($campaignId)
    {
        $this->db->query("SELECT * FROM item_prizes 
                         WHERE campaign_id = :campaign_id 
                         ORDER BY prize_position ASC");
        $this->db->bind(':campaign_id', $campaignId);
        return $this->db->resultSet();
    }

    /**
     * Add item prize to campaign
     */
    public function addItemPrize($data)
    {
        $this->db->query("INSERT INTO item_prizes 
                         (campaign_id, prize_position, prize_type, item_name, item_description, 
                          item_value, item_image, cash_amount, cash_percentage) 
                         VALUES 
                         (:campaign_id, :prize_position, :prize_type, :item_name, :item_description, 
                          :item_value, :item_image, :cash_amount, :cash_percentage)");
        
        $this->db->bind(':campaign_id', $data['campaign_id']);
        $this->db->bind(':prize_position', $data['prize_position']);
        $this->db->bind(':prize_type', $data['prize_type']);
        $this->db->bind(':item_name', $data['item_name'] ?? null);
        $this->db->bind(':item_description', $data['item_description'] ?? null);
        $this->db->bind(':item_value', $data['item_value'] ?? null);
        $this->db->bind(':item_image', $data['item_image'] ?? null);
        $this->db->bind(':cash_amount', $data['cash_amount'] ?? null);
        $this->db->bind(':cash_percentage', $data['cash_percentage'] ?? null);
        
        return $this->db->execute();
    }

    /**
     * Get item images for campaign
     */
    public function getItemImages($campaignId)
    {
        $this->db->query("SELECT * FROM item_images 
                         WHERE campaign_id = :campaign_id 
                         ORDER BY is_primary DESC, display_order ASC");
        $this->db->bind(':campaign_id', $campaignId);
        return $this->db->resultSet();
    }

    /**
     * Add item image
     */
    public function addItemImage($campaignId, $imagePath, $isPrimary = false, $displayOrder = 0)
    {
        $this->db->query("INSERT INTO item_images 
                         (campaign_id, image_path, is_primary, display_order) 
                         VALUES (:campaign_id, :image_path, :is_primary, :display_order)");
        
        $this->db->bind(':campaign_id', $campaignId);
        $this->db->bind(':image_path', $imagePath);
        $this->db->bind(':is_primary', $isPrimary ? 1 : 0);
        $this->db->bind(':display_order', $displayOrder);
        
        return $this->db->execute();
    }

    /**
     * Get item specifications
     */
    public function getItemSpecifications($campaignId)
    {
        $this->db->query("SELECT * FROM item_specifications 
                         WHERE campaign_id = :campaign_id 
                         ORDER BY display_order ASC");
        $this->db->bind(':campaign_id', $campaignId);
        return $this->db->resultSet();
    }

    /**
     * Add item specification
     */
    public function addItemSpecification($campaignId, $key, $value, $displayOrder = 0)
    {
        $this->db->query("INSERT INTO item_specifications 
                         (campaign_id, spec_key, spec_value, display_order) 
                         VALUES (:campaign_id, :spec_key, :spec_value, :display_order)");
        
        $this->db->bind(':campaign_id', $campaignId);
        $this->db->bind(':spec_key', $key);
        $this->db->bind(':spec_value', $value);
        $this->db->bind(':display_order', $displayOrder);
        
        return $this->db->execute();
    }

    /**
     * Check if minimum tickets reached for item campaign
     */
    public function hasReachedMinimumTickets($campaignId)
    {
        $campaign = $this->findById($campaignId);
        
        if (!$this->isItemCampaign($campaign) || !$campaign->min_tickets_for_draw) {
            return true; // No minimum set or not item campaign
        }
        
        $stats = $this->getStats($campaignId);
        return ($stats->total_tickets ?? 0) >= $campaign->min_tickets_for_draw;
    }

    /**
     * Calculate break-even tickets for item campaign
     */
    public function calculateBreakEvenTickets($itemValue, $ticketPrice)
    {
        if ($ticketPrice <= 0) {
            return 0;
        }
        return ceil($itemValue / $ticketPrice);
    }

    /**
     * Get item campaigns only
     */
    public function getItemCampaigns($status = 'active')
    {
        $this->db->query("SELECT c.*, s.name as sponsor_name 
                         FROM {$this->table} c
                         LEFT JOIN sponsors s ON c.sponsor_id = s.id
                         WHERE c.campaign_type = 'item' 
                         AND c.status = :status
                         ORDER BY c.start_date DESC");
        $this->db->bind(':status', $status);
        return $this->db->resultSet();
    }

    /**
     * Get cash campaigns only
     */
    public function getCashCampaigns($status = 'active')
    {
        $this->db->query("SELECT c.*, s.name as sponsor_name 
                         FROM {$this->table} c
                         LEFT JOIN sponsors s ON c.sponsor_id = s.id
                         WHERE c.campaign_type = 'cash' 
                         AND c.status = :status
                         ORDER BY c.start_date DESC");
        $this->db->bind(':status', $status);
        return $this->db->resultSet();
    }

    public function getTopByRevenue($limit = 5)
    {
        $this->db->query("SELECT c.id, c.name, st.name as station_name,
                         (SELECT COALESCE(SUM(p.amount), 0)
                          FROM payments p
                          WHERE p.campaign_id = c.id AND p.status = 'success') as revenue,
                         (SELECT COALESCE(SUM(t.quantity), 0)
                          FROM tickets t
                          WHERE t.campaign_id = c.id) as ticket_count
                         FROM {$this->table} c
                         LEFT JOIN stations st ON c.station_id = st.id
                         ORDER BY revenue DESC
                         LIMIT :limit");
        $this->db->bind(':limit', $limit);
        return $this->db->resultSet();
    }
}
