<?php require_once '../app/views/layouts/header.php'; ?>
<?php require_once '../app/views/layouts/sidebar.php'; ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Conduct Draw</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url('home') ?>">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= url('draw') ?>">Draws</a></li>
                        <li class="breadcrumb-item active">Conduct</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            
            <?php if (flash('error')): ?>
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <i class="icon fas fa-ban"></i> <?= flash('error') ?>
                </div>
            <?php endif; ?>

            <div class="card <?= $campaign->campaign_type === 'item' ? 'card-success' : 'card-warning' ?>">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-exclamation-triangle"></i> Confirm Draw Execution</h3>
                </div>
                <div class="card-body">
                    <div class="alert <?= $campaign->campaign_type === 'item' ? 'alert-success' : 'alert-info' ?>">
                        <h5><i class="icon fas fa-info-circle"></i> Draw Information</h5>
                        <ul>
                            <li><strong>Campaign:</strong> <?= htmlspecialchars($campaign->name) ?></li>
                            <li><strong>Draw ID:</strong> <?= $draw->id ?></li>
                            <li><strong>Draw Type:</strong> <?= strtoupper($draw->draw_type) ?></li>
                            <li><strong>Draw Date:</strong> <?= formatDate($draw->draw_date, 'M d, Y') ?></li>
                            <?php if ($campaign->campaign_type === 'item'): ?>
                                <li><strong>Campaign Type:</strong> <span class="badge badge-success"><i class="fas fa-gift"></i> Item Campaign</span></li>
                                <li><strong>Prize Item:</strong> <?= htmlspecialchars($campaign->item_name) ?></li>
                                <li><strong>Item Value:</strong> GHS <?= number_format($campaign->item_value ?? 0, 2) ?></li>
                                <li><strong>Winner Selection:</strong> 
                                    <?php
                                    if ($campaign->winner_selection_type === 'single') {
                                        echo '1 Winner (Single)';
                                    } elseif ($campaign->winner_selection_type === 'multiple') {
                                        echo ($campaign->item_quantity ?? 1) . ' Winners (Multiple Items)';
                                    } else {
                                        echo '3 Winners (Tiered Prizes)';
                                    }
                                    ?>
                                </li>
                                <?php if ($campaign->min_tickets_for_draw): ?>
                                <li><strong>Minimum Tickets:</strong> <?= number_format($campaign->min_tickets_for_draw) ?> tickets required</li>
                                <?php endif; ?>
                            <?php else: ?>
                                <li><strong>Campaign Type:</strong> <span class="badge badge-primary"><i class="fas fa-money-bill-wave"></i> Cash Campaign</span></li>
                                <li><strong>Prize Pool:</strong> GHS <?= number_format($draw->total_prize_pool ?? 0, 2) ?></li>
                                <li><strong>Winners:</strong> <?= $draw->winner_count ?? 3 ?></li>
                            <?php endif; ?>
                        </ul>
                    </div>

                    <div class="alert alert-warning">
                        <h5><i class="icon fas fa-exclamation-triangle"></i> Important</h5>
                        <p>
                            This action will:
                        </p>
                        <ul>
                            <li>Select random winner(s) from eligible tickets</li>
                            <?php if ($campaign->campaign_type === 'item'): ?>
                                <li>Assign the item prize to winner(s)</li>
                                <li>Send SMS notifications with item details to all winners</li>
                                <li>Track item delivery status</li>
                            <?php else: ?>
                                <li>Distribute the prize pool among winners</li>
                                <li>Send SMS notifications with prize amounts to all winners</li>
                                <li>Track payment status</li>
                            <?php endif; ?>
                            <li>Mark the draw as completed (cannot be undone)</li>
                        </ul>
                        <p class="mb-0">
                            <strong>Are you sure you want to proceed?</strong>
                        </p>
                    </div>

                    <form action="<?= url('draw/conduct/' . $draw->id) ?>" method="POST">
                        <?= csrf_field() ?>
                        <div class="text-center">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fas fa-random"></i> Conduct Draw Now
                            </button>
                            <a href="<?= url('draw/pending') ?>" class="btn btn-default btn-lg">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>

<?php require_once '../app/views/layouts/footer.php'; ?>
