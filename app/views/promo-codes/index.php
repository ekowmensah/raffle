<?php require_once '../app/views/layouts/header.php'; ?>
<?php require_once '../app/views/layouts/sidebar.php'; ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Promo Codes</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url('home') ?>">Home</a></li>
                        <li class="breadcrumb-item active">Promo Codes</li>
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

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">All Promo Codes</h3>
                    <div class="card-tools">
                        <a href="<?= url('promocode/create') ?>" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Create Promo Code
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Name</th>
                                <th>Extra Commission</th>
                                <th>Assigned To</th>
                                <th>Created</th>
                                <th>Station</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($promos)): ?>
                                <?php foreach ($promos as $promo): ?>
                                    <tr>
                                        <td><strong><?= htmlspecialchars($promo->code) ?></strong></td>
                                        <td><?= htmlspecialchars($promo->name ?? 'N/A') ?></td>
                                        <td><?= $promo->extra_commission_percent ?>%</td>
                                        <td>
                                            <?php if ($promo->user_id): ?>
                                                <span class="badge badge-info">User</span>
                                            <?php elseif ($promo->programme_id): ?>
                                                <span class="badge badge-success">Programme</span>
                                            <?php else: ?>
                                                <span class="badge badge-secondary">All</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= formatDate($promo->created_at, 'M d, Y') ?></td>
                                        <td><?= htmlspecialchars($promo->station_name ?? 'N/A') ?></td>
                                        <td>
                                            <?php if ($promo->is_active): ?>
                                                <span class="badge badge-success">Active</span>
                                            <?php else: ?>
                                                <span class="badge badge-danger">Inactive</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="<?= url('promocode/edit/' . $promo->id) ?>" class="btn btn-warning btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="<?= url('promocode/analytics/' . $promo->id) ?>" class="btn btn-info btn-sm">
                                                <i class="fas fa-chart-bar"></i>
                                            </a>
                                            <a href="<?= url('promocode/delete/' . $promo->id) ?>" class="btn btn-danger btn-sm"
                                               onclick="return confirm('Delete this promo code?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center">No promo codes found</td>
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
