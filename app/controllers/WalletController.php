<?php

namespace App\Controllers;

use App\Core\Controller;

class WalletController extends Controller
{
    private $walletModel;
    private $transactionModel;
    private $stationModel;

    public function __construct()
    {
        $this->walletModel = $this->model('StationWallet');
        $this->transactionModel = $this->model('StationWalletTransaction');
        $this->stationModel = $this->model('Station');
    }

    public function index()
    {
        $this->requireAuth();
        
        // Get all station wallets with balances
        $wallets = $this->walletModel->getAllWithStations();
        
        // Calculate totals
        $totalBalance = 0;
        $totalCredits = 0;
        $totalDebits = 0;
        
        foreach ($wallets as $wallet) {
            $totalBalance += $wallet->balance;
            $totalCredits += $wallet->total_credits ?? 0;
            $totalDebits += $wallet->total_debits ?? 0;
        }
        
        $data = [
            'title' => 'Station Wallets',
            'wallets' => $wallets,
            'totalBalance' => $totalBalance,
            'totalCredits' => $totalCredits,
            'totalDebits' => $totalDebits
        ];
        
        $this->view('wallets/index', $data);
    }

    public function show($walletId)
    {
        $this->requireAuth();
        
        $wallet = $this->walletModel->getWithStation($walletId);
        
        if (!$wallet) {
            flash('error', 'Wallet not found');
            $this->redirect('wallet');
            return;
        }
        
        // Get transaction history
        $transactions = $this->transactionModel->getByWallet($walletId);
        
        // Get summary
        $summary = $this->transactionModel->getSummary($walletId);
        
        $data = [
            'title' => 'Wallet Details',
            'wallet' => $wallet,
            'transactions' => $transactions,
            'summary' => $summary
        ];
        
        $this->view('wallets/view', $data);
    }

    public function transactions($walletId)
    {
        $this->requireAuth();
        
        $wallet = $this->walletModel->getWithStation($walletId);
        
        if (!$wallet) {
            flash('error', 'Wallet not found');
            $this->redirect('wallet');
            return;
        }
        
        // Filters
        $type = $_GET['type'] ?? null;
        $startDate = $_GET['start_date'] ?? date('Y-m-01');
        $endDate = $_GET['end_date'] ?? date('Y-m-d');
        
        $transactions = $this->transactionModel->getByWalletFiltered($walletId, $type, $startDate, $endDate);
        
        $data = [
            'title' => 'Wallet Transactions',
            'wallet' => $wallet,
            'transactions' => $transactions,
            'type' => $type,
            'start_date' => $startDate,
            'end_date' => $endDate
        ];
        
        $this->view('wallets/transactions', $data);
    }

    public function withdraw($walletId = null)
    {
        $this->requireAuth();
        $this->requireRole('super_admin');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            verify_csrf();
            
            $walletId = $_POST['wallet_id'];
            $amount = floatval($_POST['amount']);
            $description = $_POST['description'] ?? 'Withdrawal';
            
            $wallet = $this->walletModel->findById($walletId);
            
            if (!$wallet) {
                flash('error', 'Wallet not found');
                $this->redirect('wallet');
                return;
            }
            
            if ($amount <= 0) {
                flash('error', 'Invalid withdrawal amount');
                $this->redirect('wallet/view/' . $walletId);
                return;
            }
            
            if ($amount > $wallet->balance) {
                flash('error', 'Insufficient balance');
                $this->redirect('wallet/view/' . $walletId);
                return;
            }
            
            // Process withdrawal
            $success = $this->walletModel->debit($walletId, $amount);
            
            if ($success) {
                // Record transaction
                $this->transactionModel->recordDebit(
                    $walletId,
                    $amount,
                    null,
                    null,
                    $description
                );
                
                flash('success', 'Withdrawal processed successfully');
            } else {
                flash('error', 'Failed to process withdrawal');
            }
            
            $this->redirect('wallet/show/' . $walletId);
            return;
        }
        
        // Show withdrawal form
        $wallet = $this->walletModel->getWithStation($walletId);
        
        if (!$wallet) {
            flash('error', 'Wallet not found');
            $this->redirect('wallet');
            return;
        }
        
        $data = [
            'title' => 'Withdraw Funds',
            'wallet' => $wallet
        ];
        
        $this->view('wallets/withdraw', $data);
    }

    public function statement($walletId)
    {
        $this->requireAuth();
        
        $wallet = $this->walletModel->getWithStation($walletId);
        
        if (!$wallet) {
            flash('error', 'Wallet not found');
            $this->redirect('wallet');
            return;
        }
        
        $startDate = $_GET['start_date'] ?? date('Y-m-01');
        $endDate = $_GET['end_date'] ?? date('Y-m-d');
        
        $transactions = $this->transactionModel->getByWalletFiltered($walletId, null, $startDate, $endDate);
        $summary = $this->transactionModel->getSummaryByDateRange($walletId, $startDate, $endDate);
        
        $data = [
            'title' => 'Wallet Statement',
            'wallet' => $wallet,
            'transactions' => $transactions,
            'summary' => $summary,
            'start_date' => $startDate,
            'end_date' => $endDate
        ];
        
        $this->view('wallets/transactions', $data);
    }
}
