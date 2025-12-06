<?php require_once '../app/views/layouts/header.php'; ?>
<?php require_once '../app/views/layouts/sidebar.php'; ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Payout Report</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url('home') ?>">Home</a></li>
                        <li class="breadcrumb-item active">Payout Report</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            
            <!-- Filter Card -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Filter Options</h3>
                </div>
                <div class="card-body">
                    <form method="GET" action="<?= url('financial/payouts') ?>">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Campaign</label>
                                    <select name="campaign" class="form-control">
                                        <option value="">All Campaigns</option>
                                        <?php foreach ($campaigns as $campaign): ?>
                                            <option value="<?= $campaign->id ?>" <?= $selected_campaign == $campaign->id ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($campaign->name) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Status</label>
                                    <select name="status" class="form-control">
                                        <option value="">All Status</option>
                                        <option value="pending" <?= $selected_status == 'pending' ? 'selected' : '' ?>>Pending</option>
                                        <option value="paid" <?= $selected_status == 'paid' ? 'selected' : '' ?>>Paid</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Start Date</label>
                                    <input type="date" name="start_date" class="form-control" value="<?= $start_date ?>">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>End Date</label>
                                    <input type="date" name="end_date" class="form-control" value="<?= $end_date ?>">
                                </div>
                            </div>
                            <div class="col-md-2">
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

            <!-- Summary Cards -->
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3><?= $summary['total_winners'] ?></h3>
                            <p>Total Winners</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-trophy"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>GHS <?= number_format($summary['total_amount'], 2) ?></h3>
                            <p>Total Prize Amount</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>GHS <?= number_format($summary['paid_amount'], 2) ?></h3>
                            <p>Paid Amount</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3>GHS <?= number_format($summary['pending_amount'], 2) ?></h3>
                            <p>Pending Amount</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payout Table -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Payout Details</h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>Draw Date</th>
                                <th>Campaign</th>
                                <th>Draw Type</th>
                                <th>Rank</th>
                                <th>Player</th>
                                <th>Phone</th>
                                <th>Ticket</th>
                                <th>Prize Amount</th>
                                <th>Status</th>
                                <th>Paid Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($payouts)): ?>
                                <?php foreach ($payouts as $payout): ?>
                                    <tr>
                                        <td><?= formatDate($payout->draw_date, 'M d, Y') ?></td>
                                        <td><?= htmlspecialchars($payout->campaign_name) ?></td>
                                        <td>
                                            <span class="badge badge-<?= $payout->draw_type == 'daily' ? 'info' : ($payout->draw_type == 'final' ? 'warning' : 'success') ?>">
                                                <?= strtoupper($payout->draw_type) ?>
                                            </span>
                                        </td>
                                        <td><?= $payout->prize_rank ?></td>
                                        <td><?= htmlspecialchars($payout->player_name) ?></td>
                                        <td><?= htmlspecialchars($payout->player_phone) ?></td>
                                        <td><code><?= htmlspecialchars($payout->ticket_code) ?></code></td>
                                        <td><strong>GHS <?= number_format($payout->prize_amount, 2) ?></strong></td>
                                        <td>
                                            <span class="badge badge-<?= $payout->prize_paid_status == 'paid' ? 'success' : 'warning' ?>">
                                                <?= strtoupper($payout->prize_paid_status) ?>
                                            </span>
                                        </td>
                                        <td><?= $payout->prize_paid_at ? formatDate($payout->prize_paid_at, 'M d, Y') : '-' ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="10" class="text-center">No payouts found</td>
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
