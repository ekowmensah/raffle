<?php require_once '../app/views/layouts/header.php'; ?>
<?php require_once '../app/views/layouts/sidebar.php'; ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Create Station</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url('home') ?>">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= url('station') ?>">Stations</a></li>
                        <li class="breadcrumb-item active">Create</li>
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
                    <h3 class="card-title">Station Information</h3>
                </div>
                <form action="<?= url('station/create') ?>" method="POST">
                    <?= csrf_field() ?>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Station Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name" 
                                           value="<?= old('name') ?>" placeholder="e.g., Hope FM" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="short_code_label">Short Code <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="short_code_label" name="short_code_label" 
                                           value="<?= old('short_code_label') ?>" placeholder="e.g., HFM" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="code">Code <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="code" name="code" 
                                           value="<?= old('code') ?>" placeholder="e.g., HOPE_FM" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="phone">Phone</label>
                                    <input type="text" class="form-control" id="phone" name="phone" 
                                           value="<?= old('phone') ?>" placeholder="+233...">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="<?= old('email') ?>" placeholder="info@station.com">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="location">Location</label>
                            <input type="text" class="form-control" id="location" name="location" 
                                   value="<?= old('location') ?>" placeholder="City, Region">
                        </div>

                        <hr>
                        <h5>Commission Configuration</h5>
                        <p class="text-muted">Default commission percentages for campaigns</p>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="default_station_percent">Station Commission (%)</label>
                                    <input type="number" class="form-control" id="default_station_percent" 
                                           name="default_station_percent" value="<?= old('default_station_percent', 25) ?>" 
                                           min="0" max="100" step="0.01">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="default_programme_percent">Programme Commission (%)</label>
                                    <input type="number" class="form-control" id="default_programme_percent" 
                                           name="default_programme_percent" value="<?= old('default_programme_percent', 10) ?>" 
                                           min="0" max="100" step="0.01">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="default_prize_pool_percent">Prize Pool (%)</label>
                                    <input type="number" class="form-control" id="default_prize_pool_percent" 
                                           name="default_prize_pool_percent" value="<?= old('default_prize_pool_percent', 40) ?>" 
                                           min="0" max="100" step="0.01">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" 
                                       <?= old('is_active', true) ? 'checked' : '' ?>>
                                <label class="custom-control-label" for="is_active">Active</label>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Create Station
                        </button>
                        <a href="<?= url('station') ?>" class="btn btn-default">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>

<?php require_once '../app/views/layouts/footer.php'; ?>
