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
                        <li class="breadcrumb-item"><a href="<?= url('payment') ?>">Payments</a></li>
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
                    <form method="GET" action="<?= url('payment/reconcile') ?>">
                        <div class="row">
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
                                    <label>Gateway</label>
                                    <select name="gateway" class="form-control">
                                        <option value="">All Gateways</option>
                                        <option value="mtn" <?= $gateway == 'mtn' ? 'selected' : '' ?>>MTN MoMo</option>
                                        <option value="hubtel" <?= $gateway == 'hubtel' ? 'selected' : '' ?>>Hubtel</option>
                                        <option value="paystack" <?= $gateway == 'paystack' ? 'selected' : '' ?>>Paystack</option>
                                    </select>
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
                <div class="col-lg-4 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>GHS <?= number_format($total_success, 2) ?></h3>
                            <p>Successful (<?= $count_success ?>)</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>GHS <?= number_format($total_pending, 2) ?></h3>
                            <p>Pending (<?= $count_pending ?>)</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3>GHS <?= number_format($total_failed, 2) ?></h3>
                            <p>Failed (<?= $count_failed ?>)</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-times-circle"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payments Table -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Payment Transactions</h3>
                    <div class="card-tools">
                        <button onclick="exportToCSV()" class="btn btn-success btn-sm">
                            <i class="fas fa-file-excel"></i> Export CSV
                        </button>
                    </div>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap" id="paymentsTable">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Reference</th>
                                <th>Player</th>
                                <th>Campaign</th>
                                <th>Station</th>
                                <th>Gateway</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($payments as $payment): ?>
                                <tr>
                                    <td><?= formatDate($payment->created_at, 'M d, Y H:i') ?></td>
                                    <td><code><?= htmlspecialchars($payment->internal_reference ?? $payment->gateway_reference ?? 'N/A') ?></code></td>
                                    <td><?= htmlspecialchars($payment->player_phone ?? 'N/A') ?></td>
                                    <td><?= htmlspecialchars($payment->campaign_name ?? 'N/A') ?></td>
                                    <td><?= htmlspecialchars($payment->station_name ?? 'N/A') ?></td>
                                    <td><span class="badge badge-info"><?= strtoupper($payment->gateway ?? 'N/A') ?></span></td>
                                    <td><?= $payment->currency ?> <?= number_format($payment->amount, 2) ?></td>
                                    <td>
                                        <?php
                                        $statusColors = [
                                            'success' => 'success',
                                            'pending' => 'warning',
                                            'failed' => 'danger'
                                        ];
                                        $color = $statusColors[$payment->status] ?? 'secondary';
                                        ?>
                                        <span class="badge badge-<?= $color ?>"><?= strtoupper($payment->status) ?></span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
function exportToCSV() {
    let csv = [];
    let rows = document.querySelectorAll("#paymentsTable tr");
    
    for (let i = 0; i < rows.length; i++) {
        let row = [], cols = rows[i].querySelectorAll("td, th");
        
        for (let j = 0; j < cols.length; j++) {
            row.push(cols[j].innerText);
        }
        
        csv.push(row.join(","));
    }
    
    let csvFile = new Blob([csv.join("\n")], { type: "text/csv" });
    let downloadLink = document.createElement("a");
    downloadLink.download = "payment_reconciliation_<?= date('Y-m-d') ?>.csv";
    downloadLink.href = window.URL.createObjectURL(csvFile);
    downloadLink.style.display = "none";
    document.body.appendChild(downloadLink);
    downloadLink.click();
}
</script>

<?php require_once '../app/views/layouts/footer.php'; ?>
