<?php

namespace App\Controllers;

use App\Core\Controller;

class FinancialController extends Controller
{
    public function index()
    {
        // Redirect to commissions report as default
        $this->redirect('financial/commissions');
    }

    private $revenueModel;
    private $walletModel;
    private $paymentModel;
    private $drawWinnerModel;
    private $campaignModel;

    public function __construct()
    {
        $this->revenueModel = $this->model('RevenueAllocation');
        $this->walletModel = $this->model('StationWallet');
        $this->paymentModel = $this->model('Payment');
        $this->drawWinnerModel = $this->model('DrawWinner');
        $this->campaignModel = $this->model('Campaign');
    }

    public function commissions()
    {
        $this->requireAuth();
        
        // Filters
        $stationId = $_GET['station'] ?? null;
        $programmeId = $_GET['programme'] ?? null;
        $campaignId = $_GET['campaign'] ?? null;
        $startDate = $_GET['start_date'] ?? date('Y-m-01');
        $endDate = $_GET['end_date'] ?? date('Y-m-d');
        
        // Get commission breakdown
        $report = $this->revenueModel->getRevenueReport($startDate, $endDate, $campaignId, $stationId, $programmeId);
        
        // Calculate totals
        $totals = [
            'platform' => 0,
            'station' => 0,
            'programme' => 0,
            'prize_pool' => 0
        ];
        
        foreach ($report as $row) {
            $totals['platform'] += $row->platform_total;
            $totals['station'] += $row->station_total;
            $totals['programme'] += $row->programme_total;
            $totals['prize_pool'] += $row->prize_pool_total;
        }
        
        $data = [
            'title' => 'Commission Report',
            'report' => $report,
            'totals' => $totals,
            'campaigns' => $this->campaignModel->findAll(),
            'selected_station' => $stationId,
            'selected_programme' => $programmeId,
            'selected_campaign' => $campaignId,
            'start_date' => $startDate,
            'end_date' => $endDate
        ];
        
        $this->view('reports/commissions', $data);
    }

    public function payouts()
    {
        $this->requireAuth();
        
        // Filters
        $campaignId = $_GET['campaign'] ?? null;
        $status = $_GET['status'] ?? null;
        $startDate = $_GET['start_date'] ?? date('Y-m-01');
        $endDate = $_GET['end_date'] ?? date('Y-m-d');
        
        // Get payout data
        $payouts = $this->drawWinnerModel->getPayoutReport($startDate, $endDate, $campaignId, $status);
        
        // Calculate summary
        $summary = [
            'total_amount' => 0,
            'paid_amount' => 0,
            'pending_amount' => 0,
            'total_winners' => count($payouts)
        ];
        
        foreach ($payouts as $payout) {
            $summary['total_amount'] += $payout->prize_amount;
            if ($payout->prize_paid_status == 'paid') {
                $summary['paid_amount'] += $payout->prize_amount;
            } else {
                $summary['pending_amount'] += $payout->prize_amount;
            }
        }
        
        $data = [
            'title' => 'Payout Report',
            'payouts' => $payouts,
            'summary' => $summary,
            'campaigns' => $this->campaignModel->findAll(),
            'selected_campaign' => $campaignId,
            'selected_status' => $status,
            'start_date' => $startDate,
            'end_date' => $endDate
        ];
        
        $this->view('reports/payouts', $data);
    }

    public function profitability()
    {
        $this->requireAuth();
        
        // Filters
        $campaignId = $_GET['campaign'] ?? null;
        $startDate = $_GET['start_date'] ?? date('Y-m-01');
        $endDate = $_GET['end_date'] ?? date('Y-m-d');
        
        // Get revenue data
        $revenue = $this->revenueModel->getRevenueReport($startDate, $endDate, $campaignId);
        
        // Get payout data
        $payouts = $this->drawWinnerModel->getPayoutReport($startDate, $endDate, $campaignId);
        
        // Calculate profitability
        $analysis = [];
        $totalRevenue = 0;
        $totalPlatform = 0;
        $totalStation = 0;
        $totalProgramme = 0;
        $totalPrizePool = 0;
        $totalPayouts = 0;
        
        foreach ($revenue as $row) {
            $totalRevenue += $row->prize_pool_total + $row->platform_total + $row->station_total + $row->programme_total;
            $totalPlatform += $row->platform_total;
            $totalStation += $row->station_total;
            $totalProgramme += $row->programme_total;
            $totalPrizePool += $row->prize_pool_total;
        }
        
        foreach ($payouts as $payout) {
            if ($payout->prize_paid_status == 'paid') {
                $totalPayouts += $payout->prize_amount;
            }
        }
        
        $analysis = [
            'total_revenue' => $totalRevenue,
            'platform_commission' => $totalPlatform,
            'station_commission' => $totalStation,
            'programme_commission' => $totalProgramme,
            'prize_pool_allocated' => $totalPrizePool,
            'prizes_paid' => $totalPayouts,
            'prize_pool_remaining' => $totalPrizePool - $totalPayouts,
            'platform_profit_margin' => $totalRevenue > 0 ? ($totalPlatform / $totalRevenue) * 100 : 0
        ];
        
        $data = [
            'title' => 'Profitability Analysis',
            'analysis' => $analysis,
            'campaigns' => $this->campaignModel->findAll(),
            'selected_campaign' => $campaignId,
            'start_date' => $startDate,
            'end_date' => $endDate
        ];
        
        $this->view('reports/profitability', $data);
    }

    public function stationPerformance()
    {
        $this->requireAuth();
        
        $startDate = $_GET['start_date'] ?? date('Y-m-01');
        $endDate = $_GET['end_date'] ?? date('Y-m-d');
        
        // Get station performance data
        $performance = $this->revenueModel->getStationPerformance($startDate, $endDate);
        
        $data = [
            'title' => 'Station Performance Report',
            'performance' => $performance,
            'start_date' => $startDate,
            'end_date' => $endDate
        ];
        
        $this->view('reports/station-performance', $data);
    }
}
