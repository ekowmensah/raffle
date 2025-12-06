<?php

namespace App\Models;

use App\Core\Model;

class PasswordReset extends Model
{
    protected $table = 'password_resets';

    public function createToken($email)
    {
        // Delete any existing tokens for this email
        $this->deleteByEmail($email);

        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $data = [
            'email' => $email,
            'token' => hash('sha256', $token),
            'expires_at' => $expires
        ];

        $this->db->query("INSERT INTO {$this->table} (email, token, expires_at) 
                         VALUES (:email, :token, :expires_at)");
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':token', $data['token']);
        $this->db->bind(':expires_at', $data['expires_at']);
        $this->db->execute();

        return $token; // Return unhashed token for email
    }

    public function findByToken($token)
    {
        $hashedToken = hash('sha256', $token);
        
        $this->db->query("SELECT * FROM {$this->table} 
                         WHERE token = :token 
                         AND expires_at > NOW()");
        $this->db->bind(':token', $hashedToken);
        
        return $this->db->single();
    }

    public function deleteByEmail($email)
    {
        $this->db->query("DELETE FROM {$this->table} WHERE email = :email");
        $this->db->bind(':email', $email);
        $this->db->execute();
    }

    public function deleteByToken($token)
    {
        $hashedToken = hash('sha256', $token);
        
        $this->db->query("DELETE FROM {$this->table} WHERE token = :token");
        $this->db->bind(':token', $hashedToken);
        $this->db->execute();
    }
}
