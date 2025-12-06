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

    public function __construct()
    {
        $this->paymentModel = $this->model('Payment');
        $this->ticketModel = $this->model('Ticket');
        $this->playerModel = $this->model('Player');
        $this->drawModel = $this->model('Draw');
        $this->campaignModel = $this->model('Campaign');
    }

    /**
     * Get revenue trend data for charts
     */
    public function getRevenueTrend()
    {
        header('Content-Type: application/json');
        
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
        
        echo json_encode([
            'labels' => array_map(function($item) {
                return date('M d', strtotime($item->date));
            }, $data),
            'revenue' => array_column($data, 'revenue'),
            'transactions' => array_column($data, 'transaction_count')
        ]);
        exit;
    }

    /**
     * Get ticket sales trend
     */
    public function getTicketSalesTrend()
    {
        header('Content-Type: application/json');
        
        $days = $_GET['days'] ?? 30;
        
        $this->ticketModel->db->query("
            SELECT DATE(created_at) as date, 
                   COUNT(*) as ticket_count,
                   SUM(quantity) as total_quantity
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
            'tickets' => array_column($data, 'ticket_count'),
            'quantity' => array_column($data, 'total_quantity')
        ]);
        exit;
    }

    /**
     * Get player growth data
     */
    public function getPlayerGrowth()
    {
        header('Content-Type: application/json');
        
        $days = $_GET['days'] ?? 30;
        
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
        exit;
    }

    /**
     * Get campaign performance comparison
     */
    public function getCampaignPerformance()
    {
        header('Content-Type: application/json');
        
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
        exit;
    }

    /**
     * Get hourly sales pattern
     */
    public function getHourlySalesPattern()
    {
        header('Content-Type: application/json');
        
        $this->paymentModel->db->query("
            SELECT HOUR(created_at) as hour,
                   COUNT(*) as transaction_count,
                   SUM(amount) as revenue
            FROM payments
            WHERE status = 'success'
            AND created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
            GROUP BY HOUR(created_at)
            ORDER BY hour ASC
        ");
        
        $data = $this->paymentModel->db->resultSet();
        
        // Fill in missing hours with 0
        $hourlyData = array_fill(0, 24, 0);
        $hourlyRevenue = array_fill(0, 24, 0);
        
        foreach ($data as $item) {
            $hourlyData[$item->hour] = $item->transaction_count;
            $hourlyRevenue[$item->hour] = $item->revenue;
        }
        
        echo json_encode([
            'labels' => array_map(function($h) {
                return sprintf('%02d:00', $h);
            }, range(0, 23)),
            'transactions' => array_values($hourlyData),
            'revenue' => array_values($hourlyRevenue)
        ]);
        exit;
    }

    /**
     * Get loyalty level distribution
     */
    public function getLoyaltyDistribution()
    {
        header('Content-Type: application/json');
        
        $this->playerModel->db->query("
            SELECT loyalty_level, COUNT(*) as count
            FROM players
            GROUP BY loyalty_level
            ORDER BY FIELD(loyalty_level, 'bronze', 'silver', 'gold', 'platinum')
        ");
        
        $data = $this->playerModel->db->resultSet();
        
        echo json_encode([
            'labels' => array_map(function($item) {
                return ucfirst($item->loyalty_level);
            }, $data),
            'counts' => array_column($data, 'count')
        ]);
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
}
