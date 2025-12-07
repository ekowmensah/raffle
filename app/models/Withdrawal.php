<?php

namespace App\Models;

use App\Core\Model;

class Withdrawal extends Model
{
    protected $table = 'withdrawals';

    public function getAllWithDetails()
    {
        $this->db->query("SELECT w.*, 
                         s.name as station_name,
                         u1.name as requested_by_name,
                         u2.name as approved_by_name
                         FROM {$this->table} w
                         LEFT JOIN stations s ON w.station_id = s.id
                         LEFT JOIN users u1 ON w.requested_by_user_id = u1.id
                         LEFT JOIN users u2 ON w.approved_by_user_id = u2.id
                         ORDER BY w.created_at DESC");
        return $this->db->resultSet();
    }

    public function getByStation($stationId)
    {
        $this->db->query("SELECT w.*, 
                         s.name as station_name,
                         u1.name as requested_by_name,
                         u2.name as approved_by_name
                         FROM {$this->table} w
                         LEFT JOIN stations s ON w.station_id = s.id
                         LEFT JOIN users u1 ON w.requested_by_user_id = u1.id
                         LEFT JOIN users u2 ON w.approved_by_user_id = u2.id
                         WHERE w.station_id = :station_id
                         ORDER BY w.created_at DESC");
        $this->db->bind(':station_id', $stationId);
        return $this->db->resultSet();
    }

    public function getPending()
    {
        $this->db->query("SELECT w.*, 
                         s.name as station_name,
                         u.name as requested_by_name
                         FROM {$this->table} w
                         LEFT JOIN stations s ON w.station_id = s.id
                         LEFT JOIN users u ON w.requested_by_user_id = u.id
                         WHERE w.status = 'pending'
                         ORDER BY w.created_at ASC");
        return $this->db->resultSet();
    }

    public function getByStatus($status)
    {
        $this->db->query("SELECT w.*, 
                         s.name as station_name,
                         u1.name as requested_by_name,
                         u2.name as approved_by_name
                         FROM {$this->table} w
                         LEFT JOIN stations s ON w.station_id = s.id
                         LEFT JOIN users u1 ON w.requested_by_user_id = u1.id
                         LEFT JOIN users u2 ON w.approved_by_user_id = u2.id
                         WHERE w.status = :status
                         ORDER BY w.created_at DESC");
        $this->db->bind(':status', $status);
        return $this->db->resultSet();
    }

    public function approve($id, $approvedByUserId)
    {
        $this->db->query("UPDATE {$this->table} 
                         SET status = 'approved',
                             approved_by_user_id = :approved_by,
                             approved_at = NOW()
                         WHERE id = :id");
        $this->db->bind(':id', $id);
        $this->db->bind(':approved_by', $approvedByUserId);
        return $this->db->execute();
    }

    public function reject($id, $approvedByUserId, $reason)
    {
        $this->db->query("UPDATE {$this->table} 
                         SET status = 'rejected',
                             approved_by_user_id = :approved_by,
                             approved_at = NOW(),
                             rejected_reason = :reason
                         WHERE id = :id");
        $this->db->bind(':id', $id);
        $this->db->bind(':approved_by', $approvedByUserId);
        $this->db->bind(':reason', $reason);
        return $this->db->execute();
    }

    public function complete($id, $transactionRef, $walletBalanceAfter)
    {
        $this->db->query("UPDATE {$this->table} 
                         SET status = 'completed',
                             completed_at = NOW(),
                             transaction_reference = :ref,
                             wallet_balance_after = :balance
                         WHERE id = :id");
        $this->db->bind(':id', $id);
        $this->db->bind(':ref', $transactionRef);
        $this->db->bind(':balance', $walletBalanceAfter);
        return $this->db->execute();
    }

    public function getTotalByStation($stationId, $status = null)
    {
        $sql = "SELECT COALESCE(SUM(amount), 0) as total
                FROM {$this->table}
                WHERE station_id = :station_id";
        
        if ($status) {
            $sql .= " AND status = :status";
        }
        
        $this->db->query($sql);
        $this->db->bind(':station_id', $stationId);
        
        if ($status) {
            $this->db->bind(':status', $status);
        }
        
        $result = $this->db->single();
        return $result->total ?? 0;
    }

    public function countPending()
    {
        $this->db->query("SELECT COUNT(*) as count FROM {$this->table} WHERE status = 'pending'");
        $result = $this->db->single();
        return $result->count ?? 0;
    }
}
