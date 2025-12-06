<?php

namespace App\Controllers;

use App\Core\Controller;

class ReportController extends Controller
{
    public function index()
    {
        // Redirect to revenue report as default
        $this->redirect('report/revenue');
    }

    public function revenue()
    {
        $this->requireAuth();

        $campaignId = $_GET['campaign'] ?? null;
        $stationId = $_GET['station'] ?? null;
        $programmeId = $_GET['programme'] ?? null;
        $startDate = $_GET['start_date'] ?? date('Y-m-01');
        $endDate = $_GET['end_date'] ?? date('Y-m-d');

        $revenueModel = $this->model('RevenueAllocation');
        $report = $revenueModel->getRevenueReport($startDate, $endDate, $campaignId, $stationId, $programmeId);

        // Calculate grand totals
        $grandTotals = [
            'platform' => 0,
            'station' => 0,
            'programme' => 0,
            'prize_pool' => 0,
            'payments' => 0
        ];

        foreach ($report as $row) {
            $grandTotals['platform'] += $row->platform_total;
            $grandTotals['station'] += $row->station_total;
            $grandTotals['programme'] += $row->programme_total;
            $grandTotals['prize_pool'] += $row->prize_pool_total;
            $grandTotals['payments'] += $row->payment_count;
        }

        $data = [
            'title' => 'Revenue Report',
            'report' => $report,
            'grand_totals' => $grandTotals,
            'campaigns' => $this->model('Campaign')->findAll(),
            'selected_campaign' => $campaignId,
            'start_date' => $startDate,
            'end_date' => $endDate
        ];

        $this->view('reports/revenue', $data);
    }
}
