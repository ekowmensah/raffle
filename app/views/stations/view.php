<?php require_once '../app/views/layouts/header.php'; ?>
<?php require_once '../app/views/layouts/sidebar.php'; ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Station Details</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url('home') ?>">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= url('station') ?>">Stations</a></li>
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

            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Station Information</h3>
                            <div class="card-tools">
                                <a href="<?= url('station/edit/' . $station->id) ?>" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <a href="<?= url('station/delete/' . $station->id) ?>" class="btn btn-danger btn-sm" 
                                   onclick="return confirm('Are you sure you want to delete this station?')">
                                    <i class="fas fa-trash"></i> Delete
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width: 200px">ID</th>
                                    <td><?= $station->id ?></td>
                                </tr>
                                <tr>
                                    <th>Station Name</th>
                                    <td><?= htmlspecialchars($station->name) ?></td>
                                </tr>
                                <tr>
                                    <th>Short Code</th>
                                    <td><span class="badge badge-info"><?= htmlspecialchars($station->short_code_label) ?></span></td>
                                </tr>
                                <tr>
                                    <th>Code</th>
                                    <td><code><?= htmlspecialchars($station->code) ?></code></td>
                                </tr>
                                <tr>
                                    <th>Phone</th>
                                    <td><?= htmlspecialchars($station->phone ?? 'N/A') ?></td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td><?= htmlspecialchars($station->email ?? 'N/A') ?></td>
                                </tr>
                                <tr>
                                    <th>Location</th>
                                    <td><?= htmlspecialchars($station->location ?? 'N/A') ?></td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        <?php if ($station->is_active): ?>
                                            <span class="badge badge-success">Active</span>
                                        <?php else: ?>
                                            <span class="badge badge-danger">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Created At</th>
                                    <td><?= formatDate($station->created_at) ?></td>
                                </tr>
                                <tr>
                                    <th>Updated At</th>
                                    <td><?= formatDate($station->updated_at) ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Commission Configuration</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="info-box bg-info">
                                        <span class="info-box-icon"><i class="fas fa-broadcast-tower"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Station Commission</span>
                                            <span class="info-box-number"><?= $station->default_station_percent ?? 0 ?>%</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="info-box bg-success">
                                        <span class="info-box-icon"><i class="fas fa-microphone"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Programme Commission</span>
                                            <span class="info-box-number"><?= $station->default_programme_percent ?? 0 ?>%</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="info-box bg-warning">
                                        <span class="info-box-icon"><i class="fas fa-trophy"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Prize Pool</span>
                                            <span class="info-box-number"><?= $station->default_prize_pool_percent ?? 0 ?>%</span>
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
                            <a href="<?= url('station') ?>" class="btn btn-default btn-block">
                                <i class="fas fa-list"></i> All Stations
                            </a>
                            <a href="<?= url('station/create') ?>" class="btn btn-primary btn-block">
                                <i class="fas fa-plus"></i> Create New Station
                            </a>
                            <a href="<?= url('programme?station=' . $station->id) ?>" class="btn btn-info btn-block">
                                <i class="fas fa-microphone"></i> View Programmes
                            </a>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Station Stats</h3>
                        </div>
                        <div class="card-body">
                            <div class="info-box mb-3">
                                <span class="info-box-icon bg-primary"><i class="fas fa-microphone"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Programmes</span>
                                    <span class="info-box-number"><?= count($programmes ?? []) ?></span>
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
