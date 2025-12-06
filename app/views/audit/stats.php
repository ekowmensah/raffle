<?php require_once '../app/views/layouts/header.php'; ?>
<?php require_once '../app/views/layouts/sidebar.php'; ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0"><i class="fas fa-chart-bar"></i> Audit Statistics</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url('home') ?>">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= url('audit') ?>">Audit Logs</a></li>
                        <li class="breadcrumb-item active">Statistics</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <!-- Date Filter -->
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-calendar"></i> Date Range</h3>
                </div>
                <div class="card-body">
                    <form method="GET" action="<?= url('audit/stats') ?>" class="form-inline">
                        <div class="form-group mr-3">
                            <label class="mr-2">From:</label>
                            <input type="date" name="date_from" class="form-control" value="<?= htmlspecialchars($dateFrom) ?>">
                        </div>
                        <div class="form-group mr-3">
                            <label class="mr-2">To:</label>
                            <input type="date" name="date_to" class="form-control" value="<?= htmlspecialchars($dateTo) ?>">
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Apply
                        </button>
                    </form>
                </div>
            </div>

            <div class="row">
                <!-- Action Statistics -->
                <div class="col-md-6">
                    <div class="card card-info card-outline">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-tasks"></i> Actions Breakdown</h3>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($actionStats)): ?>
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Action</th>
                                            <th class="text-right">Count</th>
                                            <th style="width: 40%">Graph</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $maxCount = max(array_column($actionStats, 'count'));
                                        foreach ($actionStats as $stat): 
                                            $percentage = ($stat->count / $maxCount) * 100;
                                        ?>
                                            <tr>
                                                <td>
                                                    <span class="badge badge-primary">
                                                        <?= ucwords(str_replace('_', ' ', $stat->action)) ?>
                                                    </span>
                                                </td>
                                                <td class="text-right">
                                                    <strong><?= number_format($stat->count) ?></strong>
                                                </td>
                                                <td>
                                                    <div class="progress" style="height: 20px;">
                                                        <div class="progress-bar bg-info" role="progressbar" 
                                                             style="width: <?= $percentage ?>%"
                                                             aria-valuenow="<?= $stat->count ?>" 
                                                             aria-valuemin="0" 
                                                             aria-valuemax="<?= $maxCount ?>">
                                                            <?= number_format($percentage, 1) ?>%
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <p class="text-center text-muted">No action statistics available</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- User Activity -->
                <div class="col-md-6">
                    <div class="card card-success card-outline">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-users"></i> Top Active Users</h3>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($userActivityStats)): ?>
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>User</th>
                                            <th class="text-right">Actions</th>
                                            <th>Last Activity</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($userActivityStats as $stat): ?>
                                            <tr>
                                                <td>
                                                    <strong><?= htmlspecialchars($stat->username) ?></strong><br>
                                                    <small class="text-muted"><?= htmlspecialchars($stat->email) ?></small>
                                                </td>
                                                <td class="text-right">
                                                    <span class="badge badge-success" style="font-size: 1rem;">
                                                        <?= number_format($stat->action_count) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <small><?= date('M d, Y H:i', strtotime($stat->last_activity)) ?></small>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <p class="text-center text-muted">No user activity data available</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Critical Actions -->
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-warning card-outline">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-exclamation-triangle"></i> Recent Critical Actions</h3>
                        </div>
                        <div class="card-body table-responsive p-0">
                            <?php if (!empty($criticalActions)): ?>
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Date/Time</th>
                                            <th>User</th>
                                            <th>Action</th>
                                            <th>Entity</th>
                                            <th>IP Address</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($criticalActions as $log): ?>
                                            <tr>
                                                <td>
                                                    <small><?= date('M d, Y H:i:s', strtotime($log->created_at)) ?></small>
                                                </td>
                                                <td>
                                                    <?php if ($log->username): ?>
                                                        <strong><?= htmlspecialchars($log->username) ?></strong>
                                                    <?php else: ?>
                                                        <span class="text-muted">System</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    $actionClass = [
                                                        'draw_conducted' => 'warning',
                                                        'winner_selected' => 'success',
                                                        'payment_processed' => 'info',
                                                        'campaign_deleted' => 'danger',
                                                        'user_deleted' => 'danger',
                                                        'configuration_changed' => 'warning'
                                                    ];
                                                    $class = $actionClass[$log->action] ?? 'primary';
                                                    ?>
                                                    <span class="badge badge-<?= $class ?>">
                                                        <?= ucwords(str_replace('_', ' ', $log->action)) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php if ($log->entity_type): ?>
                                                        <code><?= htmlspecialchars($log->entity_type) ?></code>
                                                        <?php if ($log->entity_id): ?>
                                                            #<?= $log->entity_id ?>
                                                        <?php endif; ?>
                                                    <?php else: ?>
                                                        <span class="text-muted">-</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <small><?= htmlspecialchars($log->ip_address ?? '-') ?></small>
                                                </td>
                                                <td>
                                                    <a href="<?= url('audit/show/' . $log->id) ?>" class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <p class="text-center text-muted p-3">No critical actions recorded</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Back Button -->
            <div class="row">
                <div class="col-md-12">
                    <a href="<?= url('audit') ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Audit Logs
                    </a>
                </div>
            </div>
        </div>
    </section>
</div>

<?php require_once '../app/views/layouts/footer.php'; ?>
