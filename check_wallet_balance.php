<?php
/**
 * Wallet Balance Reconciliation Check
 * Run: php check_wallet_balance.php
 */

require_once 'app/config/config.php';
require_once 'app/core/Database.php';

$db = new App\Core\Database();

echo "🔍 Wallet Balance Reconciliation Check\n";
echo "=====================================\n\n";

// Get all wallets
$db->query("SELECT w.id, w.station_id, w.balance, s.name as station_name
           FROM station_wallets w
           INNER JOIN stations s ON w.station_id = s.id");
$wallets = $db->resultSet();

foreach ($wallets as $wallet) {
    echo "Station: {$wallet->station_name} (ID: {$wallet->station_id})\n";
    echo "Wallet ID: {$wallet->id}\n";
    echo "Current Balance: GHS " . number_format($wallet->balance, 2) . "\n";
    
    // Get transaction totals
    $db->query("SELECT 
               SUM(CASE WHEN transaction_type = 'credit' THEN amount ELSE 0 END) as total_credits,
               SUM(CASE WHEN transaction_type = 'debit' THEN amount ELSE 0 END) as total_debits,
               COUNT(*) as transaction_count
               FROM station_wallet_transactions
               WHERE station_wallet_id = :wallet_id");
    $db->bind(':wallet_id', $wallet->id);
    $transactions = $db->single();
    
    $totalCredits = $transactions->total_credits ?? 0;
    $totalDebits = $transactions->total_debits ?? 0;
    $calculatedBalance = $totalCredits - $totalDebits;
    $difference = $wallet->balance - $calculatedBalance;
    
    echo "Total Credits: GHS " . number_format($totalCredits, 2) . "\n";
    echo "Total Debits: GHS " . number_format($totalDebits, 2) . "\n";
    echo "Calculated Balance: GHS " . number_format($calculatedBalance, 2) . "\n";
    echo "Difference: GHS " . number_format($difference, 2) . "\n";
    echo "Transaction Count: {$transactions->transaction_count}\n";
    
    if (abs($difference) > 0.01) {
        echo "⚠️  WARNING: Balance mismatch detected!\n";
        
        // Check revenue allocations
        $db->query("SELECT COUNT(*) as count, SUM(station_amount) as total
                   FROM revenue_allocations
                   WHERE station_id = :station_id");
        $db->bind(':station_id', $wallet->station_id);
        $allocations = $db->single();
        
        echo "Revenue Allocations: {$allocations->count} records, Total: GHS " . number_format($allocations->total ?? 0, 2) . "\n";
        
        // Check if there are allocations without transactions
        $db->query("SELECT ra.id, ra.payment_id, ra.station_amount, ra.created_at
                   FROM revenue_allocations ra
                   LEFT JOIN station_wallet_transactions swt ON ra.payment_id = swt.related_payment_id
                   WHERE ra.station_id = :station_id
                   AND swt.id IS NULL
                   LIMIT 5");
        $db->bind(':station_id', $wallet->station_id);
        $missing = $db->resultSet();
        
        if (!empty($missing)) {
            echo "❌ Found " . count($missing) . " allocations without transactions:\n";
            foreach ($missing as $m) {
                echo "   - Payment ID: {$m->payment_id}, Amount: GHS " . number_format($m->station_amount, 2) . ", Date: {$m->created_at}\n";
            }
        }
    } else {
        echo "✅ Balance matches!\n";
    }
    
    echo "\n" . str_repeat("-", 50) . "\n\n";
}

echo "✅ Reconciliation check complete!\n";
