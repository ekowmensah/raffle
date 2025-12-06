<?php require_once '../app/views/layouts/header.php'; ?>
<?php require_once '../app/views/layouts/sidebar.php'; ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Draw #<?= $draw->id ?></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url('home') ?>">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= url('draw') ?>">Draws</a></li>
                        <li class="breadcrumb-item active">View</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            
            <?php if (flash('success')): ?>
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <i class="icon fas fa-check"></i> <?= flash('success') ?>
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Draw Details</h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width: 200px">Draw Type</th>
                                    <td>
                                        <span class="badge badge-<?= $draw->draw_type == 'daily' ? 'info' : 'warning' ?> badge-lg">
                                            <?= strtoupper($draw->draw_type) ?>
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Draw Date</th>
                                    <td><?= formatDate($draw->draw_date, 'M d, Y') ?></td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        <?php
                                        $statusColors = [
                                            'pending' => 'warning',
                                            'completed' => 'success',
                                            'published' => 'info'
                                        ];
                                        $color = $statusColors[$draw->status] ?? 'secondary';
                                        ?>
                                        <span class="badge badge-<?= $color ?>"><?= strtoupper($draw->status) ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Prize Pool</th>
                                    <td>
                                        <strong>GHS <?= number_format($draw->total_prize_pool ?? 0, 2) ?></strong>
                                        <?php if (($draw->total_prize_pool ?? 0) == 0): ?>
                                            <br><small class="text-muted">
                                                <i class="fas fa-info-circle"></i> 
                                                Prize pool will be calculated when draw is conducted
                                            </small>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php if ($draw->updated_at && $draw->status == 'completed'): ?>
                                <tr>
                                    <th>Completed At</th>
                                    <td><?= formatDate($draw->updated_at) ?></td>
                                </tr>
                                <?php endif; ?>
                            </table>
                        </div>
                    </div>

                    <?php if (!empty($winners)): ?>
                    <div class="card card-success">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-trophy"></i> Winners</h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Rank</th>
                                        <th>Ticket Code</th>
                                        <th>Player</th>
                                        <th>Prize Amount</th>
                                        <th>Status</th>
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
                                            <td><strong>GHS <?= number_format($winner->prize_amount, 2) ?></strong></td>
                                            <td>
                                                <span class="badge badge-<?= $winner->prize_paid_status == 'paid' ? 'success' : 'warning' ?>">
                                                    <?= strtoupper($winner->prize_paid_status) ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Statistics</h3>
                        </div>
                        <div class="card-body">
                            <div class="info-box bg-success">
                                <span class="info-box-icon"><i class="fas fa-trophy"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Prizes</span>
                                    <span class="info-box-number">GHS <?= number_format($draw->total_prizes ?? 0, 2) ?></span>
                                </div>
                            </div>

                            <div class="info-box bg-info">
                                <span class="info-box-icon"><i class="fas fa-users"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Winners</span>
                                    <span class="info-box-number"><?= $draw->winner_count ?? 0 ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php require_once '../app/views/layouts/footer.php'; ?>
