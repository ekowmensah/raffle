<?php require_once '../app/views/layouts/header.php'; ?>
<?php require_once '../app/views/layouts/sidebar.php'; ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0"><i class="fas fa-database"></i> Cache Management</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url('home') ?>">Home</a></li>
                        <li class="breadcrumb-item active">Cache</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <!-- Cache Statistics -->
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3><?= $stats['total_entries'] ?></h3>
                            <p>Total Cache Entries</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-database"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3><?= $stats['active_entries'] ?></h3>
                            <p>Active Entries</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3><?= $stats['expired_entries'] ?></h3>
                            <p>Expired Entries</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3><?= $stats['total_size_formatted'] ?></h3>
                            <p>Total Cache Size</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-hdd"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cache Actions -->
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-tools"></i> Cache Actions</h3>
                        </div>
                        <div class="card-body">
                            <p class="text-muted">
                                <i class="fas fa-info-circle"></i> 
                                The cache system stores frequently accessed data to improve performance. 
                                Use these actions to manage cached data.
                            </p>
                            
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="card card-outline card-warning">
                                        <div class="card-header">
                                            <h5 class="card-title"><i class="fas fa-broom"></i> Clean Expired Cache</h5>
                                        </div>
                                        <div class="card-body">
                                            <p>Remove expired cache entries to free up space. This is safe and recommended.</p>
                                            <form method="POST" action="<?= url('cache/cleanExpired') ?>">
                                                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                                <button type="submit" class="btn btn-warning" onclick="return confirm('Clean expired cache entries?')">
                                                    <i class="fas fa-broom"></i> Clean Expired
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="card card-outline card-danger">
                                        <div class="card-header">
                                            <h5 class="card-title"><i class="fas fa-trash"></i> Clear All Cache</h5>
                                        </div>
                                        <div class="card-body">
                                            <p>Remove all cached data. Use this if you experience issues or after major updates.</p>
                                            <form method="POST" action="<?= url('cache/clear') ?>">
                                                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure? This will clear ALL cached data.')">
                                                    <i class="fas fa-trash"></i> Clear All Cache
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cached Items Info -->
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-list"></i> Cached Data Types</h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Data Type</th>
                                        <th>Cache Duration</th>
                                        <th>Purpose</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><code>dashboard_stats</code></td>
                                        <td>5 minutes</td>
                                        <td>Dashboard statistics (campaigns, players, revenue)</td>
                                    </tr>
                                    <tr>
                                        <td><code>recent_payments</code></td>
                                        <td>2 minutes</td>
                                        <td>Recent payment transactions</td>
                                    </tr>
                                    <tr>
                                        <td><code>recent_tickets</code></td>
                                        <td>2 minutes</td>
                                        <td>Recently generated tickets</td>
                                    </tr>
                                    <tr>
                                        <td><code>upcoming_draws</code></td>
                                        <td>2 minutes</td>
                                        <td>Upcoming scheduled draws</td>
                                    </tr>
                                    <tr>
                                        <td><code>revenue_trend_*</code></td>
                                        <td>5 minutes</td>
                                        <td>Revenue trend analytics data</td>
                                    </tr>
                                    <tr>
                                        <td><code>campaign_performance</code></td>
                                        <td>10 minutes</td>
                                        <td>Campaign performance metrics</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php require_once '../app/views/layouts/footer.php'; ?>
