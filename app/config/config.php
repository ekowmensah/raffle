<?php

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'raffle');

// App Configuration
define('APP_NAME', 'Raffle System');
define('APP_VERSION', '1.0.0');

// URL Configuration - Dynamic based on environment
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';

// Determine base path
if (strpos($host, 'localhost') !== false || strpos($host, '127.0.0.1') !== false) {
    // Localhost environment
    $basePath = '/raffle/public';
} else {
    // Production environment (document root is public/)
    $basePath = '';
}

define('BASE_URL', $protocol . '://' . $host . $basePath);

// Session Configuration
define('SESSION_LIFETIME', 3600); // 1 hour

// Timezone
date_default_timezone_set('UTC');

// Error Reporting (set to 0 in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Load .env file if exists
$envFile = __DIR__ . '/../../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Skip comments
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        
        // Parse KEY=VALUE
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            
            // Set as environment variable if not already set
            if (!getenv($key)) {
                putenv("$key=$value");
                $_ENV[$key] = $value;
                $_SERVER[$key] = $value;
            }
        }
    }
}

// Hubtel Payment Gateway Configuration
define('HUBTEL_MODE', getenv('HUBTEL_MODE') ?: 'sandbox');
define('HUBTEL_CLIENT_ID', getenv('HUBTEL_CLIENT_ID') ?: '');
define('HUBTEL_CLIENT_SECRET', getenv('HUBTEL_CLIENT_SECRET') ?: '');
define('HUBTEL_MERCHANT_ACCOUNT', getenv('HUBTEL_MERCHANT_ACCOUNT') ?: '');
define('HUBTEL_IP_WHITELIST', getenv('HUBTEL_IP_WHITELIST') ?: '');
