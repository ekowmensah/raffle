<?php

namespace App\Core;

class Middleware
{
    /**
     * Check if user has required role
     */
    public static function requireRole($role)
    {
        if (!isset($_SESSION['user'])) {
            flash('error', 'Please login to access this page');
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }

        $userRole = $_SESSION['user']->role_name ?? '';

        if (is_array($role)) {
            if (!in_array($userRole, $role)) {
                flash('error', 'You do not have permission to access this page');
                header('Location: ' . BASE_URL . '/home');
                exit;
            }
        } else {
            if ($userRole !== $role) {
                flash('error', 'You do not have permission to access this page');
                header('Location: ' . BASE_URL . '/home');
                exit;
            }
        }

        return true;
    }

    /**
     * Check if user has any of the required permissions
     */
    public static function can($permission)
    {
        if (!isset($_SESSION['user'])) {
            return false;
        }

        $userRole = $_SESSION['user']->role_name ?? '';

        // Define role permissions
        $permissions = [
            'super_admin' => ['*'], // All permissions
            
            'station_admin' => [
                'view_station', 'edit_station',
                'manage_programmes', 'create_programme', 'edit_programme', 'delete_programme',
                'manage_campaigns', 'create_campaign', 'edit_campaign', 'delete_campaign',
                'view_station_users', 'create_station_user', 'edit_station_user',
                'view_station_reports', 'view_station_analytics', 'view_station_finances',
                'view_players', 'view_tickets', 'view_draws'
            ],
            
            'programme_manager' => [
                'view_programme', 'edit_programme',
                'manage_campaigns', 'create_campaign', 'edit_campaign',
                'conduct_draw', 'view_draws', 'view_winners',
                'view_tickets', 'view_players',
                'view_programme_analytics', 'view_programme_finances'
            ],
            
            'finance' => [
                'process_payment', 'view_payments', 'edit_payment',
                'manage_payouts', 'view_wallets', 'reconciliation',
                'view_financial_reports', 'export_financial_data'
            ],
            
            'auditor' => [
                'view_audit_logs', 'view_security_logs',
                'view_all_reports', 'export_data', 'view_analytics'
            ]
        ];

        // Super admin has all permissions
        if ($userRole === 'super_admin') {
            return true;
        }

        // Check if role has permission
        if (isset($permissions[$userRole])) {
            return in_array($permission, $permissions[$userRole]);
        }

        return false;
    }

    /**
     * Check if user owns the resource
     */
    public static function ownsResource($resourceUserId)
    {
        if (!isset($_SESSION['user_id'])) {
            return false;
        }

        return $_SESSION['user_id'] == $resourceUserId;
    }

    /**
     * Check if user belongs to the station
     */
    public static function belongsToStation($stationId)
    {
        if (!isset($_SESSION['user'])) {
            return false;
        }

        $userRole = $_SESSION['user']->role_name ?? '';
        
        // Super admin can access all stations
        if ($userRole === 'super_admin') {
            return true;
        }

        $userStationId = $_SESSION['user']->station_id ?? null;
        return $userStationId == $stationId;
    }

    /**
     * Check if user belongs to the programme
     */
    public static function belongsToProgramme($programmeId)
    {
        if (!isset($_SESSION['user'])) {
            return false;
        }

        $userRole = $_SESSION['user']->role_name ?? '';
        
        // Super admin can access all programmes
        if ($userRole === 'super_admin') {
            return true;
        }

        // Station admin can access all programmes in their station
        if ($userRole === 'station_admin') {
            // Would need to check if programme belongs to their station
            // For now, return true if they have a station_id
            return isset($_SESSION['user']->station_id);
        }

        // Programme manager can only access their own programme
        $userProgrammeId = $_SESSION['user']->programme_id ?? null;
        return $userProgrammeId == $programmeId;
    }

    /**
     * Verify CSRF token
     */
    public static function verifyCsrf()
    {
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            flash('error', 'Invalid security token. Please try again.');
            return false;
        }
        return true;
    }
}
