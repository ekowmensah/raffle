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
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3><?= $data['stats']['total_programmes'] ?? 0 ?></h3>
                            <p>Total Programmes</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-broadcast-tower"></i>
                        </div>
                        <a href="<?= url('programme') ?>" class="small-box-footer">
                            View Programmes <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
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
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>GHS <?= number_format($data['stats']['station_revenue'] ?? 0, 2) ?></h3>
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
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3><?= $data['stats']['total_users'] ?? 0 ?></h3>
                            <p>Station Users</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <a href="<?= url('user') ?>" class="small-box-footer">
                            View Users <i class="fas fa-arrow-circle-right"></i>
                        </a>
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

                <!-- Recent Campaigns -->
                <div class="col-md-4">
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

                    <!-- Station Users -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-users"></i> Station Users
                            </h3>
                        </div>
                        <div class="card-body p-0">
                            <?php if (!empty($data['users'])): ?>
                                <ul class="list-group list-group-flush">
                                    <?php foreach (array_slice($data['users'], 0, 5) as $user): ?>
                                        <li class="list-group-item">
                                            <strong><?= htmlspecialchars($user->name) ?></strong>
                                            <br>
                                            <small class="text-muted"><?= htmlspecialchars($user->email) ?></small>
                                            <br>
                                            <span class="badge badge-info"><?= htmlspecialchars($user->role_name ?? 'User') ?></span>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                                <div class="card-footer text-center">
                                    <a href="<?= url('user') ?>">View All Users</a>
                                </div>
                            <?php else: ?>
                                <div class="p-3 text-center text-muted">
                                    <p>No users found</p>
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
