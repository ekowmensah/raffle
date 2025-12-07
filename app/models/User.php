<?php

namespace App\Models;

use App\Core\Model;

class User extends Model
{
    protected $table = 'users';

    public function findByEmail($email)
    {
        $this->db->query("SELECT u.*, r.name as role_name, s.name as station_name, p.name as programme_name 
                         FROM {$this->table} u 
                         LEFT JOIN roles r ON u.role_id = r.id 
                         LEFT JOIN stations s ON u.station_id = s.id
                         LEFT JOIN programmes p ON u.programme_id = p.id
                         WHERE u.email = :email");
        $this->db->bind(':email', $email);
        return $this->db->single();
    }

    public function findByPhone($phone)
    {
        $this->db->query("SELECT u.*, r.name as role_name, s.name as station_name, p.name as programme_name 
                         FROM {$this->table} u 
                         LEFT JOIN roles r ON u.role_id = r.id 
                         WHERE u.phone = :phone");
        $this->db->bind(':phone', $phone);
        return $this->db->single();
    }

    public function authenticate($email, $password)
    {
        $user = $this->findByEmail($email);
        
        if ($user && password_verify($password, $user->password_hash)) {
            // Update last login
            $this->db->query("UPDATE {$this->table} SET last_login_at = NOW() WHERE id = :id");
            $this->db->bind(':id', $user->id);
            $this->db->execute();
            
            return $user;
        }
        
        return false;
    }

    public function register($data)
    {
        $data['password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
        unset($data['password']);
        
        return $this->create($data);
    }

    public function getAllWithRoles()
    {
        $this->db->query("SELECT u.*, r.name as role_name, s.name as station_name, p.name as programme_name
                         FROM {$this->table} u 
                         LEFT JOIN roles r ON u.role_id = r.id
                         LEFT JOIN stations s ON u.station_id = s.id
                         LEFT JOIN programmes p ON u.programme_id = p.id
                         ORDER BY u.created_at DESC");
        return $this->db->resultSet();
    }

    public function countByProgramme($programmeId)
    {
        $this->db->query("SELECT COUNT(*) as count FROM {$this->table} WHERE programme_id = :programme_id");
        $this->db->bind(':programme_id', $programmeId);
        $result = $this->db->single();
        return $result->count ?? 0;
    }

    public function countByStation($stationId)
    {
        $this->db->query("SELECT COUNT(*) as count FROM {$this->table} WHERE station_id = :station_id");
        $this->db->bind(':station_id', $stationId);
        $result = $this->db->single();
        return $result->count ?? 0;
    }

    public function getByStation($stationId)
    {
        $this->db->query("SELECT u.*, r.name as role_name, p.name as programme_name
                         FROM {$this->table} u
                         LEFT JOIN roles r ON u.role_id = r.id
                         LEFT JOIN programmes p ON u.programme_id = p.id
                         WHERE u.station_id = :station_id
                         ORDER BY u.created_at DESC");
        $this->db->bind(':station_id', $stationId);
        return $this->db->resultSet();
    }
}
