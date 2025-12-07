<?php require_once '../app/views/layouts/header.php'; ?>
<?php require_once '../app/views/layouts/sidebar.php'; ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <i class="fas fa-user-circle fa-5x text-muted mb-4"></i>
                            <h3>Welcome, <?= htmlspecialchars($data['user']->name ?? 'User') ?>!</h3>
                            <p class="text-muted">
                                Role: <span class="badge badge-info"><?= htmlspecialchars($data['user']->role_name ?? 'N/A') ?></span>
                            </p>
                            <p class="mt-4">
                                Your dashboard is being set up. Please contact your administrator if you need access to specific features.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php require_once '../app/views/layouts/footer.php'; ?>
