<?php require_once '../app/views/layouts/header.php'; ?>
<?php require_once '../app/views/layouts/sidebar.php'; ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Programmes</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url('home') ?>">Home</a></li>
                        <li class="breadcrumb-item active">Programmes</li>
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

            <?php if (flash('error')): ?>
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <i class="icon fas fa-ban"></i> <?= flash('error') ?>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">All Programmes</h3>
                    <div class="card-tools">
                        <a href="<?= url('programme/create') ?>" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Add Programme
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th style="width: 10px">ID</th>
                                <th>Programme Name</th>
                                <th>Station</th>
                                <th>Code</th>
                                <th>USSD Option</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th style="width: 150px">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($programmes)): ?>
                                <?php foreach ($programmes as $programme): ?>
                                    <tr>
                                        <td><?= $programme->id ?></td>
                                        <td><?= htmlspecialchars($programme->name) ?></td>
                                        <td>
                                            <a href="<?= url('station/show/' . $programme->station_id) ?>">
                                                <?= htmlspecialchars($programme->station_name ?? 'N/A') ?>
                                            </a>
                                        </td>
                                        <td><code><?= htmlspecialchars($programme->code) ?></code></td>
                                        <td>
                                            <?php if ($programme->ussd_option_number): ?>
                                                <span class="badge badge-info"><?= $programme->ussd_option_number ?></span>
                                            <?php else: ?>
                                                <span class="text-muted">N/A</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($programme->is_active): ?>
                                                <span class="badge badge-success">Active</span>
                                            <?php else: ?>
                                                <span class="badge badge-danger">Inactive</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= formatDate($programme->created_at, 'M d, Y') ?></td>
                                        <td>
                                            <a href="<?= url('programme/show/' . $programme->id) ?>" class="btn btn-info btn-sm" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="<?= url('programme/edit/' . $programme->id) ?>" class="btn btn-warning btn-sm" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="<?= url('programme/delete/' . $programme->id) ?>" class="btn btn-danger btn-sm" title="Delete" 
                                               onclick="return confirm('Are you sure you want to delete this programme?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center">No programmes found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

<?php require_once '../app/views/layouts/footer.php'; ?>
