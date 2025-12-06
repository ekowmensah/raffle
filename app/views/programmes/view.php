<?php require_once '../app/views/layouts/header.php'; ?>
<?php require_once '../app/views/layouts/sidebar.php'; ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Programme Details</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url('home') ?>">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= url('programme') ?>">Programmes</a></li>
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
                            <h3 class="card-title">Programme Information</h3>
                            <div class="card-tools">
                                <a href="<?= url('programme/edit/' . $programme->id) ?>" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <a href="<?= url('programme/delete/' . $programme->id) ?>" class="btn btn-danger btn-sm" 
                                   onclick="return confirm('Are you sure you want to delete this programme?')">
                                    <i class="fas fa-trash"></i> Delete
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width: 200px">ID</th>
                                    <td><?= $programme->id ?></td>
                                </tr>
                                <tr>
                                    <th>Programme Name</th>
                                    <td><?= htmlspecialchars($programme->name) ?></td>
                                </tr>
                                <tr>
                                    <th>Station</th>
                                    <td>
                                        <a href="<?= url('station/show/' . $programme->station_id) ?>">
                                            <?= htmlspecialchars($programme->station_name ?? 'N/A') ?>
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Code</th>
                                    <td><code><?= htmlspecialchars($programme->code) ?></code></td>
                                </tr>
                                <tr>
                                    <th>USSD Option Number</th>
                                    <td>
                                        <?php if ($programme->ussd_option_number): ?>
                                            <span class="badge badge-info"><?= $programme->ussd_option_number ?></span>
                                        <?php else: ?>
                                            <span class="text-muted">Not set</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Station Commission</th>
                                    <td><?= $programme->station_percent ?? 'Using station default' ?><?= $programme->station_percent ? '%' : '' ?></td>
                                </tr>
                                <tr>
                                    <th>Programme Commission</th>
                                    <td><?= $programme->programme_percent ?? 'Using station default' ?><?= $programme->programme_percent ? '%' : '' ?></td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        <?php if ($programme->is_active): ?>
                                            <span class="badge badge-success">Active</span>
                                        <?php else: ?>
                                            <span class="badge badge-danger">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Created At</th>
                                    <td><?= formatDate($programme->created_at) ?></td>
                                </tr>
                                <tr>
                                    <th>Updated At</th>
                                    <td><?= formatDate($programme->updated_at) ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Quick Actions</h3>
                        </div>
                        <div class="card-body">
                            <a href="<?= url('programme') ?>" class="btn btn-default btn-block">
                                <i class="fas fa-list"></i> All Programmes
                            </a>
                            <a href="<?= url('programme/create') ?>" class="btn btn-primary btn-block">
                                <i class="fas fa-plus"></i> Create New Programme
                            </a>
                            <a href="<?= url('station/show/' . $programme->station_id) ?>" class="btn btn-info btn-block">
                                <i class="fas fa-broadcast-tower"></i> View Station
                            </a>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Programme Stats</h3>
                        </div>
                        <div class="card-body">
                            <div class="info-box mb-3">
                                <span class="info-box-icon bg-info"><i class="fas fa-users"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Users</span>
                                    <span class="info-box-number"><?= $programme->user_count ?? 0 ?></span>
                                </div>
                            </div>
                            <div class="info-box mb-3">
                                <span class="info-box-icon bg-success"><i class="fas fa-ticket-alt"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Tickets</span>
                                    <span class="info-box-number"><?= $programme->ticket_count ?? 0 ?></span>
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
