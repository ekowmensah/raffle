<?php require_once '../app/views/layouts/header.php'; ?>
<?php require_once '../app/views/layouts/sidebar.php'; ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">All Winners</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url('home') ?>">Home</a></li>
                        <li class="breadcrumb-item active">Winners</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Filter by Campaign</h3>
                </div>
                <div class="card-body">
                    <form method="GET" action="<?= url('draw/winners') ?>">
                        <div class="row">
                            <div class="col-md-6">
                                <select name="campaign" class="form-control" onchange="this.form.submit()">
                                    <option value="">Select Campaign</option>
                                    <?php foreach ($campaigns as $campaign): ?>
                                        <option value="<?= $campaign->id ?>" <?= $selected_campaign == $campaign->id ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($campaign->name) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <?php if (!empty($winners)): ?>
            <?php if ($campaign): ?>
            <?php 
            // Check if this is actually an item campaign by looking at winners
            $hasItemWinners = false;
            if (!empty($winners)) {
                foreach ($winners as $w) {
                    if (!empty($w->item_name) || (isset($w->campaign_type) && $w->campaign_type === 'item')) {
                        $hasItemWinners = true;
                        break;
                    }
                }
            }
            $isItemCampaign = $hasItemWinners || $campaign->campaign_type === 'item';
            ?>
            <div class="alert <?= $isItemCampaign ? 'alert-success' : 'alert-info' ?>">
                <h5><i class="fas fa-info-circle"></i> Campaign Information</h5>
                <div class="row">
                    <div class="col-md-3"><strong>Campaign:</strong> <?= htmlspecialchars($campaign->name) ?></div>
                    <?php if ($isItemCampaign): ?>
                        <div class="col-md-3"><strong>Type:</strong> <span class="badge badge-success"><i class="fas fa-gift"></i> Item Campaign</span></div>
                        <?php if (!empty($winners) && !empty($winners[0]->item_name)): ?>
                            <div class="col-md-3"><strong>Prize:</strong> <?= htmlspecialchars($winners[0]->item_name) ?></div>
                            <div class="col-md-3"><strong>Value:</strong> GHS <?= number_format($winners[0]->item_value ?? 0, 2) ?></div>
                        <?php elseif (!empty($campaign->item_name)): ?>
                            <div class="col-md-3"><strong>Prize:</strong> <?= htmlspecialchars($campaign->item_name) ?></div>
                            <div class="col-md-3"><strong>Value:</strong> GHS <?= number_format($campaign->item_value ?? 0, 2) ?></div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="col-md-3"><strong>Type:</strong> <span class="badge badge-primary"><i class="fas fa-money-bill-wave"></i> Cash Campaign</span></div>
                        <div class="col-md-3"><strong>Prize Pool:</strong> <?= $campaign->prize_pool_percent ?? 0 ?>%</div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-trophy"></i> Winners List</h3>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Rank</th>
                                <th>Ticket Code</th>
                                <th>Player</th>
                                <th>Draw Date</th>
                                <th>Draw Type</th>
                                <th>Prize/Value</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($winners as $winner): ?>
                                <tr>
                                    <td>
                                        <?php if ($winner->prize_rank == 1): ?>
                                            <i class="fas fa-trophy text-warning"></i> #<?= $winner->prize_rank ?>
                                        <?php else: ?>
                                            #<?= $winner->prize_rank ?>
                                        <?php endif; ?>
                                    </td>
                                    <td><strong><?= htmlspecialchars($winner->ticket_code) ?></strong></td>
                                    <td><?= htmlspecialchars($winner->player_phone) ?></td>
                                    <td><?= formatDate($winner->draw_date, 'M d, Y') ?></td>
                                    <td>
                                        <span class="badge badge-<?= $winner->draw_type == 'daily' ? 'info' : 'warning' ?>">
                                            <?= strtoupper($winner->draw_type) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if (isset($winner->campaign_type) && $winner->campaign_type === 'item'): ?>
                                            <i class="fas fa-gift text-success"></i> <strong><?= htmlspecialchars($winner->item_name ?? $winner->campaign_item_name ?? 'Item Prize') ?></strong><br>
                                            <small class="text-muted">Value: GHS <?= number_format($winner->item_value ?? $winner->prize_amount, 2) ?></small>
                                        <?php else: ?>
                                            <i class="fas fa-money-bill-wave text-success"></i> <strong>GHS <?= number_format($winner->prize_amount, 2) ?></strong>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php
                                        $statusColors = [
                                            'pending' => 'warning',
                                            'processing' => 'info',
                                            'paid' => 'success',
                                            'failed' => 'danger'
                                        ];
                                        $color = $statusColors[$winner->prize_paid_status] ?? 'secondary';
                                        
                                        // Different labels for item vs cash
                                        $isItemCampaign = isset($winner->campaign_type) && $winner->campaign_type === 'item';
                                        $statusLabels = [
                                            'pending' => $isItemCampaign ? 'PENDING DELIVERY' : 'PENDING',
                                            'processing' => 'PROCESSING',
                                            'paid' => $isItemCampaign ? 'DELIVERED' : 'PAID',
                                            'failed' => 'CANCELLED'
                                        ];
                                        $statusLabel = $statusLabels[$winner->prize_paid_status] ?? strtoupper($winner->prize_paid_status);
                                        ?>
                                        <span class="badge badge-<?= $color ?>">
                                            <?= $statusLabel ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($winner->prize_paid_status != 'paid'): ?>
                                        <form method="POST" action="<?= url('draw/updatePrizeStatus/' . $winner->id) ?>" style="display: inline;">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="status" value="paid">
                                            <?php $isItem = isset($winner->campaign_type) && $winner->campaign_type === 'item'; ?>
                                            <button type="submit" class="btn btn-success btn-sm" 
                                                    onclick="return confirm('<?= $isItem ? 'Mark this item as delivered?' : 'Mark this prize as paid?' ?>')">
                                                <i class="fas fa-<?= $isItem ? 'truck' : 'check' ?>"></i> <?= $isItem ? 'Mark Delivered' : 'Mark Paid' ?>
                                            </button>
                                        </form>
                                        <?php else: ?>
                                            <?php $isItem = isset($winner->campaign_type) && $winner->campaign_type === 'item'; ?>
                                            <span class="text-success"><i class="fas fa-check-circle"></i> <?= $isItem ? 'Delivered' : 'Paid' ?></span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php else: ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> No winners found. Please select a campaign.
                </div>
            <?php endif; ?>
        </div>
    </section>
</div>

<?php require_once '../app/views/layouts/footer.php'; ?>
