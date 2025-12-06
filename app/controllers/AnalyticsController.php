<?php

namespace App\Controllers;

use App\Core\Controller;

class AnalyticsController extends Controller
{
    private $paymentModel;
    private $ticketModel;
    private $playerModel;
    private $drawModel;
    private $campaignModel;

    private $cache;

    public function __construct()
    {
        $this->paymentModel = $this->model('Payment');
        $this->ticketModel = $this->model('Ticket');
        $this->playerModel = $this->model('Player');
        $this->drawModel = $this->model('Draw');
        $this->campaignModel = $this->model('Campaign');
        $this->cache = new \App\Services\CacheService();
    }

    /**
     * Analytics dashboard page
     */
    public function index()
    {
        $this->requireAuth();

        $data = [
            'title' => 'Analytics Dashboard'
        ];

        $this->view('analytics/dashboard', $data);
    }

    /**
     * Get revenue trend data for charts
     */
    public function getRevenueTrend()
    {
        header('Content-Type: application/json');
        
        try {
            $days = $_GET['days'] ?? 30;
            
            $this->paymentModel->db->query("
                SELECT DATE(created_at) as date, 
                       SUM(amount) as revenue,
                       COUNT(*) as transaction_count
                FROM payments
                WHERE status = 'success'
                AND created_at >= DATE_SUB(CURDATE(), INTERVAL :days DAY)
                GROUP BY DATE(created_at)
                ORDER BY date ASC
            ");
            
            $this->paymentModel->db->bind(':days', $days);
            $data = $this->paymentModel->db->resultSet();
            
            $result = [
                'labels' => array_map(function($item) {
                    return date('M d', strtotime($item->date));
                }, $data),
                'revenue' => array_map('floatval', array_column($data, 'revenue')),
                'transactions' => array_map('intval', array_column($data, 'transaction_count'))
            ];
            
            echo json_encode($result);
        } catch (\Exception $e) {
            echo json_encode(['labels' => [], 'revenue' => [], 'transactions' => [], 'error' => $e->getMessage()]);
        }
        exit;
    }

    /**
     * Get ticket sales trend
     */
    public function getTicketSalesTrend()
    {
        header('Content-Type: application/json');
        
        try {
            $days = $_GET['days'] ?? 30;
            
            $this->ticketModel->db->query("
                SELECT DATE(created_at) as date, 
                       COUNT(*) as ticket_count,
                       COALESCE(SUM(quantity), 0) as total_quantity
                FROM tickets
                WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL :days DAY)
                GROUP BY DATE(created_at)
                ORDER BY date ASC
            ");
            
            $this->ticketModel->db->bind(':days', $days);
            $data = $this->ticketModel->db->resultSet();
            
            echo json_encode([
                'labels' => array_map(function($item) {
                    return date('M d', strtotime($item->date));
                }, $data),
                'tickets' => array_map('intval', array_column($data, 'total_quantity'))
            ]);
        } catch (\Exception $e) {
            echo json_encode(['labels' => [], 'tickets' => [], 'error' => $e->getMessage()]);
        }
        exit;
    }

    /**
     * Get player growth data
     */
    public function getPlayerGrowth()
    {
        header('Content-Type: application/json');
        
        try {
            $days = (int)($_GET['days'] ?? 30);
            
            $this->playerModel->db->query("
                SELECT DATE(created_at) as date, 
                       COUNT(*) as new_players
                FROM players
                WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL :days DAY)
                GROUP BY DATE(created_at)
                ORDER BY date ASC
            ");
            
            $this->playerModel->db->bind(':days', $days);
            $data = $this->playerModel->db->resultSet();
            
            // Calculate cumulative
            $cumulative = 0;
            $cumulativeData = [];
            foreach ($data as $item) {
                $cumulative += $item->new_players;
                $cumulativeData[] = $cumulative;
            }
            
            echo json_encode([
                'labels' => array_map(function($item) {
                    return date('M d', strtotime($item->date));
                }, $data),
                'new_players' => array_column($data, 'new_players'),
                'cumulative' => $cumulativeData
            ]);
        } catch (\Exception $e) {
            echo json_encode(['labels' => [], 'new_players' => [], 'cumulative' => [], 'error' => $e->getMessage()]);
        }
        exit;
    }

    /**
     * Get campaign performance comparison
     */
    public function getCampaignPerformance()
    {
        header('Content-Type: application/json');
        
        try {
            $this->campaignModel->db->query("
                SELECT c.name,
                       COUNT(DISTINCT t.id) as total_tickets,
                       COALESCE(SUM(p.amount), 0) as total_revenue,
                       COUNT(DISTINCT t.player_id) as unique_players
                FROM raffle_campaigns c
                LEFT JOIN tickets t ON c.id = t.campaign_id
                LEFT JOIN payments p ON t.payment_id = p.id AND p.status = 'success'
                WHERE c.status = 'active'
                GROUP BY c.id
                ORDER BY total_revenue DESC
                LIMIT 10
            ");
            
            $data = $this->campaignModel->db->resultSet();
            
            echo json_encode([
                'labels' => array_column($data, 'name'),
                'tickets' => array_column($data, 'total_tickets'),
                'revenue' => array_column($data, 'total_revenue'),
                'players' => array_column($data, 'unique_players')
            ]);
        } catch (\Exception $e) {
            echo json_encode(['labels' => [], 'tickets' => [], 'revenue' => [], 'players' => [], 'error' => $e->getMessage()]);
        }
        exit;
    }

    /**
     * Get hourly sales pattern
     */
    public function getHourlySalesPattern()
    {
        header('Content-Type: application/json');
        
        try {
            $this->paymentModel->db->query("
                SELECT HOUR(created_at) as hour,
                       COUNT(*) as transaction_count,
                       SUM(amount) as total_amount
                FROM payments
                WHERE status = 'success'
                AND created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
                GROUP BY HOUR(created_at)
                ORDER BY hour ASC
            ");
            
            $data = $this->paymentModel->db->resultSet();
            
            // Fill in missing hours with 0
            $hourlyData = array_fill(0, 24, 0);
            foreach ($data as $row) {
                $hourlyData[(int)$row->hour] = (int)$row->transaction_count;
            }
            
            echo json_encode([
                'labels' => array_map(function($h) { return sprintf('%02d:00', $h); }, range(0, 23)),
                'transactions' => array_values($hourlyData)
            ]);
        } catch (\Exception $e) {
            echo json_encode(['labels' => [], 'transactions' => [], 'error' => $e->getMessage()]);
        }
        exit;
    }

    /**
     * Get loyalty level distribution
     */
    public function getLoyaltyDistribution()
    {
        header('Content-Type: application/json');
        
        try {
            $this->playerModel->db->query("
                SELECT COALESCE(loyalty_level, 'bronze') as loyalty_level, COUNT(*) as count
                FROM players
                GROUP BY loyalty_level
                ORDER BY 
                    CASE loyalty_level
                        WHEN 'bronze' THEN 1
                        WHEN 'silver' THEN 2
                        WHEN 'gold' THEN 3
                        WHEN 'platinum' THEN 4
                        ELSE 1
                    END
            ");
            
            $data = $this->playerModel->db->resultSet();
            
            echo json_encode([
                'labels' => array_map(function($item) {
                    return ucfirst($item->loyalty_level ?? 'Bronze');
                }, $data),
                'counts' => array_map('intval', array_column($data, 'count'))
            ]);
        } catch (\Exception $e) {
            echo json_encode(['labels' => [], 'counts' => [], 'error' => $e->getMessage()]);
        }
        exit;
    }

    /**
     * Get payment method distribution
     */
    public function getPaymentMethodDistribution()
    {
        header('Content-Type: application/json');
        
        $this->paymentModel->db->query("
            SELECT payment_method, 
                   COUNT(*) as count,
                   SUM(amount) as total_amount
            FROM payments
            WHERE status = 'success'
            GROUP BY payment_method
        ");
        
        $data = $this->paymentModel->db->resultSet();
        
        echo json_encode([
            'labels' => array_map(function($item) {
                return strtoupper($item->payment_method ?? 'Unknown');
            }, $data),
            'counts' => array_column($data, 'count'),
            'amounts' => array_column($data, 'total_amount')
        ]);
        exit;
    }

    /**
     * Get station performance comparison
     */
    public function getStationPerformance()
    {
        header('Content-Type: application/json');
        
        $stationModel = $this->model('Station');
        
        $stationModel->db->query("
            SELECT s.name,
                   COUNT(DISTINCT t.id) as total_tickets,
                   COALESCE(SUM(p.amount), 0) as total_revenue,
                   COUNT(DISTINCT t.player_id) as unique_players,
                   COUNT(DISTINCT c.id) as active_campaigns
            FROM stations s
            LEFT JOIN programmes prog ON s.id = prog.station_id
            LEFT JOIN raffle_campaigns c ON s.id = c.station_id
            LEFT JOIN tickets t ON c.id = t.campaign_id
            LEFT JOIN payments p ON t.payment_id = p.id AND p.status = 'success'
            WHERE s.is_active = 1
            GROUP BY s.id
            ORDER BY total_revenue DESC
        ");
        
        $data = $stationModel->db->resultSet();
        
        echo json_encode([
            'labels' => array_column($data, 'name'),
            'tickets' => array_column($data, 'total_tickets'),
            'revenue' => array_column($data, 'total_revenue'),
            'players' => array_column($data, 'unique_players'),
            'campaigns' => array_column($data, 'active_campaigns')
        ]);
        exit;
    }

    /**
     * Get draw success rate
     */
    public function getDrawSuccessRate()
    {
        header('Content-Type: application/json');
        
        $this->drawModel->db->query("
            SELECT 
                DATE(draw_date) as date,
                COUNT(*) as total_draws,
                SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_draws,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_draws
            FROM draws
            WHERE draw_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
            GROUP BY DATE(draw_date)
            ORDER BY date ASC
        ");
        
        $data = $this->drawModel->db->resultSet();
        
        echo json_encode([
            'labels' => array_map(function($item) {
                return date('M d', strtotime($item->date));
            }, $data),
            'completed' => array_column($data, 'completed_draws'),
            'pending' => array_column($data, 'pending_draws'),
            'total' => array_column($data, 'total_draws')
        ]);
        exit;
    }

    /**
     * Get top players by spending
     */
    public function getTopPlayers()
    {
        header('Content-Type: application/json');
        
        $limit = $_GET['limit'] ?? 10;
        
        $this->playerModel->db->query("
            SELECT p.phone,
                   COUNT(DISTINCT t.id) as total_tickets,
                   COALESCE(SUM(pay.amount), 0) as total_spent,
                   COUNT(DISTINCT dw.id) as total_wins,
                   p.loyalty_level
            FROM players p
            LEFT JOIN tickets t ON p.id = t.player_id
            LEFT JOIN payments pay ON p.id = pay.player_id AND pay.status = 'success'
            LEFT JOIN draw_winners dw ON p.id = dw.player_id
            GROUP BY p.id
            ORDER BY total_spent DESC
            LIMIT :limit
        ");
        
        $this->playerModel->db->bind(':limit', (int)$limit);
        $data = $this->playerModel->db->resultSet();
        
        echo json_encode([
            'labels' => array_map(function($item) {
                return substr($item->phone, -4);
            }, $data),
            'spent' => array_column($data, 'total_spent'),
            'tickets' => array_column($data, 'total_tickets'),
            'wins' => array_column($data, 'total_wins')
        ]);
        exit;
    }
}
