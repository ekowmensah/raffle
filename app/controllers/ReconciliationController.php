<?php

namespace App\Controllers;

use App\Core\Controller;

class ReconciliationController extends Controller
{
    private $paymentModel;
    private $revenueModel;
    private $campaignModel;

    public function __construct()
    {
        $this->paymentModel = $this->model('Payment');
        $this->revenueModel = $this->model('RevenueAllocation');
        $this->campaignModel = $this->model('Campaign');
    }

    public function index()
    {
        $this->requireAuth();
        $this->requireRole('super_admin');
        
        // Filters
        $gateway = $_GET['gateway'] ?? null;
        $startDate = $_GET['start_date'] ?? date('Y-m-d');
        $endDate = $_GET['end_date'] ?? date('Y-m-d');
        
        // Get payment summary by gateway
        $gatewaySummary = $this->paymentModel->getGatewaySummary($startDate, $endDate, $gateway);
        
        // Get revenue allocation summary
        $revenueSummary = $this->revenueModel->getReconciliationSummary($startDate, $endDate);
        
        // Get discrepancies
        $discrepancies = $this->findDiscrepancies($startDate, $endDate);
        
        // Calculate totals
        $totals = [
            'payments_count' => 0,
            'payments_amount' => 0,
            'allocations_count' => 0,
            'allocations_amount' => 0,
            'discrepancy_count' => count($discrepancies)
        ];
        
        foreach ($gatewaySummary as $row) {
            $totals['payments_count'] += $row->payment_count;
            $totals['payments_amount'] += $row->total_amount;
        }
        
        if ($revenueSummary) {
            $totals['allocations_count'] = $revenueSummary->allocation_count ?? 0;
            $totals['allocations_amount'] = $revenueSummary->total_allocated ?? 0;
        }
        
        $data = [
            'title' => 'Payment Reconciliation',
            'gatewaySummary' => $gatewaySummary,
            'revenueSummary' => $revenueSummary,
            'discrepancies' => $discrepancies,
            'totals' => $totals,
            'selected_gateway' => $gateway,
            'start_date' => $startDate,
            'end_date' => $endDate
        ];
        
        $this->view('reconciliation/index', $data);
    }

    public function discrepancies()
    {
        $this->requireAuth();
        $this->requireRole('super_admin');
        
        $startDate = $_GET['start_date'] ?? date('Y-m-d');
        $endDate = $_GET['end_date'] ?? date('Y-m-d');
        
        $discrepancies = $this->findDiscrepancies($startDate, $endDate);
        
        $data = [
            'title' => 'Payment Discrepancies',
            'discrepancies' => $discrepancies,
            'start_date' => $startDate,
            'end_date' => $endDate
        ];
        
        $this->view('reconciliation/discrepancies', $data);
    }

    private function findDiscrepancies($startDate, $endDate)
    {
        // Find payments without revenue allocations
        $paymentsWithoutAllocation = $this->paymentModel->getPaymentsWithoutAllocation($startDate, $endDate);
        
        // Find revenue allocations without payments
        $allocationsWithoutPayment = $this->revenueModel->getAllocationsWithoutPayment($startDate, $endDate);
        
        $discrepancies = [];
        
        // Add payments without allocation
        foreach ($paymentsWithoutAllocation as $payment) {
            $discrepancies[] = [
                'type' => 'missing_allocation',
                'payment_id' => $payment->id,
                'reference' => $payment->internal_reference,
                'amount' => $payment->amount,
                'date' => $payment->created_at,
                'description' => 'Payment has no revenue allocation'
            ];
        }
        
        // Add allocations without payment
        foreach ($allocationsWithoutPayment as $allocation) {
            $discrepancies[] = [
                'type' => 'missing_payment',
                'allocation_id' => $allocation->id,
                'payment_id' => $allocation->payment_id,
                'amount' => $allocation->gross_amount,
                'date' => $allocation->created_at,
                'description' => 'Revenue allocation has no valid payment'
            ];
        }
        
        return $discrepancies;
    }

    public function resolve($type, $id)
    {
        $this->requireAuth();
        $this->requireRole('super_admin');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            verify_csrf();
            
            $action = $_POST['action'];
            
            if ($type === 'missing_allocation' && $action === 'create') {
                // Create missing allocation
                $payment = $this->paymentModel->findById($id);
                
                if ($payment && $payment->status === 'success') {
                    $campaign = $this->campaignModel->findById($payment->campaign_id);
                    
                    if ($campaign) {
                        $paymentData = [
                            'payment_id' => $payment->id,
                            'campaign_id' => $payment->campaign_id,
                            'station_id' => $payment->station_id,
                            'programme_id' => $payment->programme_id,
                            'amount' => $payment->amount
                        ];
                        
                        $this->revenueModel->allocateRevenue($paymentData, $campaign);
                        flash('success', 'Revenue allocation created successfully');
                    }
                }
            }
            
            $this->redirect('reconciliation');
            return;
        }
        
        $this->redirect('reconciliation');
    }
}
