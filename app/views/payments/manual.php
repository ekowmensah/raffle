<?php require_once '../app/views/layouts/header.php'; ?>
<?php require_once '../app/views/layouts/sidebar.php'; ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Manual Payment (Testing)</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url('home') ?>">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= url('payment') ?>">Payments</a></li>
                        <li class="breadcrumb-item active">Manual Payment</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> <strong>Testing Tool:</strong> This page allows you to create manual test payments without using real payment gateways.
            </div>

            <?php if (flash('error')): ?>
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <?= flash('error') ?>
                </div>
            <?php endif; ?>

            <?php if (flash('success')): ?>
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <?= flash('success') ?>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Create Test Payment</h3>
                </div>
                <form action="<?= url('payment/processManual') ?>" method="POST">
                    <?= csrf_field() ?>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Campaign <span class="text-danger">*</span></label>
                                    <select name="campaign_id" class="form-control" required>
                                        <option value="">Select Campaign</option>
                                        <?php foreach ($campaigns as $campaign): ?>
                                            <option value="<?= $campaign->id ?>">
                                                <?= htmlspecialchars($campaign->name) ?> (GHS <?= $campaign->ticket_price ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Station <span class="text-danger">*</span></label>
                                    <select name="station_id" class="form-control" required>
                                        <option value="">Select Platform</option>
                                        <?php foreach ($stations as $station): ?>
                                            <option value="<?= $station->id ?>"><?= htmlspecialchars($station->name) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Programme (Optional)</label>
                                    <select name="programme_id" class="form-control">
                                        <option value="">Select Programme</option>
                                        <?php foreach ($programmes as $programme): ?>
                                            <option value="<?= $programme->id ?>"><?= htmlspecialchars($programme->name) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Amount (GHS) <span class="text-danger">*</span></label>
                                    <input type="number" name="amount" class="form-control" step="0.01" min="1" required placeholder="e.g., 10.00">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Player Phone <span class="text-danger">*</span></label>
                                    <input type="text" name="phone" class="form-control" required placeholder="e.g., 0244123456">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Player Name</label>
                                    <input type="text" name="player_name" class="form-control" placeholder="e.g., John Doe">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Promo Code (Optional)</label>
                            <input type="text" name="promo_code" class="form-control" placeholder="Enter promo code">
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-check"></i> Process Manual Payment
                        </button>
                        <a href="<?= url('payment') ?>" class="btn btn-default">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>

<?php require_once '../app/views/layouts/footer.php'; ?>
