<?php require_once '../app/views/layouts/header.php'; ?>
<?php require_once '../app/views/layouts/sidebar.php'; ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Conduct Draw</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url('home') ?>">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= url('draw') ?>">Draws</a></li>
                        <li class="breadcrumb-item active">Conduct</li>
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

            <div class="card card-warning">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-exclamation-triangle"></i> Confirm Draw Execution</h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h5><i class="icon fas fa-info-circle"></i> Draw Information</h5>
                        <ul>
                            <li><strong>Draw ID:</strong> <?= $draw->id ?></li>
                            <li><strong>Draw Type:</strong> <?= strtoupper($draw->draw_type) ?></li>
                            <li><strong>Draw Date:</strong> <?= formatDate($draw->draw_date, 'M d, Y') ?></li>
                            <li><strong>Prize Pool:</strong> GHS <?= number_format($draw->total_prize_pool ?? 0, 2) ?></li>
                        </ul>
                    </div>

                    <div class="alert alert-warning">
                        <h5><i class="icon fas fa-exclamation-triangle"></i> Important</h5>
                        <p>
                            This action will:
                        </p>
                        <ul>
                            <li>Select random winner(s) from eligible tickets</li>
                            <li>Distribute the prize pool among winners</li>
                            <li>Send SMS notifications to all winners</li>
                            <li>Mark the draw as completed (cannot be undone)</li>
                        </ul>
                        <p class="mb-0">
                            <strong>Are you sure you want to proceed?</strong>
                        </p>
                    </div>

                    <form action="<?= url('draw/conduct/' . $draw->id) ?>" method="POST">
                        <?= csrf_field() ?>
                        <div class="text-center">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fas fa-random"></i> Conduct Draw Now
                            </button>
                            <a href="<?= url('draw/pending') ?>" class="btn btn-default btn-lg">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>

<?php require_once '../app/views/layouts/footer.php'; ?>
