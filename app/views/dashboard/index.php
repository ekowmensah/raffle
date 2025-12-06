<?php require_once '../app/views/layouts/header.php'; ?>
<?php require_once '../app/views/layouts/sidebar.php'; ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Dashboard</h1>
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
            
            <?php if (flash('success')): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <i class="icon fas fa-check"></i> <?= flash('success') ?>
                </div>
            <?php endif; ?>

            <!-- Primary Stats Row -->
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3><?= number_format($stats['active_campaigns'] ?? 0) ?></h3>
                            <p>Active Campaigns</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-trophy"></i>
                        </div>
                        <a href="<?= url('campaign') ?>" class="small-box-footer">
                            View All <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3><?= number_format($stats['total_tickets'] ?? 0) ?></h3>
                            <p>Tickets Sold</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-ticket-alt"></i>
                        </div>
                        <a href="<?= url('ticket') ?>" class="small-box-footer">
                            View All <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>GHS <?= number_format($stats['total_revenue'] ?? 0, 2) ?></h3>
                            <p>Total Revenue</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                        <a href="<?= url('payment') ?>" class="small-box-footer">
                            View Payments <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3><?= number_format($stats['total_players'] ?? 0) ?></h3>
                            <p>Total Players</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <a href="<?= url('player') ?>" class="small-box-footer">
                            View All <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Secondary Stats Row -->
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="info-box">
                        <span class="info-box-icon bg-primary"><i class="fas fa-broadcast-tower"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Active Stations</span>
                            <span class="info-box-number"><?= number_format($stats['active_stations'] ?? 0) ?></span>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="info-box">
                        <span class="info-box-icon bg-secondary"><i class="fas fa-calendar-check"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Campaigns</span>
                            <span class="info-box-number"><?= number_format($stats['total_campaigns'] ?? 0) ?></span>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="info-box">
                        <span class="info-box-icon bg-warning"><i class="fas fa-clock"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Pending Draws</span>
                            <span class="info-box-number"><?= number_format($stats['pending_draws'] ?? 0) ?></span>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="info-box">
                        <span class="info-box-icon bg-success"><i class="fas fa-check-circle"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Completed Draws</span>
                            <span class="info-box-number"><?= number_format($stats['completed_draws'] ?? 0) ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Activity Row -->
            <div class="row">
                <!-- Recent Payments -->
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header border-0">
                            <h3 class="card-title">
                                <i class="fas fa-money-bill-wave mr-1"></i>
                                Recent Payments
                            </h3>
                            <div class="card-tools">
                                <a href="<?= url('payment') ?>" class="btn btn-tool btn-sm">
                                    <i class="fas fa-list"></i>
                                </a>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <?php if (!empty($recentPayments)): ?>
                                <table class="table table-striped table-valign-middle">
                                    <thead>
                                        <tr>
                                            <th>Reference</th>
                                            <th>Amount</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($recentPayments as $payment): ?>
                                            <tr>
                                                <td><small><?= htmlspecialchars($payment->internal_reference ?? 'N/A') ?></small></td>
                                                <td><strong><?= $payment->currency ?> <?= number_format($payment->amount, 2) ?></strong></td>
                                                <td><small><?= formatDate($payment->created_at, 'M d, H:i') ?></small></td>
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

                <!-- Upcoming Draws -->
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header border-0">
                            <h3 class="card-title">
                                <i class="fas fa-calendar-alt mr-1"></i>
                                Upcoming Draws
                            </h3>
                            <div class="card-tools">
                                <a href="<?= url('draw/pending') ?>" class="btn btn-tool btn-sm">
                                    <i class="fas fa-list"></i>
                                </a>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <?php if (!empty($upcomingDraws)): ?>
                                <table class="table table-striped table-valign-middle">
                                    <thead>
                                        <tr>
                                            <th>Campaign</th>
                                            <th>Type</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($upcomingDraws as $draw): ?>
                                            <tr>
                                                <td><small><?= htmlspecialchars($draw->campaign_name ?? 'N/A') ?></small></td>
                                                <td>
                                                    <span class="badge badge-<?= $draw->draw_type == 'daily' ? 'info' : 'warning' ?>">
                                                        <?= strtoupper($draw->draw_type) ?>
                                                    </span>
                                                </td>
                                                <td><small><?= formatDate($draw->draw_date, 'M d, Y') ?></small></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <p class="text-center text-muted p-3">No upcoming draws</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="row">
                <!-- Revenue Trend Chart -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header border-0">
                            <h3 class="card-title">
                                <i class="fas fa-chart-line mr-1"></i>
                                Revenue Trend (Last 30 Days)
                            </h3>
                        </div>
                        <div class="card-body">
                            <canvas id="revenueTrendChart" height="200"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Ticket Sales Chart -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header border-0">
                            <h3 class="card-title">
                                <i class="fas fa-ticket-alt mr-1"></i>
                                Ticket Sales (Last 30 Days)
                            </h3>
                        </div>
                        <div class="card-body">
                            <canvas id="ticketSalesChart" height="200"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- More Charts Row -->
            <div class="row">
                <!-- Loyalty Distribution -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header border-0">
                            <h3 class="card-title">
                                <i class="fas fa-users mr-1"></i>
                                Player Loyalty Levels
                            </h3>
                        </div>
                        <div class="card-body">
                            <canvas id="loyaltyChart" height="200"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Hourly Sales Pattern -->
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header border-0">
                            <h3 class="card-title">
                                <i class="fas fa-clock mr-1"></i>
                                Hourly Sales Pattern (Last 7 Days)
                            </h3>
                        </div>
                        <div class="card-body">
                            <canvas id="hourlySalesChart" height="150"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Tickets -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header border-0">
                            <h3 class="card-title">
                                <i class="fas fa-ticket-alt mr-1"></i>
                                Recent Tickets
                            </h3>
                            <div class="card-tools">
                                <a href="<?= url('ticket') ?>" class="btn btn-tool btn-sm">
                                    <i class="fas fa-list"></i>
                                </a>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <?php if (!empty($recentTickets)): ?>
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Ticket Code</th>
                                            <th>Player</th>
                                            <th>Campaign</th>
                                            <th>Quantity</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($recentTickets as $ticket): ?>
                                            <tr>
                                                <td><code><?= htmlspecialchars($ticket->ticket_code) ?></code></td>
                                                <td><?= htmlspecialchars($ticket->player_phone ?? 'N/A') ?></td>
                                                <td><?= htmlspecialchars($ticket->campaign_name ?? 'N/A') ?></td>
                                                <td><span class="badge badge-primary"><?= $ticket->quantity ?? 1 ?></span></td>
                                                <td><?= formatDate($ticket->created_at, 'M d, H:i') ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <p class="text-center text-muted p-3">No recent tickets</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
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
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
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

// Ticket Sales Chart
fetch('<?= url('analytics/getTicketSalesTrend') ?>')
    .then(response => response.json())
    .then(data => {
        new Chart(document.getElementById('ticketSalesChart'), {
            type: 'bar',
            data: {
                labels: data.labels,
                datasets: [{
                    label: 'Tickets Sold',
                    data: data.tickets,
                    backgroundColor: 'rgba(54, 162, 235, 0.7)',
                    borderColor: 'rgb(54, 162, 235)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
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
                        'rgba(128, 128, 128, 0.7)', // Bronze
                        'rgba(192, 192, 192, 0.7)', // Silver
                        'rgba(255, 215, 0, 0.7)',   // Gold
                        'rgba(138, 43, 226, 0.7)'   // Platinum
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom'
                    }
                }
            }
        });
    });

// Hourly Sales Pattern Chart
fetch('<?= url('analytics/getHourlySalesPattern') ?>')
    .then(response => response.json())
    .then(data => {
        new Chart(document.getElementById('hourlySalesChart'), {
            type: 'line',
            data: {
                labels: data.labels,
                datasets: [{
                    label: 'Transactions',
                    data: data.transactions,
                    borderColor: 'rgb(255, 99, 132)',
                    backgroundColor: 'rgba(255, 99, 132, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    });
</script>

<?php require_once '../app/views/layouts/footer.php'; ?>
