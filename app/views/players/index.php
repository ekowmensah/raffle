<?php require_once '../app/views/layouts/header.php'; ?>
<?php require_once '../app/views/layouts/sidebar.php'; ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Players</h1>
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
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">All Players</h3>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Phone</th>
                                <th>Total Tickets</th>
                                <th>Total Spent</th>
                                <th>Wins</th>
                                <th>Loyalty Level</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($players)): ?>
                                <?php foreach ($players as $player): ?>
                                    <tr>
                                        <td><?= $player->id ?></td>
                                        <td><?= htmlspecialchars($player->name ?? 'N/A') ?></td>
                                        <td><?= htmlspecialchars($player->phone) ?></td>
                                        <td><?= $player->total_tickets ?? 0 ?></td>
                                        <td><?= formatMoney($player->total_spent) ?></td>
                                        <td><?= $player->total_wins ?? 0 ?></td>
                                        <td>
                                            <?php
                                            $levelClass = [
                                                'bronze' => 'secondary',
                                                'silver' => 'info',
                                                'gold' => 'warning',
                                                'platinum' => 'primary'
                                            ];
                                            ?>
                                            <span class="badge badge-<?= $levelClass[$player->loyalty_level] ?? 'secondary' ?>">
                                                <?= ucfirst($player->loyalty_level) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="<?= url('player/show/' . $player->id) ?>" class="btn btn-info btn-sm">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center">No players found</td>
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
