<?php

/**
 * Raffle System - Front Controller
 * Entry point for all requests
 */

// Start session
session_start();

// Load configuration
require_once '../app/config/config.php';

// Load helper functions
require_once '../app/helpers/functions.php';

// Autoloader
spl_autoload_register(function ($class) {
    $class = str_replace('\\', '/', $class);
    $class = str_replace('App/', '../app/', $class);
    
    if (file_exists($class . '.php')) {
        require_once $class . '.php';
    }
});

// Initialize the application
$app = new App\Core\App();
