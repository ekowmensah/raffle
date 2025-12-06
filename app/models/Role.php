<?php

namespace App\Models;

use App\Core\Model;

class Role extends Model
{
    protected $table = 'roles';

    public function findByName($name)
    {
        $this->db->query("SELECT * FROM {$this->table} WHERE name = :name");
        $this->db->bind(':name', $name);
        return $this->db->single();
    }

    public function getUserCount($roleId)
    {
        $this->db->query("SELECT COUNT(*) as count FROM users WHERE role_id = :role_id");
        $this->db->bind(':role_id', $roleId);
        $result = $this->db->single();
        return $result->count ?? 0;
    }

    public function getAllWithUserCount()
    {
        $this->db->query("SELECT r.*, COUNT(u.id) as user_count 
                         FROM {$this->table} r 
                         LEFT JOIN users u ON r.id = u.role_id 
                         GROUP BY r.id 
                         ORDER BY r.name");
        return $this->db->resultSet();
    }

    public function canDelete($roleId)
    {
        $userCount = $this->getUserCount($roleId);
        return $userCount === 0;
    }
}
