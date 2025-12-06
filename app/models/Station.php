<?php

namespace App\Models;

use App\Core\Model;

class Station extends Model
{
    protected $table = 'stations';

    public function findByCode($code)
    {
        $this->db->query("SELECT * FROM {$this->table} WHERE code = :code");
        $this->db->bind(':code', $code);
        return $this->db->single();
    }

    public function getAll()
    {
        $this->db->query("SELECT * FROM {$this->table} ORDER BY name");
        return $this->db->resultSet();
    }

    public function getActive()
    {
        $this->db->query("SELECT * FROM {$this->table} WHERE is_active = 1 ORDER BY name");
        return $this->db->resultSet();
    }

    public function getProgrammes($stationId)
    {
        $this->db->query("SELECT * FROM programmes WHERE station_id = :station_id AND is_active = 1");
        $this->db->bind(':station_id', $stationId);
        return $this->db->resultSet();
    }

    public function getWithStats($stationId)
    {
        $this->db->query("SELECT s.*, 
                         COUNT(DISTINCT p.id) as programme_count,
                         COUNT(DISTINCT u.id) as user_count
                         FROM {$this->table} s
                         LEFT JOIN programmes p ON s.id = p.station_id
                         LEFT JOIN users u ON s.id = u.station_id
                         WHERE s.id = :station_id
                         GROUP BY s.id");
        $this->db->bind(':station_id', $stationId);
        return $this->db->single();
    }

    public function countActive()
    {
        $this->db->query("SELECT COUNT(*) as count FROM {$this->table} WHERE is_active = 1");
        $result = $this->db->single();
        return $result->count ?? 0;
    }
}
