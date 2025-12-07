<?php require_once '../app/views/layouts/header.php'; ?>
<?php require_once '../app/views/layouts/sidebar.php'; ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Withdrawal Details</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url('home') ?>">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= url('withdrawal') ?>">Withdrawals</a></li>
                        <li class="breadcrumb-item active">Details</li>
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

            <?php if (flash('error')): ?>
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <i class="icon fas fa-ban"></i> <?= flash('error') ?>
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Withdrawal #<?= $withdrawal->id ?></h3>
                            <div class="card-tools">
                                <?php
                                $statusClass = [
                                    'pending' => 'warning',
                                    'approved' => 'info',
                                    'rejected' => 'danger',
                                    'completed' => 'success'
                                ];
                                $class = $statusClass[$withdrawal->status] ?? 'secondary';
                                ?>
                                <span class="badge badge-<?= $class ?> badge-lg">
                                    <?= ucfirst($withdrawal->status) ?>
                                </span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Station:</strong>
                                    <p><?= htmlspecialchars($withdrawal->station_name ?? 'N/A') ?></p>
                                </div>
                                <div class="col-md-6">
                                    <strong>Amount:</strong>
                                    <p class="text-lg"><strong>GHS <?= number_format($withdrawal->amount, 2) ?></strong></p>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Requested By:</strong>
                                    <p><?= htmlspecialchars($withdrawal->requested_by_name ?? 'N/A') ?></p>
                                </div>
                                <div class="col-md-6">
                                    <strong>Date Requested:</strong>
                                    <p><?= formatDate($withdrawal->created_at) ?></p>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Payment Method:</strong>
                                    <p><?= htmlspecialchars($withdrawal->payment_method ?? 'N/A') ?></p>
                                </div>
                                <div class="col-md-6">
                                    <strong>Wallet Balance Before:</strong>
                                    <p>GHS <?= number_format($withdrawal->wallet_balance_before, 2) ?></p>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <strong>Account Details:</strong>
                                    <p class="border p-2 bg-light"><?= nl2br(htmlspecialchars($withdrawal->account_details ?? 'N/A')) ?></p>
                                </div>
                            </div>

                            <?php if (!empty($withdrawal->notes)): ?>
                                <div class="row">
                                    <div class="col-md-12">
                                        <strong>Notes:</strong>
                                        <p class="border p-2 bg-light"><?= nl2br(htmlspecialchars($withdrawal->notes)) ?></p>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if ($withdrawal->status === 'approved' || $withdrawal->status === 'completed'): ?>
                                <hr>
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Approved By:</strong>
                                        <p><?= htmlspecialchars($withdrawal->approved_by_name ?? 'N/A') ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Approved At:</strong>
                                        <p><?= formatDate($withdrawal->approved_at) ?></p>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if ($withdrawal->status === 'rejected'): ?>
                                <hr>
                                <div class="alert alert-danger">
                                    <h5><i class="icon fas fa-ban"></i> Rejection Reason</h5>
                                    <p class="mb-0"><?= nl2br(htmlspecialchars($withdrawal->rejected_reason ?? 'No reason provided')) ?></p>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Rejected By:</strong>
                                        <p><?= htmlspecialchars($withdrawal->approved_by_name ?? 'N/A') ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Rejected At:</strong>
                                        <p><?= formatDate($withdrawal->approved_at) ?></p>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if ($withdrawal->status === 'completed'): ?>
                                <hr>
                                <div class="alert alert-success">
                                    <h5><i class="icon fas fa-check"></i> Payment Completed</h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <strong>Transaction Reference:</strong>
                                            <p><?= htmlspecialchars($withdrawal->transaction_reference ?? 'N/A') ?></p>
                                        </div>
                                        <div class="col-md-6">
                                            <strong>Completed At:</strong>
                                            <p><?= formatDate($withdrawal->completed_at) ?></p>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <strong>Wallet Balance After:</strong>
                                            <p>GHS <?= number_format($withdrawal->wallet_balance_after ?? 0, 2) ?></p>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="card-footer">
                            <a href="<?= url('withdrawal') ?>" class="btn btn-default">
                                <i class="fas fa-arrow-left"></i> Back to List
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <?php if (hasRole('super_admin')): ?>
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">Admin Actions</h3>
                            </div>
                            <div class="card-body">
                                <?php if ($withdrawal->status === 'pending'): ?>
                                    <form action="<?= url('withdrawal/approve/' . $withdrawal->id) ?>" method="POST" class="mb-2">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-success btn-block" 
                                                onclick="return confirm('Approve this withdrawal request?')">
                                            <i class="fas fa-check"></i> Approve Request
                                        </button>
                                    </form>

                                    <button type="button" class="btn btn-danger btn-block" data-toggle="modal" data-target="#rejectModal">
                                        <i class="fas fa-times"></i> Reject Request
                                    </button>
                                <?php elseif ($withdrawal->status === 'approved'): ?>
                                    <button type="button" class="btn btn-primary btn-block" data-toggle="modal" data-target="#completeModal">
                                        <i class="fas fa-check-circle"></i> Complete Payment
                                    </button>
                                <?php else: ?>
                                    <div class="alert alert-info mb-0">
                                        <i class="fas fa-info-circle"></i> No actions available
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= url('withdrawal/reject/' . $withdrawal->id) ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-header bg-danger">
                    <h4 class="modal-title">Reject Withdrawal</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="reason">Reason for Rejection <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="reason" name="reason" rows="3" required 
                                  placeholder="Provide a clear reason for rejecting this withdrawal"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject Withdrawal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Complete Modal -->
<div class="modal fade" id="completeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= url('withdrawal/complete/' . $withdrawal->id) ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-header bg-primary">
                    <h4 class="modal-title">Complete Payment</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i> 
                        This will deduct <strong>GHS <?= number_format($withdrawal->amount, 2) ?></strong> from the station wallet.
                    </div>
                    <div class="form-group">
                        <label for="transaction_reference">Transaction Reference <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="transaction_reference" 
                               name="transaction_reference" required 
                               placeholder="e.g., TXN123456789">
                        <small class="form-text text-muted">Enter the payment transaction reference/receipt number</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Complete Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once '../app/views/layouts/footer.php'; ?>
