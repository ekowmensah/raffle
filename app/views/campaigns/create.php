<?php require_once '../app/views/layouts/header.php'; ?>
<?php require_once '../app/views/layouts/sidebar.php'; ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Create Campaign</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url('home') ?>">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= url('campaign') ?>">Campaigns</a></li>
                        <li class="breadcrumb-item active">Create</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            
            <?php 
            $errorMsg = flash('error');
            if ($errorMsg): 
            ?>
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <i class="icon fas fa-ban"></i> <?= htmlspecialchars($errorMsg) ?>
                </div>
            <?php endif; ?>

            <form action="<?= url('campaign/create') ?>" method="POST">
                <?= csrf_field() ?>
                
                <!-- Basic Information -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Basic Information</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Campaign Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name" 
                                           value="<?= old('name') ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="code">Campaign Code <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="code" name="code" 
                                           value="<?= old('code') ?>" placeholder="e.g., DEC2024" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"><?= old('description') ?></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="station_id">Station <span class="text-danger">*</span></label>
                                    <?php if (hasRole('station_admin')): ?>
                                        <input type="hidden" name="station_id" id="station_id" value="<?= $_SESSION['user']->station_id ?>">
                                        <input type="text" class="form-control" value="<?= htmlspecialchars($_SESSION['user']->station_name ?? 'Your Station') ?>" readonly>
                                        <small class="form-text text-muted">You can only create campaigns for your station</small>
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
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="programme_id">Programme <small class="text-muted">(Optional - leave blank for station-wide campaign)</small></label>
                                    <select class="form-control" id="programme_id" name="programme_id">
                                        <option value="">Station-wide (No specific programme)</option>
                                    </select>
                                    <small class="form-text text-muted">
                                        Select a programme for programme-specific campaigns, or leave as "Station-wide" for campaigns accessible to all programmes under this station.
                                    </small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="sponsor_id">Sponsor</label>
                                    <select class="form-control" id="sponsor_id" name="sponsor_id">
                                        <option value="">No Sponsor</option>
                                        <?php foreach ($sponsors as $sponsor): ?>
                                            <option value="<?= $sponsor->id ?>" <?= old('sponsor_id') == $sponsor->id ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($sponsor->name) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pricing & Duration -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Pricing & Duration</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="ticket_price">Ticket Price <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="ticket_price" name="ticket_price" 
                                           value="<?= old('ticket_price', 1) ?>" step="0.01" min="0" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="start_date">Start Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="start_date" name="start_date" 
                                           value="<?= old('start_date') ?>" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="end_date">End Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="end_date" name="end_date" 
                                           value="<?= old('end_date') ?>" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Revenue Sharing -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Revenue Sharing Configuration</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="platform_percent">Platform %</label>
                                    <input type="number" class="form-control revenue-percent" id="platform_percent" name="platform_percent" 
                                           value="<?= old('platform_percent', 25) ?>" step="0.01" min="0" max="100" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="station_percent">Station %</label>
                                    <input type="number" class="form-control revenue-percent" id="station_percent" name="station_percent" 
                                           value="<?= old('station_percent', 25) ?>" step="0.01" min="0" max="100" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="programme_percent">Programme %</label>
                                    <input type="number" class="form-control revenue-percent" id="programme_percent" name="programme_percent" 
                                           value="<?= old('programme_percent', 10) ?>" step="0.01" min="0" max="100" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="prize_pool_percent">Prize Pool %</label>
                                    <input type="number" class="form-control revenue-percent" id="prize_pool_percent" name="prize_pool_percent" 
                                           value="<?= old('prize_pool_percent', 40) ?>" step="0.01" min="0" max="100" required>
                                </div>
                            </div>
                        </div>
                        <div class="alert alert-info">
                            <strong>Total: <span id="revenue-total">100</span>%</strong>
                            <span id="revenue-warning" class="text-danger ml-2" style="display:none;">
                                <i class="fas fa-exclamation-triangle"></i> Must equal 100%!
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Draw Configuration -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Draw Configuration</h3>
                    </div>
                    <div class="card-body">
                        <div class="custom-control custom-checkbox mb-3">
                            <input type="checkbox" class="custom-control-input" id="daily_draw_enabled" name="daily_draw_enabled" 
                                   <?= old('daily_draw_enabled', true) ? 'checked' : '' ?>>
                            <label class="custom-control-label" for="daily_draw_enabled">Enable Daily Draws</label>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="daily_share_percent_of_pool">Daily Draw Pool %</label>
                                    <input type="number" class="form-control pool-percent" id="daily_share_percent_of_pool" 
                                           name="daily_share_percent_of_pool" value="<?= old('daily_share_percent_of_pool', 50) ?>" 
                                           step="0.01" min="0" max="100" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="final_share_percent_of_pool">Final Draw Pool %</label>
                                    <input type="number" class="form-control pool-percent" id="final_share_percent_of_pool" 
                                           name="final_share_percent_of_pool" value="<?= old('final_share_percent_of_pool', 50) ?>" 
                                           step="0.01" min="0" max="100" required>
                                </div>
                            </div>
                        </div>
                        <div class="alert alert-info">
                            <strong>Prize Pool Split - Total: <span id="pool-total">100</span>%</strong>
                            <span id="pool-warning" class="text-danger ml-2" style="display:none;">
                                <i class="fas fa-exclamation-triangle"></i> Cannot exceed 100%!
                            </span>
                            <br><small class="text-muted">Bonus Pool (overflow): <span id="bonus-pool">0</span>%</small>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Create Campaign
                        </button>
                        <a href="<?= url('campaign') ?>" class="btn btn-default">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </section>
</div>

<?php require_once '../app/views/layouts/footer.php'; ?>

<script>
// Revenue sharing validation
function validateRevenueSharing() {
    const platform = parseFloat($('#platform_percent').val()) || 0;
    const station = parseFloat($('#station_percent').val()) || 0;
    const programme = parseFloat($('#programme_percent').val()) || 0;
    const prizePool = parseFloat($('#prize_pool_percent').val()) || 0;
    
    const total = platform + station + programme + prizePool;
    $('#revenue-total').text(total.toFixed(2));
    
    if (Math.abs(total - 100) > 0.01) {
        $('#revenue-warning').show();
        $('.revenue-percent').addClass('is-invalid');
        return false;
    } else {
        $('#revenue-warning').hide();
        $('.revenue-percent').removeClass('is-invalid');
        return true;
    }
}

// Prize pool split validation
function validatePoolSplit() {
    const daily = parseFloat($('#daily_share_percent_of_pool').val()) || 0;
    const final = parseFloat($('#final_share_percent_of_pool').val()) || 0;
    
    const total = daily + final;
    const bonus = Math.max(0, 100 - total);
    
    $('#pool-total').text(total.toFixed(2));
    $('#bonus-pool').text(bonus.toFixed(2));
    
    if (total > 100) {
        $('#pool-warning').show();
        $('.pool-percent').addClass('is-invalid');
        return false;
    } else {
        $('#pool-warning').hide();
        $('.pool-percent').removeClass('is-invalid');
        return true;
    }
}

// Attach event listeners
$(document).ready(function() {
    $('.revenue-percent').on('input change', validateRevenueSharing);
    $('.pool-percent').on('input change', validatePoolSplit);
    
    // Load programmes based on station selection
    $('#station_id').change(function() {
        const stationId = $(this).val();
        const programmeSelect = $('#programme_id');
        
        console.log('Station selected:', stationId);
        programmeSelect.html('<option value="">Loading...</option>');
        
        if (!stationId) {
            programmeSelect.html('<option value="">Select Programme</option>');
            return;
        }
        
        const ajaxUrl = '<?= url('campaign/getProgrammesByStation') ?>';
        console.log('AJAX URL:', ajaxUrl);
        
        $.ajax({
            url: ajaxUrl,
            method: 'GET',
            data: { station_id: stationId },
            dataType: 'json',
            success: function(response) {
                console.log('Full response:', response);
                console.log('Programmes array:', response.programmes);
                console.log('Programme count:', response.count);
                
                programmeSelect.html('<option value="">Select Programme</option>');
                
                if (response.programmes && response.programmes.length > 0) {
                    console.log('Adding programmes to dropdown...');
                    response.programmes.forEach(function(programme) {
                        console.log('Adding programme:', programme.id, programme.name);
                        const option = $('<option></option>')
                            .attr('value', programme.id)
                            .text(programme.name);
                        programmeSelect.append(option);
                    });
                    console.log('Programmes added successfully');
                } else {
                    console.log('No programmes found');
                    programmeSelect.html('<option value="">No programmes available</option>');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error);
                console.error('Status code:', xhr.status);
                console.error('Response text:', xhr.responseText);
                programmeSelect.html('<option value="">Error loading programmes</option>');
            }
        });
    });
    
    // Initial validation
    validateRevenueSharing();
    validatePoolSplit();
    
    // Form submission validation
    $('form').on('submit', function(e) {
        const revenueValid = validateRevenueSharing();
        const poolValid = validatePoolSplit();
        
        if (!revenueValid || !poolValid) {
            e.preventDefault();
            alert('Please fix the validation errors before submitting.');
            return false;
        }
    });
});
</script>
