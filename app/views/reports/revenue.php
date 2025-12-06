<?php require_once '../app/views/layouts/header.php'; ?>
<?php require_once '../app/views/layouts/sidebar.php'; ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Revenue Allocation Report</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url('home') ?>">Home</a></li>
                        <li class="breadcrumb-item active">Revenue Report</li>
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
                    <form method="GET" action="<?= url('report/revenue') ?>">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Station</label>
                                    <select name="station" id="stationFilter" class="form-control" onchange="loadProgrammesForReport()">
                                        <option value="">All Stations</option>
                                        <?php
                                        $stationModel = new \App\Models\Station();
                                        $stations = $stationModel->getActive();
                                        foreach ($stations as $station):
                                        ?>
                                            <option value="<?= $station->id ?>"><?= htmlspecialchars($station->name) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Programme</label>
                                    <select name="programme" id="programmeFilter" class="form-control">
                                        <option value="">All Programmes</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
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
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Start Date</label>
                                    <input type="date" name="start_date" class="form-control" value="<?= $start_date ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>End Date</label>
                                    <input type="date" name="end_date" class="form-control" value="<?= $end_date ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
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
                            <h3>GHS <?= number_format($grand_totals['platform'], 2) ?></h3>
                            <p>Platform Commission</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-building"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>GHS <?= number_format($grand_totals['station'], 2) ?></h3>
                            <p>Station Commission</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-broadcast-tower"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>GHS <?= number_format($grand_totals['programme'], 2) ?></h3>
                            <p>Programme Commission</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-microphone"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3>GHS <?= number_format($grand_totals['prize_pool'], 2) ?></h3>
                            <p>Prize Pool</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-trophy"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Revenue Table -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Revenue Breakdown</h3>
                    <div class="card-tools">
                        <span class="badge badge-primary"><?= $grand_totals['payments'] ?> Payments</span>
                    </div>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>Campaign</th>
                                <th>Station</th>
                                <th>Programme</th>
                                <th>Platform</th>
                                <th>Station</th>
                                <th>Programme</th>
                                <th>Prize Pool (Total)</th>
                                <th>Daily Pool</th>
                                <th>Final Pool</th>
                                <th>Bonus Pool</th>
                                <th>Payments</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($report)): ?>
                                <?php foreach ($report as $row): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($row->campaign_name) ?></td>
                                        <td><?= htmlspecialchars($row->station_name ?? 'N/A') ?></td>
                                        <td><?= htmlspecialchars($row->programme_name ?? 'N/A') ?></td>
                                        <td>GHS <?= number_format($row->platform_total, 2) ?></td>
                                        <td>GHS <?= number_format($row->station_total, 2) ?></td>
                                        <td>GHS <?= number_format($row->programme_total, 2) ?></td>
                                        <td><strong>GHS <?= number_format($row->prize_pool_total, 2) ?></strong></td>
                                        <td><span class="badge badge-info">GHS <?= number_format($row->daily_pool ?? 0, 2) ?></span></td>
                                        <td><span class="badge badge-warning">GHS <?= number_format($row->final_pool ?? 0, 2) ?></span></td>
                                        <td><span class="badge badge-success">GHS <?= number_format($row->bonus_pool ?? 0, 2) ?></span></td>
                                        <td><?= $row->payment_count ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="11" class="text-center">No data available</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

<script src="<?= vendor('jquery/jquery.min.js') ?>"></script>
<script>
function loadProgrammesForReport() {
    const stationId = $('#stationFilter').val();
    const programmeSelect = $('#programmeFilter');
    
    programmeSelect.html('<option value="">Loading...</option>');
    
    if (!stationId) {
        programmeSelect.html('<option value="">All Programmes</option>');
        return;
    }
    
    $.get('<?= url('public/getProgrammesByStation') ?>/' + stationId, function(response) {
        if (response.success && response.programmes.length > 0) {
            programmeSelect.html('<option value="">All Programmes</option>');
            response.programmes.forEach(function(programme) {
                programmeSelect.append($('<option></option>').val(programme.id).text(programme.name));
            });
        } else {
            programmeSelect.html('<option value="">No programmes</option>');
        }
    });
}
</script>

<?php require_once '../app/views/layouts/footer.php'; ?>
