<?php require_once '../app/views/layouts/header.php'; ?>
<?php require_once '../app/views/layouts/sidebar.php'; ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        <i class="fas fa-user-circle"></i> <?= htmlspecialchars($player->phone) ?>
                    </h1>
                    <small class="text-muted">Player ID: #<?= $player->id ?></small>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url('home') ?>">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= url('player') ?>">Players</a></li>
                        <li class="breadcrumb-item active"><?= substr($player->phone, -4) ?></li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <!-- Stats Cards -->
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-primary">
                        <div class="inner">
                            <h3><?= $stats['total_tickets'] ?></h3>
                            <p>Total Tickets</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-ticket-alt"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3><?= formatMoney($stats['total_spent']) ?></h3>
                            <p>Total Spent</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3><?= $stats['total_wins'] ?></h3>
                            <p>Total Wins</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-trophy"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3><?= formatMoney($stats['total_winnings']) ?></h3>
                            <p>Total Winnings</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-award"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Player Info Card -->
                <div class="col-md-4">
                    <div class="card card-primary card-outline">
                        <div class="card-body box-profile">
                            <div class="text-center">
                                <div class="profile-user-img img-fluid img-circle" style="width: 100px; height: 100px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                                    <i class="fas fa-user" style="font-size: 3rem; color: white;"></i>
                                </div>
                            </div>

                            <h3 class="profile-username text-center"><?= htmlspecialchars($player->phone) ?></h3>

                            <p class="text-muted text-center">
                                <?php
                                $levelClass = [
                                    'bronze' => 'secondary',
                                    'silver' => 'info',
                                    'gold' => 'warning',
                                    'platinum' => 'primary'
                                ];
                                $levelIcon = [
                                    'bronze' => '🥉',
                                    'silver' => '🥈',
                                    'gold' => '🥇',
                                    'platinum' => '💎'
                                ];
                                ?>
                                <span class="badge badge-<?= $levelClass[$player->loyalty_level] ?? 'secondary' ?>" style="font-size: 1rem; padding: 0.5rem 1rem;">
                                    <?= $levelIcon[$player->loyalty_level] ?? '' ?> <?= ucfirst($player->loyalty_level) ?>
                                </span>
                            </p>

                            <ul class="list-group list-group-unbordered mb-3">
                                <li class="list-group-item">
                                    <b><i class="fas fa-star text-warning"></i> Loyalty Points</b>
                                    <a class="float-right"><?= number_format($player->loyalty_points ?? 0) ?></a>
                                </li>
                                <li class="list-group-item">
                                    <b><i class="fas fa-calendar text-info"></i> Member Since</b>
                                    <a class="float-right"><?= date('M d, Y', strtotime($player->created_at)) ?></a>
                                </li>
                                <li class="list-group-item">
                                    <b><i class="fas fa-clock text-muted"></i> Last Activity</b>
                                    <a class="float-right"><?= date('M d, Y', strtotime($player->updated_at ?? $player->created_at)) ?></a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Wins Section -->
                <div class="col-md-8">
                    <?php if (!empty($wins)): ?>
                    <div class="card card-warning card-outline">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-trophy"></i> Wins (<?= count($wins) ?>)</h3>
                        </div>
                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Rank</th>
                                        <th>Campaign</th>
                                        <th>Ticket Code</th>
                                        <th>Prize Amount</th>
                                        <th>Draw Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($wins as $win): ?>
                                        <tr>
                                            <td>
                                                <?php
                                                $rankIcons = [1 => '🥇', 2 => '🥈', 3 => '🥉'];
                                                echo $rankIcons[$win->prize_rank] ?? "#{$win->prize_rank}";
                                                ?>
                                                <strong><?= $win->prize_rank ?><?= $win->prize_rank == 1 ? 'st' : ($win->prize_rank == 2 ? 'nd' : ($win->prize_rank == 3 ? 'rd' : 'th')) ?></strong>
                                            </td>
                                            <td><?= htmlspecialchars($win->campaign_name ?? 'N/A') ?></td>
                                            <td><code><?= htmlspecialchars($win->ticket_code) ?></code></td>
                                            <td><strong class="text-success"><?= formatMoney($win->prize_amount) ?></strong></td>
                                            <td><?= date('M d, Y', strtotime($win->draw_date)) ?></td>
                                            <td>
                                                <?php
                                                $statusClass = [
                                                    'pending' => 'warning',
                                                    'paid' => 'success',
                                                    'processing' => 'info'
                                                ];
                                                ?>
                                                <span class="badge badge-<?= $statusClass[$win->prize_paid_status] ?? 'secondary' ?>">
                                                    <?= ucfirst($win->prize_paid_status) ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Tickets Section -->
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-ticket-alt"></i> Tickets (<?= count($tickets) ?>)</h3>
                        </div>
                        <div class="card-body table-responsive p-0" style="max-height: 400px;">
                            <?php if (!empty($tickets)): ?>
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Ticket Code</th>
                                            <th>Campaign</th>
                                            <th>Station</th>
                                            <th>Programme</th>
                                            <th>Purchase Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($tickets as $ticket): ?>
                                            <tr>
                                                <td><code><?= htmlspecialchars($ticket->ticket_code) ?></code></td>
                                                <td><?= htmlspecialchars($ticket->campaign_name ?? 'N/A') ?></td>
                                                <td><?= htmlspecialchars($ticket->station_name ?? 'N/A') ?></td>
                                                <td><?= htmlspecialchars($ticket->programme_name ?? 'N/A') ?></td>
                                                <td><small class="text-muted"><?= date('M d, Y H:i', strtotime($ticket->created_at)) ?></small></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <div class="p-4 text-center text-muted">
                                    <i class="fas fa-ticket-alt fa-3x mb-3"></i>
                                    <p>No tickets found for this player.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php require_once '../app/views/layouts/footer.php'; ?>
