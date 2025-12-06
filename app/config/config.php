<?php

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'raffle');

// App Configuration
define('APP_NAME', 'Raffle System');
define('APP_VERSION', '1.0.0');

// URL Configuration
define('BASE_URL', 'http://localhost/raffle/public');

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
