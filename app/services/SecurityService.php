<?php

namespace App\Services;

use App\Models\SecurityLog;

class SecurityService
{
    private $securityLog;
    private $maxAttempts = 5;
    private $lockoutMinutes = 15;
    private $blockDuration = 60; // minutes

    public function __construct()
    {
        $this->securityLog = new SecurityLog();
    }

    /**
     * Check if login should be allowed
     */
    public function canAttemptLogin($email, $ipAddress)
    {
        // Check if IP is blocked
        if ($this->securityLog->isBlocked($ipAddress)) {
            return [
                'allowed' => false,
                'reason' => 'IP address is temporarily blocked due to suspicious activity'
            ];
        }

        // Check failed attempts by email
        $emailAttempts = $this->securityLog->getFailedAttempts($email, null, $this->lockoutMinutes);
        
        if ($emailAttempts >= $this->maxAttempts) {
            return [
                'allowed' => false,
                'reason' => "Too many failed login attempts. Please try again in {$this->lockoutMinutes} minutes."
            ];
        }

        // Check failed attempts by IP
        $ipAttempts = $this->securityLog->getFailedAttempts(null, $ipAddress, $this->lockoutMinutes);
        
        if ($ipAttempts >= ($this->maxAttempts * 2)) {
            // Block IP if too many attempts from same IP
            $this->securityLog->blockIP($ipAddress, 'Too many failed login attempts', $this->blockDuration);
            
            return [
                'allowed' => false,
                'reason' => 'IP address has been temporarily blocked due to multiple failed login attempts'
            ];
        }

        return [
            'allowed' => true,
            'remaining_attempts' => $this->maxAttempts - $emailAttempts
        ];
    }

    /**
     * Record failed login attempt
     */
    public function recordFailedLogin($email, $ipAddress, $userAgent)
    {
        return $this->securityLog->logFailedLogin($email, $ipAddress, $userAgent);
    }

    /**
     * Validate password strength
     */
    public function validatePasswordStrength($password)
    {
        $errors = [];

        if (strlen($password) < 8) {
            $errors[] = 'Password must be at least 8 characters long';
        }

        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = 'Password must contain at least one uppercase letter';
        }

        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = 'Password must contain at least one lowercase letter';
        }

        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = 'Password must contain at least one number';
        }

        if (!preg_match('/[^A-Za-z0-9]/', $password)) {
            $errors[] = 'Password must contain at least one special character';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }

    /**
     * Check for common/weak passwords
     */
    public function isCommonPassword($password)
    {
        $commonPasswords = [
            'password', 'password123', '12345678', 'qwerty', 'abc123',
            'monkey', '1234567', 'letmein', 'trustno1', 'dragon',
            'baseball', 'iloveyou', 'master', 'sunshine', 'ashley',
            'bailey', 'passw0rd', 'shadow', '123123', '654321'
        ];

        return in_array(strtolower($password), $commonPasswords);
    }

    /**
     * Rate limiting check
     */
    public function checkRateLimit($identifier, $maxRequests = 60, $timeWindow = 60)
    {
        $key = 'rate_limit_' . md5($identifier);
        
        if (!isset($_SESSION[$key])) {
            $_SESSION[$key] = [
                'count' => 1,
                'reset_time' => time() + $timeWindow
            ];
            return ['allowed' => true, 'remaining' => $maxRequests - 1];
        }

        $data = $_SESSION[$key];

        // Reset if time window expired
        if (time() > $data['reset_time']) {
            $_SESSION[$key] = [
                'count' => 1,
                'reset_time' => time() + $timeWindow
            ];
            return ['allowed' => true, 'remaining' => $maxRequests - 1];
        }

        // Increment counter
        $data['count']++;
        $_SESSION[$key] = $data;

        if ($data['count'] > $maxRequests) {
            return [
                'allowed' => false,
                'retry_after' => $data['reset_time'] - time()
            ];
        }

        return [
            'allowed' => true,
            'remaining' => $maxRequests - $data['count']
        ];
    }

    /**
     * Detect suspicious patterns
     */
    public function detectSuspiciousActivity($userId, $action)
    {
        // Check for rapid successive actions
        $recentActions = $this->getRecentUserActions($userId, 1); // Last 1 minute
        
        if (count($recentActions) > 20) {
            $this->securityLog->logSuspiciousActivity(
                'rapid_actions',
                ['user_id' => $userId, 'action' => $action, 'count' => count($recentActions)],
                $userId
            );
            return true;
        }

        return false;
    }

    /**
     * Get recent user actions (placeholder - would integrate with audit log)
     */
    private function getRecentUserActions($userId, $minutes)
    {
        // This would query the audit log
        return [];
    }

    /**
     * Generate secure session token
     */
    public function generateSecureToken($length = 32)
    {
        return bin2hex(random_bytes($length));
    }

    /**
     * Validate session security
     */
    public function validateSession()
    {
        // Check if session has required security markers
        if (!isset($_SESSION['created_at'])) {
            $_SESSION['created_at'] = time();
        }

        if (!isset($_SESSION['last_activity'])) {
            $_SESSION['last_activity'] = time();
        }

        // Session timeout (30 minutes of inactivity)
        $timeout = 1800; // 30 minutes
        if (time() - $_SESSION['last_activity'] > $timeout) {
            return [
                'valid' => false,
                'reason' => 'Session expired due to inactivity'
            ];
        }

        // Update last activity
        $_SESSION['last_activity'] = time();

        // Session lifetime (24 hours max)
        $maxLifetime = 86400; // 24 hours
        if (time() - $_SESSION['created_at'] > $maxLifetime) {
            return [
                'valid' => false,
                'reason' => 'Session expired. Please login again.'
            ];
        }

        // Check for session hijacking (IP change)
        if (isset($_SESSION['ip_address'])) {
            $currentIP = $_SERVER['REMOTE_ADDR'] ?? '';
            if ($_SESSION['ip_address'] !== $currentIP) {
                return [
                    'valid' => false,
                    'reason' => 'Session security violation detected'
                ];
            }
        } else {
            $_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR'] ?? '';
        }

        return ['valid' => true];
    }

    /**
     * Sanitize input to prevent XSS
     */
    public function sanitizeInput($input)
    {
        if (is_array($input)) {
            return array_map([$this, 'sanitizeInput'], $input);
        }

        return htmlspecialchars(strip_tags($input), ENT_QUOTES, 'UTF-8');
    }
}
