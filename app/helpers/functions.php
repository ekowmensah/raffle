<?php

/**
 * Helper Functions
 */

function url($path = '')
{
    return BASE_URL . '/' . ltrim($path, '/');
}

function asset($path)
{
    return BASE_URL . '/assets/' . ltrim($path, '/');
}

function vendor($path)
{
    return BASE_URL . '/assets/vendor/' . ltrim($path, '/');
}

function redirect($url)
{
    header('Location: ' . url($url));
    exit;
}

function old($key, $default = '')
{
    return $_SESSION['old'][$key] ?? $default;
}

function flash($key, $message = null)
{
    if ($message === null) {
        $msg = $_SESSION['flash'][$key] ?? null;
        unset($_SESSION['flash'][$key]);
        return $msg;
    }
    $_SESSION['flash'][$key] = $message;
}

function csrf_token()
{
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field()
{
    return '<input type="hidden" name="csrf_token" value="' . csrf_token() . '">';
}

function verify_csrf()
{
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('CSRF token validation failed');
    }
}

function sanitize($data)
{
    if (is_array($data)) {
        return array_map('sanitize', $data);
    }
    // Trim whitespace
    $data = trim($data);
    
    // Remove actual HTML tags (more precise than strip_tags)
    // This regex removes <tag> and </tag> but preserves quotes and other characters
    $data = preg_replace('/<[^>]*>/', '', $data);
    
    // Remove any remaining < or > characters that might be used for XSS
    $data = str_replace(['<', '>'], '', $data);
    
    return $data;
}

function formatDate($date, $format = 'M d, Y h:i A')
{
    if (empty($date)) {
        return 'N/A';
    }
    return date($format, strtotime($date));
}

function formatMoney($amount, $currency = 'GHS')
{
    return $currency . ' ' . number_format($amount, 2);
}

function dd($data)
{
    echo '<pre>';
    var_dump($data);
    echo '</pre>';
    die();
}

function isActive($path)
{
    $currentPath = $_GET['url'] ?? '';
    return strpos($currentPath, $path) === 0 ? 'active' : '';
}

function hasRole($role)
{
    if (!isset($_SESSION['user'])) {
        return false;
    }
    
    $userRole = $_SESSION['user']->role_name ?? '';
    
    // Support array of roles
    if (is_array($role)) {
        return in_array($userRole, $role);
    }
    
    return $userRole === $role;
}

function can($permission)
{
    return \App\Core\Middleware::can($permission);
}

function requireRole($role)
{
    require_once '../app/core/Middleware.php';
    return \App\Core\Middleware::requireRole($role);
}
