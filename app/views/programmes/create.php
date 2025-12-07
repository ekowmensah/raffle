<?php require_once '../app/views/layouts/header.php'; ?>
<?php require_once '../app/views/layouts/sidebar.php'; ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Create Programme</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url('home') ?>">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= url('programme') ?>">Programmes</a></li>
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
                    <h3 class="card-title">Programme Information</h3>
                </div>
                <form action="<?= url('programme/create') ?>" method="POST">
                    <?= csrf_field() ?>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="station_id">Station <span class="text-danger">*</span></label>
                                    <?php if (hasRole('station_admin')): ?>
                                        <input type="hidden" name="station_id" value="<?= $_SESSION['user']->station_id ?>">
                                        <input type="text" class="form-control" value="<?= htmlspecialchars($_SESSION['user']->station_name ?? 'Your Station') ?>" readonly>
                                        <small class="form-text text-muted">You can only create programmes for your station</small>
                                    <?php else: ?>
                                        <select class="form-control" id="station_id" name="station_id" required>
                                            <option value="">Select Station</option>
                                            <?php foreach ($stations as $station): ?>
                                                <option value="<?= $station->id ?>" <?= old('station_id') == $station->id ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($station->name) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Programme Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name" 
                                           value="<?= old('name') ?>" placeholder="e.g., Morning Show" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="code">Programme Code <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="code" name="code" 
                                           value="<?= old('code') ?>" placeholder="e.g., MORNING_SHOW" required>
                                    <small class="form-text text-muted">Use uppercase with underscores</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="ussd_option_number">USSD Option Number</label>
                                    <input type="number" class="form-control" id="ussd_option_number" name="ussd_option_number" 
                                           value="<?= old('ussd_option_number') ?>" min="1" max="9" placeholder="1-9">
                                    <small class="form-text text-muted">Menu option number in USSD (1-9)</small>
                                </div>
                            </div>
                        </div>

                        <hr>
                        <h5>Commission Configuration (Optional)</h5>
                        <p class="text-muted">Override default station commission percentages for this programme</p>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="station_percent">Station Commission (%)</label>
                                    <input type="number" class="form-control" id="station_percent" name="station_percent" 
                                           value="<?= old('station_percent') ?>" min="0" max="100" step="0.01" 
                                           placeholder="Leave blank to use station default">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="programme_percent">Programme Commission (%)</label>
                                    <input type="number" class="form-control" id="programme_percent" name="programme_percent" 
                                           value="<?= old('programme_percent') ?>" min="0" max="100" step="0.01" 
                                           placeholder="Leave blank to use station default">
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
                            <i class="fas fa-save"></i> Create Programme
                        </button>
                        <a href="<?= url('programme') ?>" class="btn btn-default">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>

<?php require_once '../app/views/layouts/footer.php'; ?>
