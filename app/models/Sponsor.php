<?php

namespace App\Models;

use App\Core\Model;

class Sponsor extends Model
{
    protected $table = 'sponsors';

    public function findByName($name)
    {
        $this->db->query("SELECT * FROM {$this->table} WHERE name = :name");
        $this->db->bind(':name', $name);
        return $this->db->single();
    }

    public function getActive()
    {
        $this->db->query("SELECT * FROM {$this->table} ORDER BY name");
        return $this->db->resultSet();
    }

    public function getCampaignCount($sponsorId)
    {
        $this->db->query("SELECT COUNT(*) as count FROM raffle_campaigns WHERE sponsor_id = :sponsor_id");
        $this->db->bind(':sponsor_id', $sponsorId);
        $result = $this->db->single();
        return $result->count ?? 0;
    }

    public function getAllWithStats()
    {
        $this->db->query("SELECT s.*, 
                         COUNT(DISTINCT rc.id) as campaign_count
                         FROM {$this->table} s
                         LEFT JOIN raffle_campaigns rc ON s.id = rc.sponsor_id
                         GROUP BY s.id
                         ORDER BY s.name");
        return $this->db->resultSet();
    }
}
