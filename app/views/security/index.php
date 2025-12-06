<?php require_once '../app/views/layouts/header.php'; ?>
<?php require_once '../app/views/layouts/sidebar.php'; ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0"><i class="fas fa-shield-alt"></i> Security Dashboard</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url('home') ?>">Home</a></li>
                        <li class="breadcrumb-item active">Security</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <!-- Action Buttons -->
            <div class="row mb-3">
                <div class="col-md-12">
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#blockIPModal">
                        <i class="fas fa-ban"></i> Block IP Address
                    </button>
                    <form method="POST" action="<?= url('security/cleanExpired') ?>" style="display: inline;">
                        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                        <button type="submit" class="btn btn-secondary" onclick="return confirm('Clean all expired IP blocks?')">
                            <i class="fas fa-broom"></i> Clean Expired Blocks
                        </button>
                    </form>
                </div>
            </div>

            <!-- Blocked IPs -->
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-danger card-outline">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-ban"></i> Blocked IP Addresses (<?= count($blockedIPs) ?>)</h3>
                        </div>
                        <div class="card-body table-responsive p-0">
                            <?php if (!empty($blockedIPs)): ?>
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>IP Address</th>
                                            <th>Reason</th>
                                            <th>Blocked At</th>
                                            <th>Expires At</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($blockedIPs as $block): ?>
                                            <tr>
                                                <td><code><?= htmlspecialchars($block->ip_address) ?></code></td>
                                                <td><?= htmlspecialchars($block->reason) ?></td>
                                                <td><?= date('M d, Y H:i', strtotime($block->created_at)) ?></td>
                                                <td>
                                                    <?php if ($block->expires_at): ?>
                                                        <?= date('M d, Y H:i', strtotime($block->expires_at)) ?>
                                                    <?php else: ?>
                                                        <span class="badge badge-danger">Permanent</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <form method="POST" action="<?= url('security/unblock/' . urlencode($block->ip_address)) ?>" style="display: inline;">
                                                        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                                        <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Unblock this IP address?')">
                                                            <i class="fas fa-unlock"></i> Unblock
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <p class="text-center text-muted p-3">No blocked IP addresses</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Security Events -->
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-warning card-outline">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-exclamation-triangle"></i> Recent Security Events</h3>
                        </div>
                        <div class="card-body table-responsive p-0">
                            <?php if (!empty($recentEvents)): ?>
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Event Type</th>
                                            <th>Email/User</th>
                                            <th>IP Address</th>
                                            <th>User Agent</th>
                                            <th>Date/Time</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($recentEvents as $event): ?>
                                            <tr>
                                                <td>
                                                    <?php
                                                    $eventClass = [
                                                        'failed_login' => 'danger',
                                                        'suspicious_activity' => 'warning',
                                                        'blocked_attempt' => 'dark'
                                                    ];
                                                    $class = $eventClass[$event->event_type] ?? 'info';
                                                    ?>
                                                    <span class="badge badge-<?= $class ?>">
                                                        <?= ucwords(str_replace('_', ' ', $event->event_type)) ?>
                                                    </span>
                                                </td>
                                                <td><?= htmlspecialchars($event->email ?? 'N/A') ?></td>
                                                <td><code><?= htmlspecialchars($event->ip_address ?? 'N/A') ?></code></td>
                                                <td><small><?= htmlspecialchars(substr($event->user_agent ?? '', 0, 50)) ?>...</small></td>
                                                <td><?= date('M d, Y H:i:s', strtotime($event->created_at)) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <p class="text-center text-muted p-3">No recent security events</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Block IP Modal -->
<div class="modal fade" id="blockIPModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="<?= url('security/block') ?>">
                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                <div class="modal-header">
                    <h5 class="modal-title">Block IP Address</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>IP Address *</label>
                        <input type="text" name="ip_address" class="form-control" required placeholder="e.g., 192.168.1.1">
                    </div>
                    <div class="form-group">
                        <label>Reason *</label>
                        <input type="text" name="reason" class="form-control" required placeholder="e.g., Suspicious activity">
                    </div>
                    <div class="form-group">
                        <label>Duration (minutes)</label>
                        <select name="duration" class="form-control">
                            <option value="15">15 minutes</option>
                            <option value="30">30 minutes</option>
                            <option value="60" selected>1 hour</option>
                            <option value="360">6 hours</option>
                            <option value="1440">24 hours</option>
                            <option value="10080">7 days</option>
                            <option value="0">Permanent</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Block IP</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once '../app/views/layouts/footer.php'; ?>
