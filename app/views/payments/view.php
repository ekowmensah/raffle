<?php require_once '../app/views/layouts/header.php'; ?>
<?php require_once '../app/views/layouts/sidebar.php'; ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Payment Details</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url('home') ?>">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= url('payment') ?>">Payments</a></li>
                        <li class="breadcrumb-item active">View</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Payment Information</h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="30%">Payment ID</th>
                                    <td><?= $payment->id ?></td>
                                </tr>
                                <tr>
                                    <th>Reference</th>
                                    <td><code><?= htmlspecialchars($payment->internal_reference ?? $payment->gateway_reference ?? 'N/A') ?></code></td>
                                </tr>
                                <tr>
                                    <th>Gateway</th>
                                    <td><span class="badge badge-info"><?= strtoupper($payment->gateway ?? 'N/A') ?></span></td>
                                </tr>
                                <tr>
                                    <th>Amount</th>
                                    <td><strong><?= $payment->currency ?> <?= number_format($payment->amount, 2) ?></strong></td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        <?php if ($payment->status === 'success'): ?>
                                            <span class="badge badge-success">Success</span>
                                        <?php elseif ($payment->status === 'pending'): ?>
                                            <span class="badge badge-warning">Pending</span>
                                        <?php else: ?>
                                            <span class="badge badge-danger">Failed</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Channel</th>
                                    <td><?= $payment->channel ?? 'N/A' ?></td>
                                </tr>
                                <tr>
                                    <th>Payment Date</th>
                                    <td><?= formatDate($payment->paid_at ?? $payment->created_at) ?></td>
                                </tr>
                                <tr>
                                    <th>Created At</th>
                                    <td><?= formatDate($payment->created_at) ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Generated Tickets (<?= count($tickets) ?>)</h3>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($tickets)): ?>
                                <div class="row">
                                    <?php foreach ($tickets as $ticket): ?>
                                        <div class="col-md-6 mb-2">
                                            <div class="alert alert-info mb-2">
                                                <i class="fas fa-ticket-alt"></i>
                                                <strong><?= htmlspecialchars($ticket->ticket_code) ?></strong>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <p class="text-muted">No tickets generated for this payment.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Player Information</h3>
                        </div>
                        <div class="card-body">
                            <p><strong>Phone:</strong><br><?= htmlspecialchars($payment->player_phone ?? 'N/A') ?></p>
                            <p><strong>Name:</strong><br><?= htmlspecialchars($payment->player_name ?? 'N/A') ?></p>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Campaign Information</h3>
                        </div>
                        <div class="card-body">
                            <p><strong>Campaign:</strong><br><?= htmlspecialchars($payment->campaign_name ?? 'N/A') ?></p>
                            <p><strong>Station:</strong><br><?= htmlspecialchars($payment->station_name ?? 'N/A') ?></p>
                            <?php if (!empty($payment->programme_name)): ?>
                                <p><strong>Programme:</strong><br><?= htmlspecialchars($payment->programme_name) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Actions</h3>
                        </div>
                        <div class="card-body">
                            <a href="<?= url('payment') ?>" class="btn btn-default btn-block">
                                <i class="fas fa-arrow-left"></i> Back to Payments
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php require_once '../app/views/layouts/footer.php'; ?>
