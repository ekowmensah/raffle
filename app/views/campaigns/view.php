<?php require_once '../app/views/layouts/header.php'; ?>
<?php require_once '../app/views/layouts/sidebar.php'; ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0"><?= htmlspecialchars($campaign->name) ?></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url('home') ?>">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= url('campaign') ?>">Campaigns</a></li>
                        <li class="breadcrumb-item active">View</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            
            <?php if (flash('success')): ?>
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <i class="icon fas fa-check"></i> <?= flash('success') ?>
                </div>
            <?php endif; ?>

            <!-- Stats Row -->
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3><?= number_format($stats->total_tickets ?? 0) ?></h3>
                            <p>Total Tickets</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-ticket-alt"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3><?= number_format($stats->total_players ?? 0) ?></h3>
                            <p>Players</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3><?= $campaign->currency ?> <?= number_format($stats->total_revenue ?? 0, 2) ?></h3>
                            <p>Revenue</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3><?= number_format($stats->total_draws ?? 0) ?></h3>
                            <p>Draws</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-random"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Campaign Details</h3>
                            <div class="card-tools">
                                <?php if (!$campaign->is_config_locked): ?>
                                <a href="<?= url('campaign/edit/' . $campaign->id) ?>" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <?php endif; ?>
                                <a href="<?= url('campaign/clone/' . $campaign->id) ?>" class="btn btn-info btn-sm">
                                    <i class="fas fa-clone"></i> Clone
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width: 200px">Code</th>
                                    <td><code><?= htmlspecialchars($campaign->code) ?></code></td>
                                </tr>
                                <tr>
                                    <th>Description</th>
                                    <td><?= nl2br(htmlspecialchars($campaign->description ?? 'N/A')) ?></td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        <?php
                                        $statusColors = [
                                            'draft' => 'secondary',
                                            'active' => 'success',
                                            'closed' => 'warning',
                                            'draw_done' => 'info'
                                        ];
                                        $color = $statusColors[$campaign->status] ?? 'secondary';
                                        ?>
                                        <span class="badge badge-<?= $color ?>"><?= strtoupper($campaign->status) ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Ticket Price</th>
                                    <td><?= $campaign->currency ?> <?= number_format($campaign->ticket_price, 2) ?></td>
                                </tr>
                                <tr>
                                    <th>Duration</th>
                                    <td><?= formatDate($campaign->start_date, 'M d, Y') ?> - <?= formatDate($campaign->end_date, 'M d, Y') ?></td>
                                </tr>
                                <tr>
                                    <th>Configuration</th>
                                    <td>
                                        <?php if ($campaign->is_config_locked): ?>
                                            <span class="badge badge-danger"><i class="fas fa-lock"></i> Locked</span>
                                        <?php else: ?>
                                            <span class="badge badge-success"><i class="fas fa-unlock"></i> Unlocked</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Daily Draw</th>
                                    <td>
                                        <?php if ($campaign->daily_draw_enabled): ?>
                                            <span class="badge badge-success">Enabled</span>
                                        <?php else: ?>
                                            <span class="badge badge-secondary">Disabled</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Revenue Sharing</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="info-box bg-primary">
                                        <span class="info-box-icon"><i class="fas fa-building"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Platform</span>
                                            <span class="info-box-number"><?= $campaign->platform_percent ?>%</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="info-box bg-info">
                                        <span class="info-box-icon"><i class="fas fa-broadcast-tower"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Station</span>
                                            <span class="info-box-number"><?= $campaign->station_percent ?>%</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="info-box bg-success">
                                        <span class="info-box-icon"><i class="fas fa-microphone"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Programme</span>
                                            <span class="info-box-number"><?= $campaign->programme_percent ?>%</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="info-box bg-warning">
                                        <span class="info-box-icon"><i class="fas fa-trophy"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Prize Pool</span>
                                            <span class="info-box-number"><?= $campaign->prize_pool_percent ?>%</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Quick Actions</h3>
                        </div>
                        <div class="card-body">
                            <a href="<?= url('campaign/configureAccess/' . $campaign->id) ?>" class="btn btn-primary btn-block">
                                <i class="fas fa-cog"></i> Configure Programme Access
                            </a>
                            <?php if ($campaign->is_config_locked): ?>
                            <a href="<?= url('campaign/unlock/' . $campaign->id) ?>" class="btn btn-warning btn-block">
                                <i class="fas fa-unlock"></i> Unlock Configuration
                            </a>
                            <?php else: ?>
                            <a href="<?= url('campaign/lock/' . $campaign->id) ?>" class="btn btn-danger btn-block">
                                <i class="fas fa-lock"></i> Lock Configuration
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Change Status</h3>
                        </div>
                        <div class="card-body">
                            <form action="<?= url('campaign/updateStatus/' . $campaign->id) ?>" method="POST">
                                <?= csrf_field() ?>
                                <div class="form-group">
                                    <select class="form-control" name="status">
                                        <option value="draft" <?= $campaign->status == 'draft' ? 'selected' : '' ?>>Draft</option>
                                        <option value="active" <?= $campaign->status == 'active' ? 'selected' : '' ?>>Active</option>
                                        <option value="closed" <?= $campaign->status == 'closed' ? 'selected' : '' ?>>Closed</option>
                                        <option value="draw_done" <?= $campaign->status == 'draw_done' ? 'selected' : '' ?>>Draw Done</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-success btn-block">Update Status</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php require_once '../app/views/layouts/footer.php'; ?>
