<?php require_once '../app/views/layouts/header.php'; ?>
<?php require_once '../app/views/layouts/sidebar.php'; ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        <i class="fas fa-shield-alt"></i> Auditor Dashboard
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url('home') ?>">Home</a></li>
                        <li class="breadcrumb-item active">Auditor Dashboard</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <!-- Stats Row -->
            <div class="row">
                <div class="col-lg-4 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3><?= $data['stats']['total_logs_today'] ?? 0 ?></h3>
                            <p>Audit Logs Today</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                        <a href="<?= url('audit') ?>" class="small-box-footer">
                            View Logs <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <div class="col-lg-4 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3><?= $data['stats']['critical_actions'] ?? 0 ?></h3>
                            <p>Critical Actions</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <a href="<?= url('audit/stats') ?>" class="small-box-footer">
                            View Stats <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <div class="col-lg-4 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3><?= $data['stats']['security_events'] ?? 0 ?></h3>
                            <p>Security Events Today</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-lock"></i>
                        </div>
                        <a href="<?= url('security') ?>" class="small-box-footer">
                            View Security <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Recent Audit Logs -->
            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-clipboard-list"></i> Recent Audit Logs
                            </h3>
                            <div class="card-tools">
                                <a href="<?= url('audit') ?>" class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye"></i> View All
                                </a>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <?php if (!empty($data['recent_audits'])): ?>
                                <div class="table-responsive">
                                    <table class="table table-sm table-striped">
                                        <thead>
                                            <tr>
                                                <th>Time</th>
                                                <th>User</th>
                                                <th>Action</th>
                                                <th>Entity</th>
                                                <th>IP</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach (array_slice($data['recent_audits'], 0, 15) as $log): ?>
                                                <tr>
                                                    <td>
                                                        <small><?= date('H:i:s', strtotime($log->created_at)) ?></small>
                                                    </td>
                                                    <td><?= htmlspecialchars($log->username ?? 'System') ?></td>
                                                    <td>
                                                        <span class="badge badge-info">
                                                            <?= htmlspecialchars($log->action) ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <?php if ($log->entity_type): ?>
                                                            <?= htmlspecialchars($log->entity_type) ?> 
                                                            <small class="text-muted">#<?= $log->entity_id ?></small>
                                                        <?php else: ?>
                                                            <span class="text-muted">-</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><code><?= htmlspecialchars($log->ip_address ?? 'N/A') ?></code></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="p-4 text-center text-muted">
                                    <i class="fas fa-clipboard-list fa-3x mb-3"></i>
                                    <p>No audit logs found</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Security Events -->
                <div class="col-md-4">
                    <div class="card card-danger">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-shield-alt"></i> Security Events
                            </h3>
                        </div>
                        <div class="card-body p-0">
                            <?php if (!empty($data['recent_security'])): ?>
                                <ul class="list-group list-group-flush">
                                    <?php foreach (array_slice($data['recent_security'], 0, 10) as $event): ?>
                                        <li class="list-group-item">
                                            <strong><?= htmlspecialchars($event->event_type) ?></strong>
                                            <br>
                                            <small class="text-muted">
                                                <?= date('M d, H:i', strtotime($event->created_at)) ?>
                                            </small>
                                            <br>
                                            <code><?= htmlspecialchars($event->ip_address ?? 'N/A') ?></code>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                                <div class="card-footer text-center">
                                    <a href="<?= url('security') ?>">View All Security Events</a>
                                </div>
                            <?php else: ?>
                                <div class="p-3 text-center text-muted">
                                    <i class="fas fa-check-circle fa-2x mb-2"></i>
                                    <p>No security events</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Quick Stats -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-chart-pie"></i> Quick Stats
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="info-box mb-3">
                                <span class="info-box-icon bg-info"><i class="fas fa-clipboard-list"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Logs Today</span>
                                    <span class="info-box-number"><?= $data['stats']['total_logs_today'] ?? 0 ?></span>
                                </div>
                            </div>

                            <div class="info-box mb-3">
                                <span class="info-box-icon bg-warning"><i class="fas fa-exclamation-triangle"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Critical Actions</span>
                                    <span class="info-box-number"><?= $data['stats']['critical_actions'] ?? 0 ?></span>
                                </div>
                            </div>

                            <div class="info-box">
                                <span class="info-box-icon bg-danger"><i class="fas fa-lock"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Security Events</span>
                                    <span class="info-box-number"><?= $data['stats']['security_events'] ?? 0 ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="row">
                <div class="col-12">
                    <div class="card bg-gradient-info">
                        <div class="card-body">
                            <h5 class="text-white"><i class="fas fa-bolt"></i> Quick Actions</h5>
                            <div class="btn-group" role="group">
                                <a href="<?= url('audit') ?>" class="btn btn-light">
                                    <i class="fas fa-clipboard-list"></i> Audit Logs
                                </a>
                                <a href="<?= url('audit/stats') ?>" class="btn btn-light">
                                    <i class="fas fa-chart-bar"></i> Audit Statistics
                                </a>
                                <a href="<?= url('security') ?>" class="btn btn-light">
                                    <i class="fas fa-shield-alt"></i> Security Dashboard
                                </a>
                                <a href="<?= url('audit/export') ?>" class="btn btn-light">
                                    <i class="fas fa-download"></i> Export Logs
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php require_once '../app/views/layouts/footer.php'; ?>
