<?php require_once '../app/views/layouts/header.php'; ?>
<?php require_once '../app/views/layouts/sidebar.php'; ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Edit Programme</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url('home') ?>">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= url('programme') ?>">Programmes</a></li>
                        <li class="breadcrumb-item active">Edit</li>
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
                <form action="<?= url('programme/edit/' . $programme->id) ?>" method="POST">
                    <?= csrf_field() ?>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="station_id">Station <span class="text-danger">*</span></label>
                                    <select class="form-control" id="station_id" name="station_id" required>
                                        <option value="">Select Platform</option>
                                        <?php foreach ($stations as $station): ?>
                                            <option value="<?= $station->id ?>" <?= $programme->station_id == $station->id ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($station->name) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Programme Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name" 
                                           value="<?= htmlspecialchars($programme->name) ?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="code">Programme Code <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="code" name="code" 
                                           value="<?= htmlspecialchars($programme->code) ?>" required>
                                    <small class="form-text text-muted">Use uppercase with underscores</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="ussd_option_number">USSD Option Number</label>
                                    <input type="number" class="form-control" id="ussd_option_number" name="ussd_option_number" 
                                           value="<?= $programme->ussd_option_number ?? '' ?>" min="1" max="9">
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
                                           value="<?= $programme->station_percent ?? '' ?>" min="0" max="100" step="0.01" 
                                           placeholder="Leave blank to use station default">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="programme_percent">Programme Commission (%)</label>
                                    <input type="number" class="form-control" id="programme_percent" name="programme_percent" 
                                           value="<?= $programme->programme_percent ?? '' ?>" min="0" max="100" step="0.01" 
                                           placeholder="Leave blank to use station default">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" 
                                       <?= $programme->is_active ? 'checked' : '' ?>>
                                <label class="custom-control-label" for="is_active">Active</label>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Programme
                        </button>
                        <a href="<?= url('programme/show/' . $programme->id) ?>" class="btn btn-default">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>

<?php require_once '../app/views/layouts/footer.php'; ?>
