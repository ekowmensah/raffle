<?php

namespace App\Models;

use App\Core\Model;

class PromoCode extends Model
{
    protected $table = 'promo_codes';

    public function findByCode($code)
    {
        $this->db->query("SELECT * FROM {$this->table} WHERE code = :code");
        $this->db->bind(':code', $code);
        return $this->db->single();
    }

    public function getActive()
    {
        $this->db->query("SELECT pc.*, u.name as user_name, s.name as station_name, p.name as programme_name
                         FROM {$this->table} pc
                         LEFT JOIN users u ON pc.user_id = u.id
                         LEFT JOIN stations s ON pc.station_id = s.id
                         LEFT JOIN programmes p ON pc.programme_id = p.id
                         WHERE pc.is_active = 1
                         ORDER BY pc.created_at DESC");
        return $this->db->resultSet();
    }

    public function validatePromoCode($code, $stationId = null, $programmeId = null)
    {
        $promo = $this->findByCode($code);
        
        if (!$promo) {
            return ['valid' => false, 'message' => 'Invalid promo code'];
        }
        
        if (!$promo->is_active) {
            return ['valid' => false, 'message' => 'Promo code is inactive'];
        }
        
        // Check station restriction
        if ($promo->station_id && $promo->station_id != $stationId) {
            return ['valid' => false, 'message' => 'Promo code not valid for this station'];
        }
        
        // Check programme restriction
        if ($promo->programme_id && $promo->programme_id != $programmeId) {
            return ['valid' => false, 'message' => 'Promo code not valid for this programme'];
        }
        
        return [
            'valid' => true,
            'promo' => $promo,
            'extra_commission' => $promo->extra_commission_percent
        ];
    }

    public function getUsageStats($promoCodeId)
    {
        // Return basic stats - usage tracking via payments table
        $this->db->query("SELECT 
                         COUNT(p.id) as usage_count,
                         COALESCE(SUM(p.amount), 0) as total_revenue,
                         0 as total_commission,
                         COUNT(DISTINCT p.player_id) as unique_users
                         FROM payments p
                         WHERE p.promo_code_id = :promo_code_id
                         AND p.status = 'success'");
        $this->db->bind(':promo_code_id', $promoCodeId);
        return $this->db->single();
    }
}
