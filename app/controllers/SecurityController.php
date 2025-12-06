<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\SecurityLog;

class SecurityController extends Controller
{
    private $securityLog;

    public function __construct()
    {
        $this->securityLog = $this->model('SecurityLog');
    }

    /**
     * Security dashboard
     */
    public function index()
    {
        $this->requireAuth();
        $this->requireRole('super_admin');

        $recentEvents = $this->securityLog->getRecentEvents(50);
        $blockedIPs = $this->securityLog->getBlockedIPs();

        $data = [
            'title' => 'Security Dashboard',
            'recentEvents' => $recentEvents,
            'blockedIPs' => $blockedIPs
        ];

        $this->view('security/index', $data);
    }

    /**
     * Unblock an IP address
     */
    public function unblock($ipAddress)
    {
        $this->requireAuth();
        $this->requireRole('super_admin');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            verify_csrf();

            $result = $this->securityLog->unblockIP($ipAddress);

            if ($result) {
                flash('success', 'IP address unblocked successfully');
            } else {
                flash('error', 'Failed to unblock IP address');
            }

            $this->redirect('security');
        }
    }

    /**
     * Block an IP address manually
     */
    public function block()
    {
        $this->requireAuth();
        $this->requireRole('super_admin');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            verify_csrf();

            $ipAddress = sanitize($_POST['ip_address']);
            $reason = sanitize($_POST['reason']);
            $duration = (int)($_POST['duration'] ?? 60);

            if (empty($ipAddress)) {
                flash('error', 'IP address is required');
                $this->redirect('security');
            }

            $result = $this->securityLog->blockIP($ipAddress, $reason, $duration);

            if ($result) {
                flash('success', 'IP address blocked successfully');
            } else {
                flash('error', 'Failed to block IP address');
            }

            $this->redirect('security');
        }

        $data = ['title' => 'Block IP Address'];
        $this->view('security/block', $data);
    }

    /**
     * Clean expired blocks
     */
    public function cleanExpired()
    {
        $this->requireAuth();
        $this->requireRole('super_admin');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            verify_csrf();

            $this->securityLog->cleanExpiredBlocks();
            flash('success', 'Expired IP blocks cleaned successfully');
            $this->redirect('security');
        }
    }
}
