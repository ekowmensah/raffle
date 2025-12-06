<?php require_once '../app/views/layouts/header.php'; ?>
<?php require_once '../app/views/layouts/sidebar.php'; ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Campaign Dashboard</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url('home') ?>">Home</a></li>
                        <li class="breadcrumb-item active">Campaign Dashboard</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Active Campaigns</h3>
                            <span class="badge badge-success float-right"><?= count($active_campaigns) ?></span>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($active_campaigns)): ?>
                                <div class="list-group">
                                    <?php foreach ($active_campaigns as $campaign): ?>
                                        <a href="<?= url('campaign/show/' . $campaign->id) ?>" class="list-group-item list-group-item-action">
                                            <div class="d-flex w-100 justify-content-between">
                                                <h5 class="mb-1"><?= htmlspecialchars($campaign->name) ?></h5>
                                                <small><?= formatDate($campaign->start_date, 'M d') ?> - <?= formatDate($campaign->end_date, 'M d') ?></small>
                                            </div>
                                            <p class="mb-1">
                                                <span class="badge badge-info"><?= htmlspecialchars($campaign->code) ?></span>
                                                <?php if (!empty($campaign->sponsor_name)): ?>
                                                    <span class="badge badge-secondary"><?= htmlspecialchars($campaign->sponsor_name) ?></span>
                                                <?php endif; ?>
                                            </p>
                                            <small>Ticket Price: <?= $campaign->currency ?> <?= number_format($campaign->ticket_price, 2) ?></small>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <p class="text-muted text-center">No active campaigns</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Draft Campaigns</h3>
                            <span class="badge badge-secondary float-right"><?= count($draft_campaigns) ?></span>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($draft_campaigns)): ?>
                                <div class="list-group">
                                    <?php foreach ($draft_campaigns as $campaign): ?>
                                        <a href="<?= url('campaign/show/' . $campaign->id) ?>" class="list-group-item list-group-item-action">
                                            <div class="d-flex w-100 justify-content-between">
                                                <h5 class="mb-1"><?= htmlspecialchars($campaign->name) ?></h5>
                                                <small class="text-muted">Draft</small>
                                            </div>
                                            <p class="mb-1">
                                                <span class="badge badge-info"><?= htmlspecialchars($campaign->code) ?></span>
                                                <?php if (!empty($campaign->sponsor_name)): ?>
                                                    <span class="badge badge-secondary"><?= htmlspecialchars($campaign->sponsor_name) ?></span>
                                                <?php endif; ?>
                                            </p>
                                            <small>Created: <?= formatDate($campaign->created_at, 'M d, Y') ?></small>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <p class="text-muted text-center">No draft campaigns</p>
                            <?php endif; ?>
                        </div>
                        <div class="card-footer">
                            <a href="<?= url('campaign/create') ?>" class="btn btn-primary btn-block">
                                <i class="fas fa-plus"></i> Create New Campaign
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Quick Actions</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <a href="<?= url('campaign') ?>" class="btn btn-default btn-block">
                                        <i class="fas fa-list"></i> All Campaigns
                                    </a>
                                </div>
                                <div class="col-md-3">
                                    <a href="<?= url('sponsor') ?>" class="btn btn-info btn-block">
                                        <i class="fas fa-handshake"></i> Manage Sponsors
                                    </a>
                                </div>
                                <div class="col-md-3">
                                    <a href="<?= url('programme') ?>" class="btn btn-success btn-block">
                                        <i class="fas fa-microphone"></i> Manage Programmes
                                    </a>
                                </div>
                                <div class="col-md-3">
                                    <a href="<?= url('station') ?>" class="btn btn-warning btn-block">
                                        <i class="fas fa-broadcast-tower"></i> Manage Stations
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php require_once '../app/views/layouts/footer.php'; ?>
