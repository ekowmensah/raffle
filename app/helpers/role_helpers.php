<?php

/**
 * Role-based helper functions
 */

/**
 * Check if user has specific role
 */
function hasRole($role)
{
    if (!isset($_SESSION['user'])) {
        return false;
    }
    
    $userRole = $_SESSION['user']->role_name ?? '';
    
    if (is_array($role)) {
        return in_array($userRole, $role);
    }
    
    return $userRole === $role;
}

/**
 * Check if user has permission
 */
function can($permission)
{
    return \App\Core\Middleware::can($permission);
}

/**
 * Get user's station ID
 */
function getUserStationId()
{
    return $_SESSION['user']->station_id ?? null;
}

/**
 * Get user's programme ID
 */
function getUserProgrammeId()
{
    return $_SESSION['user']->programme_id ?? null;
}

/**
 * Check if user can access station
 */
function canAccessStation($stationId)
{
    if (hasRole('super_admin')) {
        return true;
    }
    
    return getUserStationId() == $stationId;
}

/**
 * Check if user can access programme
 */
function canAccessProgramme($programmeId)
{
    if (hasRole('super_admin')) {
        return true;
    }
    
    if (hasRole('station_manager')) {
        // Station manager can access all programmes in their station
        // Need to check if programme belongs to their station
        return true; // Implement station check
    }
    
    return getUserProgrammeId() == $programmeId;
}

/**
 * Get role display name
 */
function getRoleDisplayName($role)
{
    $names = [
        'super_admin' => 'Super Administrator',
        'station_manager' => 'Station Manager',
        'programme_presenter' => 'Programme Presenter',
        'programme_manager' => 'Programme Manager',
        'finance' => 'Finance Officer',
        'auditor' => 'Auditor',
        'cashier' => 'Cashier'
    ];
    
    return $names[$role] ?? ucwords(str_replace('_', ' ', $role));
}

/**
 * Get role badge class
 */
function getRoleBadgeClass($role)
{
    $classes = [
        'super_admin' => 'badge-danger',
        'station_manager' => 'badge-primary',
        'programme_presenter' => 'badge-info',
        'programme_manager' => 'badge-success',
        'finance' => 'badge-warning',
        'auditor' => 'badge-secondary'
    ];
    
    return $classes[$role] ?? 'badge-default';
}

/**
 * Check if current page is active
 */
function isActive($path)
{
    $currentPath = $_SERVER['REQUEST_URI'] ?? '';
    $currentPath = str_replace(BASE_URL, '', $currentPath);
    $currentPath = trim($currentPath, '/');
    
    if (strpos($currentPath, $path) === 0) {
        return 'active';
    }
    
    return '';
}

/**
 * Get pending draws count for presenter
 */
function getPendingDrawsCount()
{
    if (!hasRole(['programme_presenter', 'programme_manager'])) {
        return 0;
    }
    
    $programmeId = getUserProgrammeId();
    if (!$programmeId) {
        return 0;
    }
    
    // This would query the database
    // For now, return 0
    return 0;
}
