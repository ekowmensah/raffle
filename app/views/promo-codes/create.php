<?php require_once '../app/views/layouts/header.php'; ?>
<?php require_once '../app/views/layouts/sidebar.php'; ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Create Promo Code</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url('home') ?>">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= url('promocode') ?>">Promo Codes</a></li>
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
                    <h3 class="card-title">Promo Code Details</h3>
                </div>
                <form action="<?= url('promocode/create') ?>" method="POST">
                    <?= csrf_field() ?>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="code">Promo Code <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="code" name="code" 
                                           value="<?= old('code') ?>" placeholder="e.g., SAVE20" required>
                                    <small class="text-muted">Code will be converted to uppercase</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="extra_commission_percent">Extra Commission %</label>
                                    <input type="number" class="form-control" id="extra_commission_percent" 
                                           name="extra_commission_percent" value="<?= old('extra_commission_percent', 5) ?>" 
                                           step="0.01" min="0" max="100">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="name">Name/Description <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" 
                                   value="<?= old('name') ?>" placeholder="e.g., Morning Show Promo" required>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="user_id">Assign to User (Optional)</label>
                                    <select class="form-control" id="user_id" name="user_id">
                                        <option value="">All Users</option>
                                        <?php foreach ($users as $user): ?>
                                            <option value="<?= $user->id ?>" <?= old('user_id') == $user->id ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($user->name) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="station_id">Station <span class="text-danger">*</span></label>
                                    <select class="form-control" id="station_id" name="station_id" required>
                                        <option value="">Select Station</option>
                                        <?php foreach ($stations as $station): ?>
                                            <option value="<?= $station->id ?>" <?= old('station_id') == $station->id ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($station->name) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="programme_id">Assign to Programme (Optional)</label>
                                    <select class="form-control" id="programme_id" name="programme_id">
                                        <option value="">All Programmes</option>
                                        <?php foreach ($programmes as $programme): ?>
                                            <option value="<?= $programme->id ?>" <?= old('programme_id') == $programme->id ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($programme->name) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
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
                            <i class="fas fa-save"></i> Create Promo Code
                        </button>
                        <a href="<?= url('promocode') ?>" class="btn btn-default">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>

<?php require_once '../app/views/layouts/footer.php'; ?>
