<?php require_once '../app/views/layouts/header.php'; ?>
<?php require_once '../app/views/layouts/sidebar.php'; ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        <i class="fas fa-money-bill-wave"></i> Finance Dashboard
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url('home') ?>">Home</a></li>
                        <li class="breadcrumb-item active">Finance Dashboard</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <!-- Stats Row -->
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>GHS <?= number_format($data['stats']['today_revenue'] ?? 0, 2) ?></h3>
                            <p>Today's Revenue</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                        <a href="<?= url('payment') ?>" class="small-box-footer">
                            View Details <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3><?= $data['stats']['pending_payments'] ?? 0 ?></h3>
                            <p>Pending Payments</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <a href="<?= url('payment?status=pending') ?>" class="small-box-footer">
                            View Pending <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3><?= $data['stats']['successful_today'] ?? 0 ?></h3>
                            <p>Successful Today</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <a href="<?= url('payment') ?>" class="small-box-footer">
                            View Payments <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-primary">
                        <div class="inner">
                            <h3>GHS <?= number_format($data['stats']['total_revenue'] ?? 0, 2) ?></h3>
                            <p>Total Revenue</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <a href="<?= url('payment') ?>" class="small-box-footer">
                            View Reports <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Recent Payments -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-list"></i> Recent Payments
                            </h3>
                            <div class="card-tools">
                                <a href="<?= url('payment') ?>" class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye"></i> View All
                                </a>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <?php if (!empty($data['recent_payments'])): ?>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Player</th>
                                                <th>Campaign</th>
                                                <th>Amount</th>
                                                <th>Method</th>
                                                <th>Status</th>
                                                <th>Date</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($data['recent_payments'] as $payment): ?>
                                                <tr>
                                                    <td><code>#<?= $payment->id ?></code></td>
                                                    <td>
                                                        <?= htmlspecialchars($payment->player_name ?? 'N/A') ?>
                                                        <br>
                                                        <small class="text-muted"><?= htmlspecialchars($payment->player_phone ?? '') ?></small>
                                                    </td>
                                                    <td><?= htmlspecialchars($payment->campaign_name ?? 'N/A') ?></td>
                                                    <td><strong>GHS <?= number_format($payment->amount, 2) ?></strong></td>
                                                    <td>
                                                        <span class="badge badge-info">
                                                            <?= ucfirst($payment->payment_method ?? 'N/A') ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <?php if ($payment->status === 'success'): ?>
                                                            <span class="badge badge-success">
                                                                <i class="fas fa-check"></i> Success
                                                            </span>
                                                        <?php elseif ($payment->status === 'pending'): ?>
                                                            <span class="badge badge-warning">
                                                                <i class="fas fa-clock"></i> Pending
                                                            </span>
                                                        <?php else: ?>
                                                            <span class="badge badge-danger">
                                                                <i class="fas fa-times"></i> <?= ucfirst($payment->status) ?>
                                                            </span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?= date('M d, Y', strtotime($payment->created_at)) ?>
                                                        <br>
                                                        <small class="text-muted"><?= date('h:i A', strtotime($payment->created_at)) ?></small>
                                                    </td>
                                                    <td>
                                                        <a href="<?= url('payment/view/' . $payment->id) ?>" class="btn btn-sm btn-info">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="p-4 text-center text-muted">
                                    <i class="fas fa-money-bill-wave fa-3x mb-3"></i>
                                    <p>No recent payments</p>
                                </div>
                            <?php endif; ?>
                        </div>
                        <?php if (!empty($data['recent_payments'])): ?>
                            <div class="card-footer clearfix">
                                <a href="<?= url('payment') ?>" class="btn btn-sm btn-primary float-right">
                                    View All Payments
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="row">
                <div class="col-12">
                    <div class="card bg-gradient-success">
                        <div class="card-body">
                            <h5 class="text-white"><i class="fas fa-bolt"></i> Quick Actions</h5>
                            <div class="btn-group" role="group">
                                <a href="<?= url('payment') ?>" class="btn btn-light">
                                    <i class="fas fa-list"></i> All Payments
                                </a>
                                <a href="<?= url('payment?status=pending') ?>" class="btn btn-light">
                                    <i class="fas fa-clock"></i> Pending Payments
                                </a>
                                <a href="<?= url('payment?status=success') ?>" class="btn btn-light">
                                    <i class="fas fa-check"></i> Successful Payments
                                </a>
                                <a href="<?= url('payment/export') ?>" class="btn btn-light">
                                    <i class="fas fa-download"></i> Export Report
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php require_once '../app/views/layouts/footer.php'; ?>
