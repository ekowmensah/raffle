<?php require_once '../app/views/layouts/header.php'; ?>
<?php require_once '../app/views/layouts/sidebar.php'; ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0"><i class="fas fa-calendar-plus"></i> Schedule Draw</h1>
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
            
            <?php 
            $successMsg = flash('success');
            $errorMsg = flash('error');
            ?>
            
            <?php if ($successMsg): ?>
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <i class="icon fas fa-check"></i> <?= htmlspecialchars($successMsg) ?>
                </div>
            <?php endif; ?>

            <?php if ($errorMsg): ?>
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <i class="icon fas fa-ban"></i> <?= htmlspecialchars($errorMsg) ?>
                </div>
            <?php endif; ?>

            <form action="<?= url('draw/schedule') ?>" method="POST" id="scheduleForm" onsubmit="return validateScheduleForm()">
                <?= csrf_field() ?>
                
                <!-- Campaign Selection Card -->
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-bullseye"></i> Select Campaign</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="station_id"><i class="fas fa-broadcast-tower"></i> Station <span class="text-danger">*</span></label>
                            <?php
                            $user = $_SESSION['user'];
                            $role = $user->role_name ?? '';
                            
                            if (($role === 'station_admin' || $role === 'programme_manager') && $user->station_id):
                                $stationModel = new \App\Models\Station();
                                $station = $stationModel->findById($user->station_id);
                            ?>
                                <input type="hidden" name="station_id" id="station_id" value="<?= $user->station_id ?>">
                                <input type="text" class="form-control" value="<?= htmlspecialchars($station->name ?? 'Your Station') ?>" readonly>
                                <small class="form-text text-muted">You can only schedule draws for your station</small>
                            <?php else: ?>
                                <select class="form-control" id="station_id" name="station_id" required onchange="onStationChange()">
                                    <option value="">Select Station</option>
                                    <?php
                                    $stationModel = new \App\Models\Station();
                                    $stations = $stationModel->getActive();
                                    foreach ($stations as $station):
                                    ?>
                                        <option value="<?= $station->id ?>"><?= htmlspecialchars($station->name ?? '') ?></option>
                                    <?php endforeach; ?>
                                </select>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label><i class="fas fa-layer-group"></i> Campaign Type</label>
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
                            <label for="programme_id"><i class="fas fa-microphone"></i> Programme</label>
                            <select class="form-control" id="programme_id" name="programme_id" disabled onchange="loadCampaigns()">
                                <option value="">First select a station...</option>
                            </select>
                            <small class="form-text text-muted">Required for programme-specific campaigns</small>
                        </div>

                        <div class="form-group">
                            <label for="campaign_id"><i class="fas fa-gamepad"></i> Campaign <span class="text-danger">*</span></label>
                            <select class="form-control" id="campaign_id" name="campaign_id" required disabled onchange="onCampaignChange()">
                                <option value="">First select a station...</option>
                            </select>
                        </div>

                        <!-- Campaign Info Display -->
                        <div id="campaign_info" style="display: none;">
                            <div class="alert alert-info" id="campaign_info_box">
                                <h5><i class="fas fa-info-circle"></i> Campaign Information</h5>
                                <div id="campaign_details"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Draw Configuration Card -->
                <div class="card card-success card-outline">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-cog"></i> Draw Configuration</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label><i class="fas fa-calendar-alt"></i> Scheduling Type</label>
                            <div class="custom-control custom-radio">
                                <input type="radio" id="schedule_single" name="schedule_type" value="single" 
                                       class="custom-control-input" checked onchange="toggleScheduleFields()">
                                <label class="custom-control-label" for="schedule_single">
                                    <i class="fas fa-calendar-day"></i> Schedule Single Draw
                                </label>
                            </div>
                            <div class="custom-control custom-radio">
                                <input type="radio" id="schedule_auto" name="schedule_type" value="auto_daily" 
                                       class="custom-control-input" onchange="toggleScheduleFields()">
                                <label class="custom-control-label" for="schedule_auto">
                                    <i class="fas fa-calendar-week"></i> Auto-Schedule All Daily Draws (from start to end date)
                                </label>
                            </div>
                        </div>

                        <div id="single_draw_fields">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="draw_type"><i class="fas fa-tag"></i> Draw Type <span class="text-danger">*</span></label>
                                        <select class="form-control" id="draw_type" name="draw_type">
                                            <option value="daily">Daily Draw</option>
                                            <option value="final">Final Draw</option>
                                            <option value="bonus">Bonus Draw</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="draw_date"><i class="fas fa-calendar"></i> Draw Date <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="draw_date" name="draw_date">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group" id="winner_count_group">
                                        <label for="winner_count"><i class="fas fa-users"></i> Number of Winners <span class="text-danger">*</span></label>
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
                </div>

                <!-- Submit Buttons -->
                <div class="card">
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-calendar-check"></i> <span id="submit_text">Schedule Draw</span>
                        </button>
                        <a href="<?= url('draw/pending') ?>" class="btn btn-default btn-lg">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </section>
</div>

<script src="<?= vendor('jquery/jquery.min.js') ?>"></script>
<script>
// Store selected campaign data
let selectedCampaign = null;

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
    $('#campaign_info').hide();
    
    if (!stationId) {
        campaignSelect.html('<option value="">First select a station...</option>');
        return;
    }
    
    $.get('<?= url('public/getCampaignsByStation') ?>/' + stationId, function(response) {
        if (response.success && response.campaigns.length > 0) {
            campaignSelect.html('<option value="">Select a campaign...</option>');
            response.campaigns.forEach(function(campaign) {
                const campaignType = campaign.campaign_type === 'item' ? '🎁' : '💰';
                campaignSelect.append(
                    $('<option></option>')
                        .val(campaign.id)
                        .text(campaignType + ' ' + campaign.name + ' (' + campaign.code + ')')
                        .data('campaign', campaign)
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
    
    programmeSelect.html('<option value="">Loading...</option>').prop('disabled', true);
    campaignSelect.html('<option value="">First select a programme...</option>').prop('disabled', true);
    $('#campaign_info').hide();
    
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
    $('#campaign_info').hide();
    
    if (!programmeId) {
        campaignSelect.html('<option value="">First select a programme...</option>');
        return;
    }
    
    $.get('<?= url('public/getCampaignsByProgramme') ?>/' + programmeId, function(response) {
        if (response.success && response.campaigns.length > 0) {
            campaignSelect.html('<option value="">Select a campaign...</option>');
            response.campaigns.forEach(function(campaign) {
                const campaignType = campaign.campaign_type === 'item' ? '🎁' : '💰';
                campaignSelect.append(
                    $('<option></option>')
                        .val(campaign.id)
                        .text(campaignType + ' ' + campaign.name + ' (' + campaign.code + ')')
                        .data('campaign', campaign)
                );
            });
            campaignSelect.prop('disabled', false);
        } else {
            campaignSelect.html('<option value="">No campaigns available</option>');
        }
    });
}

// Handle campaign selection
function onCampaignChange() {
    const campaignSelect = $('#campaign_id');
    const selectedOption = campaignSelect.find('option:selected');
    selectedCampaign = selectedOption.data('campaign');
    
    if (!selectedCampaign) {
        $('#campaign_info').hide();
        return;
    }
    
    // Display campaign information
    displayCampaignInfo(selectedCampaign);
    
    // Configure draw options based on campaign type
    configureDrawOptions(selectedCampaign);
    
    // Update schedule type availability
    updateScheduleType(selectedCampaign);
}

// Display campaign information
function displayCampaignInfo(campaign) {
    let html = '<div class="row">';
    
    // Campaign Type
    if (campaign.campaign_type === 'item') {
        html += '<div class="col-md-12"><p><strong><i class="fas fa-gift text-success"></i> Campaign Type:</strong> <span class="badge badge-success">Item Campaign</span></p></div>';
        html += '<div class="col-md-6"><p><strong><i class="fas fa-trophy"></i> Prize Item:</strong> ' + (campaign.item_name || 'N/A') + '</p></div>';
        html += '<div class="col-md-6"><p><strong><i class="fas fa-money-bill-wave"></i> Item Value:</strong> ' + (campaign.currency || 'GHS') + ' ' + parseFloat(campaign.item_value || 0).toFixed(2) + '</p></div>';
        
        // Winner selection type
        let selectionType = 'Single Winner';
        if (campaign.winner_selection_type === 'multiple') {
            selectionType = 'Multiple Winners (' + (campaign.item_quantity || 1) + ' items)';
        } else if (campaign.winner_selection_type === 'tiered') {
            selectionType = 'Tiered Prizes';
        }
        html += '<div class="col-md-6"><p><strong><i class="fas fa-users"></i> Winner Selection:</strong> ' + selectionType + '</p></div>';
        
        // Minimum tickets
        if (campaign.min_tickets_for_draw) {
            html += '<div class="col-md-6"><p><strong><i class="fas fa-exclamation-triangle text-warning"></i> Minimum Tickets:</strong> ' + campaign.min_tickets_for_draw + ' tickets required</p></div>';
        }
    } else {
        html += '<div class="col-md-12"><p><strong><i class="fas fa-money-bill-wave text-primary"></i> Campaign Type:</strong> <span class="badge badge-primary">Cash Campaign</span></p></div>';
        html += '<div class="col-md-6"><p><strong><i class="fas fa-trophy"></i> Prize Pool:</strong> ' + (campaign.prize_pool_percent || 0) + '% of revenue</p></div>';
    }
    
    // Common info
    html += '<div class="col-md-6"><p><strong><i class="fas fa-ticket-alt"></i> Ticket Price:</strong> ' + (campaign.currency || 'GHS') + ' ' + parseFloat(campaign.ticket_price || 0).toFixed(2) + '</p></div>';
    html += '<div class="col-md-6"><p><strong><i class="fas fa-calendar-alt"></i> Period:</strong> ' + (campaign.start_date || 'N/A') + ' to ' + (campaign.end_date || 'N/A') + '</p></div>';
    
    if (campaign.daily_draw_enabled == 1) {
        html += '<div class="col-md-6"><p><strong><i class="fas fa-bolt text-warning"></i> Daily Draws:</strong> <span class="badge badge-success">Enabled</span></p></div>';
    }
    
    html += '</div>';
    
    $('#campaign_details').html(html);
    $('#campaign_info').show();
    
    // Update info box color based on campaign type
    if (campaign.campaign_type === 'item') {
        $('#campaign_info_box').removeClass('alert-info').addClass('alert-success');
    } else {
        $('#campaign_info_box').removeClass('alert-success').addClass('alert-info');
    }
}

// Configure draw options based on campaign type
function configureDrawOptions(selectedCampaign) {
    const winnerCountGroup = $('#winner_count_group');
    const winnerCountSelect = $('#winner_count');
    const prizePreview = $('#prize_preview');
    
    if (selectedCampaign.campaign_type === 'item') {
        // For item campaigns, winner count depends on selection type
        if (selectedCampaign.winner_selection_type === 'single') {
            // Single winner only
            winnerCountSelect.html('<option value="1">1 Winner</option>');
            winnerCountSelect.val('1').prop('disabled', true);
            prizePreview.html('<i class="fas fa-gift text-success"></i> Winner gets: ' + (selectedCampaign.item_name || 'Item'));
        } else if (selectedCampaign.winner_selection_type === 'multiple') {
            // Multiple winners based on item quantity
            const quantity = parseInt(selectedCampaign.item_quantity) || 1;
            winnerCountSelect.html('<option value="' + quantity + '">' + quantity + ' Winner(s)</option>');
            winnerCountSelect.val(quantity).prop('disabled', true);
            prizePreview.html('<i class="fas fa-gift text-success"></i> Each winner gets 1 ' + (selectedCampaign.item_name || 'Item'));
        } else if (selectedCampaign.winner_selection_type === 'tiered') {
            // Tiered prizes - typically 3 levels
            winnerCountSelect.html('<option value="3">3 Winners (Tiered)</option>');
            winnerCountSelect.val('3').prop('disabled', true);
            prizePreview.html('<i class="fas fa-trophy text-warning"></i> Tiered prizes: 1st, 2nd, 3rd place');
        }
    } else {
        // Cash campaign - allow flexible winner count
        winnerCountSelect.prop('disabled', false);
        // Restore options if they were removed
        if (winnerCountSelect.find('option').length < 5) {
            winnerCountSelect.html(`
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
            `);
        }
        updatePrizePreview();
    }
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
        
        document.getElementById('draw_type').removeAttribute('required');
        document.getElementById('draw_date').removeAttribute('required');
    } else {
        singleFields.style.display = 'block';
        autoInfo.style.display = 'none';
        submitText.textContent = 'Schedule Draw';
        
        document.getElementById('draw_type').setAttribute('required', 'required');
        document.getElementById('draw_date').setAttribute('required', 'required');
    }
}

function updateScheduleType(campaign) {
    if (campaign.daily_draw_enabled == 0) {
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
        preview.html('<i class="fas fa-trophy text-warning"></i> Prize split: 100% (Winner takes all)');
    } else if (winnerCount === 2) {
        preview.html('<i class="fas fa-trophy text-warning"></i> Prize split: 60% / 40%');
    } else if (winnerCount === 3) {
        preview.html('<i class="fas fa-trophy text-warning"></i> Prize split: 50% / 30% / 20%');
    } else {
        preview.html('<i class="fas fa-trophy text-warning"></i> Prize split: Equal distribution (' + (100/winnerCount).toFixed(1) + '% each)');
    }
}

// Validate form before submission
function validateScheduleForm() {
    const campaignType = $('input[name="campaign_type"]:checked').val();
    const programmeId = $('#programme_id').val();
    const campaignId = $('#campaign_id').val();
    const scheduleType = $('input[name="schedule_type"]:checked').val();
    
    if (!campaignId) {
        alert('Please select a campaign');
        return false;
    }
    
    if (campaignType === 'programme' && !programmeId) {
        alert('Please select a programme for programme-specific campaigns');
        return false;
    }
    
    if (campaignType === 'station') {
        $('#programme_id').val('');
    }
    
    // For item campaigns, check minimum tickets if set
    if (selectedCampaign && selectedCampaign.campaign_type === 'item' && selectedCampaign.min_tickets_for_draw) {
        if (!confirm('This item campaign requires ' + selectedCampaign.min_tickets_for_draw + ' tickets before draw. Make sure this threshold is met before the draw date. Continue?')) {
            return false;
        }
    }
    
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
