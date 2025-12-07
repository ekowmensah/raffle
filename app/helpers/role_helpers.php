<?php

/**
 * Role-based helper functions
 * Note: hasRole() and can() are defined in functions.php
 */

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
        // Check if campaign is linked to user's programme via campaign_programme_access
        $accessModel = new \App\Models\CampaignProgrammeAccess();
        $access = $accessModel->findByCampaignAndProgramme($campaign->id, $user->programme_id);
        return $access !== null;
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
    $user = $_SESSION['user'] ?? null;
    $userRole = $user->role_name ?? 'none';
    
    error_log("canAccessDraw called - Draw ID: {$draw->id}, Campaign ID: {$draw->campaign_id}, User Role: {$userRole}");
    
    if (hasRole('super_admin')) {
        error_log("Access granted - Super Admin");
        return true;
    }
    
    if (!$user) {
        error_log("Access denied - No user session");
        return false;
    }
    
    // Get campaign for the draw
    $campaignModel = new \App\Models\Campaign();
    $campaign = $campaignModel->findById($draw->campaign_id);
    
    if (!$campaign) {
        error_log("Access denied - Campaign not found");
        return false;
    }
    
    // Station admin check
    if (hasRole('station_admin')) {
        $hasAccess = $campaign->station_id == $user->station_id;
        error_log("Station Admin check - Campaign Station: {$campaign->station_id}, User Station: {$user->station_id}, Access: " . ($hasAccess ? 'YES' : 'NO'));
        return $hasAccess;
    }
    
    // Programme manager check
    if (hasRole('programme_manager')) {
        error_log("Programme Manager check - Campaign ID: {$campaign->id}, User Programme ID: {$user->programme_id}");
        
        // Programme managers can only access campaigns linked to their programme
        // Station-wide campaigns (without programme) should NOT be accessible
        $accessModel = new \App\Models\CampaignProgrammeAccess();
        $access = $accessModel->findByCampaignAndProgramme($campaign->id, $user->programme_id);
        
        $hasAccess = $access !== null;
        error_log("Programme Manager access result: " . ($hasAccess ? 'YES' : 'NO'));
        
        // Only return true if there's an explicit link in campaign_programme_access
        return $hasAccess;
    }
    
    // Auditors can view
    if (hasRole('auditor')) {
        error_log("Access granted - Auditor");
        return true;
    }
    
    error_log("Access denied - No matching role");
    return false;
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
