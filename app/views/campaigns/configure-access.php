<?php require_once '../app/views/layouts/header.php'; ?>
<?php require_once '../app/views/layouts/sidebar.php'; ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Configure Programme Access</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url('home') ?>">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= url('campaign') ?>">Campaigns</a></li>
                        <li class="breadcrumb-item active">Configure Access</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Campaign: <?= htmlspecialchars($campaign->name) ?></h3>
                </div>
                <form action="<?= url('campaign/configureAccess/' . $campaign->id) ?>" method="POST">
                    <?= csrf_field() ?>
                    <div class="card-body">
                        <p class="text-muted">Select which programmes can participate in this campaign:</p>
                        
                        <?php
                        $currentStation = null;
                        foreach ($all_programmes as $programme):
                            if ($currentStation !== $programme->station_name):
                                if ($currentStation !== null) echo '</div></div>';
                                $currentStation = $programme->station_name;
                        ?>
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-broadcast-tower"></i> <?= htmlspecialchars($programme->station_name) ?>
                                </h5>
                            </div>
                            <div class="card-body">
                        <?php endif; ?>
                                
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" 
                                           id="prog_<?= $programme->id ?>" 
                                           name="programme_ids[]" 
                                           value="<?= $programme->id ?>"
                                           <?= in_array($programme->id, $assigned_ids) ? 'checked' : '' ?>>
                                    <label class="custom-control-label" for="prog_<?= $programme->id ?>">
                                        <?= htmlspecialchars($programme->name) ?>
                                        <small class="text-muted">(<?= htmlspecialchars($programme->code) ?>)</small>
                                    </label>
                                </div>
                                
                        <?php endforeach; ?>
                        <?php if ($currentStation !== null): ?>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if (empty($all_programmes)): ?>
                            <p class="text-center text-muted">No programmes available. Please create programmes first.</p>
                        <?php endif; ?>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Programme Access
                        </button>
                        <a href="<?= url('campaign/show/' . $campaign->id) ?>" class="btn btn-default">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>

<?php require_once '../app/views/layouts/footer.php'; ?>
