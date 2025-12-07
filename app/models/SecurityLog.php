<?php

namespace App\Models;

use App\Core\Model;

class SecurityLog extends Model
{
    protected $table = 'security_logs';

    /**
     * Log failed login attempt
     */
    public function logFailedLogin($email, $ipAddress, $userAgent)
    {
        return $this->create([
            'event_type' => 'failed_login',
            'email' => $email,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Get failed login attempts for an email/IP
     */
    public function getFailedAttempts($email = null, $ipAddress = null, $minutes = 15)
    {
        $sql = "SELECT COUNT(*) as attempts 
                FROM {$this->table} 
                WHERE event_type = 'failed_login'
                AND created_at >= DATE_SUB(NOW(), INTERVAL :minutes MINUTE)";
        
        $params = [':minutes' => $minutes];
        
        if ($email) {
            $sql .= " AND email = :email";
            $params[':email'] = $email;
        }
        
        if ($ipAddress) {
            $sql .= " AND ip_address = :ip_address";
            $params[':ip_address'] = $ipAddress;
        }
        
        $this->db->query($sql);
        
        foreach ($params as $key => $value) {
            $this->db->bind($key, $value);
        }
        
        $result = $this->db->single();
        return $result ? $result->attempts : 0;
    }

    /**
     * Check if IP is blocked
     */
    public function isBlocked($ipAddress)
    {
        $this->db->query("SELECT * FROM ip_blocks 
                         WHERE ip_address = :ip_address 
                         AND (expires_at IS NULL OR expires_at > NOW())
                         LIMIT 1");
        
        $this->db->bind(':ip_address', $ipAddress);
        
        return $this->db->single() !== false;
    }

    /**
     * Block an IP address
     */
    public function blockIP($ipAddress, $reason, $duration = 60)
    {
        $this->db->query("INSERT INTO ip_blocks (ip_address, reason, expires_at, created_at)
                         VALUES (:ip_address, :reason, DATE_ADD(NOW(), INTERVAL :duration MINUTE), NOW())
                         ON DUPLICATE KEY UPDATE 
                         expires_at = DATE_ADD(NOW(), INTERVAL :duration MINUTE),
                         reason = :reason");
        
        $this->db->bind(':ip_address', $ipAddress);
        $this->db->bind(':reason', $reason);
        $this->db->bind(':duration', $duration);
        
        return $this->db->execute();
    }

    /**
     * Unblock an IP address
     */
    public function unblockIP($ipAddress)
    {
        $this->db->query("DELETE FROM ip_blocks WHERE ip_address = :ip_address");
        $this->db->bind(':ip_address', $ipAddress);
        return $this->db->execute();
    }

    /**
     * Get recent security events
     */
    public function getRecentEvents($limit = 50)
    {
        $this->db->query("SELECT * FROM {$this->table} 
                         ORDER BY created_at DESC 
                         LIMIT :limit");
        
        $this->db->bind(':limit', $limit);
        
        return $this->db->resultSet();
    }

    /**
     * Get blocked IPs
     */
    public function getBlockedIPs()
    {
        $this->db->query("SELECT * FROM ip_blocks 
                         WHERE expires_at IS NULL OR expires_at > NOW()
                         ORDER BY created_at DESC");
        
        return $this->db->resultSet();
    }

    /**
     * Clean expired blocks
     */
    public function cleanExpiredBlocks()
    {
        $this->db->query("DELETE FROM ip_blocks WHERE expires_at < NOW()");
        return $this->db->execute();
    }

    /**
     * Log suspicious activity
     */
    public function logSuspiciousActivity($type, $details, $userId = null)
    {
        return $this->create([
            'event_type' => $type,
            'user_id' => $userId,
            'details' => json_encode($details),
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }

    public function countToday()
    {
        $this->db->query("SELECT COUNT(*) as count FROM {$this->table} 
                         WHERE DATE(created_at) = CURDATE()");
        $result = $this->db->single();
        return $result->count ?? 0;
    }
}
