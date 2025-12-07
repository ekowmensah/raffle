<?php

namespace App\Models;

use App\Core\Model;

class CampaignProgrammeAccess extends Model
{
    protected $table = 'campaign_programme_access';

    public function getByCampaign($campaignId)
    {
        $this->db->query("SELECT cpa.*, p.name as programme_name, s.name as station_name
                         FROM {$this->table} cpa
                         LEFT JOIN programmes p ON cpa.programme_id = p.id
                         LEFT JOIN stations s ON p.station_id = s.id
                         WHERE cpa.campaign_id = :campaign_id
                         ORDER BY s.name, p.name");
        $this->db->bind(':campaign_id', $campaignId);
        return $this->db->resultSet();
    }

    public function addProgramme($campaignId, $programmeId)
    {
        // Check if already exists
        $this->db->query("SELECT * FROM {$this->table} 
                         WHERE campaign_id = :campaign_id AND programme_id = :programme_id");
        $this->db->bind(':campaign_id', $campaignId);
        $this->db->bind(':programme_id', $programmeId);
        
        if ($this->db->single()) {
            return true; // Already exists
        }

        return $this->create([
            'campaign_id' => $campaignId,
            'programme_id' => $programmeId
        ]);
    }

    public function removeProgramme($campaignId, $programmeId)
    {
        $this->db->query("DELETE FROM {$this->table} 
                         WHERE campaign_id = :campaign_id AND programme_id = :programme_id");
        $this->db->bind(':campaign_id', $campaignId);
        $this->db->bind(':programme_id', $programmeId);
        return $this->db->execute();
    }

    public function removeAllByCampaign($campaignId)
    {
        $this->db->query("DELETE FROM {$this->table} WHERE campaign_id = :campaign_id");
        $this->db->bind(':campaign_id', $campaignId);
        return $this->db->execute();
    }

    public function syncProgrammes($campaignId, $programmeIds)
    {
        // Remove all existing
        $this->removeAllByCampaign($campaignId);

        // Add new ones
        if (!empty($programmeIds)) {
            foreach ($programmeIds as $programmeId) {
                $this->addProgramme($campaignId, $programmeId);
            }
        }

        return true;
    }

    public function countByProgramme($programmeId)
    {
        $this->db->query("SELECT COUNT(*) as count FROM {$this->table} WHERE programme_id = :programme_id");
        $this->db->bind(':programme_id', $programmeId);
        $result = $this->db->single();
        return $result->count ?? 0;
    }

    public function findByCampaignAndProgramme($campaignId, $programmeId)
    {
        $this->db->query("SELECT * FROM {$this->table} 
                         WHERE campaign_id = :campaign_id AND programme_id = :programme_id");
        $this->db->bind(':campaign_id', $campaignId);
        $this->db->bind(':programme_id', $programmeId);
        $result = $this->db->single();
        
        // Debug logging
        error_log("DB Query - Campaign: {$campaignId}, Programme: {$programmeId}, Found: " . ($result ? 'YES (ID: ' . $result->id . ')' : 'NO'));
        
        return $result;
    }
}
