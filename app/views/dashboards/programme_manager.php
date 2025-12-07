<?php require_once '../app/views/layouts/header.php'; ?>
<?php require_once '../app/views/layouts/sidebar.php'; ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        <i class="fas fa-microphone"></i> 
                        <?= htmlspecialchars($data['programme']->name ?? 'Programme Dashboard') ?>
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url('home') ?>">Home</a></li>
                        <li class="breadcrumb-item active">Programme Dashboard</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <!-- Stats Row -->
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3><?= $data['stats']['pending_draws'] ?? 0 ?></h3>
                            <p>Pending Draws</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <a href="<?= url('draw') ?>" class="small-box-footer">
                            View Draws <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3><?= $data['stats']['completed_today'] ?? 0 ?></h3>
                            <p>Completed Today</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <a href="<?= url('draw') ?>" class="small-box-footer">
                            View History <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3><?= $data['stats']['active_campaigns'] ?? 0 ?></h3>
                            <p>Active Campaigns</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-bullhorn"></i>
                        </div>
                        <a href="<?= url('campaign') ?>" class="small-box-footer">
                            View Campaigns <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-primary">
                        <div class="inner">
                            <h3>GHS <?= number_format($data['stats']['total_revenue'] ?? 0, 2) ?></h3>
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
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3><?= $data['stats']['tickets_sold_today'] ?? 0 ?></h3>
                            <p>Tickets Sold Today</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-ticket-alt"></i>
                        </div>
                        <a href="<?= url('ticket') ?>" class="small-box-footer">
                            View Tickets <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Pending Draws - Priority Section -->
            <?php if (!empty($data['pending_draws'])): ?>
                <div class="row">
                    <div class="col-12">
                        <div class="card card-danger">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-exclamation-triangle"></i> 
                                    <strong>PENDING DRAWS - ACTION REQUIRED</strong>
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th>Campaign</th>
                                                <th>Draw Date</th>
                                                <th>Type</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($data['pending_draws'] as $draw): ?>
                                                <tr>
                                                    <td><strong><?= htmlspecialchars($draw->campaign_name) ?></strong></td>
                                                    <td><?= date('M d, Y h:i A', strtotime($draw->draw_date)) ?></td>
                                                    <td>
                                                        <span class="badge badge-primary">
                                                            <?= ucfirst(str_replace('_', ' ', $draw->draw_type)) ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span class="badge badge-warning">Pending</span>
                                                    </td>
                                                    <td>
                                                        <a href="<?= url('draw/live/' . $draw->id) ?>" class="btn btn-success btn-sm">
                                                            <i class="fas fa-play-circle"></i> Start Draw
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Today's Draws & Campaigns -->
            <div class="row">
                <!-- Today's Completed Draws -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-check-circle"></i> Today's Completed Draws
                            </h3>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($data['today_draws'])): ?>
                                <ul class="list-group">
                                    <?php foreach ($data['today_draws'] as $draw): ?>
                                        <li class="list-group-item">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <strong><?= htmlspecialchars($draw->campaign_name) ?></strong>
                                                    <br>
                                                    <small class="text-muted">
                                                        <?= date('h:i A', strtotime($draw->draw_date)) ?>
                                                    </small>
                                                </div>
                                                <div>
                                                    <?php if ($draw->status === 'completed'): ?>
                                                        <span class="badge badge-success">
                                                            <i class="fas fa-check"></i> Completed
                                                        </span>
                                                    <?php endif; ?>
                                                    <a href="<?= url('draw/view/' . $draw->id) ?>" class="btn btn-sm btn-info ml-2">
                                                        <i class="fas fa-eye"></i> View
                                                    </a>
                                                </div>
                                            </div>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
                                <div class="text-center text-muted py-4">
                                    <i class="fas fa-calendar-check fa-3x mb-3"></i>
                                    <p>No draws completed today</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Active Campaigns -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-bullhorn"></i> Active Campaigns
                            </h3>
                            <div class="card-tools">
                                <a href="<?= url('campaign/create') ?>" class="btn btn-sm btn-primary">
                                    <i class="fas fa-plus"></i> New Campaign
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($data['campaigns'])): ?>
                                <ul class="list-group">
                                    <?php foreach (array_slice($data['campaigns'], 0, 5) as $campaign): ?>
                                        <li class="list-group-item">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <strong><?= htmlspecialchars($campaign->name) ?></strong>
                                                    <br>
                                                    <small class="text-muted">
                                                        Ticket Price: GHS <?= number_format($campaign->ticket_price ?? 0, 2) ?>
                                                    </small>
                                                </div>
                                                <div>
                                                    <?php if ($campaign->status === 'active'): ?>
                                                        <span class="badge badge-success">Active</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-secondary"><?= ucfirst($campaign->status) ?></span>
                                                    <?php endif; ?>
                                                    <a href="<?= url('campaign/show/' . $campaign->id) ?>" class="btn btn-sm btn-info ml-2">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                                <?php if (count($data['campaigns']) > 5): ?>
                                    <div class="text-center mt-3">
                                        <a href="<?= url('campaign') ?>" class="btn btn-sm btn-outline-primary">
                                            View All Campaigns
                                        </a>
                                    </div>
                                <?php endif; ?>
                            <?php else: ?>
                                <div class="text-center text-muted py-4">
                                    <i class="fas fa-bullhorn fa-3x mb-3"></i>
                                    <p>No active campaigns</p>
                                    <a href="<?= url('campaign/create') ?>" class="btn btn-primary">
                                        <i class="fas fa-plus"></i> Create Campaign
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="row">
                <div class="col-12">
                    <div class="card bg-gradient-primary">
                        <div class="card-body">
                            <h5 class="text-white"><i class="fas fa-bolt"></i> Quick Actions</h5>
                            <div class="btn-group" role="group">
                                <a href="<?= url('draw') ?>" class="btn btn-light">
                                    <i class="fas fa-list"></i> View All Draws
                                </a>
                                <a href="<?= url('campaign') ?>" class="btn btn-light">
                                    <i class="fas fa-bullhorn"></i> Manage Campaigns
                                </a>
                                <a href="<?= url('ticket') ?>" class="btn btn-light">
                                    <i class="fas fa-ticket-alt"></i> View Tickets
                                </a>
                                <a href="<?= url('winner') ?>" class="btn btn-light">
                                    <i class="fas fa-trophy"></i> View Winners
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php require_once '../app/views/layouts/footer.php'; ?>
