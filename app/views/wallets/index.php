<?php require_once '../app/views/layouts/header.php'; ?>
<?php require_once '../app/views/layouts/sidebar.php'; ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Station Wallets</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url('home') ?>">Home</a></li>
                        <li class="breadcrumb-item active">Wallets</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            
            <?php
            $successMsg = flash('success');
            if ($successMsg):
            ?>
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <i class="icon fas fa-check"></i> <?= htmlspecialchars($successMsg) ?>
                </div>
            <?php endif; ?>

            <!-- Summary Cards -->
            <div class="row">
                <div class="col-lg-4 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>GHS <?= number_format($totalBalance, 2) ?></h3>
                            <p>Total Balance</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-wallet"></i>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>GHS <?= number_format($totalCredits, 2) ?></h3>
                            <p>Total Credits</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-arrow-up"></i>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>GHS <?= number_format($totalDebits, 2) ?></h3>
                            <p>Total Withdrawals</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-arrow-down"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Wallets Table -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">All Station Wallets</h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Station</th>
                                <th>Station Code</th>
                                <th>Balance</th>
                                <th>Total Credits</th>
                                <th>Total Debits</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($wallets)): ?>
                                <?php foreach ($wallets as $wallet): ?>
                                    <tr>
                                        <td><?= $wallet->id ?></td>
                                        <td><?= htmlspecialchars($wallet->station_name) ?></td>
                                        <td><code><?= htmlspecialchars($wallet->station_code) ?></code></td>
                                        <td>
                                            <strong class="text-<?= $wallet->balance > 0 ? 'success' : 'muted' ?>">
                                                GHS <?= number_format($wallet->balance, 2) ?>
                                            </strong>
                                        </td>
                                        <td class="text-success">GHS <?= number_format($wallet->total_credits ?? 0, 2) ?></td>
                                        <td class="text-warning">GHS <?= number_format($wallet->total_debits ?? 0, 2) ?></td>
                                        <td>
                                            <a href="<?= url('wallet/show/' . $wallet->id) ?>" class="btn btn-info btn-sm">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                            <a href="<?= url('wallet/transactions/' . $wallet->id) ?>" class="btn btn-primary btn-sm">
                                                <i class="fas fa-list"></i> Transactions
                                            </a>
                                            <?php if ($wallet->balance > 0): ?>
                                                <a href="<?= url('wallet/withdraw/' . $wallet->id) ?>" class="btn btn-warning btn-sm">
                                                    <i class="fas fa-money-bill-wave"></i> Withdraw
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center">No wallets found</td>
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
