<?php require_once '../app/views/layouts/header.php'; ?>
<?php require_once '../app/views/layouts/sidebar.php'; ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Pending Draws</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url('home') ?>">Home</a></li>
                        <li class="breadcrumb-item active">Pending Draws</li>
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

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Filter Draws</h3>
                </div>
                <div class="card-body">
                    <form method="GET" action="<?= url('draw/pending') ?>" id="filterForm">
                        <div class="row">
                            <div class="col-md-3">
                                <label>Station</label>
                                <select name="station" id="stationFilter" class="form-control" onchange="onStationChangeFilter()">
                                    <option value="">All Stations</option>
                                    <?php
                                    $stationModel = new \App\Models\Station();
                                    $stations = $stationModel->getActive();
                                    foreach ($stations as $station):
                                    ?>
                                        <option value="<?= $station->id ?>"><?= htmlspecialchars($station->name) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label>Campaign Type</label>
                                <select name="campaign_type" id="campaignTypeFilter" class="form-control" onchange="toggleCampaignTypeFilter()">
                                    <option value="">All Types</option>
                                    <option value="station">Station-Wide</option>
                                    <option value="programme">Programme-Specific</option>
                                </select>
                            </div>
                            <div class="col-md-3" id="programmeFilterField">
                                <label>Programme</label>
                                <select name="programme" id="programmeFilter" class="form-control" disabled onchange="loadCampaignsFilter()">
                                    <option value="">Select station first...</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label>Campaign</label>
                                <select name="campaign" id="campaignFilter" class="form-control" disabled onchange="this.form.submit()">
                                    <option value="">Select station first...</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <button type="button" class="btn btn-secondary" onclick="resetFilters()">
                                    <i class="fas fa-redo"></i> Reset Filters
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Draws Ready to Conduct</h3>
                    <div class="card-tools">
                        <a href="<?= url('draw/schedule') ?>" class="btn btn-primary btn-sm">
                            <i class="fas fa-calendar-plus"></i> Schedule New Draw
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Station</th>
                                <th>Programme</th>
                                <th>Campaign</th>
                                <th>Draw Type</th>
                                <th>Draw Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($draws)): ?>
                                <?php foreach ($draws as $draw): ?>
                                    <tr>
                                        <td><?= $draw->id ?></td>
                                        <td><?= htmlspecialchars($draw->station_name ?? 'N/A') ?></td>
                                        <td><?= htmlspecialchars($draw->programme_name ?? 'N/A') ?></td>
                                        <td><?= htmlspecialchars($draw->campaign_name) ?></td>
                                        <td>
                                            <span class="badge badge-<?= $draw->draw_type == 'daily' ? 'info' : 'warning' ?>">
                                                <?= strtoupper($draw->draw_type) ?>
                                            </span>
                                        </td>
                                        <td><?= formatDate($draw->draw_date, 'M d, Y') ?></td>
                                        <td><span class="badge badge-warning">Pending</span></td>
                                        <td>
                                            <a href="<?= url('draw/show/' . $draw->id) ?>" class="btn btn-info btn-sm" title="View Details">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                            <?php if (hasRole(['super_admin', 'station_admin'])): ?>
                                                <a href="<?= url('draw/edit/' . $draw->id) ?>" class="btn btn-warning btn-sm" title="Edit Draw">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                            <?php endif; ?>
                                            <a href="<?= url('draw/live/' . $draw->id) ?>" class="btn btn-primary btn-sm" title="Live Draw with Animation">
                                                <i class="fas fa-tv"></i> Live Draw
                                            </a>
                                            <a href="<?= url('draw/conduct/' . $draw->id) ?>" class="btn btn-success btn-sm" title="Standard Draw">
                                                <i class="fas fa-play"></i> Conduct
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center">No pending draws</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

<script src="<?= vendor('jquery/jquery.min.js') ?>"></script>
<script>
// Handle station selection
function onStationChangeFilter() {
    const campaignType = $('#campaignTypeFilter').val();
    const stationId = $('#stationFilter').val();
    
    if (!stationId) {
        $('#programmeFilter').html('<option value="">All Programmes</option>').prop('disabled', false);
        $('#campaignFilter').html('<option value="">All Campaigns</option>').prop('disabled', false);
        return;
    }
    
    if (campaignType === 'station') {
        loadStationWideCampaignsFilter();
    } else if (campaignType === 'programme') {
        loadProgrammesFilter();
    } else {
        // All types - load both
        loadAllCampaignsFilter();
    }
}

// Toggle campaign type filter
function toggleCampaignTypeFilter() {
    const campaignType = $('#campaignTypeFilter').val();
    const programmeField = $('#programmeFilterField');
    const stationId = $('#stationFilter').val();
    
    if (campaignType === 'programme') {
        programmeField.show();
        if (stationId) {
            loadProgrammesFilter();
        }
    } else if (campaignType === 'station') {
        programmeField.hide();
        $('#programmeFilter').val('');
        if (stationId) {
            loadStationWideCampaignsFilter();
        }
    } else {
        // All types
        programmeField.show();
        if (stationId) {
            loadAllCampaignsFilter();
        }
    }
}

// Load all campaigns (station-wide + programme-specific)
function loadAllCampaignsFilter() {
    const stationId = $('#stationFilter').val();
    const campaignSelect = $('#campaignFilter');
    const programmeSelect = $('#programmeFilter');
    
    programmeSelect.html('<option value="">All Programmes</option>').prop('disabled', false);
    campaignSelect.html('<option value="">Loading...</option>').prop('disabled', true);
    
    if (!stationId) {
        campaignSelect.html('<option value="">All Campaigns</option>').prop('disabled', false);
        return;
    }
    
    // Load all campaigns for the station
    $.get('<?= url('public/getCampaignsByStation') ?>/' + stationId, function(response) {
        if (response.success && response.campaigns.length > 0) {
            campaignSelect.html('<option value="">All Campaigns</option>');
            response.campaigns.forEach(function(campaign) {
                campaignSelect.append($('<option></option>').val(campaign.id).text(campaign.name));
            });
            campaignSelect.prop('disabled', false);
        } else {
            campaignSelect.html('<option value="">No campaigns</option>');
        }
    });
}

// Load station-wide campaigns only
function loadStationWideCampaignsFilter() {
    const stationId = $('#stationFilter').val();
    const campaignSelect = $('#campaignFilter');
    
    campaignSelect.html('<option value="">Loading...</option>').prop('disabled', true);
    
    if (!stationId) {
        campaignSelect.html('<option value="">All Campaigns</option>').prop('disabled', false);
        return;
    }
    
    $.get('<?= url('public/getCampaignsByStation') ?>/' + stationId, function(response) {
        if (response.success && response.campaigns.length > 0) {
            campaignSelect.html('<option value="">All Station-Wide</option>');
            response.campaigns.forEach(function(campaign) {
                campaignSelect.append($('<option></option>').val(campaign.id).text(campaign.name));
            });
            campaignSelect.prop('disabled', false);
        } else {
            campaignSelect.html('<option value="">No station-wide campaigns</option>');
        }
    });
}

function loadProgrammesFilter() {
    const stationId = $('#stationFilter').val();
    const programmeSelect = $('#programmeFilter');
    const campaignSelect = $('#campaignFilter');
    
    programmeSelect.html('<option value="">Loading...</option>').prop('disabled', true);
    campaignSelect.html('<option value="">Select programme first...</option>').prop('disabled', true);
    
    if (!stationId) {
        programmeSelect.html('<option value="">All Programmes</option>').prop('disabled', false);
        return;
    }
    
    $.get('<?= url('public/getProgrammesByStation') ?>/' + stationId, function(response) {
        if (response.success && response.programmes.length > 0) {
            programmeSelect.html('<option value="">All Programmes</option>');
            response.programmes.forEach(function(programme) {
                programmeSelect.append($('<option></option>').val(programme.id).text(programme.name));
            });
            programmeSelect.prop('disabled', false);
        } else {
            programmeSelect.html('<option value="">No programmes</option>');
        }
    });
}

function loadCampaignsFilter() {
    const programmeId = $('#programmeFilter').val();
    const campaignSelect = $('#campaignFilter');
    
    campaignSelect.html('<option value="">Loading...</option>').prop('disabled', true);
    
    if (!programmeId) {
        campaignSelect.html('<option value="">All Campaigns</option>').prop('disabled', false);
        return;
    }
    
    $.get('<?= url('public/getCampaignsByProgramme') ?>/' + programmeId, function(response) {
        if (response.success && response.campaigns.length > 0) {
            campaignSelect.html('<option value="">All Programme Campaigns</option>');
            response.campaigns.forEach(function(campaign) {
                campaignSelect.append($('<option></option>').val(campaign.id).text(campaign.name));
            });
            campaignSelect.prop('disabled', false);
        } else {
            campaignSelect.html('<option value="">No campaigns</option>');
        }
    });
}

function resetFilters() {
    window.location.href = '<?= url('draw/pending') ?>';
}
</script>

<?php require_once '../app/views/layouts/footer.php'; ?>
