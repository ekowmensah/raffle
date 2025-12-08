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
            
            <?php 
            $successMsg = flash('success');
            $errorMsg = flash('error');
            ?>
            
            <?php if ($successMsg): ?>
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
                    <?php if ($campaign->campaign_type === 'item'): ?>
                        <div class="small-box bg-success" style="background: linear-gradient(135deg, #10b981, #059669) !important;">
                            <div class="inner">
                                <h3><?= $campaign->currency ?> <?= number_format($stats->total_prize_pool ?? 0, 2) ?></h3>
                                <p>Prize Value</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-gift"></i>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="small-box bg-purple">
                            <div class="inner">
                                <h3><?= $campaign->currency ?> <?= number_format($stats->total_prize_pool ?? 0, 2) ?></h3>
                                <p>Prize Pool</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-trophy"></i>
                            </div>
                        </div>
                    <?php endif; ?>
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
                                <?php if (!$campaign->is_config_locked && ($campaign->total_tickets ?? 0) == 0): ?>
                                <a href="<?= url('campaign/edit/' . $campaign->id) ?>" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <?php elseif (($campaign->total_tickets ?? 0) > 0): ?>
                                <button class="btn btn-secondary btn-sm" disabled title="Cannot edit - tickets already sold">
                                    <i class="fas fa-lock"></i> Locked
                                </button>
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
                                    <th>Campaign Type</th>
                                    <td>
                                        <?php if ($campaign->campaign_type === 'item'): ?>
                                            <span class="badge badge-success"><i class="fas fa-gift"></i> Item Campaign</span>
                                        <?php else: ?>
                                            <span class="badge badge-primary"><i class="fas fa-money-bill-wave"></i> Cash Campaign</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php if ($campaign->campaign_type === 'item'): ?>
                                <tr>
                                    <th>Prize Item</th>
                                    <td>
                                        <strong><?= htmlspecialchars($campaign->item_name ?? 'N/A') ?></strong>
                                        <br><small class="text-muted">Value: <?= $campaign->currency ?> <?= number_format($campaign->item_value ?? 0, 2) ?></small>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Item Description</th>
                                    <td><?= nl2br(htmlspecialchars($campaign->item_description ?? 'N/A')) ?></td>
                                </tr>
                                <tr>
                                    <th>Winner Selection</th>
                                    <td>
                                        <?php
                                        $selectionTypes = [
                                            'single' => '<i class="fas fa-user"></i> Single Winner',
                                            'multiple' => '<i class="fas fa-users"></i> Multiple Winners (' . ($campaign->item_quantity ?? 1) . ' items)',
                                            'tiered' => '<i class="fas fa-trophy"></i> Tiered Prizes'
                                        ];
                                        echo $selectionTypes[$campaign->winner_selection_type] ?? 'Single Winner';
                                        ?>
                                    </td>
                                </tr>
                                <?php if ($campaign->min_tickets_for_draw): ?>
                                <tr>
                                    <th>Minimum Tickets</th>
                                    <td>
                                        <span class="badge badge-warning"><?= number_format($campaign->min_tickets_for_draw) ?> tickets required</span>
                                        <br><small class="text-muted">Draw won't happen until minimum is reached</small>
                                    </td>
                                </tr>
                                <?php endif; ?>
                                <?php if (isset($campaign->items_total) && $campaign->items_total > 0): ?>
                                <tr>
                                    <th>Inventory Status</th>
                                    <td>
                                        <?php
                                        $itemsAwarded = $campaign->items_awarded ?? 0;
                                        $itemsTotal = $campaign->items_total ?? 0;
                                        $itemsRemaining = $itemsTotal - $itemsAwarded;
                                        $percentAwarded = $itemsTotal > 0 ? ($itemsAwarded / $itemsTotal) * 100 : 0;
                                        
                                        $statusColor = 'success';
                                        if ($itemsRemaining == 0) {
                                            $statusColor = 'danger';
                                        } elseif ($percentAwarded > 75) {
                                            $statusColor = 'warning';
                                        }
                                        ?>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <strong>Total Items:</strong> <?= number_format($itemsTotal) ?>
                                            </div>
                                            <div class="col-md-4">
                                                <strong>Awarded:</strong> <span class="text-danger"><?= number_format($itemsAwarded) ?></span>
                                            </div>
                                            <div class="col-md-4">
                                                <strong>Remaining:</strong> <span class="text-<?= $statusColor ?>"><?= number_format($itemsRemaining) ?></span>
                                            </div>
                                        </div>
                                        <div class="progress mt-2" style="height: 25px;">
                                            <div class="progress-bar bg-<?= $statusColor ?>" role="progressbar" 
                                                 style="width: <?= $percentAwarded ?>%;" 
                                                 aria-valuenow="<?= $percentAwarded ?>" aria-valuemin="0" aria-valuemax="100">
                                                <?= number_format($percentAwarded, 1) ?>% Awarded
                                            </div>
                                        </div>
                                        <?php if ($itemsRemaining == 0): ?>
                                        <div class="alert alert-danger mt-2 mb-0">
                                            <i class="fas fa-exclamation-triangle"></i> <strong>All items have been awarded!</strong> No more draws can be conducted.
                                        </div>
                                        <?php elseif ($itemsRemaining < 5): ?>
                                        <div class="alert alert-warning mt-2 mb-0">
                                            <i class="fas fa-exclamation-circle"></i> <strong>Low inventory!</strong> Only <?= $itemsRemaining ?> item(s) remaining.
                                        </div>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endif; ?>
                                <?php if ($campaign->campaign_type === 'item' && !empty($campaign->item_image)): ?>
                                <tr>
                                    <th>Item Image</th>
                                    <td>
                                        <div class="text-center">
                                            <img src="<?= BASE_URL ?>/<?= htmlspecialchars($campaign->item_image) ?>" 
                                                 alt="<?= htmlspecialchars($campaign->item_name) ?>" 
                                                 class="img-fluid img-thumbnail" 
                                                 style="max-height: 300px; border-radius: 10px;">
                                        </div>
                                    </td>
                                </tr>
                                <?php endif; ?>
                                <?php endif; ?>
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
                            <h3 class="card-title">
                                <?php if ($campaign->campaign_type === 'item'): ?>
                                    Revenue Sharing & Prize Value
                                <?php else: ?>
                                    Revenue Sharing
                                <?php endif; ?>
                            </h3>
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
                                    <?php if ($campaign->campaign_type === 'item'): ?>
                                        <div class="info-box bg-warning">
                                            <span class="info-box-icon"><i class="fas fa-gift"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Prize Value</span>
                                                <span class="info-box-number"><?= $campaign->currency ?> <?= number_format($campaign->item_value ?? 0, 2) ?></span>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <div class="info-box bg-warning">
                                            <span class="info-box-icon"><i class="fas fa-trophy"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Prize Pool</span>
                                                <span class="info-box-number"><?= $campaign->prize_pool_percent ?>%</span>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <?php if ($campaign->campaign_type === 'item'): ?>
                            <div class="alert alert-info mt-3">
                                <i class="fas fa-info-circle"></i> <strong>Note:</strong> 
                                For item campaigns, the prize is the physical item worth <strong><?= $campaign->currency ?> <?= number_format($campaign->item_value ?? 0, 2) ?></strong>. 
                                Revenue is shared among Platform, Station, and Programme only (Prize Pool = 0%).
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Quick Actions</h3>
                        </div>
                        <div class="card-body">
                            <button type="button" class="btn btn-success btn-block" data-toggle="modal" data-target="#scheduleDrawModal">
                                <i class="fas fa-calendar-plus"></i> Schedule Draw
                            </button>
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

<!-- Schedule Draw Modal -->
<div class="modal fade" id="scheduleDrawModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success">
                <h5 class="modal-title"><i class="fas fa-calendar-plus"></i> Schedule Draw for <?= htmlspecialchars($campaign->name) ?></h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="<?= url('draw/schedule') ?>" method="POST" id="quickScheduleForm">
                <?= csrf_field() ?>
                <input type="hidden" name="campaign_id" value="<?= $campaign->id ?>">
                <input type="hidden" name="station_id" value="<?= $campaign->station_id ?>">
                <?php if (isset($campaign->programme_id) && $campaign->programme_id): ?>
                <input type="hidden" name="programme_id" value="<?= $campaign->programme_id ?>">
                <input type="hidden" name="campaign_type" value="programme">
                <?php else: ?>
                <input type="hidden" name="campaign_type" value="station">
                <?php endif; ?>
                
                <div class="modal-body">
                    <!-- Campaign Info -->
                    <div class="alert <?= $campaign->campaign_type === 'item' ? 'alert-success' : 'alert-info' ?>">
                        <h6><i class="fas fa-info-circle"></i> Campaign Information</h6>
                        <div class="row">
                            <?php if ($campaign->campaign_type === 'item'): ?>
                                <div class="col-md-6">
                                    <strong><i class="fas fa-gift"></i> Type:</strong> <span class="badge badge-success">Item Campaign</span>
                                </div>
                                <div class="col-md-6">
                                    <strong><i class="fas fa-trophy"></i> Prize:</strong> <?= htmlspecialchars($campaign->item_name) ?>
                                </div>
                                <div class="col-md-6">
                                    <strong><i class="fas fa-money-bill-wave"></i> Value:</strong> <?= $campaign->currency ?> <?= number_format($campaign->item_value ?? 0, 2) ?>
                                </div>
                                <div class="col-md-6">
                                    <strong><i class="fas fa-users"></i> Winners:</strong> 
                                    <?php
                                    if ($campaign->winner_selection_type === 'single') {
                                        echo '1 Winner';
                                    } elseif ($campaign->winner_selection_type === 'multiple') {
                                        echo ($campaign->item_quantity ?? 1) . ' Winners';
                                    } else {
                                        echo '3 Winners (Tiered)';
                                    }
                                    ?>
                                </div>
                                <?php if ($campaign->min_tickets_for_draw): ?>
                                <div class="col-md-12 mt-2">
                                    <div class="alert alert-warning mb-0">
                                        <i class="fas fa-exclamation-triangle"></i> <strong>Minimum Tickets Required:</strong> <?= number_format($campaign->min_tickets_for_draw) ?> tickets must be sold before draw
                                    </div>
                                </div>
                                <?php endif; ?>
                            <?php else: ?>
                                <div class="col-md-6">
                                    <strong><i class="fas fa-money-bill-wave"></i> Type:</strong> <span class="badge badge-primary">Cash Campaign</span>
                                </div>
                                <div class="col-md-6">
                                    <strong><i class="fas fa-trophy"></i> Prize Pool:</strong> <?= $campaign->prize_pool_percent ?>% of revenue
                                </div>
                            <?php endif; ?>
                            <div class="col-md-6">
                                <strong><i class="fas fa-calendar-alt"></i> Period:</strong> <?= formatDate($campaign->start_date, 'M d, Y') ?> - <?= formatDate($campaign->end_date, 'M d, Y') ?>
                            </div>
                            <div class="col-md-6">
                                <strong><i class="fas fa-bolt"></i> Daily Draws:</strong> 
                                <?php if ($campaign->daily_draw_enabled): ?>
                                    <span class="badge badge-success">Enabled</span>
                                <?php else: ?>
                                    <span class="badge badge-secondary">Disabled</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Scheduling Options -->
                    <div class="form-group">
                        <label><i class="fas fa-calendar-alt"></i> Scheduling Type</label>
                        <div class="custom-control custom-radio">
                            <input type="radio" id="modal_schedule_single" name="schedule_type" value="single" 
                                   class="custom-control-input" checked onchange="toggleModalScheduleFields()">
                            <label class="custom-control-label" for="modal_schedule_single">
                                <i class="fas fa-calendar-day"></i> Schedule Single Draw
                            </label>
                        </div>
                        <?php if ($campaign->daily_draw_enabled): ?>
                        <div class="custom-control custom-radio">
                            <input type="radio" id="modal_schedule_auto" name="schedule_type" value="auto_daily" 
                                   class="custom-control-input" onchange="toggleModalScheduleFields()">
                            <label class="custom-control-label" for="modal_schedule_auto">
                                <i class="fas fa-calendar-week"></i> Auto-Schedule All Daily Draws
                            </label>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Single Draw Fields -->
                    <div id="modal_single_draw_fields">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="modal_draw_type"><i class="fas fa-tag"></i> Draw Type <span class="text-danger">*</span></label>
                                    <select class="form-control" id="modal_draw_type" name="draw_type" required>
                                        <option value="daily">Daily Draw</option>
                                        <option value="final">Final Draw</option>
                                        <option value="bonus">Bonus Draw</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="modal_draw_date"><i class="fas fa-calendar"></i> Draw Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="modal_draw_date" name="draw_date" 
                                           min="<?= $campaign->start_date ?>" max="<?= $campaign->end_date ?>" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="modal_winner_count"><i class="fas fa-users"></i> Winners <span class="text-danger">*</span></label>
                                    <?php if ($campaign->campaign_type === 'item'): ?>
                                        <?php
                                        if ($campaign->winner_selection_type === 'single') {
                                            $winnerCount = 1;
                                        } elseif ($campaign->winner_selection_type === 'multiple') {
                                            $winnerCount = $campaign->item_quantity ?? 1;
                                        } else {
                                            $winnerCount = 3; // Tiered
                                        }
                                        ?>
                                        <input type="number" class="form-control" id="modal_winner_count" name="winner_count" 
                                               value="<?= $winnerCount ?>" readonly>
                                        <small class="form-text text-muted">
                                            <?php if ($campaign->winner_selection_type === 'single'): ?>
                                                <i class="fas fa-gift"></i> Single winner gets the item
                                            <?php elseif ($campaign->winner_selection_type === 'multiple'): ?>
                                                <i class="fas fa-gift"></i> Each winner gets 1 item
                                            <?php else: ?>
                                                <i class="fas fa-trophy"></i> Tiered prizes (1st, 2nd, 3rd)
                                            <?php endif; ?>
                                        </small>
                                    <?php else: ?>
                                        <select class="form-control" id="modal_winner_count" name="winner_count" required onchange="updateModalPrizePreview()">
                                            <option value="1">1 Winner</option>
                                            <option value="2">2 Winners</option>
                                            <option value="3" selected>3 Winners</option>
                                            <option value="4">4 Winners</option>
                                            <option value="5">5 Winners</option>
                                            <option value="6">6 Winners</option>
                                            <option value="7">7 Winners</option>
                                            <option value="8">8 Winners</option>
                                            <option value="9">9 Winners</option>
                                            <option value="10">10 Winners</option>
                                        </select>
                                        <small class="form-text text-muted" id="modal_prize_preview">
                                            Prize split: 50% / 30% / 20%
                                        </small>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Auto Schedule Info -->
                    <div id="modal_auto_schedule_info" style="display: none;">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> 
                            This will automatically schedule daily draws from campaign start date to one day before end date.
                            Existing draws will be skipped.
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-calendar-check"></i> <span id="modal_submit_text">Schedule Draw</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function toggleModalScheduleFields() {
    const scheduleType = document.querySelector('input[name="schedule_type"]:checked').value;
    const singleFields = document.getElementById('modal_single_draw_fields');
    const autoInfo = document.getElementById('modal_auto_schedule_info');
    const submitText = document.getElementById('modal_submit_text');
    
    if (scheduleType === 'auto_daily') {
        singleFields.style.display = 'none';
        autoInfo.style.display = 'block';
        submitText.textContent = 'Auto-Schedule Draws';
        
        document.getElementById('modal_draw_type').removeAttribute('required');
        document.getElementById('modal_draw_date').removeAttribute('required');
    } else {
        singleFields.style.display = 'block';
        autoInfo.style.display = 'none';
        submitText.textContent = 'Schedule Draw';
        
        document.getElementById('modal_draw_type').setAttribute('required', 'required');
        document.getElementById('modal_draw_date').setAttribute('required', 'required');
    }
}

function updateModalPrizePreview() {
    const winnerCount = parseInt(document.getElementById('modal_winner_count').value);
    const preview = document.getElementById('modal_prize_preview');
    
    if (!preview) return;
    
    if (winnerCount === 1) {
        preview.innerHTML = '<i class="fas fa-trophy text-warning"></i> Prize split: 100% (Winner takes all)';
    } else if (winnerCount === 2) {
        preview.innerHTML = '<i class="fas fa-trophy text-warning"></i> Prize split: 60% / 40%';
    } else if (winnerCount === 3) {
        preview.innerHTML = '<i class="fas fa-trophy text-warning"></i> Prize split: 50% / 30% / 20%';
    } else {
        preview.innerHTML = '<i class="fas fa-trophy text-warning"></i> Prize split: Equal (' + (100/winnerCount).toFixed(1) + '% each)';
    }
}

// Set default draw date to today
document.addEventListener('DOMContentLoaded', function() {
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('modal_draw_date').value = today;
});
</script>

<?php require_once '../app/views/layouts/footer.php'; ?>
