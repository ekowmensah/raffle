<?php

namespace App\Models;

use App\Core\Model;

class AuditLog extends Model
{
    protected $table = 'audit_logs';

    /**
     * Log an action
     */
    public function logAction($data)
    {
        $logData = [
            'user_id' => $data['user_id'] ?? null,
            'action' => $data['action'],
            'entity_type' => $data['entity_type'] ?? null,
            'entity_id' => $data['entity_id'] ?? null,
            'old_values' => isset($data['old_values']) ? json_encode($data['old_values']) : null,
            'new_values' => isset($data['new_values']) ? json_encode($data['new_values']) : null,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
            'created_at' => date('Y-m-d H:i:s')
        ];

        return $this->create($logData);
    }

    /**
     * Get logs with filters
     */
    public function getWithFilters($filters = [])
    {
        $sql = "SELECT al.*, u.name as username, u.email 
                FROM {$this->table} al
                LEFT JOIN users u ON al.user_id = u.id
                WHERE 1=1";
        
        $params = [];

        // Filter by user
        if (!empty($filters['user_id'])) {
            $sql .= " AND al.user_id = :user_id";
            $params[':user_id'] = $filters['user_id'];
        }

        // Filter by action
        if (!empty($filters['action'])) {
            $sql .= " AND al.action = :action";
            $params[':action'] = $filters['action'];
        }

        // Filter by entity type
        if (!empty($filters['entity_type'])) {
            $sql .= " AND al.entity_type = :entity_type";
            $params[':entity_type'] = $filters['entity_type'];
        }

        // Filter by entity ID
        if (!empty($filters['entity_id'])) {
            $sql .= " AND al.entity_id = :entity_id";
            $params[':entity_id'] = $filters['entity_id'];
        }

        // Filter by date range
        if (!empty($filters['date_from'])) {
            $sql .= " AND DATE(al.created_at) >= :date_from";
            $params[':date_from'] = $filters['date_from'];
        }

        if (!empty($filters['date_to'])) {
            $sql .= " AND DATE(al.created_at) <= :date_to";
            $params[':date_to'] = $filters['date_to'];
        }

        // Filter by IP address
        if (!empty($filters['ip_address'])) {
            $sql .= " AND al.ip_address = :ip_address";
            $params[':ip_address'] = $filters['ip_address'];
        }

        $sql .= " ORDER BY al.created_at DESC";

        // Add limit
        $limit = $filters['limit'] ?? 100;
        $sql .= " LIMIT :limit";
        $params[':limit'] = (int)$limit;

        $this->db->query($sql);
        
        foreach ($params as $key => $value) {
            $this->db->bind($key, $value);
        }

        return $this->db->resultSet();
    }

    /**
     * Get logs by entity
     */
    public function getByEntity($entityType, $entityId)
    {
        $this->db->query("SELECT al.*, u.name as username 
                         FROM {$this->table} al
                         LEFT JOIN users u ON al.user_id = u.id
                         WHERE al.entity_type = :entity_type 
                         AND al.entity_id = :entity_id
                         ORDER BY al.created_at DESC");
        
        $this->db->bind(':entity_type', $entityType);
        $this->db->bind(':entity_id', $entityId);
        
        return $this->db->resultSet();
    }

    /**
     * Get logs by user
     */
    public function getByUser($userId, $limit = 50)
    {
        $this->db->query("SELECT * FROM {$this->table} 
                         WHERE user_id = :user_id 
                         ORDER BY created_at DESC 
                         LIMIT :limit");
        
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':limit', $limit);
        
        return $this->db->resultSet();
    }

    /**
     * Get action statistics
     */
    public function getActionStats($dateFrom = null, $dateTo = null)
    {
        $sql = "SELECT action, COUNT(*) as count 
                FROM {$this->table} 
                WHERE 1=1";
        
        $params = [];

        if ($dateFrom) {
            $sql .= " AND DATE(created_at) >= :date_from";
            $params[':date_from'] = $dateFrom;
        }

        if ($dateTo) {
            $sql .= " AND DATE(created_at) <= :date_to";
            $params[':date_to'] = $dateTo;
        }

        $sql .= " GROUP BY action ORDER BY count DESC";

        $this->db->query($sql);
        
        foreach ($params as $key => $value) {
            $this->db->bind($key, $value);
        }

        return $this->db->resultSet();
    }

    /**
     * Get user activity stats
     */
    public function getUserActivityStats($limit = 10)
    {
        $this->db->query("SELECT u.name as username, u.email, COUNT(*) as action_count,
                         MAX(al.created_at) as last_activity
                         FROM {$this->table} al
                         LEFT JOIN users u ON al.user_id = u.id
                         WHERE al.user_id IS NOT NULL
                         GROUP BY al.user_id
                         ORDER BY action_count DESC
                         LIMIT :limit");
        
        $this->db->bind(':limit', $limit);
        
        return $this->db->resultSet();
    }

    /**
     * Get recent critical actions
     */
    public function getCriticalActions($limit = 20)
    {
        $criticalActions = ['draw_conducted', 'winner_selected', 'payment_processed', 
                           'campaign_deleted', 'user_deleted', 'configuration_changed'];
        
        // Use named parameters instead of positional
        $namedParams = [];
        foreach ($criticalActions as $index => $action) {
            $namedParams[] = ":action{$index}";
        }
        $placeholders = implode(',', $namedParams);
        
        $this->db->query("SELECT al.*, u.name as username 
                         FROM {$this->table} al
                         LEFT JOIN users u ON al.user_id = u.id
                         WHERE al.action IN ($placeholders)
                         ORDER BY al.created_at DESC
                         LIMIT {$limit}");
        
        foreach ($criticalActions as $index => $action) {
            $this->db->bind(":action{$index}", $action);
        }
        
        return $this->db->resultSet();
    }

    /**
     * Clean old logs (older than specified days)
     */
    public function cleanOldLogs($daysToKeep = 90)
    {
        $this->db->query("DELETE FROM {$this->table} 
                         WHERE created_at < DATE_SUB(NOW(), INTERVAL :days DAY)");
        $this->db->bind(':days', $daysToKeep);
        return $this->db->execute();
    }

    /**
     * Count today's logs
     */
    public function countToday()
    {
        $this->db->query("SELECT COUNT(*) as count FROM {$this->table} 
                         WHERE DATE(created_at) = CURDATE()");
        $result = $this->db->single();
        return $result->count ?? 0;
    }
}
