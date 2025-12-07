<?php require_once '../app/views/layouts/header.php'; ?>
<?php require_once '../app/views/layouts/sidebar.php'; ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Withdrawals</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url('home') ?>">Home</a></li>
                        <li class="breadcrumb-item active">Withdrawals</li>
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

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Withdrawal Requests</h3>
                    <div class="card-tools">
                        <?php if (hasRole('station_admin')): ?>
                            <a href="<?= url('withdrawal/create') ?>" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> Request Withdrawal
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Station</th>
                                <th>Amount</th>
                                <th>Requested By</th>
                                <th>Status</th>
                                <th>Payment Method</th>
                                <th>Date Requested</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($withdrawals)): ?>
                                <tr>
                                    <td colspan="8" class="text-center">No withdrawal requests found</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($withdrawals as $withdrawal): ?>
                                    <tr>
                                        <td><?= $withdrawal->id ?></td>
                                        <td><?= htmlspecialchars($withdrawal->station_name) ?></td>
                                        <td><strong>GHS <?= number_format($withdrawal->amount, 2) ?></strong></td>
                                        <td><?= htmlspecialchars($withdrawal->requested_by_name) ?></td>
                                        <td>
                                            <?php
                                            $statusClass = [
                                                'pending' => 'warning',
                                                'approved' => 'info',
                                                'rejected' => 'danger',
                                                'completed' => 'success'
                                            ];
                                            $class = $statusClass[$withdrawal->status] ?? 'secondary';
                                            ?>
                                            <span class="badge badge-<?= $class ?>">
                                                <?= ucfirst($withdrawal->status) ?>
                                            </span>
                                        </td>
                                        <td><?= htmlspecialchars($withdrawal->payment_method ?? 'N/A') ?></td>
                                        <td><?= formatDate($withdrawal->created_at) ?></td>
                                        <td>
                                            <a href="<?= url('withdrawal/show/' . $withdrawal->id) ?>" 
                                               class="btn btn-info btn-sm">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

<?php require_once '../app/views/layouts/footer.php'; ?>
