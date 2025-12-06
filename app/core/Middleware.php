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
                'view_stations', 'edit_station', 'view_programmes', 'edit_programmes',
                'view_campaigns', 'view_players', 'view_tickets', 'view_reports'
            ],
            'programme_manager' => [
                'view_programmes', 'view_campaigns', 'view_players', 'view_tickets'
            ],
            'finance' => [
                'view_payments', 'view_reports', 'view_wallets', 'manage_payouts'
            ],
            'auditor' => [
                'view_all', 'view_audit_logs', 'view_reports'
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
