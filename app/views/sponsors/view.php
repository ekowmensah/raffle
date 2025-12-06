<?php require_once '../app/views/layouts/header.php'; ?>
<?php require_once '../app/views/layouts/sidebar.php'; ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Sponsor Details</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url('home') ?>">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= url('sponsor') ?>">Sponsors</a></li>
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
                            <h3 class="card-title">Sponsor Information</h3>
                            <div class="card-tools">
                                <a href="<?= url('sponsor/edit/' . $sponsor->id) ?>" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <?php if ($campaign_count == 0): ?>
                                <a href="<?= url('sponsor/delete/' . $sponsor->id) ?>" class="btn btn-danger btn-sm" 
                                   onclick="return confirm('Are you sure you want to delete this sponsor?')">
                                    <i class="fas fa-trash"></i> Delete
                                </a>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($sponsor->logo_url)): ?>
                            <div class="text-center mb-3">
                                <img src="<?= asset($sponsor->logo_url) ?>" alt="Logo" style="max-width: 300px; max-height: 300px;">
                            </div>
                            <?php endif; ?>

                            <table class="table table-bordered">
                                <tr>
                                    <th style="width: 200px">ID</th>
                                    <td><?= $sponsor->id ?></td>
                                </tr>
                                <tr>
                                    <th>Sponsor Name</th>
                                    <td><?= htmlspecialchars($sponsor->name) ?></td>
                                </tr>
                                <tr>
                                    <th>Contact Person</th>
                                    <td><?= htmlspecialchars($sponsor->contact_person ?? 'N/A') ?></td>
                                </tr>
                                <tr>
                                    <th>Phone</th>
                                    <td><?= htmlspecialchars($sponsor->phone ?? 'N/A') ?></td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td><?= htmlspecialchars($sponsor->email ?? 'N/A') ?></td>
                                </tr>
                                <tr>
                                    <th>Notes</th>
                                    <td><?= nl2br(htmlspecialchars($sponsor->notes ?? 'N/A')) ?></td>
                                </tr>
                                <tr>
                                    <th>Created At</th>
                                    <td><?= formatDate($sponsor->created_at) ?></td>
                                </tr>
                                <tr>
                                    <th>Updated At</th>
                                    <td><?= formatDate($sponsor->updated_at) ?></td>
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
                            <a href="<?= url('sponsor') ?>" class="btn btn-default btn-block">
                                <i class="fas fa-list"></i> All Sponsors
                            </a>
                            <a href="<?= url('sponsor/create') ?>" class="btn btn-primary btn-block">
                                <i class="fas fa-plus"></i> Create New Sponsor
                            </a>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Statistics</h3>
                        </div>
                        <div class="card-body">
                            <div class="info-box mb-3">
                                <span class="info-box-icon bg-info"><i class="fas fa-trophy"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Campaigns</span>
                                    <span class="info-box-number"><?= $campaign_count ?></span>
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
