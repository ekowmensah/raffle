<?php

namespace App\Models;

use App\Core\Model;

class Programme extends Model
{
    protected $table = 'programmes';

    public function findByCode($stationId, $code)
    {
        $this->db->query("SELECT * FROM {$this->table} WHERE station_id = :station_id AND code = :code");
        $this->db->bind(':station_id', $stationId);
        $this->db->bind(':code', $code);
        return $this->db->single();
    }

    public function getByStation($stationId, $activeOnly = true)
    {
        $sql = "SELECT * FROM {$this->table} WHERE station_id = :station_id";
        
        if ($activeOnly) {
            $sql .= " AND is_active = 1";
        }
        
        $sql .= " ORDER BY name";
        
        $this->db->query($sql);
        $this->db->bind(':station_id', $stationId);
        return $this->db->resultSet();
    }

    public function getActive()
    {
        $this->db->query("SELECT p.*, s.name as station_name 
                         FROM {$this->table} p
                         LEFT JOIN stations s ON p.station_id = s.id
                         WHERE p.is_active = 1 
                         ORDER BY s.name, p.name");
        return $this->db->resultSet();
    }

    public function getAllWithStation()
    {
        $this->db->query("SELECT p.*, s.name as station_name, s.code as station_code
                         FROM {$this->table} p
                         LEFT JOIN stations s ON p.station_id = s.id
                         ORDER BY s.name, p.name");
        return $this->db->resultSet();
    }

    public function getWithStats($programmeId)
    {
        $this->db->query("SELECT p.*, 
                         s.name as station_name,
                         COUNT(DISTINCT u.id) as user_count,
                         COUNT(DISTINCT t.id) as ticket_count
                         FROM {$this->table} p
                         LEFT JOIN stations s ON p.station_id = s.id
                         LEFT JOIN users u ON p.id = u.programme_id
                         LEFT JOIN tickets t ON p.id = t.programme_id
                         WHERE p.id = :programme_id
                         GROUP BY p.id");
        $this->db->bind(':programme_id', $programmeId);
        return $this->db->single();
    }

    public function countByStation($stationId, $activeOnly = false)
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE station_id = :station_id";
        
        if ($activeOnly) {
            $sql .= " AND is_active = 1";
        }
        
        $this->db->query($sql);
        $this->db->bind(':station_id', $stationId);
        $result = $this->db->single();
        return $result->count ?? 0;
    }

    public function countByUser($userId)
    {
        $this->db->query("SELECT COUNT(*) as count FROM {$this->table} WHERE created_by = :user_id");
        $this->db->bind(':user_id', $userId);
        $result = $this->db->single();
        return $result->count ?? 0;
    }
}
