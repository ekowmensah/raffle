<?php require_once '../app/views/layouts/header.php'; ?>
<?php require_once '../app/views/layouts/sidebar.php'; ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Profitability Analysis</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url('home') ?>">Home</a></li>
                        <li class="breadcrumb-item active">Profitability</li>
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
                    <form method="GET" action="<?= url('financial/profitability') ?>">
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

            <!-- Revenue Breakdown -->
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-primary">
                            <h3 class="card-title">Revenue Breakdown</h3>
                        </div>
                        <div class="card-body">
                            <table class="table">
                                <tr>
                                    <th>Total Revenue</th>
                                    <td class="text-right">
                                        <strong class="text-primary">GHS <?= number_format($analysis['total_revenue'], 2) ?></strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Platform Commission</td>
                                    <td class="text-right">GHS <?= number_format($analysis['platform_commission'], 2) ?></td>
                                </tr>
                                <tr>
                                    <td>Station Commission</td>
                                    <td class="text-right">GHS <?= number_format($analysis['station_commission'], 2) ?></td>
                                </tr>
                                <tr>
                                    <td>Programme Commission</td>
                                    <td class="text-right">GHS <?= number_format($analysis['programme_commission'], 2) ?></td>
                                </tr>
                                <tr>
                                    <td>Prize Pool Allocated</td>
                                    <td class="text-right">GHS <?= number_format($analysis['prize_pool_allocated'], 2) ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-success">
                            <h3 class="card-title">Prize Pool Status</h3>
                        </div>
                        <div class="card-body">
                            <table class="table">
                                <tr>
                                    <th>Prize Pool Allocated</th>
                                    <td class="text-right">
                                        <strong>GHS <?= number_format($analysis['prize_pool_allocated'], 2) ?></strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Prizes Paid Out</td>
                                    <td class="text-right text-danger">GHS <?= number_format($analysis['prizes_paid'], 2) ?></td>
                                </tr>
                                <tr class="bg-light">
                                    <th>Prize Pool Remaining</th>
                                    <td class="text-right">
                                        <strong class="text-success">GHS <?= number_format($analysis['prize_pool_remaining'], 2) ?></strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <div class="progress">
                                            <?php 
                                            $paidPercentage = $analysis['prize_pool_allocated'] > 0 
                                                ? ($analysis['prizes_paid'] / $analysis['prize_pool_allocated']) * 100 
                                                : 0;
                                            ?>
                                            <div class="progress-bar bg-danger" style="width: <?= $paidPercentage ?>%">
                                                <?= number_format($paidPercentage, 1) ?>% Paid
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Profitability Metrics -->
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>GHS <?= number_format($analysis['total_revenue'], 2) ?></h3>
                            <p>Total Revenue</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>GHS <?= number_format($analysis['platform_commission'], 2) ?></h3>
                            <p>Platform Earnings</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3><?= number_format($analysis['platform_profit_margin'], 2) ?>%</h3>
                            <p>Platform Margin</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-percentage"></i>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3>GHS <?= number_format($analysis['prize_pool_remaining'], 2) ?></h3>
                            <p>Prize Pool Balance</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-trophy"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Commission Distribution Chart -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Commission Distribution</h3>
                </div>
                <div class="card-body">
                    <canvas id="commissionChart" height="80"></canvas>
                </div>
            </div>
        </div>
    </section>
</div>

<script src="<?= vendor('chart.js/Chart.min.js') ?>"></script>
<script>
$(document).ready(function() {
    var ctx = document.getElementById('commissionChart').getContext('2d');
    var chart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ['Platform', 'Station', 'Programme', 'Prize Pool'],
            datasets: [{
                data: [
                    <?= $analysis['platform_commission'] ?>,
                    <?= $analysis['station_commission'] ?>,
                    <?= $analysis['programme_commission'] ?>,
                    <?= $analysis['prize_pool_allocated'] ?>
                ],
                backgroundColor: [
                    '#007bff',
                    '#28a745',
                    '#ffc107',
                    '#dc3545'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            legend: {
                position: 'bottom'
            }
        }
    });
});
</script>

<?php require_once '../app/views/layouts/footer.php'; ?>
