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
    // Convert namespace to file path
    $class = str_replace('\\', '/', $class);
    $class = str_replace('App/', '../app/', $class);
    
    // Convert to lowercase for directory names (case-sensitive on Linux)
    $parts = explode('/', $class);
    if (count($parts) > 2) {
        // Keep the class name as-is, but lowercase the directory names
        $className = array_pop($parts);
        $parts = array_map('strtolower', $parts);
        $parts[] = $className;
        $class = implode('/', $parts);
    }
    
    $file = $class . '.php';
    
    if (file_exists($file)) {
        require_once $file;
    }
});

// Initialize the application
$app = new App\Core\App();
