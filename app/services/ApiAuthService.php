<?php

namespace App\Services;

class ApiAuthService
{
    private $db;
    private $secretKey;
    
    public function __construct()
    {
        $this->db = new \App\Core\Database();
        $this->secretKey = getenv('JWT_SECRET') ?: 'your-secret-key-change-in-production';
    }
    
    /**
     * Generate JWT token for player
     */
    public function generateToken($playerId, $phoneNumber)
    {
        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
        $payload = json_encode([
            'player_id' => $playerId,
            'phone' => $phoneNumber,
            'iat' => time(),
            'exp' => time() + (86400 * 30) // 30 days
        ]);
        
        $base64UrlHeader = $this->base64UrlEncode($header);
        $base64UrlPayload = $this->base64UrlEncode($payload);
        
        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $this->secretKey, true);
        $base64UrlSignature = $this->base64UrlEncode($signature);
        
        return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
    }
    
    /**
     * Verify JWT token
     */
    public function verifyToken($token)
    {
        if (!$token) {
            return false;
        }
        
        $tokenParts = explode('.', $token);
        
        if (count($tokenParts) != 3) {
            return false;
        }
        
        $header = base64_decode($tokenParts[0]);
        $payload = base64_decode($tokenParts[1]);
        $signatureProvided = $tokenParts[2];
        
        // Verify signature
        $base64UrlHeader = $this->base64UrlEncode($header);
        $base64UrlPayload = $this->base64UrlEncode($payload);
        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $this->secretKey, true);
        $base64UrlSignature = $this->base64UrlEncode($signature);
        
        if ($base64UrlSignature !== $signatureProvided) {
            return false;
        }
        
        // Verify expiration
        $payloadData = json_decode($payload, true);
        
        if (isset($payloadData['exp']) && $payloadData['exp'] < time()) {
            return false;
        }
        
        return $payloadData;
    }
    
    /**
     * Get player from token
     */
    public function getPlayerFromToken($token)
    {
        $payload = $this->verifyToken($token);
        
        if (!$payload) {
            return null;
        }
        
        $this->db->query("SELECT * FROM players WHERE id = :id");
        $this->db->bind(':id', $payload['player_id']);
        
        return $this->db->single();
    }
    
    /**
     * Generate API key for external integrations
     */
    public function generateApiKey($name, $permissions = [])
    {
        $apiKey = 'sk_' . bin2hex(random_bytes(32));
        
        $this->db->query("INSERT INTO api_keys (api_key, name, permissions, is_active, created_at) 
                         VALUES (:api_key, :name, :permissions, 1, NOW())");
        $this->db->bind(':api_key', hash('sha256', $apiKey));
        $this->db->bind(':name', $name);
        $this->db->bind(':permissions', json_encode($permissions));
        $this->db->execute();
        
        return $apiKey;
    }
    
    /**
     * Verify API key
     */
    public function verifyApiKey($apiKey)
    {
        $hashedKey = hash('sha256', $apiKey);
        
        $this->db->query("SELECT * FROM api_keys WHERE api_key = :api_key AND is_active = 1");
        $this->db->bind(':api_key', $hashedKey);
        
        return $this->db->single();
    }
    
    /**
     * Base64 URL encode
     */
    private function base64UrlEncode($text)
    {
        return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($text));
    }
    
    /**
     * Send OTP for phone verification
     */
    public function sendOtp($phoneNumber)
    {
        $otp = sprintf("%06d", mt_rand(1, 999999));
        $expiresAt = date('Y-m-d H:i:s', time() + 600); // 10 minutes
        
        // Store OTP
        $this->db->query("INSERT INTO otp_verifications (phone_number, otp_code, expires_at, created_at) 
                         VALUES (:phone, :otp, :expires, NOW())
                         ON DUPLICATE KEY UPDATE otp_code = :otp, expires_at = :expires, created_at = NOW()");
        $this->db->bind(':phone', $phoneNumber);
        $this->db->bind(':otp', $otp);
        $this->db->bind(':expires', $expiresAt);
        $this->db->execute();
        
        // Send SMS
        $smsService = new SmsGatewayService();
        $message = "Your Raffle verification code is: {$otp}\nValid for 10 minutes.";
        $smsService->send($phoneNumber, $message);
        
        return true;
    }
    
    /**
     * Verify OTP
     */
    public function verifyOtp($phoneNumber, $otp)
    {
        $this->db->query("SELECT * FROM otp_verifications 
                         WHERE phone_number = :phone 
                         AND otp_code = :otp 
                         AND expires_at > NOW()
                         AND is_verified = 0");
        $this->db->bind(':phone', $phoneNumber);
        $this->db->bind(':otp', $otp);
        $record = $this->db->single();
        
        if (!$record) {
            return false;
        }
        
        // Mark as verified
        $this->db->query("UPDATE otp_verifications SET is_verified = 1 WHERE id = :id");
        $this->db->bind(':id', $record->id);
        $this->db->execute();
        
        return true;
    }
}
