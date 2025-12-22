<?php require_once '../app/views/layouts/header.php'; ?>
<?php require_once '../app/views/layouts/sidebar.php'; ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0"><i class="fas fa-tachometer-alt"></i> Super Admin Dashboard</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url('home') ?>">Home</a></li>
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            
            <!-- Revenue Overview Cards -->
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-gradient-success">
                        <div class="inner">
                            <h3>GHS <?= number_format($revenue['gross_revenue'] ?? 0, 2) ?></h3>
                            <p>Gross Revenue</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                        <a href="<?= url('payment') ?>" class="small-box-footer">
                            All Payments <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-gradient-danger">
                        <div class="inner">
                            <h3>GHS <?= number_format($revenue['platform_revenue'] ?? 0, 2) ?></h3>
                            <p>Platform Revenue (<?= $revenue['platform_percent'] ?? 30 ?>%)</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-building"></i>
                        </div>
                        <div class="small-box-footer">
                            Platform Share
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-gradient-info">
                        <div class="inner">
                            <h3>GHS <?= number_format($revenue['station_revenue'] ?? 0, 2) ?></h3>
                            <p>Station Revenue (<?= $revenue['station_percent'] ?? 20 ?>%)</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-broadcast-tower"></i>
                        </div>
                        <a href="<?= url('station') ?>" class="small-box-footer">
                            View Stations <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-gradient-warning">
                        <div class="inner">
                            <h3>GHS <?= number_format($revenue['prize_pool'] ?? 0, 2) ?></h3>
                            <p>Prize Pool (<?= $revenue['prize_pool_percent'] ?? 50 ?>%)</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-trophy"></i>
                        </div>
                        <a href="<?= url('draw') ?>" class="small-box-footer">
                            View Draws <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Primary Stats -->
            <div class="row">
                <div class="col-lg-2 col-6">
                    <div class="info-box bg-gradient-primary">
                        <span class="info-box-icon"><i class="fas fa-broadcast-tower"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Stations</span>
                            <span class="info-box-number"><?= $stats['total_stations'] ?? 0 ?></span>
                            <small><?= $stats['active_stations'] ?? 0 ?> Active</small>
                        </div>
                    </div>
                </div>

                <div class="col-lg-2 col-6">
                    <div class="info-box bg-gradient-success">
                        <span class="info-box-icon"><i class="fas fa-bullhorn"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Campaigns</span>
                            <span class="info-box-number"><?= $stats['total_campaigns'] ?? 0 ?></span>
                            <small><?= $stats['active_campaigns'] ?? 0 ?> Active</small>
                        </div>
                    </div>
                </div>

                <div class="col-lg-2 col-6">
                    <div class="info-box bg-gradient-info">
                        <span class="info-box-icon"><i class="fas fa-ticket-alt"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Tickets</span>
                            <span class="info-box-number"><?= number_format($stats['total_tickets'] ?? 0) ?></span>
                            <small><?= $stats['tickets_today'] ?? 0 ?> Today</small>
                        </div>
                    </div>
                </div>

                <div class="col-lg-2 col-6">
                    <div class="info-box bg-gradient-warning">
                        <span class="info-box-icon"><i class="fas fa-users"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Players</span>
                            <span class="info-box-number"><?= number_format($stats['total_players'] ?? 0) ?></span>
                            <small><?= $stats['new_players_today'] ?? 0 ?> New Today</small>
                        </div>
                    </div>
                </div>

                <div class="col-lg-2 col-6">
                    <div class="info-box bg-gradient-danger">
                        <span class="info-box-icon"><i class="fas fa-trophy"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Draws</span>
                            <span class="info-box-number"><?= $stats['completed_draws'] ?? 0 ?></span>
                            <small><?= $stats['pending_draws'] ?? 0 ?> Pending</small>
                        </div>
                    </div>
                </div>

                <div class="col-lg-2 col-6">
                    <div class="info-box bg-gradient-secondary">
                        <span class="info-box-icon"><i class="fas fa-hand-holding-usd"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Withdrawals</span>
                            <span class="info-box-number"><?= $stats['pending_withdrawals'] ?? 0 ?></span>
                            <small>Pending Review</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Revenue Breakdown and Prize Pool -->
            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-chart-pie"></i> Revenue Distribution</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <canvas id="revenueDistributionChart" height="250"></canvas>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Category</th>
                                                <th class="text-right">Amount</th>
                                                <th class="text-right">%</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><span class="badge badge-danger">Platform</span></td>
                                                <td class="text-right"><strong>GHS <?= number_format($revenue['platform_revenue'] ?? 0, 2) ?></strong></td>
                                                <td class="text-right"><?= $revenue['platform_percent'] ?? 30 ?>%</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-info">Stations</span></td>
                                                <td class="text-right"><strong>GHS <?= number_format($revenue['station_revenue'] ?? 0, 2) ?></strong></td>
                                                <td class="text-right"><?= $revenue['station_percent'] ?? 20 ?>%</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-secondary">Programmes</span></td>
                                                <td class="text-right"><strong>GHS <?= number_format($revenue['programme_revenue'] ?? 0, 2) ?></strong></td>
                                                <td class="text-right"><?= $revenue['programme_percent'] ?? 0 ?>%</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge badge-warning">Prize Pool</span></td>
                                                <td class="text-right"><strong>GHS <?= number_format($revenue['prize_pool'] ?? 0, 2) ?></strong></td>
                                                <td class="text-right"><?= $revenue['prize_pool_percent'] ?? 50 ?>%</td>
                                            </tr>
                                            <tr class="bg-light">
                                                <th>Total</th>
                                                <th class="text-right">GHS <?= number_format($revenue['gross_revenue'] ?? 0, 2) ?></th>
                                                <th class="text-right">100%</th>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-trophy"></i> Prize Pool Status</h3>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <small class="text-muted">Total Prize Pool</small>
                                <h3 class="text-warning">GHS <?= number_format($prizePool['total'] ?? 0, 2) ?></h3>
                            </div>
                            <div class="progress mb-2" style="height: 25px;">
                                <div class="progress-bar bg-success" style="width: <?= $prizePool['daily_percent'] ?? 0 ?>%">
                                    Daily <?= $prizePool['daily_percent'] ?? 0 ?>%
                                </div>
                                <div class="progress-bar bg-warning" style="width: <?= $prizePool['final_percent'] ?? 0 ?>%">
                                    Final <?= $prizePool['final_percent'] ?? 0 ?>%
                                </div>
                                <div class="progress-bar bg-info" style="width: <?= $prizePool['bonus_percent'] ?? 0 ?>%">
                                    Bonus <?= $prizePool['bonus_percent'] ?? 0 ?>%
                                </div>
                            </div>
                            <table class="table table-sm">
                                <tr>
                                    <td>Daily Draws</td>
                                    <td class="text-right"><strong>GHS <?= number_format($prizePool['daily'] ?? 0, 2) ?></strong></td>
                                </tr>
                                <tr>
                                    <td>Final Draws</td>
                                    <td class="text-right"><strong>GHS <?= number_format($prizePool['final'] ?? 0, 2) ?></strong></td>
                                </tr>
                                <tr>
                                    <td>Bonus Pool</td>
                                    <td class="text-right"><strong>GHS <?= number_format($prizePool['bonus'] ?? 0, 2) ?></strong></td>
                                </tr>
                                <tr class="bg-light">
                                    <td><strong>Paid Out</strong></td>
                                    <td class="text-right"><strong class="text-danger">GHS <?= number_format($prizePool['paid_out'] ?? 0, 2) ?></strong></td>
                                </tr>
                                <tr class="bg-success">
                                    <td><strong>Remaining</strong></td>
                                    <td class="text-right"><strong class="text-white">GHS <?= number_format($prizePool['remaining'] ?? 0, 2) ?></strong></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Revenue Trend -->
            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-chart-line"></i> Revenue Trend (Last 30 Days)</h3>
                        </div>
                        <div class="card-body">
                            <canvas id="revenueTrendChart" height="100"></canvas>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-medal"></i> Player Loyalty</h3>
                        </div>
                        <div class="card-body">
                            <canvas id="loyaltyChart" height="200"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top Performers -->
            <div class="row">
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-star"></i> Top Stations</h3>
                        </div>
                        <div class="card-body p-0">
                            <?php if (!empty($topStations)): ?>
                                <table class="table table-sm">
                                    <tbody>
                                        <?php foreach ($topStations as $station): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($station->name) ?></td>
                                                <td class="text-right">
                                                    <strong>GHS <?= number_format($station->revenue, 2) ?></strong>
                                                    <br><small class="text-muted"><?= $station->ticket_count ?> tickets</small>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <p class="text-center text-muted p-3">No data available</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-fire"></i> Top Campaigns</h3>
                        </div>
                        <div class="card-body p-0">
                            <?php if (!empty($topCampaigns)): ?>
                                <table class="table table-sm">
                                    <tbody>
                                        <?php foreach ($topCampaigns as $campaign): ?>
                                            <tr>
                                                <td>
                                                    <?= htmlspecialchars(substr($campaign->name, 0, 20)) ?><?= strlen($campaign->name) > 20 ? '...' : '' ?>
                                                    <br><small class="text-muted"><?= htmlspecialchars($campaign->station_name) ?></small>
                                                </td>
                                                <td class="text-right">
                                                    <strong>GHS <?= number_format($campaign->revenue, 2) ?></strong>
                                                    <br><small class="text-muted"><?= $campaign->ticket_count ?> tickets</small>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <p class="text-center text-muted p-3">No data available</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-user-star"></i> Top Players</h3>
                        </div>
                        <div class="card-body p-0">
                            <?php if (!empty($topPlayers)): ?>
                                <table class="table table-sm">
                                    <tbody>
                                        <?php foreach ($topPlayers as $player): ?>
                                            <tr>
                                                <td>
                                                    <?= htmlspecialchars($player->phone) ?>
                                                    <br><small class="text-muted"><?= $player->loyalty_level ?></small>
                                                </td>
                                                <td class="text-right">
                                                    <strong>GHS <?= number_format($player->total_spent, 2) ?></strong>
                                                    <br><small class="text-muted"><?= $player->total_tickets ?> tickets</small>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <p class="text-center text-muted p-3">No data available</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="row">
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-clock"></i> Recent Payments</h3>
                            <div class="card-tools">
                                <a href="<?= url('payment') ?>" class="btn btn-tool btn-sm">View All</a>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <?php if (!empty($recentPayments)): ?>
                                <table class="table table-sm table-striped">
                                    <tbody>
                                        <?php foreach (array_slice($recentPayments, 0, 5) as $payment): ?>
                                            <tr>
                                                <td>
                                                    <small><code><?= htmlspecialchars($payment->internal_reference) ?></code></small>
                                                    <br><small class="text-muted"><?= htmlspecialchars($payment->campaign_name) ?></small>
                                                </td>
                                                <td class="text-right">
                                                    <strong>GHS <?= number_format($payment->amount, 2) ?></strong>
                                                    <br><small class="text-muted"><?= formatDate($payment->created_at, 'M d, H:i') ?></small>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <p class="text-center text-muted p-3">No recent payments</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-hand-holding-usd"></i> Pending Withdrawals</h3>
                            <div class="card-tools">
                                <a href="<?= url('withdrawal') ?>" class="btn btn-tool btn-sm">View All</a>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <?php if (!empty($pendingWithdrawals)): ?>
                                <table class="table table-sm table-striped">
                                    <tbody>
                                        <?php foreach (array_slice($pendingWithdrawals, 0, 5) as $withdrawal): ?>
                                            <tr>
                                                <td>
                                                    <strong><?= htmlspecialchars($withdrawal->station_name) ?></strong>
                                                    <br><small class="text-muted"><?= htmlspecialchars($withdrawal->requested_by_name) ?></small>
                                                </td>
                                                <td class="text-right">
                                                    <strong class="text-warning">GHS <?= number_format($withdrawal->amount, 2) ?></strong>
                                                    <br><small class="text-muted"><?= formatDate($withdrawal->created_at, 'M d, H:i') ?></small>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <p class="text-center text-muted p-3">No pending withdrawals</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
// Revenue Distribution Pie Chart
const revenueData = {
    labels: ['Platform', 'Stations', 'Programmes', 'Prize Pool'],
    datasets: [{
        data: [
            <?= $revenue['platform_revenue'] ?? 0 ?>,
            <?= $revenue['station_revenue'] ?? 0 ?>,
            <?= $revenue['programme_revenue'] ?? 0 ?>,
            <?= $revenue['prize_pool'] ?? 0 ?>
        ],
        backgroundColor: [
            'rgba(220, 53, 69, 0.8)',
            'rgba(23, 162, 184, 0.8)',
            'rgba(108, 117, 125, 0.8)',
            'rgba(255, 193, 7, 0.8)'
        ]
    }]
};

new Chart(document.getElementById('revenueDistributionChart'), {
    type: 'doughnut',
    data: revenueData,
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

// Revenue Trend Chart
fetch('<?= url('analytics/getRevenueTrend') ?>')
    .then(response => response.json())
    .then(data => {
        new Chart(document.getElementById('revenueTrendChart'), {
            type: 'line',
            data: {
                labels: data.labels,
                datasets: [{
                    label: 'Revenue (GHS)',
                    data: data.revenue,
                    borderColor: 'rgb(40, 167, 69)',
                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'GHS ' + value.toFixed(2);
                            }
                        }
                    }
                }
            }
        });
    });

// Loyalty Distribution Chart
fetch('<?= url('analytics/getLoyaltyDistribution') ?>')
    .then(response => response.json())
    .then(data => {
        new Chart(document.getElementById('loyaltyChart'), {
            type: 'doughnut',
            data: {
                labels: data.labels,
                datasets: [{
                    data: data.counts,
                    backgroundColor: [
                        'rgba(108, 117, 125, 0.8)',
                        'rgba(192, 192, 192, 0.8)',
                        'rgba(255, 193, 7, 0.8)',
                        'rgba(138, 43, 226, 0.8)'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    });
</script>

<?php require_once '../app/views/layouts/footer.php'; ?>
