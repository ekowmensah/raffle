<?php

namespace App\Models;

use App\Core\Model;

class StationWalletTransaction extends Model
{
    protected $table = 'station_wallet_transactions';

    public function getByWallet($walletId, $limit = 50)
    {
        $this->db->query("SELECT swt.*, c.name as campaign_name
                         FROM {$this->table} swt
                         LEFT JOIN raffle_campaigns c ON swt.related_campaign_id = c.id
                         WHERE swt.station_wallet_id = :wallet_id
                         ORDER BY swt.created_at DESC
                         LIMIT :limit");
        $this->db->bind(':wallet_id', $walletId);
        $this->db->bind(':limit', $limit);
        return $this->db->resultSet();
    }

    public function recordCredit($walletId, $amount, $campaignId = null, $paymentId = null, $description = null)
    {
        return $this->create([
            'station_wallet_id' => $walletId,
            'related_campaign_id' => $campaignId,
            'related_payment_id' => $paymentId,
            'transaction_type' => 'credit',
            'amount' => $amount,
            'description' => $description ?? 'Commission from ticket sale'
        ]);
    }

    public function recordDebit($walletId, $amount, $campaignId = null, $paymentId = null, $description = null)
    {
        return $this->create([
            'station_wallet_id' => $walletId,
            'related_campaign_id' => $campaignId,
            'related_payment_id' => $paymentId,
            'transaction_type' => 'debit',
            'amount' => $amount,
            'description' => $description ?? 'Withdrawal'
        ]);
    }

    public function getByWalletFiltered($walletId, $type = null, $startDate = null, $endDate = null)
    {
        $sql = "SELECT swt.*, c.name as campaign_name
                FROM {$this->table} swt
                LEFT JOIN raffle_campaigns c ON swt.related_campaign_id = c.id
                WHERE swt.station_wallet_id = :wallet_id";
        
        if ($type) {
            $sql .= " AND swt.transaction_type = :type";
        }
        
        if ($startDate && $endDate) {
            $sql .= " AND DATE(swt.created_at) BETWEEN :start_date AND :end_date";
        }
        
        $sql .= " ORDER BY swt.created_at DESC";
        
        $this->db->query($sql);
        $this->db->bind(':wallet_id', $walletId);
        
        if ($type) {
            $this->db->bind(':type', $type);
        }
        
        if ($startDate && $endDate) {
            $this->db->bind(':start_date', $startDate);
            $this->db->bind(':end_date', $endDate);
        }
        
        return $this->db->resultSet();
    }

    public function getSummary($walletId)
    {
        $this->db->query("SELECT 
                         SUM(CASE WHEN transaction_type = 'credit' THEN amount ELSE 0 END) as total_credits,
                         SUM(CASE WHEN transaction_type = 'debit' THEN amount ELSE 0 END) as total_debits,
                         COUNT(*) as total_transactions
                         FROM {$this->table}
                         WHERE station_wallet_id = :wallet_id");
        $this->db->bind(':wallet_id', $walletId);
        return $this->db->single();
    }

    public function getSummaryByDateRange($walletId, $startDate, $endDate)
    {
        $this->db->query("SELECT 
                         SUM(CASE WHEN transaction_type = 'credit' THEN amount ELSE 0 END) as total_credits,
                         SUM(CASE WHEN transaction_type = 'debit' THEN amount ELSE 0 END) as total_debits,
                         COUNT(*) as total_transactions
                         FROM {$this->table}
                         WHERE station_wallet_id = :wallet_id
                         AND DATE(created_at) BETWEEN :start_date AND :end_date");
        $this->db->bind(':wallet_id', $walletId);
        $this->db->bind(':start_date', $startDate);
        $this->db->bind(':end_date', $endDate);
        return $this->db->single();
    }
}
