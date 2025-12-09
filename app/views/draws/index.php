<?php require_once '../app/views/layouts/header.php'; ?>
<?php require_once '../app/views/layouts/sidebar.php'; ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">All Draws</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url('home') ?>">Home</a></li>
                        <li class="breadcrumb-item active">Draws</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Filter Draws</h3>
                </div>
                <div class="card-body">
                    <form method="GET" action="<?= url('draw') ?>" id="filterForm">
                        <div class="row">
                            <div class="col-md-4">
                                <label>Station</label>
                                <select name="station" id="stationFilter" class="form-control" onchange="loadProgrammesFilter()">
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
                            <div class="col-md-4">
                                <label>Programme</label>
                                <select name="programme" id="programmeFilter" class="form-control" disabled onchange="loadCampaignsFilter()">
                                    <option value="">Select platform first...</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label>Campaign</label>
                                <select name="campaign" id="campaignFilter" class="form-control" disabled onchange="this.form.submit()">
                                    <option value="">Select programme first...</option>
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
                    <h3 class="card-title">Draw History</h3>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Station</th>
                                <th>Programme</th>
                                <th>Campaign</th>
                                <th>Type</th>
                                <th>Date</th>
                                <th>Winners</th>
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
                                        <td><?= htmlspecialchars($draw->campaign_name ?? 'N/A') ?></td>
                                        <td>
                                            <span class="badge badge-<?= $draw->draw_type == 'daily' ? 'info' : 'warning' ?>">
                                                <?= strtoupper($draw->draw_type) ?>
                                            </span>
                                        </td>
                                        <td><?= formatDate($draw->draw_date, 'M d, Y') ?></td>
                                        <td><?= $draw->winner_count ?? 0 ?></td>
                                        <td>
                                            <?php
                                            $statusColors = [
                                                'pending' => 'warning',
                                                'completed' => 'success',
                                                'published' => 'info'
                                            ];
                                            $color = $statusColors[$draw->status] ?? 'secondary';
                                            ?>
                                            <span class="badge badge-<?= $color ?>"><?= strtoupper($draw->status) ?></span>
                                        </td>
                                        <td>
                                            <a href="<?= url('draw/show/' . $draw->id) ?>" class="btn btn-info btn-sm">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="9" class="text-center">No draws found</td>
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

function resetFilters() {
    window.location.href = '<?= url('draw') ?>';
}
</script>

<?php require_once '../app/views/layouts/footer.php'; ?>
