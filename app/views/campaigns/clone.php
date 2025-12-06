<?php require_once '../app/views/layouts/header.php'; ?>
<?php require_once '../app/views/layouts/sidebar.php'; ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Clone Campaign</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url('home') ?>">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= url('campaign') ?>">Campaigns</a></li>
                        <li class="breadcrumb-item active">Clone</li>
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

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Cloning: <?= htmlspecialchars($campaign->name) ?></h3>
                </div>
                <form action="<?= url('campaign/clone/' . $campaign->id) ?>" method="POST">
                    <?= csrf_field() ?>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="icon fas fa-info-circle"></i>
                            This will create a new campaign with the same configuration as the original. 
                            Programme access will also be copied. The new campaign will be in draft status.
                        </div>

                        <div class="form-group">
                            <label for="name">New Campaign Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" 
                                   value="<?= htmlspecialchars($campaign->name) ?> (Copy)" required>
                        </div>

                        <div class="form-group">
                            <label for="code">New Campaign Code <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="code" name="code" 
                                   value="<?= htmlspecialchars($campaign->code) ?>_COPY" required>
                            <small class="form-text text-muted">Must be unique</small>
                        </div>

                        <hr>
                        <h5>Original Campaign Details</h5>
                        <table class="table table-sm">
                            <tr>
                                <th>Ticket Price:</th>
                                <td><?= $campaign->currency ?> <?= number_format($campaign->ticket_price, 2) ?></td>
                            </tr>
                            <tr>
                                <th>Revenue Split:</th>
                                <td>
                                    Platform: <?= $campaign->platform_percent ?>% | 
                                    Station: <?= $campaign->station_percent ?>% | 
                                    Programme: <?= $campaign->programme_percent ?>% | 
                                    Prize Pool: <?= $campaign->prize_pool_percent ?>%
                                </td>
                            </tr>
                            <tr>
                                <th>Daily Draw:</th>
                                <td><?= $campaign->daily_draw_enabled ? 'Enabled' : 'Disabled' ?></td>
                            </tr>
                        </table>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-clone"></i> Clone Campaign
                        </button>
                        <a href="<?= url('campaign/show/' . $campaign->id) ?>" class="btn btn-default">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>

<?php require_once '../app/views/layouts/footer.php'; ?>
