<?php require_once '../app/views/layouts/header.php'; ?>
<?php require_once '../app/views/layouts/sidebar.php'; ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Tickets</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url('home') ?>">Home</a></li>
                        <li class="breadcrumb-item active">Tickets</li>
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
                    <form method="GET" action="<?= url('ticket') ?>">
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

            <?php if (!empty($tickets)): ?>
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Tickets</h3>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Ticket Code</th>
                                <th>Player</th>
                                <th>Campaign</th>
                                <th>Station</th>
                                <th>Programme</th>
                                <th>Quantity</th>
                                <th>Price</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tickets as $ticket): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($ticket->ticket_code) ?></strong></td>
                                    <td><?= htmlspecialchars($ticket->player_phone ?? 'N/A') ?></td>
                                    <td><?= htmlspecialchars($ticket->campaign_name ?? 'N/A') ?></td>
                                    <td><?= htmlspecialchars($ticket->station_name ?? 'N/A') ?></td>
                                    <td><?= htmlspecialchars($ticket->programme_name ?? 'N/A') ?></td>
                                    <td><span class="badge badge-primary"><?= $ticket->quantity ?? 1 ?></span></td>
                                    <td>GHS <?= number_format($ticket->ticket_price, 2) ?></td>
                                    <td><?= formatDate($ticket->created_at, 'M d, Y H:i') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </section>
</div>

<?php require_once '../app/views/layouts/footer.php'; ?>
