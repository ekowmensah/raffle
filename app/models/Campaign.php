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
                         (SELECT COUNT(*) FROM campaign_programme_access WHERE campaign_id = c.id) as programme_count
                         FROM {$this->table} c
                         LEFT JOIN sponsors s ON c.sponsor_id = s.id
                         LEFT JOIN stations st ON c.station_id = st.id
                         LEFT JOIN users u ON c.created_by_user_id = u.id
                         ORDER BY c.created_at DESC");
        return $this->db->resultSet();
    }

    public function getStats($campaignId)
    {
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
        return $this->db->single();
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
}
