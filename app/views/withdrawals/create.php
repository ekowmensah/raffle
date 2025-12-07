<?php require_once '../app/views/layouts/header.php'; ?>
<?php require_once '../app/views/layouts/sidebar.php'; ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Request Withdrawal</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url('home') ?>">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= url('withdrawal') ?>">Withdrawals</a></li>
                        <li class="breadcrumb-item active">Request</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            
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
                            <h3 class="card-title">Withdrawal Details</h3>
                        </div>
                        <form action="<?= url('withdrawal/create') ?>" method="POST">
                            <?= csrf_field() ?>
                            <div class="card-body">
                                <div class="alert alert-info">
                                    <h5><i class="icon fas fa-info"></i> Station Wallet Balance</h5>
                                    <h3 class="mb-0">GHS <?= number_format($wallet->balance ?? 0, 2) ?></h3>
                                    <small>Available for withdrawal</small>
                                </div>

                                <div class="form-group">
                                    <label for="amount">Withdrawal Amount <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">GHS</span>
                                        </div>
                                        <input type="number" step="0.01" class="form-control" id="amount" 
                                               name="amount" required min="1" max="<?= $wallet->balance ?? 0 ?>"
                                               placeholder="0.00">
                                    </div>
                                    <small class="form-text text-muted">
                                        Maximum: GHS <?= number_format($wallet->balance ?? 0, 2) ?>
                                    </small>
                                </div>

                                <div class="form-group">
                                    <label for="payment_method">Payment Method <span class="text-danger">*</span></label>
                                    <select class="form-control" id="payment_method" name="payment_method" required>
                                        <option value="">Select Payment Method</option>
                                        <option value="bank_transfer">Bank Transfer</option>
                                        <option value="mobile_money">Mobile Money</option>
                                        <option value="cheque">Cheque</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="account_details">Account Details <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="account_details" name="account_details" 
                                              rows="3" required placeholder="Enter bank account number, mobile money number, or other payment details"></textarea>
                                    <small class="form-text text-muted">
                                        Provide complete payment details (Account name, number, bank/network, etc.)
                                    </small>
                                </div>

                                <div class="form-group">
                                    <label for="notes">Notes/Reason (Optional)</label>
                                    <textarea class="form-control" id="notes" name="notes" 
                                              rows="2" placeholder="Optional notes or reason for withdrawal"></textarea>
                                </div>
                            </div>

                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane"></i> Submit Request
                                </button>
                                <a href="<?= url('withdrawal') ?>" class="btn btn-default">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card card-warning">
                        <div class="card-header">
                            <h3 class="card-title">Important Information</h3>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <i class="fas fa-check text-success"></i> 
                                    Requests are reviewed by administrators
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-clock text-info"></i> 
                                    Processing time: 1-3 business days
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-shield-alt text-primary"></i> 
                                    Ensure account details are correct
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-bell text-warning"></i> 
                                    You'll be notified of approval/rejection
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php require_once '../app/views/layouts/footer.php'; ?>
