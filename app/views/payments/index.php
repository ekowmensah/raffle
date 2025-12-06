<?php require_once '../app/views/layouts/header.php'; ?>
<?php require_once '../app/views/layouts/sidebar.php'; ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Payments</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url('home') ?>">Home</a></li>
                        <li class="breadcrumb-item active">Payments</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">All Payments</h3>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Reference</th>
                                <th>Player</th>
                                <th>Campaign</th>
                                <th>Amount</th>
                                <th>Method</th>
                                <th>Tickets</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($payments)): ?>
                                <?php foreach ($payments as $payment): ?>
                                    <tr>
                                        <td><?= $payment->id ?></td>
                                        <td><code><?= htmlspecialchars($payment->internal_reference ?? $payment->gateway_reference ?? 'N/A') ?></code></td>
                                        <td><?= htmlspecialchars($payment->player_phone ?? 'N/A') ?></td>
                                        <td><?= htmlspecialchars($payment->campaign_name ?? 'N/A') ?></td>
                                        <td><?= $payment->currency ?> <?= number_format($payment->amount, 2) ?></td>
                                        <td><span class="badge badge-info"><?= strtoupper($payment->gateway ?? 'N/A') ?></span></td>
                                        <td><?= $payment->ticket_count ?? 0 ?></td>
                                        <td><?= formatDate($payment->created_at, 'M d, Y H:i') ?></td>
                                        <td>
                                            <a href="<?= url('payment/show/' . $payment->id) ?>" class="btn btn-info btn-sm">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="9" class="text-center">No payments found</td>
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
