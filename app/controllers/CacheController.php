<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Services\CacheService;

class CacheController extends Controller
{
    private $cache;

    public function __construct()
    {
        $this->cache = new CacheService();
    }

    /**
     * Cache management dashboard
     */
    public function index()
    {
        $this->requireAuth();
        $this->requireRole('super_admin');

        $stats = $this->cache->getStats();

        $data = [
            'title' => 'Cache Management',
            'stats' => $stats
        ];

        $this->view('cache/index', $data);
    }

    /**
     * Clear all cache
     */
    public function clear()
    {
        $this->requireAuth();
        $this->requireRole('super_admin');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            verify_csrf();

            $this->cache->clear();
            flash('success', 'Cache cleared successfully');
            $this->redirect('cache');
        }
    }

    /**
     * Clean expired cache entries
     */
    public function cleanExpired()
    {
        $this->requireAuth();
        $this->requireRole('super_admin');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            verify_csrf();

            $cleaned = $this->cache->cleanExpired();
            flash('success', "Cleaned {$cleaned} expired cache entries");
            $this->redirect('cache');
        }
    }
}
