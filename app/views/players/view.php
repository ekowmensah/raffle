<?php require_once '../app/views/layouts/header.php'; ?>
<?php require_once '../app/views/layouts/sidebar.php'; ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Player Details</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url('home') ?>">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= url('player') ?>">Players</a></li>
                        <li class="breadcrumb-item active">View</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Player Information</h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <tr>
                                    <th>Phone</th>
                                    <td><?= htmlspecialchars($player->phone) ?></td>
                                </tr>
                                <tr>
                                    <th>Name</th>
                                    <td><?= htmlspecialchars($player->name ?? 'N/A') ?></td>
                                </tr>
                                <tr>
                                    <th>Loyalty Level</th>
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
                                </tr>
                                <tr>
                                    <th>Loyalty Points</th>
                                    <td><?= number_format($player->loyalty_points ?? 0) ?></td>
                                </tr>
                                <tr>
                                    <th>Joined</th>
                                    <td><?= formatDate($player->created_at) ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Tickets (<?= count($tickets) ?>)</h3>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($tickets)): ?>
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Ticket Code</th>
                                            <th>Campaign</th>
                                            <th>Station</th>
                                            <th>Programme</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($tickets as $ticket): ?>
                                            <tr>
                                                <td><strong><?= htmlspecialchars($ticket->ticket_code) ?></strong></td>
                                                <td><?= htmlspecialchars($ticket->campaign_name ?? 'N/A') ?></td>
                                                <td><?= htmlspecialchars($ticket->station_name ?? 'N/A') ?></td>
                                                <td><?= htmlspecialchars($ticket->programme_name ?? 'N/A') ?></td>
                                                <td><?= formatDate($ticket->created_at, 'M d, Y H:i') ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <p class="text-muted">No tickets found for this player.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php require_once '../app/views/layouts/footer.php'; ?>
