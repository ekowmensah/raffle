<?php require_once '../app/views/layouts/header.php'; ?>
<?php require_once '../app/views/layouts/sidebar.php'; ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Edit Promo Code</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url('home') ?>">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= url('promocode') ?>">Promo Codes</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Promo Code: <?= htmlspecialchars($promo->code) ?></h3>
                </div>
                <form action="<?= url('promocode/edit/' . $promo->id) ?>" method="POST">
                    <?= csrf_field() ?>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Promo Code</label>
                                    <input type="text" class="form-control" value="<?= htmlspecialchars($promo->code) ?>" disabled>
                                    <small class="text-muted">Code cannot be changed</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="extra_commission_percent">Extra Commission %</label>
                                    <input type="number" class="form-control" id="extra_commission_percent" 
                                           name="extra_commission_percent" value="<?= $promo->extra_commission_percent ?>" 
                                           step="0.01" min="0" max="100">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="name">Name/Description <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" 
                                   value="<?= htmlspecialchars($promo->name ?? '') ?>" required>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="user_id">Assign to User</label>
                                    <select class="form-control" id="user_id" name="user_id">
                                        <option value="">All Users</option>
                                        <?php foreach ($users as $user): ?>
                                            <option value="<?= $user->id ?>" <?= $promo->user_id == $user->id ? 'selected' : '' ?>>
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
                                            <option value="<?= $station->id ?>" <?= $promo->station_id == $station->id ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($station->name) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="programme_id">Assign to Programme</label>
                                    <select class="form-control" id="programme_id" name="programme_id">
                                        <option value="">All Programmes</option>
                                        <?php foreach ($programmes as $programme): ?>
                                            <option value="<?= $programme->id ?>" <?= $promo->programme_id == $programme->id ? 'selected' : '' ?>>
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
                                       <?= $promo->is_active ? 'checked' : '' ?>>
                                <label class="custom-control-label" for="is_active">Active</label>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Promo Code
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
