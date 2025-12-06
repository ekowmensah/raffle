<?php require_once '../app/views/layouts/header.php'; ?>
<?php require_once '../app/views/layouts/sidebar.php'; ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Draw Analytics</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url('home') ?>">Home</a></li>
                        <li class="breadcrumb-item active">Draw Analytics</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <!-- Filter -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Select Campaign</h3>
                </div>
                <div class="card-body">
                    <form method="GET" action="<?= url('draw/analytics') ?>">
                        <div class="row">
                            <div class="col-md-6">
                                <select name="campaign" class="form-control" onchange="this.form.submit()">
                                    <option value="">Select Campaign</option>
                                    <?php foreach ($campaigns as $campaign): ?>
                                        <option value="<?= $campaign->id ?>" <?= $selected_campaign == $campaign->id ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($campaign->name) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <?php if ($selected_campaign): ?>
            <!-- Statistics -->
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3><?= $stats['total_draws'] ?></h3>
                            <p>Total Draws</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-random"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3><?= $stats['completed_draws'] ?></h3>
                            <p>Completed Draws</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3><?= $stats['pending_draws'] ?></h3>
                            <p>Pending Draws</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3><?= $stats['total_winners'] ?></h3>
                            <p>Total Winners</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-trophy"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Prize Distribution -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Prize Distribution</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="info-box bg-gradient-success">
                                <span class="info-box-icon"><i class="fas fa-money-bill-wave"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Prizes Awarded</span>
                                    <span class="info-box-number">GHS <?= number_format($stats['total_prizes'], 2) ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php else: ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Please select a campaign to view analytics.
            </div>
            <?php endif; ?>
        </div>
    </section>
</div>

<?php require_once '../app/views/layouts/footer.php'; ?>
