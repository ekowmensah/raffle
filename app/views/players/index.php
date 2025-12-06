<?php require_once '../app/views/layouts/header.php'; ?>
<?php require_once '../app/views/layouts/sidebar.php'; ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Players Management</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url('home') ?>">Home</a></li>
                        <li class="breadcrumb-item active">Players</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <!-- Stats Cards -->
            <div class="row mb-3">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3><?= count($players) ?></h3>
                            <p>Total Players</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3><?= array_sum(array_column($players, 'total_tickets')) ?></h3>
                            <p>Total Tickets Sold</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-ticket-alt"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3><?= formatMoney(array_sum(array_column($players, 'total_spent'))) ?></h3>
                            <p>Total Revenue</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3><?= array_sum(array_column($players, 'total_wins')) ?></h3>
                            <p>Total Wins</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-trophy"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Players Table -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">All Players</h3>
                    <div class="card-tools">
                        <div class="input-group input-group-sm" style="width: 250px;">
                            <input type="text" id="searchInput" class="form-control float-right" placeholder="Search phone...">
                            <div class="input-group-append">
                                <button type="button" class="btn btn-default">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap" id="playersTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Phone</th>
                                <th>Tickets</th>
                                <th>Total Spent</th>
                                <th>Wins</th>
                                <th>Total Winnings</th>
                                <th>Loyalty</th>
                                <th>Joined</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($players)): ?>
                                <?php foreach ($players as $player): ?>
                                    <tr>
                                        <td><?= $player->id ?></td>
                                        <td>
                                            <strong><?= htmlspecialchars($player->phone) ?></strong>
                                        </td>
                                        <td>
                                            <span class="badge badge-primary"><?= $player->total_tickets ?? 0 ?></span>
                                        </td>
                                        <td>
                                            <strong class="text-success"><?= formatMoney($player->total_spent ?? 0) ?></strong>
                                        </td>
                                        <td>
                                            <?php if (($player->total_wins ?? 0) > 0): ?>
                                                <span class="badge badge-warning"><?= $player->total_wins ?> 🏆</span>
                                            <?php else: ?>
                                                <span class="text-muted">0</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (($player->total_winnings ?? 0) > 0): ?>
                                                <strong class="text-warning"><?= formatMoney($player->total_winnings) ?></strong>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
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
                                            <span class="badge badge-<?= $levelClass[$player->loyalty_level] ?? 'secondary' ?>">
                                                <?= $levelIcon[$player->loyalty_level] ?? '' ?> <?= ucfirst($player->loyalty_level) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <small class="text-muted"><?= date('M d, Y', strtotime($player->created_at)) ?></small>
                                        </td>
                                        <td>
                                            <a href="<?= url('player/show/' . $player->id) ?>" class="btn btn-info btn-sm" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="9" class="text-center">No players found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    <script>
    // Search functionality
    document.getElementById('searchInput').addEventListener('keyup', function() {
        const searchValue = this.value.toLowerCase();
        const tableRows = document.querySelectorAll('#playersTable tbody tr');
        
        tableRows.forEach(row => {
            const phone = row.cells[1]?.textContent.toLowerCase() || '';
            if (phone.includes(searchValue)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
    </script>
</div>

<?php require_once '../app/views/layouts/footer.php'; ?>
