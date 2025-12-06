<?php require_once '../app/views/layouts/header.php'; ?>
<?php require_once '../app/views/layouts/sidebar.php'; ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0"><i class="fas fa-clipboard-list"></i> Audit Logs</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url('home') ?>">Home</a></li>
                        <li class="breadcrumb-item active">Audit Logs</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <!-- Filter Card -->
            <div class="card card-primary card-outline collapsed-card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-filter"></i> Filters</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <form method="GET" action="<?= url('audit') ?>">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>User</label>
                                    <select name="user_id" class="form-control">
                                        <option value="">All Users</option>
                                        <?php foreach ($users as $user): ?>
                                            <option value="<?= $user->id ?>" <?= ($filters['user_id'] == $user->id) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($user->username) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Action</label>
                                    <select name="action" class="form-control">
                                        <option value="">All Actions</option>
                                        <?php foreach ($actionTypes as $action): ?>
                                            <option value="<?= $action ?>" <?= ($filters['action'] == $action) ? 'selected' : '' ?>>
                                                <?= ucwords(str_replace('_', ' ', $action)) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Entity Type</label>
                                    <select name="entity_type" class="form-control">
                                        <option value="">All Types</option>
                                        <?php foreach ($entityTypes as $type): ?>
                                            <option value="<?= $type ?>" <?= ($filters['entity_type'] == $type) ? 'selected' : '' ?>>
                                                <?= ucwords(str_replace('_', ' ', $type)) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Entity ID</label>
                                    <input type="text" name="entity_id" class="form-control" value="<?= htmlspecialchars($filters['entity_id'] ?? '') ?>" placeholder="Entity ID">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Date From</label>
                                    <input type="date" name="date_from" class="form-control" value="<?= htmlspecialchars($filters['date_from'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Date To</label>
                                    <input type="date" name="date_to" class="form-control" value="<?= htmlspecialchars($filters['date_to'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>IP Address</label>
                                    <input type="text" name="ip_address" class="form-control" value="<?= htmlspecialchars($filters['ip_address'] ?? '') ?>" placeholder="IP Address">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Limit</label>
                                    <select name="limit" class="form-control">
                                        <option value="50" <?= ($filters['limit'] == 50) ? 'selected' : '' ?>>50</option>
                                        <option value="100" <?= ($filters['limit'] == 100) ? 'selected' : '' ?>>100</option>
                                        <option value="500" <?= ($filters['limit'] == 500) ? 'selected' : '' ?>>500</option>
                                        <option value="1000" <?= ($filters['limit'] == 1000) ? 'selected' : '' ?>>1000</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Apply Filters
                                </button>
                                <a href="<?= url('audit') ?>" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Clear
                                </a>
                                <a href="<?= url('audit/export') . '?' . http_build_query($filters) ?>" class="btn btn-success float-right">
                                    <i class="fas fa-download"></i> Export CSV
                                </a>
                                <a href="<?= url('audit/stats') ?>" class="btn btn-info float-right mr-2">
                                    <i class="fas fa-chart-bar"></i> Statistics
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Logs Table -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Audit Trail (<?= count($logs) ?> records)</h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Date/Time</th>
                                <th>User</th>
                                <th>Action</th>
                                <th>Entity</th>
                                <th>IP Address</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($logs)): ?>
                                <?php foreach ($logs as $log): ?>
                                    <tr>
                                        <td><?= $log->id ?></td>
                                        <td>
                                            <small><?= date('M d, Y H:i:s', strtotime($log->created_at)) ?></small>
                                        </td>
                                        <td>
                                            <?php if ($log->username): ?>
                                                <strong><?= htmlspecialchars($log->username) ?></strong>
                                            <?php else: ?>
                                                <span class="text-muted">System</span>
                                            <?php endif; ?>
                                        </td>
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
                                            <span class="badge badge-<?= $class ?>">
                                                <?= ucwords(str_replace('_', ' ', $log->action)) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($log->entity_type): ?>
                                                <code><?= htmlspecialchars($log->entity_type) ?></code>
                                                <?php if ($log->entity_id): ?>
                                                    #<?= $log->entity_id ?>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <small><?= htmlspecialchars($log->ip_address ?? '-') ?></small>
                                        </td>
                                        <td>
                                            <a href="<?= url('audit/view/' . $log->id) ?>" class="btn btn-sm btn-info" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted">No audit logs found</td>
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
