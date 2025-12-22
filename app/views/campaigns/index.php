<?php require_once '../app/views/layouts/header.php'; ?>
<?php require_once '../app/views/layouts/sidebar.php'; ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Campaigns</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url('home') ?>">Home</a></li>
                        <li class="breadcrumb-item active">Campaigns</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            
            <?php 
            $successMsg = flash('success');
            $errorMsg = flash('error');
            if ($successMsg): 
            ?>
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <i class="icon fas fa-check"></i> <?= htmlspecialchars($successMsg) ?>
                </div>
            <?php endif; ?>
            
            <?php if ($errorMsg): ?>
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <i class="icon fas fa-ban"></i> <?= htmlspecialchars($errorMsg) ?>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">All Campaigns</h3>
                    <div class="card-tools">
                        <a href="<?= url('campaign/create') ?>" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Create Campaign
                        </a>
                    </div>
                </div>
                
                <!-- Status Filter Tabs -->
                <div class="card-body pb-0">
                    <ul class="nav nav-pills mb-3">
                        <li class="nav-item">
                            <a class="nav-link <?= $statusFilter === 'all' ? 'active' : '' ?>" 
                               href="<?= url('campaign?status=all') ?>">
                                All <span class="badge badge-light ml-1"><?= $statusCounts['all'] ?></span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= $statusFilter === 'active' ? 'active' : '' ?>" 
                               href="<?= url('campaign?status=active') ?>">
                                <i class="fas fa-check-circle"></i> Active 
                                <span class="badge badge-success ml-1"><?= $statusCounts['active'] ?></span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= $statusFilter === 'paused' ? 'active' : '' ?>" 
                               href="<?= url('campaign?status=paused') ?>">
                                <i class="fas fa-pause-circle"></i> Paused 
                                <span class="badge badge-warning ml-1"><?= $statusCounts['paused'] ?></span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= $statusFilter === 'inactive' ? 'active' : '' ?>" 
                               href="<?= url('campaign?status=inactive') ?>">
                                <i class="fas fa-ban"></i> Inactive 
                                <span class="badge badge-dark ml-1"><?= $statusCounts['inactive'] ?></span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= $statusFilter === 'draft' ? 'active' : '' ?>" 
                               href="<?= url('campaign?status=draft') ?>">
                                <i class="fas fa-file"></i> Draft 
                                <span class="badge badge-secondary ml-1"><?= $statusCounts['draft'] ?></span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= $statusFilter === 'closed' ? 'active' : '' ?>" 
                               href="<?= url('campaign?status=closed') ?>">
                                <i class="fas fa-lock"></i> Closed 
                                <span class="badge badge-danger ml-1"><?= $statusCounts['closed'] ?></span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= $statusFilter === 'draw_done' ? 'active' : '' ?>" 
                               href="<?= url('campaign?status=draw_done') ?>">
                                <i class="fas fa-trophy"></i> Draw Done 
                                <span class="badge badge-info ml-1"><?= $statusCounts['draw_done'] ?></span>
                            </a>
                        </li>
                    </ul>
                </div>
                
                <div class="card-body table-responsive p-0">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Code</th>
                                <th>Type</th>
                                <th>Ticket Price</th>
                                <th>Period</th>
                                <th>Status</th>
                                <th>Tickets</th>
                                <?php if (hasRole('super_admin')): ?>
                                    <th>Gross Revenue</th>
                                <?php else: ?>
                                    <th>Station Revenue</th>
                                <?php endif; ?>
                                <th>Prize Pool</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($campaigns)): ?>
                                <?php foreach ($campaigns as $campaign): ?>
                                    <tr>
                                        <td><?= $campaign->id ?></td>
                                        <td>
                                            <?= htmlspecialchars($campaign->name) ?>
                                            <br><small class="text-muted"><?= htmlspecialchars($campaign->station_name ?? 'N/A') ?></small>
                                        </td>
                                        <td><span class="badge badge-info"><?= htmlspecialchars($campaign->code) ?></span></td>
                                        <td>
                                            <?php if ($campaign->campaign_type === 'item'): ?>
                                                <span class="badge badge-success" title="Item Prize Campaign">
                                                    <i class="fas fa-gift"></i> Item
                                                </span>
                                                <?php if ($campaign->item_name): ?>
                                                    <br><small class="text-muted"><?= htmlspecialchars(substr($campaign->item_name, 0, 20)) ?><?= strlen($campaign->item_name) > 20 ? '...' : '' ?></small>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="badge badge-primary" title="Cash Prize Campaign">
                                                    <i class="fas fa-money-bill-wave"></i> Cash
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= formatMoney($campaign->ticket_price, $campaign->currency) ?></td>
                                        <td><?= formatDate($campaign->start_date, 'M d') ?> - <?= formatDate($campaign->end_date, 'M d, Y') ?></td>
                                        <td>
                                            <?php
                                            $statusClass = [
                                                'draft' => 'secondary',
                                                'active' => 'success',
                                                'paused' => 'warning',
                                                'inactive' => 'dark',
                                                'closed' => 'danger',
                                                'draw_done' => 'info'
                                            ];
                                            ?>
                                            <span class="badge badge-<?= $statusClass[$campaign->status] ?? 'secondary' ?>" 
                                                  title="Raw status: '<?= htmlspecialchars($campaign->status) ?>'">
                                                <?= ucfirst($campaign->status) ?>
                                            </span>
                                        </td>
                                        <td><?= $campaign->total_tickets ?? 0 ?></td>
                                        <td>
                                            <?php
                                            if (hasRole('station_admin')) {
                                                // Show actual allocated revenue from revenue_allocations table
                                                $stationRevenue = floatval($campaign->station_allocated_revenue ?? 0);
                                                echo formatMoney($stationRevenue, $campaign->currency ?? 'GHS');
                                            } else {
                                                // Show total revenue for super admins
                                                $totalRevenue = floatval($campaign->total_revenue ?? 0);
                                                echo formatMoney($totalRevenue, $campaign->currency ?? 'GHS');
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            // Show actual allocated prize pool amount
                                            $prizePoolAmount = floatval($campaign->prize_pool_allocated ?? 0);
                                            echo formatMoney($prizePoolAmount, $campaign->currency ?? 'GHS');
                                            ?>
                                        </td>
                                        <td>
                                            <a href="<?= url('campaign/show/' . $campaign->id) ?>" class="btn btn-info btn-sm" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <?php if (!$campaign->is_config_locked && ($campaign->total_tickets ?? 0) == 0): ?>
                                                <a href="<?= url('campaign/edit/' . $campaign->id) ?>" class="btn btn-warning btn-sm" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            <?php elseif (($campaign->total_tickets ?? 0) > 0): ?>
                                                <button class="btn btn-secondary btn-sm" disabled title="Cannot edit - tickets already sold">
                                                    <i class="fas fa-lock"></i>
                                                </button>
                                            <?php endif; ?>
                                            
                                            <?php if ($campaign->status === 'active'): ?>
                                                <a href="<?= url('campaign/pause/' . $campaign->id) ?>" 
                                                   class="btn btn-warning btn-sm" 
                                                   title="Pause Campaign"
                                                   onclick="return confirm('Pause this campaign? No new tickets can be purchased while paused.')">
                                                    <i class="fas fa-pause"></i>
                                                </a>
                                            <?php elseif ($campaign->status === 'paused' || $campaign->status === 'inactive'): ?>
                                                <a href="<?= url('campaign/resume/' . $campaign->id) ?>" 
                                                   class="btn btn-success btn-sm" 
                                                   title="<?= $campaign->status === 'inactive' ? 'Reactivate Campaign' : 'Resume Campaign' ?>"
                                                   onclick="return confirm('<?= $campaign->status === 'inactive' ? 'Reactivate this campaign? Make sure the station and programme are active.' : 'Resume this campaign? Tickets will be available for purchase.' ?>')">
                                                    <i class="fas fa-play"></i>
                                                </a>
                                            <?php elseif ($campaign->status !== 'closed' && $campaign->status !== 'draw_done' && $campaign->status !== 'draft'): ?>
                                                <!-- Debug: Status is '<?= htmlspecialchars($campaign->status) ?>' - Migration may be needed -->
                                                <span class="badge badge-warning" title="Unknown status: <?= htmlspecialchars($campaign->status) ?>. Run migration to add 'paused' and 'inactive' statuses.">
                                                    <i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($campaign->status) ?>
                                                </span>
                                            <?php endif; ?>
                                            
                                            <?php if (($campaign->total_tickets ?? 0) == 0): ?>
                                                <button type="button" class="btn btn-danger btn-sm" 
                                                        onclick="confirmDelete(<?= $campaign->id ?>, '<?= htmlspecialchars($campaign->name, ENT_QUOTES) ?>')"
                                                        title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="<?= hasRole('super_admin') ? 13 : 11 ?>" class="text-center">No campaigns found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Hidden Delete Form -->
<form id="deleteForm" method="POST" style="display: none;">
    <?= csrf_field() ?>
</form>

<?php require_once '../app/views/layouts/footer.php'; ?>

<script>
function confirmDelete(campaignId, campaignName) {
    if (confirm('Are you sure you want to delete the campaign "' + campaignName + '"?\n\nThis action cannot be undone.')) {
        const form = document.getElementById('deleteForm');
        form.action = '<?= url('campaign/delete') ?>/' + campaignId;
        form.submit();
    }
}
</script>
