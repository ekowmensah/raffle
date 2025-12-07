<?php

namespace App\Controllers;

use App\Core\Controller;

class WithdrawalController extends Controller
{
    private $withdrawalModel;
    private $stationModel;
    private $walletModel;

    public function __construct()
    {
        $this->withdrawalModel = $this->model('Withdrawal');
        $this->stationModel = $this->model('Station');
        $this->walletModel = $this->model('StationWallet');
    }

    public function index()
    {
        $this->requireAuth();

        $user = $_SESSION['user'];
        $role = $user->role_name ?? '';
        
        if ($role === 'super_admin') {
            $withdrawals = $this->withdrawalModel->getAllWithDetails();
        } elseif ($role === 'station_admin') {
            $withdrawals = $this->withdrawalModel->getByStation($user->station_id);
        } else {
            flash('error', 'You do not have permission to view withdrawals');
            $this->redirect('home');
            return;
        }

        $data = [
            'title' => 'Withdrawals',
            'withdrawals' => $withdrawals
        ];

        $this->view('withdrawals/index', $data);
    }

    public function create()
    {
        $this->requireAuth();

        if (!hasRole('station_admin')) {
            flash('error', 'Only station administrators can request withdrawals');
            $this->redirect('home');
            return;
        }

        $user = $_SESSION['user'];
        $stationId = $user->station_id;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            verify_csrf();

            $amount = floatval($_POST['amount']);
            $paymentMethod = sanitize($_POST['payment_method']);
            $accountDetails = sanitize($_POST['account_details']);
            $notes = sanitize($_POST['notes'] ?? '');

            // Validation
            if ($amount <= 0) {
                flash('error', 'Amount must be greater than zero');
                $this->redirect('withdrawal/create');
                return;
            }

            // Get station wallet balance
            $wallet = $this->walletModel->getByStation($stationId);
            $balance = $wallet->balance ?? 0;

            if ($amount > $balance) {
                flash('error', 'Insufficient wallet balance. Available: GHS ' . number_format($balance, 2));
                $this->redirect('withdrawal/create');
                return;
            }

            $data = [
                'station_id' => $stationId,
                'requested_by_user_id' => $_SESSION['user_id'],
                'amount' => $amount,
                'wallet_balance_before' => $balance,
                'payment_method' => $paymentMethod,
                'account_details' => $accountDetails,
                'notes' => $notes,
                'status' => 'pending'
            ];

            if ($this->withdrawalModel->create($data)) {
                flash('success', 'Withdrawal request submitted successfully. Awaiting approval.');
                $this->redirect('withdrawal');
            } else {
                flash('error', 'Failed to submit withdrawal request');
            }
        }

        // Get station wallet
        $wallet = $this->walletModel->getByStation($stationId);
        $station = $this->stationModel->findById($stationId);

        $data = [
            'title' => 'Request Withdrawal',
            'wallet' => $wallet,
            'station' => $station
        ];

        $this->view('withdrawals/create', $data);
    }

    public function show($id)
    {
        $this->requireAuth();

        $withdrawal = $this->withdrawalModel->findById($id);

        if (!$withdrawal) {
            flash('error', 'Withdrawal not found');
            $this->redirect('withdrawal');
            return;
        }

        // Check access
        $user = $_SESSION['user'];
        if (!hasRole('super_admin') && $withdrawal->station_id != $user->station_id) {
            flash('error', 'You do not have permission to view this withdrawal');
            $this->redirect('withdrawal');
            return;
        }

        // Get full details
        $withdrawals = $this->withdrawalModel->getAllWithDetails();
        $withdrawalDetails = null;
        foreach ($withdrawals as $w) {
            if ($w->id == $id) {
                $withdrawalDetails = $w;
                break;
            }
        }

        $data = [
            'title' => 'Withdrawal Details',
            'withdrawal' => $withdrawalDetails ?? $withdrawal
        ];

        $this->view('withdrawals/show', $data);
    }

    public function approve($id)
    {
        $this->requireAuth();
        $this->requireRole('super_admin');

        $withdrawal = $this->withdrawalModel->findById($id);

        if (!$withdrawal) {
            flash('error', 'Withdrawal not found');
            $this->redirect('withdrawal');
            return;
        }

        if ($withdrawal->status !== 'pending') {
            flash('error', 'Only pending withdrawals can be approved');
            $this->redirect('withdrawal/show/' . $id);
            return;
        }

        if ($this->withdrawalModel->approve($id, $_SESSION['user_id'])) {
            flash('success', 'Withdrawal approved successfully');
        } else {
            flash('error', 'Failed to approve withdrawal');
        }

        $this->redirect('withdrawal/show/' . $id);
    }

    public function reject($id)
    {
        $this->requireAuth();
        $this->requireRole('super_admin');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            verify_csrf();

            $withdrawal = $this->withdrawalModel->findById($id);

            if (!$withdrawal) {
                flash('error', 'Withdrawal not found');
                $this->redirect('withdrawal');
                return;
            }

            if ($withdrawal->status !== 'pending') {
                flash('error', 'Only pending withdrawals can be rejected');
                $this->redirect('withdrawal/show/' . $id);
                return;
            }

            $reason = sanitize($_POST['reason']);

            if (empty($reason)) {
                flash('error', 'Please provide a reason for rejection');
                $this->redirect('withdrawal/show/' . $id);
                return;
            }

            if ($this->withdrawalModel->reject($id, $_SESSION['user_id'], $reason)) {
                flash('success', 'Withdrawal rejected');
            } else {
                flash('error', 'Failed to reject withdrawal');
            }
        }

        $this->redirect('withdrawal/show/' . $id);
    }

    public function complete($id)
    {
        $this->requireAuth();
        $this->requireRole('super_admin');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            verify_csrf();

            $withdrawal = $this->withdrawalModel->findById($id);

            if (!$withdrawal) {
                flash('error', 'Withdrawal not found');
                $this->redirect('withdrawal');
                return;
            }

            if ($withdrawal->status !== 'approved') {
                flash('error', 'Only approved withdrawals can be completed');
                $this->redirect('withdrawal/show/' . $id);
                return;
            }

            $transactionRef = sanitize($_POST['transaction_reference']);

            if (empty($transactionRef)) {
                flash('error', 'Transaction reference is required');
                $this->redirect('withdrawal/show/' . $id);
                return;
            }

            // Deduct from station wallet
            $wallet = $this->walletModel->getByStation($withdrawal->station_id);
            $newBalance = $wallet->balance - $withdrawal->amount;

            if ($this->walletModel->updateBalance($withdrawal->station_id, $newBalance)) {
                if ($this->withdrawalModel->complete($id, $transactionRef, $newBalance)) {
                    flash('success', 'Withdrawal completed successfully. Wallet updated.');
                } else {
                    flash('error', 'Failed to complete withdrawal');
                }
            } else {
                flash('error', 'Failed to update wallet balance');
            }
        }

        $this->redirect('withdrawal/show/' . $id);
    }
}
