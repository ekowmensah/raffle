<?php require_once '../app/views/layouts/header.php'; ?>
<?php require_once '../app/views/layouts/sidebar.php'; ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Wallet Transactions</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url('home') ?>">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= url('wallet') ?>">Wallets</a></li>
                        <li class="breadcrumb-item active">Transactions</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            
            <!-- Wallet Info -->
            <div class="alert alert-info">
                <strong>Station:</strong> <?= htmlspecialchars($wallet->station_name) ?> |
                <strong>Balance:</strong> <span class="text-success">GHS <?= number_format($wallet->balance, 2) ?></span>
            </div>

            <!-- Filters -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Filter Transactions</h3>
                </div>
                <div class="card-body">
                    <form method="GET" action="<?= url('wallet/transactions/' . $wallet->id) ?>">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Transaction Type</label>
                                    <select name="type" class="form-control">
                                        <option value="">All Types</option>
                                        <option value="credit" <?= $type == 'credit' ? 'selected' : '' ?>>Credit</option>
                                        <option value="debit" <?= $type == 'debit' ? 'selected' : '' ?>>Debit</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Start Date</label>
                                    <input type="date" name="start_date" class="form-control" value="<?= $start_date ?>">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>End Date</label>
                                    <input type="date" name="end_date" class="form-control" value="<?= $end_date ?>">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <button type="submit" class="btn btn-primary btn-block">
                                        <i class="fas fa-filter"></i> Filter
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Transactions Table -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Transaction History</h3>
                    <div class="card-tools">
                        <a href="<?= url('wallet/statement/' . $wallet->id . '?start_date=' . $start_date . '&end_date=' . $end_date) ?>" 
                           class="btn btn-tool" target="_blank">
                            <i class="fas fa-print"></i> Print Statement
                        </a>
                    </div>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Date & Time</th>
                                <th>Type</th>
                                <th>Description</th>
                                <th>Campaign</th>
                                <th>Amount</th>
                                <th>Balance After</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($transactions)): ?>
                                <?php 
                                $runningBalance = $wallet->balance;
                                foreach (array_reverse($transactions) as $txn): 
                                    // Calculate balance after this transaction (going backwards)
                                    if ($txn->transaction_type == 'credit') {
                                        $balanceAfter = $runningBalance;
                                        $runningBalance -= $txn->amount;
                                    } else {
                                        $balanceAfter = $runningBalance;
                                        $runningBalance += $txn->amount;
                                    }
                                ?>
                                <?php endforeach; ?>
                                
                                <?php foreach ($transactions as $txn): ?>
                                    <tr>
                                        <td><?= $txn->id ?></td>
                                        <td><?= formatDate($txn->created_at, 'M d, Y H:i:s') ?></td>
                                        <td>
                                            <span class="badge badge-<?= $txn->transaction_type == 'credit' ? 'success' : 'warning' ?>">
                                                <?= strtoupper($txn->transaction_type) ?>
                                            </span>
                                        </td>
                                        <td><?= htmlspecialchars($txn->description) ?></td>
                                        <td><?= htmlspecialchars($txn->campaign_name ?? '-') ?></td>
                                        <td class="text-<?= $txn->transaction_type == 'credit' ? 'success' : 'warning' ?>">
                                            <strong>
                                                <?= $txn->transaction_type == 'credit' ? '+' : '-' ?>
                                                GHS <?= number_format($txn->amount, 2) ?>
                                            </strong>
                                        </td>
                                        <td>GHS <?= number_format($wallet->balance, 2) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center">No transactions found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

<?php require_once '../app/views/layouts/footer.php'; ?>
