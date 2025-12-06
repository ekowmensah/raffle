<?php require_once '../app/views/layouts/header.php'; ?>
<?php require_once '../app/views/layouts/sidebar.php'; ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Edit Campaign</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url('home') ?>">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= url('campaign') ?>">Campaigns</a></li>
                        <li class="breadcrumb-item active">Edit</li>
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

            <form action="<?= url('campaign/edit/' . $campaign->id) ?>" method="POST">
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
                                           value="<?= htmlspecialchars($campaign->name) ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Campaign Code</label>
                                    <input type="text" class="form-control" value="<?= htmlspecialchars($campaign->code) ?>" disabled>
                                    <div class="alert alert-info">
                            <strong>Total: <span id="revenue-total">100</span>%</strong>
                            <span id="revenue-warning" class="text-danger ml-2" style="display:none;">
                                <i class="fas fa-exclamation-triangle"></i> Must equal 100%!
                            </span>
                        </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea class="form-control is-invalid" id="description" name="description" rows="3"><?= htmlspecialchars($campaign->description ?? '') ?></textarea>
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
                                    <input type="number" class="form-control is-invalid" id="ticket_price" name="ticket_price" 
                                           value="<?= $campaign->ticket_price ?>" step="0.01" min="0" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="start_date">Start Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control is-invalid" id="start_date" name="start_date" 
                                           value="<?= $campaign->start_date ?>" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="end_date">End Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control is-invalid" id="end_date" name="end_date" 
                                           value="<?= $campaign->end_date ?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="status">Status</label>
                            <select class="form-control is-invalid" id="status" name="status">
                                <option value="draft" <?= $campaign->status == 'draft' ? 'selected' : '' ?>>Draft</option>
                                <option value="active" <?= $campaign->status == 'active' ? 'selected' : '' ?>>Active</option>
                                <option value="closed" <?= $campaign->status == 'closed' ? 'selected' : '' ?>>Closed</option>
                                <option value="draw_done" <?= $campaign->status == 'draw_done' ? 'selected' : '' ?>>Draw Done</option>
                            </select>
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
                                           value="<?= $campaign->platform_percent ?>" step="0.01" min="0" max="100" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="station_percent">Station %</label>
                                    <input type="number" class="form-control revenue-percent" id="station_percent" name="station_percent" 
                                           value="<?= $campaign->station_percent ?>" step="0.01" min="0" max="100" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="programme_percent">Programme %</label>
                                    <input type="number" class="form-control revenue-percent" id="programme_percent" name="programme_percent" 
                                           value="<?= $campaign->programme_percent ?>" step="0.01" min="0" max="100" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="prize_pool_percent">Prize Pool %</label>
                                    <input type="number" class="form-control revenue-percent" id="prize_pool_percent" name="prize_pool_percent" 
                                           value="<?= $campaign->prize_pool_percent ?>" step="0.01" min="0" max="100" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Draw Configuration -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Draw Configuration</h3>
                    </div>
                    <div class="card-body">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="daily_draw_enabled" name="daily_draw_enabled" 
                                   <?= $campaign->daily_draw_enabled ? 'checked' : '' ?>>
                            <label class="custom-control-label" for="daily_draw_enabled">Enable Daily Draws</label>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Campaign
                        </button>
                        <a href="<?= url('campaign/show/' . $campaign->id) ?>" class="btn btn-default">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </section>
</div>

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

<?php require_once '../app/views/layouts/footer.php'; ?>
