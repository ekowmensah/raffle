<?php require_once '../app/views/layouts/header.php'; ?>
<?php require_once '../app/views/layouts/sidebar.php'; ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Wallet Details</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url('home') ?>">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= url('wallet') ?>">Wallets</a></li>
                        <li class="breadcrumb-item active">View</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            
            <?php
            $successMsg = flash('success');
            $errorMsg = flash('error');
            if ($successMsg):
            ?>
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <i class="icon fas fa-check"></i> <?= htmlspecialchars($successMsg) ?>
                </div>
            <?php endif; ?>
            
            <?php if ($errorMsg): ?>
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <i class="icon fas fa-ban"></i> <?= htmlspecialchars($errorMsg) ?>
                </div>
            <?php endif; ?>

            <div class="row">
                <!-- Wallet Info -->
                <div class="col-md-4">
                    <div class="card card-primary card-outline">
                        <div class="card-body box-profile">
                            <div class="text-center">
                                <i class="fas fa-wallet fa-5x text-primary mb-3"></i>
                            </div>

                            <h3 class="profile-username text-center"><?= htmlspecialchars($wallet->station_name) ?></h3>
                            <p class="text-muted text-center"><?= htmlspecialchars($wallet->station_code) ?></p>

                            <ul class="list-group list-group-unbordered mb-3">
                                <li class="list-group-item">
                                    <b>Current Balance</b>
                                    <a class="float-right">
                                        <strong class="text-success">GHS <?= number_format($wallet->balance, 2) ?></strong>
                                    </a>
                                </li>
                                <li class="list-group-item">
                                    <b>Total Credits</b>
                                    <a class="float-right text-success">GHS <?= number_format($summary->total_credits ?? 0, 2) ?></a>
                                </li>
                                <li class="list-group-item">
                                    <b>Total Debits</b>
                                    <a class="float-right text-warning">GHS <?= number_format($summary->total_debits ?? 0, 2) ?></a>
                                </li>
                                <li class="list-group-item">
                                    <b>Total Transactions</b>
                                    <a class="float-right"><?= number_format($summary->total_transactions ?? 0) ?></a>
                                </li>
                                <li class="list-group-item">
                                    <b>Status</b>
                                    <a class="float-right">
                                        <span class="badge badge-<?= $wallet->station_active ? 'success' : 'danger' ?>">
                                            <?= $wallet->station_active ? 'Active' : 'Inactive' ?>
                                        </span>
                                    </a>
                                </li>
                            </ul>

                            <a href="<?= url('wallet/withdraw/' . $wallet->id) ?>" class="btn btn-warning btn-block">
                                <i class="fas fa-money-bill-wave"></i> <b>Withdraw Funds</b>
                            </a>
                            <a href="<?= url('wallet/statement/' . $wallet->id) ?>" class="btn btn-primary btn-block">
                                <i class="fas fa-file-invoice"></i> <b>Generate Statement</b>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Recent Transactions -->
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Recent Transactions</h3>
                            <div class="card-tools">
                                <a href="<?= url('wallet/show/' . $wallet->id) ?>" class="btn btn-default">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Type</th>
                                        <th>Description</th>
                                        <th>Campaign</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($transactions)): ?>
                                        <?php foreach ($transactions as $txn): ?>
                                            <tr>
                                                <td><?= formatDate($txn->created_at, 'M d, Y H:i') ?></td>
                                                <td>
                                                    <span class="badge badge-<?= $txn->transaction_type == 'credit' ? 'success' : 'warning' ?>">
                                                        <?= strtoupper($txn->transaction_type) ?>
                                                    </span>
                                                </td>
                                                <td><?= htmlspecialchars($txn->description) ?></td>
                                                <td><?= htmlspecialchars($txn->campaign_name ?? '-') ?></td>
                                                <td class="text-<?= $txn->transaction_type == 'credit' ? 'success' : 'warning' ?>">
                                                    <?= $txn->transaction_type == 'credit' ? '+' : '-' ?>
                                                    GHS <?= number_format($txn->amount, 2) ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="text-center">No transactions found</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php require_once '../app/views/layouts/footer.php'; ?>
