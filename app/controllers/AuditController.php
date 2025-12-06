<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\AuditLog;
use App\Models\User;

class AuditController extends Controller
{
    private $auditLog;
    private $userModel;

    public function __construct()
    {
        $this->auditLog = $this->model('AuditLog');
        $this->userModel = $this->model('User');
    }

    /**
     * Display audit logs with filters
     */
    public function index()
    {
        $this->requireAuth();

        // Get filter parameters
        $filters = [
            'user_id' => $_GET['user_id'] ?? null,
            'action' => $_GET['action'] ?? null,
            'entity_type' => $_GET['entity_type'] ?? null,
            'entity_id' => $_GET['entity_id'] ?? null,
            'date_from' => $_GET['date_from'] ?? null,
            'date_to' => $_GET['date_to'] ?? null,
            'ip_address' => $_GET['ip_address'] ?? null,
            'limit' => $_GET['limit'] ?? 100
        ];

        // Get logs
        $logs = $this->auditLog->getWithFilters($filters);

        // Get all users for filter dropdown
        $users = $this->userModel->all();

        // Get action types
        $actionTypes = [
            'user_login', 'user_logout', 'user_login_failed',
            'user_created', 'user_updated', 'user_deleted',
            'campaign_created', 'campaign_updated', 'campaign_deleted',
            'draw_conducted', 'winner_selected',
            'payment_processed', 'prize_paid',
            'ticket_generated', 'configuration_changed'
        ];

        // Get entity types
        $entityTypes = [
            'user', 'campaign', 'draw', 'draw_winner', 
            'payment', 'ticket', 'configuration'
        ];

        $data = [
            'title' => 'Audit Logs',
            'logs' => $logs,
            'users' => $users,
            'actionTypes' => $actionTypes,
            'entityTypes' => $entityTypes,
            'filters' => $filters
        ];

        $this->view('audit/index', $data);
    }

    /**
     * View specific log details
     */
    public function show($id)
    {
        $this->requireAuth();

        $log = $this->auditLog->findById($id);

        if (!$log) {
            flash('error', 'Audit log not found');
            $this->redirect('audit');
        }

        // Get user details if exists
        $user = null;
        if ($log->user_id) {
            $user = $this->userModel->findById($log->user_id);
        }

        $data = [
            'title' => 'Audit Log Details',
            'log' => $log,
            'user' => $user
        ];

        parent::view('audit/view', $data);
    }

    /**
     * View logs for specific entity
     */
    public function entity($entityType, $entityId)
    {
        $this->requireAuth();

        $logs = $this->auditLog->getByEntity($entityType, $entityId);

        $data = [
            'title' => 'Entity Audit Trail',
            'logs' => $logs,
            'entityType' => $entityType,
            'entityId' => $entityId
        ];

        $this->view('audit/entity', $data);
    }

    /**
     * Display audit statistics
     */
    public function stats()
    {
        $this->requireAuth();

        $dateFrom = $_GET['date_from'] ?? date('Y-m-d', strtotime('-30 days'));
        $dateTo = $_GET['date_to'] ?? date('Y-m-d');

        // Get action statistics
        $actionStats = $this->auditLog->getActionStats($dateFrom, $dateTo);

        // Get user activity stats
        $userActivityStats = $this->auditLog->getUserActivityStats(10);

        // Get critical actions
        $criticalActions = $this->auditLog->getCriticalActions(20);

        $data = [
            'title' => 'Audit Statistics',
            'actionStats' => $actionStats,
            'userActivityStats' => $userActivityStats,
            'criticalActions' => $criticalActions,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo
        ];

        $this->view('audit/stats', $data);
    }

    /**
     * Export audit logs
     */
    public function export()
    {
        $this->requireAuth();

        $filters = [
            'user_id' => $_GET['user_id'] ?? null,
            'action' => $_GET['action'] ?? null,
            'entity_type' => $_GET['entity_type'] ?? null,
            'date_from' => $_GET['date_from'] ?? null,
            'date_to' => $_GET['date_to'] ?? null,
            'limit' => 10000 // Max export limit
        ];

        $logs = $this->auditLog->getWithFilters($filters);

        // Set headers for CSV download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="audit_logs_' . date('Y-m-d_His') . '.csv"');

        $output = fopen('php://output', 'w');

        // CSV headers
        fputcsv($output, ['ID', 'User', 'Action', 'Entity Type', 'Entity ID', 'IP Address', 'Date/Time']);

        // CSV data
        foreach ($logs as $log) {
            fputcsv($output, [
                $log->id,
                $log->username ?? ($log->email ?? 'System'),
                $log->action,
                $log->entity_type ?? '',
                $log->entity_id ?? '',
                $log->ip_address ?? '',
                $log->created_at
            ]);
        }

        fclose($output);
        exit;
    }

    /**
     * Clean old logs (admin only)
     */
    public function clean()
    {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $daysToKeep = $_POST['days_to_keep'] ?? 90;

            $result = $this->auditLog->cleanOldLogs($daysToKeep);

            if ($result) {
                flash('success', 'Old audit logs cleaned successfully');
            } else {
                flash('error', 'Failed to clean old logs');
            }

            $this->redirect('audit');
        }

        $data = [
            'title' => 'Clean Audit Logs'
        ];

        $this->view('audit/clean', $data);
    }
}
