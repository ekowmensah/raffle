<?php require_once '../app/views/layouts/header.php'; ?>
<?php require_once '../app/views/layouts/sidebar.php'; ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">All Winners</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url('home') ?>">Home</a></li>
                        <li class="breadcrumb-item active">Winners</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Filter by Campaign</h3>
                </div>
                <div class="card-body">
                    <form method="GET" action="<?= url('draw/winners') ?>">
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

            <?php if (!empty($winners)): ?>
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-trophy"></i> Winners List</h3>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Rank</th>
                                <th>Ticket Code</th>
                                <th>Player</th>
                                <th>Draw Date</th>
                                <th>Draw Type</th>
                                <th>Prize Amount</th>
                                <th>Prize Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($winners as $winner): ?>
                                <tr>
                                    <td>
                                        <?php if ($winner->prize_rank == 1): ?>
                                            <i class="fas fa-trophy text-warning"></i> #<?= $winner->prize_rank ?>
                                        <?php else: ?>
                                            #<?= $winner->prize_rank ?>
                                        <?php endif; ?>
                                    </td>
                                    <td><strong><?= htmlspecialchars($winner->ticket_code) ?></strong></td>
                                    <td><?= htmlspecialchars($winner->player_phone) ?></td>
                                    <td><?= formatDate($winner->draw_date, 'M d, Y') ?></td>
                                    <td>
                                        <span class="badge badge-<?= $winner->draw_type == 'daily' ? 'info' : 'warning' ?>">
                                            <?= strtoupper($winner->draw_type) ?>
                                        </span>
                                    </td>
                                    <td><strong>GHS <?= number_format($winner->prize_amount, 2) ?></strong></td>
                                    <td>
                                        <?php
                                        $statusColors = [
                                            'pending' => 'warning',
                                            'processing' => 'info',
                                            'paid' => 'success',
                                            'failed' => 'danger'
                                        ];
                                        $color = $statusColors[$winner->prize_paid_status] ?? 'secondary';
                                        ?>
                                        <span class="badge badge-<?= $color ?>">
                                            <?= strtoupper($winner->prize_paid_status) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($winner->prize_paid_status != 'paid'): ?>
                                        <form method="POST" action="<?= url('draw/updatePrizeStatus/' . $winner->id) ?>" style="display: inline;">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="status" value="paid">
                                            <button type="submit" class="btn btn-success btn-sm" 
                                                    onclick="return confirm('Mark this prize as paid?')">
                                                <i class="fas fa-check"></i> Mark Paid
                                            </button>
                                        </form>
                                        <?php else: ?>
                                            <span class="text-success"><i class="fas fa-check-circle"></i> Paid</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php else: ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> No winners found. Please select a campaign.
                </div>
            <?php endif; ?>
        </div>
    </section>
</div>

<?php require_once '../app/views/layouts/footer.php'; ?>
