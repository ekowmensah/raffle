<?php require_once '../app/views/layouts/header.php'; ?>
<?php require_once '../app/views/layouts/sidebar.php'; ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Role Details</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url('home') ?>">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= url('role') ?>">Roles</a></li>
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
                            <h3 class="card-title">Role Information</h3>
                            <div class="card-tools">
                                <a href="<?= url('role/edit/' . $role->id) ?>" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <?php if ($user_count == 0): ?>
                                <a href="<?= url('role/delete/' . $role->id) ?>" class="btn btn-danger btn-sm" 
                                   onclick="return confirm('Are you sure you want to delete this role?')">
                                    <i class="fas fa-trash"></i> Delete
                                </a>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width: 200px">ID</th>
                                    <td><?= $role->id ?></td>
                                </tr>
                                <tr>
                                    <th>Role Name</th>
                                    <td><span class="badge badge-primary"><?= htmlspecialchars($role->name) ?></span></td>
                                </tr>
                                <tr>
                                    <th>Description</th>
                                    <td><?= htmlspecialchars($role->description ?? 'N/A') ?></td>
                                </tr>
                                <tr>
                                    <th>Users Assigned</th>
                                    <td>
                                        <span class="badge badge-info"><?= $user_count ?></span>
                                        <?php if ($user_count > 0): ?>
                                            <a href="<?= url('user?role=' . $role->id) ?>" class="ml-2">View Users</a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Created At</th>
                                    <td><?= formatDate($role->created_at) ?></td>
                                </tr>
                                <tr>
                                    <th>Updated At</th>
                                    <td><?= formatDate($role->updated_at) ?></td>
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
                            <a href="<?= url('role') ?>" class="btn btn-default btn-block">
                                <i class="fas fa-list"></i> All Roles
                            </a>
                            <a href="<?= url('role/create') ?>" class="btn btn-primary btn-block">
                                <i class="fas fa-plus"></i> Create New Role
                            </a>
                            <a href="<?= url('user') ?>" class="btn btn-info btn-block">
                                <i class="fas fa-users"></i> Manage Users
                            </a>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Role Permissions</h3>
                        </div>
                        <div class="card-body">
                            <small class="text-muted">
                                <?php if ($role->name === 'super_admin'): ?>
                                    <i class="fas fa-crown text-warning"></i> Full system access
                                <?php elseif ($role->name === 'station_admin'): ?>
                                    <i class="fas fa-broadcast-tower"></i> Station management
                                <?php elseif ($role->name === 'programme_manager'): ?>
                                    <i class="fas fa-microphone"></i> Programme operations
                                <?php elseif ($role->name === 'finance'): ?>
                                    <i class="fas fa-dollar-sign"></i> Financial access
                                <?php elseif ($role->name === 'auditor'): ?>
                                    <i class="fas fa-eye"></i> Read-only access
                                <?php else: ?>
                                    <i class="fas fa-info-circle"></i> Custom role
                                <?php endif; ?>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php require_once '../app/views/layouts/footer.php'; ?>
