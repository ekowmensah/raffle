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
                <div class="card-body">
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
                                <th>Revenue</th>
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
                                            <?php if (($campaign->programme_count ?? 0) > 0): ?>
                                                <span class="badge badge-primary" title="Programme-specific campaign">
                                                    <i class="fas fa-users"></i> Programme
                                                </span>
                                            <?php else: ?>
                                                <span class="badge badge-success" title="Station-wide campaign">
                                                    <i class="fas fa-broadcast-tower"></i> Station-wide
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
                                                'closed' => 'warning',
                                                'draw_done' => 'info'
                                            ];
                                            ?>
                                            <span class="badge badge-<?= $statusClass[$campaign->status] ?? 'secondary' ?>">
                                                <?= ucfirst($campaign->status) ?>
                                            </span>
                                        </td>
                                        <td><?= $campaign->total_tickets ?? 0 ?></td>
                                        <td><?= formatMoney($campaign->total_revenue ?? 0, $campaign->currency) ?></td>
                                        <td>
                                            <a href="<?= url('campaign/show/' . $campaign->id) ?>" class="btn btn-info btn-sm" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <?php if (!$campaign->is_config_locked): ?>
                                                <a href="<?= url('campaign/edit/' . $campaign->id) ?>" class="btn btn-warning btn-sm" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
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
                                    <td colspan="9" class="text-center">No campaigns found</td>
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
