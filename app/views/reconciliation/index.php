<?php require_once '../app/views/layouts/header.php'; ?>
<?php require_once '../app/views/layouts/sidebar.php'; ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Payment Reconciliation</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url('home') ?>">Home</a></li>
                        <li class="breadcrumb-item active">Reconciliation</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            
            <!-- Filter Card -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Filter Options</h3>
                </div>
                <div class="card-body">
                    <form method="GET" action="<?= url('reconciliation') ?>">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Payment Gateway</label>
                                    <select name="gateway" class="form-control">
                                        <option value="">All Gateways</option>
                                        <option value="paystack" <?= $selected_gateway == 'paystack' ? 'selected' : '' ?>>Paystack</option>
                                        <option value="mtn_momo" <?= $selected_gateway == 'mtn_momo' ? 'selected' : '' ?>>MTN MoMo</option>
                                        <option value="hubtel" <?= $selected_gateway == 'hubtel' ? 'selected' : '' ?>>Hubtel</option>
                                        <option value="manual" <?= $selected_gateway == 'manual' ? 'selected' : '' ?>>Manual</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Start Date</label>
                                    <input type="date" name="start_date" class="form-control" value="<?= $start_date ?>">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>End Date</label>
                                    <input type="date" name="end_date" class="form-control" value="<?= $end_date ?>">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <button type="submit" class="btn btn-primary btn-block">
                                        <i class="fas fa-filter"></i> Filter
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3><?= $totals['payments_count'] ?></h3>
                            <p>Total Payments</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-credit-card"></i>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>GHS <?= number_format($totals['payments_amount'], 2) ?></h3>
                            <p>Payment Amount</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>GHS <?= number_format($totals['allocations_amount'], 2) ?></h3>
                            <p>Allocated Amount</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-chart-pie"></i>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3><?= $totals['discrepancy_count'] ?></h3>
                            <p>Discrepancies</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gateway Summary -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Payment Gateway Summary</h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>Gateway</th>
                                <th>Payment Count</th>
                                <th>Total Amount</th>
                                <th>Successful Amount</th>
                                <th>Failed Count</th>
                                <th>Success Rate</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($gatewaySummary)): ?>
                                <?php foreach ($gatewaySummary as $row): ?>
                                    <?php 
                                    $successRate = $row->payment_count > 0 
                                        ? (($row->payment_count - $row->failed_count) / $row->payment_count) * 100 
                                        : 0;
                                    ?>
                                    <tr>
                                        <td><strong><?= strtoupper($row->gateway) ?></strong></td>
                                        <td><?= $row->payment_count ?></td>
                                        <td>GHS <?= number_format($row->total_amount, 2) ?></td>
                                        <td class="text-success">GHS <?= number_format($row->successful_amount, 2) ?></td>
                                        <td class="text-danger"><?= $row->failed_count ?></td>
                                        <td>
                                            <span class="badge badge-<?= $successRate >= 90 ? 'success' : ($successRate >= 70 ? 'warning' : 'danger') ?>">
                                                <?= number_format($successRate, 1) ?>%
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center">No data available</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Discrepancies -->
            <?php if (!empty($discrepancies)): ?>
            <div class="card card-danger">
                <div class="card-header">
                    <h3 class="card-title">Discrepancies Found</h3>
                    <div class="card-tools">
                        <a href="<?= url('reconciliation/discrepancies?start_date=' . $start_date . '&end_date=' . $end_date) ?>" 
                           class="btn btn-tool">
                            <i class="fas fa-list"></i> View All
                        </a>
                    </div>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th>Reference</th>
                                <th>Amount</th>
                                <th>Date</th>
                                <th>Description</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (array_slice($discrepancies, 0, 10) as $disc): ?>
                                <tr>
                                    <td>
                                        <span class="badge badge-<?= $disc['type'] == 'missing_allocation' ? 'warning' : 'danger' ?>">
                                            <?= str_replace('_', ' ', strtoupper($disc['type'])) ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars($disc['reference'] ?? $disc['payment_id']) ?></td>
                                    <td>GHS <?= number_format($disc['amount'], 2) ?></td>
                                    <td><?= formatDate($disc['date'], 'M d, Y H:i') ?></td>
                                    <td><?= htmlspecialchars($disc['description']) ?></td>
                                    <td>
                                        <?php if ($disc['type'] == 'missing_allocation'): ?>
                                            <button class="btn btn-sm btn-primary" onclick="resolveDiscrepancy('<?= $disc['type'] ?>', <?= $disc['payment_id'] ?>)">
                                                <i class="fas fa-check"></i> Resolve
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endif; ?>

            <!-- Reconciliation Summary -->
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title">Reconciliation Summary</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Payments</h5>
                            <ul class="list-unstyled">
                                <li><strong>Count:</strong> <?= $totals['payments_count'] ?></li>
                                <li><strong>Amount:</strong> GHS <?= number_format($totals['payments_amount'], 2) ?></li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h5>Revenue Allocations</h5>
                            <ul class="list-unstyled">
                                <li><strong>Count:</strong> <?= $totals['allocations_count'] ?></li>
                                <li><strong>Amount:</strong> GHS <?= number_format($totals['allocations_amount'], 2) ?></li>
                            </ul>
                        </div>
                    </div>
                    
                    <?php 
                    $difference = abs($totals['payments_amount'] - $totals['allocations_amount']);
                    $isBalanced = $difference < 0.01;
                    ?>
                    
                    <div class="alert alert-<?= $isBalanced ? 'success' : 'warning' ?> mt-3">
                        <h5>
                            <i class="icon fas fa-<?= $isBalanced ? 'check' : 'exclamation-triangle' ?>"></i>
                            <?= $isBalanced ? 'Balanced' : 'Discrepancy Detected' ?>
                        </h5>
                        <?php if (!$isBalanced): ?>
                            <p>Difference: GHS <?= number_format($difference, 2) ?></p>
                        <?php else: ?>
                            <p>All payments have been properly allocated.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
function resolveDiscrepancy(type, id) {
    if (confirm('Are you sure you want to resolve this discrepancy?')) {
        // Create form and submit
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?= url('reconciliation/resolve') ?>/' + type + '/' + id;
        
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = 'csrf_token';
        csrfInput.value = '<?= csrf_token() ?>';
        form.appendChild(csrfInput);
        
        const actionInput = document.createElement('input');
        actionInput.type = 'hidden';
        actionInput.name = 'action';
        actionInput.value = 'create';
        form.appendChild(actionInput);
        
        document.body.appendChild(form);
        form.submit();
    }
}
</script>

<?php require_once '../app/views/layouts/footer.php'; ?>
