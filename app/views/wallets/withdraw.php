<?php require_once '../app/views/layouts/header.php'; ?>
<?php require_once '../app/views/layouts/sidebar.php'; ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Withdraw Funds</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url('home') ?>">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= url('wallet') ?>">Wallets</a></li>
                        <li class="breadcrumb-item active">Withdraw</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6 offset-md-3">
                    <div class="card card-warning">
                        <div class="card-header">
                            <h3 class="card-title">Withdrawal Request</h3>
                        </div>
                        <form action="<?= url('wallet/withdraw') ?>" method="POST" id="withdrawForm">
                            <?= csrf_field() ?>
                            <input type="hidden" name="wallet_id" value="<?= $wallet->id ?>">
                            
                            <div class="card-body">
                                <div class="alert alert-info">
                                    <h5><i class="icon fas fa-info"></i> Wallet Information</h5>
                                    <strong>Station:</strong> <?= htmlspecialchars($wallet->station_name) ?><br>
                                    <strong>Available Balance:</strong> <span class="text-success">GHS <?= number_format($wallet->balance, 2) ?></span>
                                </div>

                                <div class="form-group">
                                    <label for="amount">Withdrawal Amount <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">GHS</span>
                                        </div>
                                        <input type="number" class="form-control" id="amount" name="amount" 
                                               step="0.01" min="0.01" max="<?= $wallet->balance ?>" required>
                                    </div>
                                    <small class="form-text text-muted">
                                        Maximum: GHS <?= number_format($wallet->balance, 2) ?>
                                    </small>
                                </div>

                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <textarea class="form-control" id="description" name="description" 
                                              rows="3" placeholder="Optional withdrawal note"></textarea>
                                </div>

                                <div class="alert alert-warning">
                                    <i class="icon fas fa-exclamation-triangle"></i>
                                    <strong>Warning:</strong> This action cannot be undone. Please verify the amount before proceeding.
                                </div>
                            </div>

                            <div class="card-footer">
                                <button type="submit" class="btn btn-warning" onclick="return confirm('Are you sure you want to withdraw this amount?')">
                                    <i class="fas fa-money-bill-wave"></i> Process Withdrawal
                                </button>
                                <a href="<?= url('wallet/show/' . $wallet->id) ?>" class="btn btn-default">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
$(document).ready(function() {
    $('#amount').on('input', function() {
        const amount = parseFloat($(this).val()) || 0;
        const maxAmount = <?= $wallet->balance ?>;
        
        if (amount > maxAmount) {
            $(this).addClass('is-invalid');
            $(this).siblings('.form-text').addClass('text-danger').removeClass('text-muted');
        } else {
            $(this).removeClass('is-invalid');
            $(this).siblings('.form-text').removeClass('text-danger').addClass('text-muted');
        }
    });
});
</script>

<?php require_once '../app/views/layouts/footer.php'; ?>
