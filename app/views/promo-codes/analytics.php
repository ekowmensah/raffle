<?php require_once '../app/views/layouts/header.php'; ?>
<?php require_once '../app/views/layouts/sidebar.php'; ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Promo Code Analytics</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url('home') ?>">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= url('promocode') ?>">Promo Codes</a></li>
                        <li class="breadcrumb-item active">Analytics</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <!-- Promo Code Info -->
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Promo Code: <?= htmlspecialchars($promo->code) ?></h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Name:</strong> <?= htmlspecialchars($promo->name ?? 'N/A') ?></p>
                            <p><strong>Extra Commission:</strong> <?= $promo->extra_commission_percent ?>%</p>
                            <p><strong>Status:</strong> 
                                <?php if ($promo->is_active): ?>
                                    <span class="badge badge-success">Active</span>
                                <?php else: ?>
                                    <span class="badge badge-danger">Inactive</span>
                                <?php endif; ?>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Station:</strong> <?= htmlspecialchars($promo->station_name ?? 'All Stations') ?></p>
                            <p><strong>Programme:</strong> <?= htmlspecialchars($promo->programme_name ?? 'All Programmes') ?></p>
                            <p><strong>Created:</strong> <?= formatDate($promo->created_at) ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics -->
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3><?= $stats->usage_count ?? 0 ?></h3>
                            <p>Total Uses</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-hashtag"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>GHS <?= number_format($stats->total_revenue ?? 0, 2) ?></h3>
                            <p>Total Revenue</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>GHS <?= number_format($stats->total_commission ?? 0, 2) ?></h3>
                            <p>Extra Commission</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-percentage"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3><?= $stats->unique_users ?? 0 ?></h3>
                            <p>Unique Users</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Usage History -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Usage History</h3>
                </div>
                <div class="card-body">
                    <p class="text-muted">Detailed usage tracking will be available once payments are linked to promo codes.</p>
                </div>
            </div>
        </div>
    </section>
</div>

<?php require_once '../app/views/layouts/footer.php'; ?>
