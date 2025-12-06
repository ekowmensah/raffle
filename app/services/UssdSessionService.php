<?php

namespace App\Services;

class UssdSessionService
{
    private $db;
    
    public function __construct()
    {
        $this->db = new \App\Core\Database();
    }
    
    /**
     * Create or retrieve USSD session
     */
    public function getOrCreateSession($sessionId, $phoneNumber)
    {
        // Check if session exists (active or inactive)
        $this->db->query("SELECT * FROM ussd_sessions WHERE session_id = :session_id");
        $this->db->bind(':session_id', $sessionId);
        $session = $this->db->single();
        
        if ($session) {
            // If session exists but is inactive, reactivate it
            if (!$session->is_active) {
                $this->db->query("UPDATE ussd_sessions 
                                 SET is_active = 1, 
                                     current_step = 'main_menu',
                                     session_data = '{}',
                                     updated_at = NOW()
                                 WHERE session_id = :session_id");
                $this->db->bind(':session_id', $sessionId);
                $this->db->execute();
                
                return $this->getSession($sessionId);
            }
            return $session;
        }
        
        // Create new session
        $this->db->query("INSERT INTO ussd_sessions (session_id, phone_number, current_step, session_data, is_active, created_at, updated_at) 
                         VALUES (:session_id, :phone_number, 'main_menu', '{}', 1, NOW(), NOW())");
        $this->db->bind(':session_id', $sessionId);
        $this->db->bind(':phone_number', $phoneNumber);
        $this->db->execute();
        
        return $this->getSession($sessionId);
    }
    
    /**
     * Get session by ID
     */
    public function getSession($sessionId)
    {
        $this->db->query("SELECT * FROM ussd_sessions WHERE session_id = :session_id");
        $this->db->bind(':session_id', $sessionId);
        return $this->db->single();
    }
    
    /**
     * Update session step and data
     */
    public function updateSession($sessionId, $step, $data = [])
    {
        $session = $this->getSession($sessionId);
        
        if (!$session) {
            return false;
        }
        
        // Merge existing data with new data
        $existingData = json_decode($session->session_data, true) ?: [];
        $newData = array_merge($existingData, $data);
        
        $this->db->query("UPDATE ussd_sessions 
                         SET current_step = :step, 
                             session_data = :data,
                             updated_at = NOW()
                         WHERE session_id = :session_id");
        $this->db->bind(':session_id', $sessionId);
        $this->db->bind(':step', $step);
        $this->db->bind(':data', json_encode($newData));
        
        return $this->db->execute();
    }
    
    /**
     * Get session data
     */
    public function getSessionData($sessionId)
    {
        $session = $this->getSession($sessionId);
        
        if (!$session) {
            return [];
        }
        
        return json_decode($session->session_data, true) ?: [];
    }
    
    /**
     * Set session data value
     */
    public function setSessionValue($sessionId, $key, $value)
    {
        $data = $this->getSessionData($sessionId);
        $data[$key] = $value;
        
        $this->db->query("UPDATE ussd_sessions 
                         SET session_data = :data,
                             updated_at = NOW()
                         WHERE session_id = :session_id");
        $this->db->bind(':session_id', $sessionId);
        $this->db->bind(':data', json_encode($data));
        
        return $this->db->execute();
    }
    
    /**
     * Close session
     */
    public function closeSession($sessionId)
    {
        $this->db->query("UPDATE ussd_sessions 
                         SET is_active = 0,
                             updated_at = NOW()
                         WHERE session_id = :session_id");
        $this->db->bind(':session_id', $sessionId);
        
        return $this->db->execute();
    }
    
    /**
     * Clean up old sessions (older than 24 hours)
     */
    public function cleanupOldSessions()
    {
        $this->db->query("UPDATE ussd_sessions 
                         SET is_active = 0 
                         WHERE updated_at < DATE_SUB(NOW(), INTERVAL 24 HOUR)");
        
        return $this->db->execute();
    }
    
    /**
     * Get session history for a phone number
     */
    public function getSessionHistory($phoneNumber, $limit = 10)
    {
        $this->db->query("SELECT * FROM ussd_sessions 
                         WHERE phone_number = :phone_number 
                         ORDER BY created_at DESC 
                         LIMIT :limit");
        $this->db->bind(':phone_number', $phoneNumber);
        $this->db->bind(':limit', $limit);
        
        return $this->db->resultSet();
    }
}
