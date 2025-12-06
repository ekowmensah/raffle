<?php require_once '../app/views/layouts/header.php'; ?>
<?php require_once '../app/views/layouts/sidebar.php'; ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Create User</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url('home') ?>">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= url('user') ?>">Users</a></li>
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
                    <h3 class="card-title">User Information</h3>
                </div>
                <form action="<?= url('user/create') ?>" method="POST">
                    <?= csrf_field() ?>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Full Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name" 
                                           value="<?= old('name') ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="<?= old('email') ?>" required>
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
                                    <label for="password">Password <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="role_id">Role <span class="text-danger">*</span></label>
                                    <select class="form-control" id="role_id" name="role_id" required>
                                        <option value="">Select Role</option>
                                        <?php foreach ($roles as $role): ?>
                                            <option value="<?= $role->id ?>" <?= old('role_id') == $role->id ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($role->name) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="station_id">Station (Optional)</label>
                                    <select class="form-control" id="station_id" name="station_id">
                                        <option value="">Select Station</option>
                                        <?php foreach ($stations as $station): ?>
                                            <option value="<?= $station->id ?>" <?= old('station_id') == $station->id ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($station->name) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="programme_id">Programme (Optional)</label>
                                    <select class="form-control" id="programme_id" name="programme_id">
                                        <option value="">Select Programme</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="custom-control custom-checkbox mt-4">
                                        <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" 
                                               <?= old('is_active') ? 'checked' : 'checked' ?>>
                                        <label class="custom-control-label" for="is_active">Active</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Create User
                        </button>
                        <a href="<?= url('user') ?>" class="btn btn-default">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>

<script>
// Load programmes when station is selected
document.getElementById('station_id').addEventListener('change', function() {
    const stationId = this.value;
    const programmeSelect = document.getElementById('programme_id');
    
    programmeSelect.innerHTML = '<option value="">Select Programme</option>';
    
    if (stationId) {
        fetch('<?= url('programme/getByStation/') ?>' + stationId)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.programmes) {
                    data.programmes.forEach(programme => {
                        const option = document.createElement('option');
                        option.value = programme.id;
                        option.textContent = programme.name;
                        programmeSelect.appendChild(option);
                    });
                }
            });
    }
});
</script>

<?php require_once '../app/views/layouts/footer.php'; ?>
