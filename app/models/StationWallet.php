<?php

namespace App\Models;

use App\Core\Model;

class StationWallet extends Model
{
    protected $table = 'station_wallets';

    public function getByStation($stationId)
    {
        $this->db->query("SELECT * FROM {$this->table} WHERE station_id = :station_id");
        $this->db->bind(':station_id', $stationId);
        return $this->db->single();
    }

    public function getOrCreate($stationId)
    {
        $wallet = $this->getByStation($stationId);
        
        if (!$wallet) {
            $walletId = $this->create([
                'station_id' => $stationId,
                'balance' => 0.00
            ]);
            $wallet = $this->findById($walletId);
        }
        
        return $wallet;
    }

    public function credit($walletId, $amount)
    {
        $this->db->query("UPDATE {$this->table} 
                         SET balance = balance + :amount 
                         WHERE id = :wallet_id");
        $this->db->bind(':amount', $amount);
        $this->db->bind(':wallet_id', $walletId);
        return $this->db->execute();
    }

    public function debit($walletId, $amount)
    {
        $this->db->query("UPDATE {$this->table} 
                         SET balance = balance - :amount 
                         WHERE id = :wallet_id 
                         AND balance >= :amount");
        $this->db->bind(':amount', $amount);
        $this->db->bind(':wallet_id', $walletId);
        return $this->db->execute();
    }

    public function getAllWithStations()
    {
        $this->db->query("SELECT w.*, 
                         s.name as station_name,
                         s.code as station_code,
                         (SELECT SUM(amount) FROM station_wallet_transactions 
                          WHERE station_wallet_id = w.id AND transaction_type = 'credit') as total_credits,
                         (SELECT SUM(amount) FROM station_wallet_transactions 
                          WHERE station_wallet_id = w.id AND transaction_type = 'debit') as total_debits
                         FROM {$this->table} w
                         LEFT JOIN stations s ON w.station_id = s.id
                         ORDER BY s.name");
        return $this->db->resultSet();
    }

    public function getWithStation($walletId)
    {
        $this->db->query("SELECT w.*, 
                         s.name as station_name,
                         s.code as station_code,
                         s.is_active as station_active
                         FROM {$this->table} w
                         LEFT JOIN stations s ON w.station_id = s.id
                         WHERE w.id = :wallet_id");
        $this->db->bind(':wallet_id', $walletId);
        return $this->db->single();
    }
}
