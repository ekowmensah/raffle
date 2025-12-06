<?php require_once '../app/views/layouts/header.php'; ?>
<?php require_once '../app/views/layouts/sidebar.php'; ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0"><i class="fas fa-file-alt"></i> Audit Log Details</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url('home') ?>">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= url('audit') ?>">Audit Logs</a></li>
                        <li class="breadcrumb-item active">View</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <!-- Main Info -->
                <div class="col-md-6">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-info-circle"></i> Log Information</h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width: 40%">Log ID</th>
                                    <td><strong>#<?= $log->id ?></strong></td>
                                </tr>
                                <tr>
                                    <th>Date & Time</th>
                                    <td><?= date('F d, Y H:i:s', strtotime($log->created_at)) ?></td>
                                </tr>
                                <tr>
                                    <th>User</th>
                                    <td>
                                        <?php if ($user): ?>
                                            <strong><?= htmlspecialchars($user->username) ?></strong><br>
                                            <small class="text-muted"><?= htmlspecialchars($user->email) ?></small>
                                        <?php else: ?>
                                            <span class="badge badge-secondary">System</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Action</th>
                                    <td>
                                        <?php
                                        $actionClass = [
                                            'user_login' => 'success',
                                            'user_logout' => 'secondary',
                                            'user_login_failed' => 'danger',
                                            'draw_conducted' => 'warning',
                                            'winner_selected' => 'success',
                                            'payment_processed' => 'info',
                                            'campaign_deleted' => 'danger',
                                            'user_deleted' => 'danger'
                                        ];
                                        $class = $actionClass[$log->action] ?? 'primary';
                                        ?>
                                        <span class="badge badge-<?= $class ?>" style="font-size: 1rem; padding: 0.5rem 1rem;">
                                            <?= ucwords(str_replace('_', ' ', $log->action)) ?>
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Entity Type</th>
                                    <td>
                                        <?php if ($log->entity_type): ?>
                                            <code><?= htmlspecialchars($log->entity_type) ?></code>
                                        <?php else: ?>
                                            <span class="text-muted">N/A</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Entity ID</th>
                                    <td>
                                        <?php if ($log->entity_id): ?>
                                            <strong>#<?= $log->entity_id ?></strong>
                                        <?php else: ?>
                                            <span class="text-muted">N/A</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>IP Address</th>
                                    <td>
                                        <code><?= htmlspecialchars($log->ip_address ?? 'N/A') ?></code>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- User Agent -->
                <div class="col-md-6">
                    <div class="card card-info card-outline">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-desktop"></i> Browser Information</h3>
                        </div>
                        <div class="card-body">
                            <p><strong>User Agent:</strong></p>
                            <p class="text-muted" style="word-break: break-all;">
                                <?= htmlspecialchars($log->user_agent ?? 'N/A') ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Old Values -->
            <?php if ($log->old_values): ?>
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-warning card-outline">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-history"></i> Old Values (Before Change)</h3>
                        </div>
                        <div class="card-body">
                            <pre class="bg-light p-3" style="border-radius: 5px; max-height: 400px; overflow-y: auto;"><?= htmlspecialchars(json_encode(json_decode($log->old_values), JSON_PRETTY_PRINT)) ?></pre>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- New Values -->
            <?php if ($log->new_values): ?>
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-success card-outline">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-check-circle"></i> New Values (After Change)</h3>
                        </div>
                        <div class="card-body">
                            <pre class="bg-light p-3" style="border-radius: 5px; max-height: 400px; overflow-y: auto;"><?= htmlspecialchars(json_encode(json_decode($log->new_values), JSON_PRETTY_PRINT)) ?></pre>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Actions -->
            <div class="row">
                <div class="col-md-12">
                    <a href="<?= url('audit') ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Logs
                    </a>
                    <?php if ($log->entity_type && $log->entity_id): ?>
                        <a href="<?= url('audit/entity/' . $log->entity_type . '/' . $log->entity_id) ?>" class="btn btn-info">
                            <i class="fas fa-list"></i> View Entity History
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
</div>

<?php require_once '../app/views/layouts/footer.php'; ?>
