<?php require_once '../app/views/layouts/header.php'; ?>
<?php require_once '../app/views/layouts/sidebar.php'; ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        <i class="fas fa-users"></i> Players
                        <?php if (hasRole('station_admin') && !empty($station)): ?>
                            <small class="text-muted">- <?= htmlspecialchars($station->name) ?></small>
                        <?php endif; ?>
                    </h1>
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
                    <div class="info-box bg-info">
                        <span class="info-box-icon"><i class="fas fa-users"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Players</span>
                            <span class="info-box-number"><?= $stats['total_players'] ?? count($players) ?></span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="info-box bg-success">
                        <span class="info-box-icon"><i class="fas fa-ticket-alt"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Tickets Purchased</span>
                            <span class="info-box-number"><?= $stats['total_tickets'] ?? 0 ?></span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="info-box bg-warning">
                        <span class="info-box-icon"><i class="fas fa-money-bill-wave"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Spent</span>
                            <span class="info-box-number">GHS <?= number_format($stats['total_spent'] ?? 0, 2) ?></span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="info-box bg-danger">
                        <span class="info-box-icon"><i class="fas fa-trophy"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Winners</span>
                            <span class="info-box-number"><?= $stats['total_winners'] ?? 0 ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Players Table -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-list"></i> Player List</h3>
                    <div class="card-tools">
                        <div class="btn-group mr-2">
                            <button type="button" class="btn btn-sm btn-default" id="filterAll" onclick="filterByLoyalty('all')">
                                All
                            </button>
                            <button type="button" class="btn btn-sm btn-default" onclick="filterByLoyalty('platinum')">
                                💎 Platinum
                            </button>
                            <button type="button" class="btn btn-sm btn-default" onclick="filterByLoyalty('gold')">
                                🥇 Gold
                            </button>
                            <button type="button" class="btn btn-sm btn-default" onclick="filterByLoyalty('silver')">
                                🥈 Silver
                            </button>
                            <button type="button" class="btn btn-sm btn-default" onclick="filterByLoyalty('bronze')">
                                🥉 Bronze
                            </button>
                        </div>
                        <div class="input-group input-group-sm" style="width: 200px;">
                            <input type="text" id="searchInput" class="form-control" placeholder="Search phone...">
                            <div class="input-group-append">
                                <button type="button" class="btn btn-default">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body table-responsive p-0" style="max-height: 600px;">
                    <table class="table table-hover table-head-fixed text-nowrap" id="playersTable">
                        <thead>
                            <tr>
                                <th>Phone</th>
                                <th class="text-center">Tickets</th>
                                <th class="text-right">Total Spent</th>
                                <th class="text-center">Wins</th>
                                <th class="text-right">Winnings</th>
                                <th class="text-center">Loyalty Level</th>
                                <th>Last Activity</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($players)): ?>
                                <?php foreach ($players as $player): ?>
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
                                    $loyaltyLevel = $player->loyalty_level ?? 'bronze';
                                    ?>
                                    <tr data-loyalty="<?= $loyaltyLevel ?>">
                                        <td>
                                            <strong><?= htmlspecialchars($player->phone) ?></strong>
                                            <?php if (!empty($player->name)): ?>
                                                <br><small class="text-muted"><?= htmlspecialchars($player->name) ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge badge-primary badge-pill"><?= $player->total_tickets ?? 0 ?></span>
                                        </td>
                                        <td class="text-right">
                                            <strong class="text-success">GHS <?= number_format($player->total_spent ?? 0, 2) ?></strong>
                                        </td>
                                        <td class="text-center">
                                            <?php if (($player->total_wins ?? 0) > 0): ?>
                                                <span class="badge badge-warning"><?= $player->total_wins ?></span>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-right">
                                            <?php if (($player->total_winnings ?? 0) > 0): ?>
                                                <strong class="text-warning">GHS <?= number_format($player->total_winnings, 2) ?></strong>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge badge-<?= $levelClass[$loyaltyLevel] ?>">
                                                <?= $levelIcon[$loyaltyLevel] ?> <?= ucfirst($loyaltyLevel) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <small class="text-muted"><?= date('M d, Y', strtotime($player->created_at)) ?></small>
                                        </td>
                                        <td class="text-center">
                                            <a href="<?= url('player/show/' . $player->id) ?>" class="btn btn-info btn-xs" title="View Details">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">No players found</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    <script>
    let currentFilter = 'all';

    // Search functionality
    document.getElementById('searchInput').addEventListener('keyup', function() {
        const searchValue = this.value.toLowerCase();
        const tableRows = document.querySelectorAll('#playersTable tbody tr');
        
        tableRows.forEach(row => {
            const phone = row.cells[0]?.textContent.toLowerCase() || '';
            const matchesSearch = phone.includes(searchValue);
            const matchesFilter = currentFilter === 'all' || row.dataset.loyalty === currentFilter;
            
            if (matchesSearch && matchesFilter) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });

    // Filter by loyalty level
    function filterByLoyalty(level) {
        currentFilter = level;
        const tableRows = document.querySelectorAll('#playersTable tbody tr');
        const searchValue = document.getElementById('searchInput').value.toLowerCase();
        
        // Update button states
        document.querySelectorAll('.btn-group .btn').forEach(btn => {
            btn.classList.remove('active', 'btn-primary');
            btn.classList.add('btn-default');
        });
        event.target.classList.remove('btn-default');
        event.target.classList.add('btn-primary', 'active');
        
        tableRows.forEach(row => {
            const phone = row.cells[0]?.textContent.toLowerCase() || '';
            const matchesSearch = searchValue === '' || phone.includes(searchValue);
            const matchesFilter = level === 'all' || row.dataset.loyalty === level;
            
            if (matchesSearch && matchesFilter) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    // Set initial filter state
    document.getElementById('filterAll').classList.add('btn-primary', 'active');
    </script>
</div>

<?php require_once '../app/views/layouts/footer.php'; ?>
