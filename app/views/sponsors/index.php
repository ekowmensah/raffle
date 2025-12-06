<?php require_once '../app/views/layouts/header.php'; ?>
<?php require_once '../app/views/layouts/sidebar.php'; ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Sponsors</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url('home') ?>">Home</a></li>
                        <li class="breadcrumb-item active">Sponsors</li>
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
                    <h3 class="card-title">All Sponsors</h3>
                    <div class="card-tools">
                        <a href="<?= url('sponsor/create') ?>" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Add Sponsor
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th style="width: 10px">ID</th>
                                <th>Logo</th>
                                <th>Name</th>
                                <th>Contact Person</th>
                                <th>Phone</th>
                                <th>Email</th>
                                <th>Campaigns</th>
                                <th>Status</th>
                                <th style="width: 150px">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($sponsors)): ?>
                                <?php foreach ($sponsors as $sponsor): ?>
                                    <tr>
                                        <td><?= $sponsor->id ?></td>
                                        <td>
                                            <?php if (!empty($sponsor->logo_url)): ?>
                                                <img src="<?= asset($sponsor->logo_url) ?>" alt="Logo" style="max-width: 50px; max-height: 50px;">
                                            <?php else: ?>
                                                <i class="fas fa-image text-muted"></i>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= htmlspecialchars($sponsor->name) ?></td>
                                        <td><?= htmlspecialchars($sponsor->contact_person ?? 'N/A') ?></td>
                                        <td><?= htmlspecialchars($sponsor->phone ?? 'N/A') ?></td>
                                        <td><?= htmlspecialchars($sponsor->email ?? 'N/A') ?></td>
                                        <td><span class="badge badge-info"><?= $sponsor->campaign_count ?? 0 ?></span></td>
                                        <td>
                                            <span class="badge badge-success">Active</span>
                                        </td>
                                        <td>
                                            <a href="<?= url('sponsor/show/' . $sponsor->id) ?>" class="btn btn-info btn-sm" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="<?= url('sponsor/edit/' . $sponsor->id) ?>" class="btn btn-warning btn-sm" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <?php if ($sponsor->campaign_count == 0): ?>
                                            <a href="<?= url('sponsor/delete/' . $sponsor->id) ?>" class="btn btn-danger btn-sm" title="Delete" 
                                               onclick="return confirm('Are you sure you want to delete this sponsor?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="9" class="text-center">No sponsors found</td>
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
