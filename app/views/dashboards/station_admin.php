<?php require_once '../app/views/layouts/header.php'; ?>
<?php require_once '../app/views/layouts/sidebar.php'; ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        <i class="fas fa-broadcast-tower"></i> 
                        <?= htmlspecialchars($data['station']->name ?? 'Station Dashboard') ?>
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url('home') ?>">Home</a></li>
                        <li class="breadcrumb-item active">Station Dashboard</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <!-- Stats Row -->
            <div class="row">
                <!-- Station Wallet Card -->
                <div class="col-lg-4 col-12">
                    <div class="card bg-gradient-success">
                        <div class="card-header border-0">
                            <h3 class="card-title">
                                <i class="fas fa-wallet"></i> Station Wallet
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="text-center">
                                <h2 class="display-4 text-white">
                                    GHS <?= number_format($data['wallet']->balance ?? 0, 2) ?>
                                </h2>
                                <p class="text-white-50">Available Balance</p>
                            </div>
                            <hr class="bg-white">
                            <div class="row text-center">
                                <div class="col-6">
                                    <div class="text-white">
                                        <small>Total Earned</small>
                                        <h5>GHS <?= number_format($data['stats']['station_revenue'] ?? 0, 2) ?></h5>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="text-white">
                                        <small>Withdrawn</small>
                                        <h5>GHS <?= number_format($data['stats']['total_withdrawn'] ?? 0, 2) ?></h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent border-0">
                            <a href="<?= url('withdrawal/create') ?>" class="btn btn-light btn-block">
                                <i class="fas fa-money-bill-wave"></i> Request Withdrawal
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Other Stats -->
                <div class="col-lg-8 col-12">
                    <div class="row">
                        <div class="col-lg-4 col-6">
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3><?= $data['stats']['total_programmes'] ?? 0 ?></h3>
                                    <p>Programmes</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-broadcast-tower"></i>
                                </div>
                                <a href="<?= url('programme') ?>" class="small-box-footer">
                                    View <i class="fas fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div>

                        <div class="col-lg-4 col-6">
                            <div class="small-box bg-warning">
                                <div class="inner">
                                    <h3><?= $data['stats']['active_campaigns'] ?? 0 ?></h3>
                                    <p>Active Campaigns</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-bullhorn"></i>
                                </div>
                                <a href="<?= url('campaign') ?>" class="small-box-footer">
                                    View <i class="fas fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div>

                        <div class="col-lg-4 col-6">
                            <div class="small-box bg-primary">
                                <div class="inner">
                                    <h3><?= $data['stats']['total_tickets'] ?? 0 ?></h3>
                                    <p>Tickets Sold</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-ticket-alt"></i>
                                </div>
                                <a href="<?= url('ticket') ?>" class="small-box-footer">
                                    View <i class="fas fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div>

                        <div class="col-lg-4 col-6">
                            <div class="small-box bg-danger">
                                <div class="inner">
                                    <h3><?= $data['stats']['total_players'] ?? 0 ?></h3>
                                    <p>Players</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-users"></i>
                                </div>
                                <a href="<?= url('player') ?>" class="small-box-footer">
                                    View <i class="fas fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div>

                        <div class="col-lg-4 col-6">
                            <div class="small-box bg-secondary">
                                <div class="inner">
                                    <h3><?= $data['stats']['pending_draws'] ?? 0 ?></h3>
                                    <p>Pending Draws</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-trophy"></i>
                                </div>
                                <a href="<?= url('draw/pending') ?>" class="small-box-footer">
                                    View <i class="fas fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Programmes Table -->
            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-broadcast-tower"></i> Programmes
                            </h3>
                            <div class="card-tools">
                                <a href="<?= url('programme/create') ?>" class="btn btn-sm btn-primary">
                                    <i class="fas fa-plus"></i> Add Programme
                                </a>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <?php if (!empty($data['programmes'])): ?>
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Code</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($data['programmes'] as $programme): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($programme->name) ?></td>
                                                <td><code><?= htmlspecialchars($programme->code) ?></code></td>
                                                <td>
                                                    <?php if ($programme->is_active): ?>
                                                        <span class="badge badge-success">Active</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-secondary">Inactive</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <a href="<?= url('programme/view/' . $programme->id) ?>" class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="<?= url('programme/edit/' . $programme->id) ?>" class="btn btn-sm btn-warning">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <div class="p-3 text-center text-muted">
                                    <i class="fas fa-broadcast-tower fa-3x mb-3"></i>
                                    <p>No programmes found</p>
                                    <a href="<?= url('programme/create') ?>" class="btn btn-primary">
                                        <i class="fas fa-plus"></i> Create First Programme
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Programme Revenue Breakdown -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-trophy"></i> Campaign Prize Pools
                            </h3>
                        </div>
                        <div class="card-body p-0">
                            <?php if (!empty($data['campaigns'])): ?>
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Campaign</th>
                                            <th class="text-right">Prize Pool</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $totalPrizePool = 0;
                                        foreach ($data['campaigns'] as $campaign): 
                                            $prizePool = floatval($campaign->prize_pool_allocated ?? 0);
                                            $totalPrizePool += $prizePool;
                                        ?>
                                            <tr>
                                                <td>
                                                    <?= htmlspecialchars(substr($campaign->name, 0, 25)) ?><?= strlen($campaign->name) > 25 ? '...' : '' ?>
                                                    <br>
                                                    <small class="text-muted">
                                                        <?php if ($campaign->status === 'active'): ?>
                                                            <span class="badge badge-success badge-sm">Active</span>
                                                        <?php else: ?>
                                                            <span class="badge badge-secondary badge-sm"><?= ucfirst($campaign->status) ?></span>
                                                        <?php endif; ?>
                                                    </small>
                                                </td>
                                                <td class="text-right">
                                                    <strong>GHS <?= number_format($prizePool, 2) ?></strong>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                    <tfoot>
                                        <tr class="bg-light">
                                            <th>Total Prize Pool</th>
                                            <th class="text-right">
                                                <strong>GHS <?= number_format($totalPrizePool, 2) ?></strong>
                                            </th>
                                        </tr>
                                    </tfoot>
                                </table>
                            <?php else: ?>
                                <div class="p-3 text-center text-muted">
                                    <p>No campaigns yet</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Revenue by Programme -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-chart-pie"></i> Revenue by Programme
                            </h3>
                        </div>
                        <div class="card-body p-0">
                            <?php if (!empty($data['programme_revenue'])): ?>
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Programme</th>
                                            <th class="text-right">Revenue</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($data['programme_revenue'] as $prog): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($prog->programme_name) ?></td>
                                                <td class="text-right">
                                                    <strong>GHS <?= number_format($prog->revenue, 2) ?></strong>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                    <tfoot>
                                        <tr class="bg-light">
                                            <th>Total</th>
                                            <th class="text-right">
                                                GHS <?= number_format($data['stats']['station_revenue'] ?? 0, 2) ?>
                                            </th>
                                        </tr>
                                    </tfoot>
                                </table>
                            <?php else: ?>
                                <div class="p-3 text-center text-muted">
                                    <p>No revenue data yet</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Recent Campaigns -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-bullhorn"></i> Recent Campaigns
                            </h3>
                        </div>
                        <div class="card-body p-0">
                            <?php if (!empty($data['campaigns'])): ?>
                                <ul class="list-group list-group-flush">
                                    <?php foreach (array_slice($data['campaigns'], 0, 5) as $campaign): ?>
                                        <li class="list-group-item">
                                            <strong><?= htmlspecialchars($campaign->name) ?></strong>
                                            <br>
                                            <small class="text-muted">
                                                <?= htmlspecialchars($campaign->programme_name ?? 'N/A') ?>
                                            </small>
                                            <br>
                                            <?php if ($campaign->status === 'active'): ?>
                                                <span class="badge badge-success">Active</span>
                                            <?php else: ?>
                                                <span class="badge badge-secondary"><?= ucfirst($campaign->status) ?></span>
                                            <?php endif; ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                                <div class="card-footer text-center">
                                    <a href="<?= url('campaign') ?>">View All Campaigns</a>
                                </div>
                            <?php else: ?>
                                <div class="p-3 text-center text-muted">
                                    <p>No campaigns yet</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php require_once '../app/views/layouts/footer.php'; ?>
