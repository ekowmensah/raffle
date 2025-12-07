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
 * @param mixed $programme Programme object or ID
 */
function canAccessProgramme($programme)
{
    if (hasRole('super_admin')) {
        return true;
    }
    
    $user = $_SESSION['user'] ?? null;
    if (!$user) {
        return false;
    }
    
    // Handle both object and ID
    $programmeId = is_object($programme) ? $programme->id : $programme;
    $stationId = is_object($programme) ? $programme->station_id : null;
    
    if (hasRole('station_admin')) {
        // If we have station_id from object, check it
        if ($stationId) {
            return $stationId == $user->station_id;
        }
        // Otherwise, allow (will be checked elsewhere)
        return true;
    }
    
    if (hasRole('programme_manager')) {
        return $programmeId == $user->programme_id;
    }
    
    // Auditors can view
    if (hasRole('auditor')) {
        return true;
    }
    
    return false;
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

/**
 * Check if user can access a campaign
 */
function canAccessCampaign($campaign)
{
    if (hasRole('super_admin')) {
        return true;
    }
    
    $user = $_SESSION['user'] ?? null;
    if (!$user) {
        return false;
    }
    
    if (hasRole('station_admin')) {
        return $campaign->station_id == $user->station_id;
    }
    
    if (hasRole('programme_manager')) {
        return $campaign->programme_id == $user->programme_id;
    }
    
    // Auditors can view
    if (hasRole('auditor')) {
        return true;
    }
    
    return false;
}

/**
 * Check if user can access a draw
 */
function canAccessDraw($draw)
{
    if (hasRole('super_admin')) {
        return true;
    }
    
    // Get campaign for the draw
    $campaignModel = new \App\Models\Campaign();
    $campaign = $campaignModel->findById($draw->campaign_id);
    
    if (!$campaign) {
        return false;
    }
    
    return canAccessCampaign($campaign);
}

/**
 * Check if user can edit a resource
 */
function canEdit($resource, $type = 'campaign')
{
    // Auditors cannot edit
    if (hasRole('auditor')) {
        return false;
    }
    
    // Finance cannot edit campaigns/programmes
    if (hasRole('finance') && in_array($type, ['campaign', 'programme', 'draw'])) {
        return false;
    }
    
    // Check if user can access the resource
    switch ($type) {
        case 'campaign':
            return canAccessCampaign($resource) && can('edit_campaign');
        case 'programme':
            return canAccessProgramme($resource) && can('edit_programme');
        case 'draw':
            return canAccessDraw($resource) && can('conduct_draw');
        default:
            return false;
    }
}

/**
 * Check if user can delete a resource
 */
function canDelete($resource, $type = 'campaign')
{
    // Only super_admin and station_admin can delete
    if (!hasRole(['super_admin', 'station_admin'])) {
        return false;
    }
    
    // Check scope access
    switch ($type) {
        case 'campaign':
            return canAccessCampaign($resource);
        case 'programme':
            return canAccessProgramme($resource);
        default:
            return false;
    }
}
