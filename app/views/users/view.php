<?php require_once '../app/views/layouts/header.php'; ?>
<?php require_once '../app/views/layouts/sidebar.php'; ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">User Details</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url('home') ?>">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= url('user') ?>">Users</a></li>
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
                            <h3 class="card-title">User Information</h3>
                            <div class="card-tools">
                                <a href="<?= url('user/edit/' . $user->id) ?>" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <?php if ($user->id != $_SESSION['user_id']): ?>
                                <a href="<?= url('user/delete/' . $user->id) ?>" class="btn btn-danger btn-sm" 
                                   onclick="return confirm('Are you sure you want to delete this user?')">
                                    <i class="fas fa-trash"></i> Delete
                                </a>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width: 200px">ID</th>
                                    <td><?= $user->id ?></td>
                                </tr>
                                <tr>
                                    <th>Full Name</th>
                                    <td><?= htmlspecialchars($user->name) ?></td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td><?= htmlspecialchars($user->email) ?></td>
                                </tr>
                                <tr>
                                    <th>Phone</th>
                                    <td><?= htmlspecialchars($user->phone ?? 'N/A') ?></td>
                                </tr>
                                <tr>
                                    <th>Role</th>
                                    <td><span class="badge badge-primary"><?= htmlspecialchars($user->role_name ?? 'N/A') ?></span></td>
                                </tr>
                                <tr>
                                    <th>Station</th>
                                    <td><?= htmlspecialchars($user->station_name ?? 'N/A') ?></td>
                                </tr>
                                <tr>
                                    <th>Programme</th>
                                    <td><?= htmlspecialchars($user->programme_name ?? 'N/A') ?></td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        <?php if ($user->is_active): ?>
                                            <span class="badge badge-success">Active</span>
                                        <?php else: ?>
                                            <span class="badge badge-danger">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Last Login</th>
                                    <td><?= $user->last_login_at ? formatDate($user->last_login_at) : 'Never' ?></td>
                                </tr>
                                <tr>
                                    <th>Created At</th>
                                    <td><?= formatDate($user->created_at) ?></td>
                                </tr>
                                <tr>
                                    <th>Updated At</th>
                                    <td><?= formatDate($user->updated_at) ?></td>
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
                            <a href="<?= url('user') ?>" class="btn btn-default btn-block">
                                <i class="fas fa-list"></i> All Users
                            </a>
                            <a href="<?= url('user/create') ?>" class="btn btn-primary btn-block">
                                <i class="fas fa-plus"></i> Create New User
                            </a>
                            <a href="<?= url('role') ?>" class="btn btn-info btn-block">
                                <i class="fas fa-user-shield"></i> Manage Roles
                            </a>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">User Stats</h3>
                        </div>
                        <div class="card-body">
                            <div class="info-box mb-3 bg-info">
                                <span class="info-box-icon"><i class="fas fa-user-clock"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Account Age</span>
                                    <span class="info-box-number">
                                        <?php
                                        $created = new DateTime($user->created_at);
                                        $now = new DateTime();
                                        $diff = $created->diff($now);
                                        echo $diff->days . ' days';
                                        ?>
                                    </span>
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
