<?php require_once '../app/views/layouts/header.php'; ?>
<?php require_once '../app/views/layouts/sidebar.php'; ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Schedule Draw</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url('home') ?>">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= url('draw') ?>">Draws</a></li>
                        <li class="breadcrumb-item active">Schedule</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            
            <?php if (flash('success')): ?>
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <i class="icon fas fa-check"></i> <?= flash('success') ?>
                </div>
            <?php endif; ?>

            <?php 
            $errorMsg = flash('error');
            if ($errorMsg): 
            ?>
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <i class="icon fas fa-ban"></i> <?= htmlspecialchars($errorMsg) ?>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Draw Details</h3>
                </div>
                <form action="<?= url('draw/schedule') ?>" method="POST" onsubmit="return validateScheduleForm()">
                    <?= csrf_field() ?>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="station_id">Station <span class="text-danger">*</span></label>
                            <select class="form-control" id="station_id" name="station_id" required onchange="onStationChange()">
                                <option value="">Select Station</option>
                                <?php
                                $stationModel = new \App\Models\Station();
                                $stations = $stationModel->getActive();
                                foreach ($stations as $station):
                                ?>
                                    <option value="<?= $station->id ?>"><?= htmlspecialchars($station->name) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Campaign Type</label>
                            <div class="btn-group btn-group-toggle d-flex" data-toggle="buttons">
                                <label class="btn btn-outline-primary active" id="station-wide-btn">
                                    <input type="radio" name="campaign_type" value="station" checked onchange="toggleCampaignType()"> 
                                    <i class="fas fa-broadcast-tower"></i> Station-Wide
                                </label>
                                <label class="btn btn-outline-primary" id="programme-btn">
                                    <input type="radio" name="campaign_type" value="programme" onchange="toggleCampaignType()"> 
                                    <i class="fas fa-microphone"></i> Programme-Specific
                                </label>
                            </div>
                        </div>

                        <div class="form-group" id="programme_field" style="display: none;">
                            <label for="programme_id">Programme</label>
                            <select class="form-control" id="programme_id" name="programme_id" disabled onchange="loadCampaigns()">
                                <option value="">First select a station...</option>
                            </select>
                            <small class="form-text text-muted">Required for programme-specific campaigns</small>
                        </div>

                        <div class="form-group">
                            <label for="campaign_id">Campaign <span class="text-danger">*</span></label>
                            <select class="form-control" id="campaign_id" name="campaign_id" required disabled onchange="updateScheduleType()">
                                <option value="">First select a station...</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Scheduling Type</label>
                            <div class="custom-control custom-radio">
                                <input type="radio" id="schedule_single" name="schedule_type" value="single" 
                                       class="custom-control-input" checked onchange="toggleScheduleFields()">
                                <label class="custom-control-label" for="schedule_single">
                                    Schedule Single Draw
                                </label>
                            </div>
                            <div class="custom-control custom-radio">
                                <input type="radio" id="schedule_auto" name="schedule_type" value="auto_daily" 
                                       class="custom-control-input" onchange="toggleScheduleFields()">
                                <label class="custom-control-label" for="schedule_auto">
                                    Auto-Schedule All Daily Draws (from start to end date)
                                </label>
                            </div>
                        </div>

                        <div id="single_draw_fields">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="draw_type">Draw Type <span class="text-danger">*</span></label>
                                        <select class="form-control" id="draw_type" name="draw_type">
                                            <option value="daily">Daily Draw</option>
                                            <option value="final">Final Draw</option>
                                            <option value="bonus">Bonus Draw</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="draw_date">Draw Date <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="draw_date" name="draw_date">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="winner_count">Number of Winners <span class="text-danger">*</span></label>
                                        <select class="form-control" id="winner_count" name="winner_count" required onchange="updatePrizePreview()">
                                            <option value="1">1 Winner</option>
                                            <option value="2">2 Winners</option>
                                            <option value="3" selected>3 Winners (Recommended)</option>
                                            <option value="4">4 Winners</option>
                                            <option value="5">5 Winners</option>
                                            <option value="6">6 Winners</option>
                                            <option value="7">7 Winners</option>
                                            <option value="8">8 Winners</option>
                                            <option value="9">9 Winners</option>
                                            <option value="10">10 Winners</option>
                                        </select>
                                        <small class="form-text text-muted" id="prize_preview">
                                            Prize split: 50% / 30% / 20%
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="auto_schedule_info" style="display: none;">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> 
                                This will automatically schedule daily draws from campaign start date to one day before end date.
                                Existing draws will be skipped.
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-calendar-check"></i> <span id="submit_text">Schedule Draw</span>
                        </button>
                        <a href="<?= url('draw/pending') ?>" class="btn btn-default">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>

<script src="<?= vendor('jquery/jquery.min.js') ?>"></script>
<script>
// Handle station selection
function onStationChange() {
    const campaignType = $('input[name="campaign_type"]:checked').val();
    
    if (campaignType === 'station') {
        loadStationWideCampaigns();
    } else {
        loadProgrammes();
    }
}

// Toggle between station-wide and programme-specific campaigns
function toggleCampaignType() {
    const campaignType = $('input[name="campaign_type"]:checked').val();
    const programmeField = $('#programme_field');
    const programmeSelect = $('#programme_id');
    const stationId = $('#station_id').val();
    
    if (campaignType === 'programme') {
        programmeField.show();
        programmeSelect.prop('required', true);
        if (stationId) {
            loadProgrammes();
        }
    } else {
        programmeField.hide();
        programmeSelect.prop('required', false);
        programmeSelect.val('');
        if (stationId) {
            loadStationWideCampaigns();
        }
    }
}

// Load station-wide campaigns
function loadStationWideCampaigns() {
    const stationId = $('#station_id').val();
    const campaignSelect = $('#campaign_id');
    
    campaignSelect.html('<option value="">Loading...</option>').prop('disabled', true);
    
    if (!stationId) {
        campaignSelect.html('<option value="">First select a station...</option>');
        return;
    }
    
    $.get('<?= url('public/getCampaignsByStation') ?>/' + stationId, function(response) {
        if (response.success && response.campaigns.length > 0) {
            campaignSelect.html('<option value="">Select a campaign...</option>');
            response.campaigns.forEach(function(campaign) {
                campaignSelect.append(
                    $('<option></option>')
                        .val(campaign.id)
                        .text(campaign.name + ' (' + campaign.code + ')')
                        .attr('data-daily-enabled', campaign.daily_draw_enabled || 0)
                        .attr('data-start', campaign.start_date || '')
                        .attr('data-end', campaign.end_date || '')
                );
            });
            campaignSelect.prop('disabled', false);
        } else {
            campaignSelect.html('<option value="">No station-wide campaigns available</option>');
        }
    });
}

// Load programmes when station is selected
function loadProgrammes() {
    const stationId = $('#station_id').val();
    const programmeSelect = $('#programme_id');
    const campaignSelect = $('#campaign_id');
    
    // Reset dependent dropdowns
    programmeSelect.html('<option value="">Loading...</option>').prop('disabled', true);
    campaignSelect.html('<option value="">First select a programme...</option>').prop('disabled', true);
    
    if (!stationId) {
        programmeSelect.html('<option value="">First select a station...</option>');
        return;
    }
    
    $.get('<?= url('public/getProgrammesByStation') ?>/' + stationId, function(response) {
        if (response.success && response.programmes.length > 0) {
            programmeSelect.html('<option value="">Select a programme...</option>');
            response.programmes.forEach(function(programme) {
                programmeSelect.append($('<option></option>').val(programme.id).text(programme.name));
            });
            programmeSelect.prop('disabled', false);
        } else {
            programmeSelect.html('<option value="">No programmes available</option>');
        }
    });
}

// Load campaigns when programme is selected
function loadCampaigns() {
    const programmeId = $('#programme_id').val();
    const campaignSelect = $('#campaign_id');
    
    campaignSelect.html('<option value="">Loading...</option>').prop('disabled', true);
    
    if (!programmeId) {
        campaignSelect.html('<option value="">First select a programme...</option>');
        return;
    }
    
    $.get('<?= url('public/getCampaignsByProgramme') ?>/' + programmeId, function(response) {
        if (response.success && response.campaigns.length > 0) {
            campaignSelect.html('<option value="">Select a campaign...</option>');
            response.campaigns.forEach(function(campaign) {
                campaignSelect.append(
                    $('<option></option>')
                        .val(campaign.id)
                        .text(campaign.name + ' (' + campaign.code + ')')
                        .attr('data-daily-enabled', campaign.daily_draw_enabled || 0)
                        .attr('data-start', campaign.start_date || '')
                        .attr('data-end', campaign.end_date || '')
                );
            });
            campaignSelect.prop('disabled', false);
        } else {
            campaignSelect.html('<option value="">No campaigns available</option>');
        }
    });
}

function toggleScheduleFields() {
    const scheduleType = document.querySelector('input[name="schedule_type"]:checked').value;
    const singleFields = document.getElementById('single_draw_fields');
    const autoInfo = document.getElementById('auto_schedule_info');
    const submitText = document.getElementById('submit_text');
    
    if (scheduleType === 'auto_daily') {
        singleFields.style.display = 'none';
        autoInfo.style.display = 'block';
        submitText.textContent = 'Auto-Schedule Daily Draws';
        
        // Make single draw fields optional
        document.getElementById('draw_type').removeAttribute('required');
        document.getElementById('draw_date').removeAttribute('required');
    } else {
        singleFields.style.display = 'block';
        autoInfo.style.display = 'none';
        submitText.textContent = 'Schedule Draw';
        
        // Make single draw fields required
        document.getElementById('draw_type').setAttribute('required', 'required');
        document.getElementById('draw_date').setAttribute('required', 'required');
    }
}

function updateScheduleType() {
    const campaignSelect = document.getElementById('campaign_id');
    const selectedOption = campaignSelect.options[campaignSelect.selectedIndex];
    const dailyEnabled = selectedOption.getAttribute('data-daily-enabled');
    
    if (dailyEnabled === '0') {
        document.getElementById('schedule_auto').disabled = true;
        document.getElementById('schedule_single').checked = true;
        toggleScheduleFields();
    } else {
        document.getElementById('schedule_auto').disabled = false;
    }
}

// Update prize distribution preview
function updatePrizePreview() {
    const winnerCount = parseInt($('#winner_count').val());
    const preview = $('#prize_preview');
    
    if (winnerCount === 1) {
        preview.text('Prize split: 100% (Winner takes all)');
    } else if (winnerCount === 2) {
        preview.text('Prize split: 60% / 40%');
    } else if (winnerCount === 3) {
        preview.text('Prize split: 50% / 30% / 20%');
    } else {
        preview.text(`Prize split: Equal distribution (${(100/winnerCount).toFixed(1)}% each)`);
    }
}

// Validate form before submission
function validateScheduleForm() {
    const campaignType = $('input[name="campaign_type"]:checked').val();
    const programmeId = $('#programme_id').val();
    const campaignId = $('#campaign_id').val();
    const scheduleType = $('input[name="schedule_type"]:checked').val();
    
    // Check if campaign is selected
    if (!campaignId) {
        alert('Please select a campaign');
        return false;
    }
    
    // For programme-specific campaigns, ensure programme is selected
    if (campaignType === 'programme' && !programmeId) {
        alert('Please select a programme for programme-specific campaigns');
        return false;
    }
    
    // For station-wide campaigns, clear programme_id
    if (campaignType === 'station') {
        $('#programme_id').val('');
    }
    
    // For single draws, validate required fields
    if (scheduleType === 'single') {
        const drawType = $('#draw_type').val();
        const drawDate = $('#draw_date').val();
        const winnerCount = $('#winner_count').val();
        
        if (!drawType || !drawDate || !winnerCount) {
            alert('Please fill in all draw details');
            return false;
        }
    }
    
    return true;
}
</script>

<?php require_once '../app/views/layouts/footer.php'; ?>
