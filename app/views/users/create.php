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
                        </div>

                        <div class="row">
                            <div class="col-md-6" id="station_field">
                                <div class="form-group">
                                    <label for="station_id">
                                        Station 
                                        <span class="text-danger station-required" style="display:none;">*</span>
                                    </label>
                                    <?php if (hasRole('station_admin')): ?>
                                        <input type="hidden" name="station_id" value="<?= $_SESSION['user']->station_id ?>">
                                        <input type="text" class="form-control" value="<?= htmlspecialchars($_SESSION['user']->station_name ?? 'Your Station') ?>" readonly>
                                        <small class="form-text text-muted">You can only create users for your station</small>
                                    <?php else: ?>
                                        <select class="form-control" id="station_id" name="station_id">
                                            <option value="">Select Station</option>
                                            <?php foreach ($stations as $station): ?>
                                                <option value="<?= $station->id ?>" <?= old('station_id') == $station->id ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($station->name) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <small class="form-text text-muted station-help"></small>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6" id="programme_field">
                                <div class="form-group">
                                    <label for="programme_id">
                                        Programme 
                                        <span class="text-danger programme-required" style="display:none;">*</span>
                                    </label>
                                    <select class="form-control" id="programme_id" name="programme_id">
                                        <option value="">Select Programme</option>
                                    </select>
                                    <small class="form-text text-muted programme-help"></small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
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
// Role-based field requirements
const roleSelect = document.getElementById('role_id');
const stationField = document.getElementById('station_field');
const programmeField = document.getElementById('programme_field');
const stationSelect = document.getElementById('station_id');
const programmeSelect = document.getElementById('programme_id');

// Role requirements mapping
const roleRequirements = {
    'station_admin': { station: true, programme: false },
    'programme_manager': { station: true, programme: true },
    'finance': { station: false, programme: false },
    'auditor': { station: false, programme: false }
};

// Update field requirements based on selected role
roleSelect.addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const roleName = selectedOption.text.toLowerCase().replace(/\s+/g, '_');
    
    const requirements = roleRequirements[roleName] || { station: false, programme: false };
    
    // Update station field
    if (requirements.station) {
        document.querySelector('.station-required').style.display = 'inline';
        stationSelect.required = true;
        document.querySelector('.station-help').textContent = 'Required for this role';
        stationField.style.display = 'block';
    } else {
        document.querySelector('.station-required').style.display = 'none';
        stationSelect.required = false;
        document.querySelector('.station-help').textContent = '';
    }
    
    // Update programme field
    if (requirements.programme) {
        document.querySelector('.programme-required').style.display = 'inline';
        programmeSelect.required = true;
        document.querySelector('.programme-help').textContent = 'Required for this role - select station first';
        programmeField.style.display = 'block';
    } else {
        document.querySelector('.programme-required').style.display = 'none';
        programmeSelect.required = false;
        document.querySelector('.programme-help').textContent = '';
    }
});

// Load programmes when station is selected
stationSelect.addEventListener('change', function() {
    const stationId = this.value;
    
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

// Trigger role change on page load if role is selected
if (roleSelect.value) {
    roleSelect.dispatchEvent(new Event('change'));
}
</script>

<?php require_once '../app/views/layouts/footer.php'; ?>
