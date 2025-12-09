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
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="campaign_type">Campaign Type <span class="text-danger">*</span></label>
                                    <select class="form-control" id="campaign_type" name="campaign_type" required>
                                        <option value="cash" <?= old('campaign_type', 'cash') == 'cash' ? 'selected' : '' ?>>Cash Prize Campaign</option>
                                        <option value="item" <?= old('campaign_type') == 'item' ? 'selected' : '' ?>>Item Prize Campaign (Phone, Car, TV, etc.)</option>
                                    </select>
                                    <small class="form-text text-muted">Choose whether winners receive cash or physical items</small>
                                </div>
                            </div>
                        </div>

                        <!-- Item Campaign Fields (shown only when type is 'item') -->
                        <div id="item-fields" style="display: none;">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> <strong>Item Campaign:</strong> Players compete to win physical prizes instead of cash.
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="item_name">Item Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="item_name" name="item_name" 
                                               value="<?= old('item_name') ?>" placeholder="e.g., iPhone 15 Pro Max 256GB">
                                        <small class="form-text text-muted">Full name of the prize item</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="item_value">Item Value (GHS) <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" id="item_value" name="item_value" 
                                               value="<?= old('item_value') ?>" step="0.01" min="0" placeholder="5000.00">
                                        <small class="form-text text-muted">Retail value of the item</small>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="item_description">Item Description</label>
                                <textarea class="form-control" id="item_description" name="item_description" rows="3" 
                                          placeholder="Detailed description of the item, including features, condition, warranty, etc."><?= old('item_description') ?></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="winner_selection_type">Winner Selection <span class="text-danger">*</span></label>
                                        <select class="form-control" id="winner_selection_type" name="winner_selection_type">
                                            <option value="single" <?= old('winner_selection_type', 'single') == 'single' ? 'selected' : '' ?>>Single Winner</option>
                                            <option value="multiple" <?= old('winner_selection_type') == 'multiple' ? 'selected' : '' ?>>Multiple Winners (Same Item)</option>
                                            <option value="tiered" <?= old('winner_selection_type') == 'tiered' ? 'selected' : '' ?>>Tiered Prizes (Different Items)</option>
                                        </select>
                                        <small class="form-text text-muted">How winners will be selected</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group" id="item-quantity-group">
                                        <label for="item_quantity">Number of Items</label>
                                        <input type="number" class="form-control" id="item_quantity" name="item_quantity" 
                                               value="<?= old('item_quantity', 1) ?>" min="1" max="100">
                                        <small class="form-text text-muted">For multiple winners</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="min_tickets_for_draw">Minimum Tickets for Draw</label>
                                        <input type="number" class="form-control" id="min_tickets_for_draw" name="min_tickets_for_draw" 
                                               value="<?= old('min_tickets_for_draw') ?>" min="0" placeholder="Optional">
                                        <small class="form-text text-muted">Draw won't happen until this many tickets sold</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Tiered Prize Configuration (shown only when tiered is selected) -->
                            <div id="tiered-prizes-section" style="display: none;">
                                <div class="alert alert-info">
                                    <h6><i class="fas fa-trophy"></i> Configure Prize Tiers</h6>
                                    <p class="mb-0">Define different prizes for each tier (1st, 2nd, 3rd place)</p>
                                </div>

                                <!-- Tier 1 -->
                                <div class="card card-warning mb-3">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0"><i class="fas fa-trophy text-warning"></i> 1st Prize</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Prize Item Name</label>
                                                    <input type="text" class="form-control" name="tier_1_item_name" 
                                                           value="<?= old('tier_1_item_name') ?>" placeholder="e.g., Toyota Corolla">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Prize Value (GHS)</label>
                                                    <input type="number" step="0.01" class="form-control" name="tier_1_item_value" 
                                                           value="<?= old('tier_1_item_value') ?>" placeholder="150000.00">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label>Prize Description</label>
                                            <textarea class="form-control" name="tier_1_item_description" rows="2" 
                                                      placeholder="Describe the 1st prize..."><?= old('tier_1_item_description') ?></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label><i class="fas fa-image"></i> Prize Image</label>
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input tier-image-upload" id="tier_1_image_upload" 
                                                       name="tier_1_image_upload" accept="image/*">
                                                <label class="custom-file-label" for="tier_1_image_upload">Choose image...</label>
                                            </div>
                                            <small class="form-text text-muted">JPG, PNG, GIF, WEBP (Max 5MB)</small>
                                            <input type="hidden" name="tier_1_item_image" value="<?= old('tier_1_item_image') ?>">
                                        </div>
                                    </div>
                                </div>

                                <!-- Tier 2 -->
                                <div class="card card-secondary mb-3">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0"><i class="fas fa-medal text-secondary"></i> 2nd Prize</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Prize Item Name</label>
                                                    <input type="text" class="form-control" name="tier_2_item_name" 
                                                           value="<?= old('tier_2_item_name') ?>" placeholder="e.g., Honda Motorcycle">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Prize Value (GHS)</label>
                                                    <input type="number" step="0.01" class="form-control" name="tier_2_item_value" 
                                                           value="<?= old('tier_2_item_value') ?>" placeholder="15000.00">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label>Prize Description</label>
                                            <textarea class="form-control" name="tier_2_item_description" rows="2" 
                                                      placeholder="Describe the 2nd prize..."><?= old('tier_2_item_description') ?></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label><i class="fas fa-image"></i> Prize Image</label>
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input tier-image-upload" id="tier_2_image_upload" 
                                                       name="tier_2_image_upload" accept="image/*">
                                                <label class="custom-file-label" for="tier_2_image_upload">Choose image...</label>
                                            </div>
                                            <small class="form-text text-muted">JPG, PNG, GIF, WEBP (Max 5MB)</small>
                                            <input type="hidden" name="tier_2_item_image" value="<?= old('tier_2_item_image') ?>">
                                        </div>
                                    </div>
                                </div>

                                <!-- Tier 3 -->
                                <div class="card card-info mb-3">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0"><i class="fas fa-award text-info"></i> 3rd Prize</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Prize Item Name</label>
                                                    <input type="text" class="form-control" name="tier_3_item_name" 
                                                           value="<?= old('tier_3_item_name') ?>" placeholder="e.g., Mountain Bicycle">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Prize Value (GHS)</label>
                                                    <input type="number" step="0.01" class="form-control" name="tier_3_item_value" 
                                                           value="<?= old('tier_3_item_value') ?>" placeholder="1500.00">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label>Prize Description</label>
                                            <textarea class="form-control" name="tier_3_item_description" rows="2" 
                                                      placeholder="Describe the 3rd prize..."><?= old('tier_3_item_description') ?></textarea>
                                        </div>
                                        <div class="form-group mb-0">
                                            <label><i class="fas fa-image"></i> Prize Image</label>
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input tier-image-upload" id="tier_3_image_upload" 
                                                       name="tier_3_image_upload" accept="image/*">
                                                <label class="custom-file-label" for="tier_3_image_upload">Choose image...</label>
                                            </div>
                                            <small class="form-text text-muted">JPG, PNG, GIF, WEBP (Max 5MB)</small>
                                            <input type="hidden" name="tier_3_item_image" value="<?= old('tier_3_item_image') ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Single/Multiple Item Fields (shown when NOT tiered) -->
                            <div id="single-item-section">
                                <div class="form-group">
                                    <label for="item_image_upload"><i class="fas fa-image"></i> Item Image</label>
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="item_image_upload" name="item_image_upload" 
                                               accept="image/jpeg,image/jpg,image/png,image/gif,image/webp">
                                        <label class="custom-file-label" for="item_image_upload">Choose image file...</label>
                                    </div>
                                    <small class="form-text text-muted">
                                        <i class="fas fa-info-circle"></i> Accepted formats: JPG, PNG, GIF, WEBP (Max 5MB)
                                    </small>
                                    <!-- Hidden field to store the path after upload -->
                                    <input type="hidden" id="item_image" name="item_image" value="<?= old('item_image') ?>">
                                </div>
                                
                                <!-- Image Preview -->
                                <div class="form-group" id="image-preview-container" style="display: none;">
                                    <label>Image Preview</label>
                                    <div class="text-center">
                                        <img id="image-preview" src="" alt="Preview" class="img-fluid img-thumbnail" style="max-height: 200px;">
                                    </div>
                                </div>
                            </div>

                            <!-- Inventory Tracking -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="items_total"><i class="fas fa-boxes"></i> Total Items Available</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-warehouse"></i></span>
                                            </div>
                                            <input type="number" class="form-control" id="items_total" name="items_total" 
                                                   value="<?= old('items_total', 1) ?>" min="1">
                                        </div>
                                        <small class="form-text text-muted">Total number of items you have for this campaign</small>
                                        <div id="inventory-warning" class="alert alert-warning mt-2" style="display: none;">
                                            <i class="fas fa-exclamation-triangle"></i> <strong>Warning:</strong> <span id="inventory-warning-text"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>&nbsp;</label>
                                        <div class="custom-control custom-checkbox" style="padding-top: 10px;">
                                            <input type="checkbox" class="custom-control-input" id="track_inventory" name="track_inventory" 
                                                   value="1" <?= old('track_inventory', 1) ? 'checked' : '' ?>>
                                            <label class="custom-control-label" for="track_inventory">
                                                <i class="fas fa-check-circle text-success"></i> <strong>Enable Inventory Tracking</strong>
                                            </label>
                                            <br><small class="text-muted">Prevents draws when items run out</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="alert alert-info">
                                <h6><i class="fas fa-info-circle"></i> Inventory Tracking</h6>
                                <ul class="mb-0">
                                    <li><strong>Single Winner:</strong> Set items_total = number of draws you plan (e.g., 30 for 30 daily draws)</li>
                                    <li><strong>Multiple Winners:</strong> Set items_total = item_quantity × number of draws</li>
                                    <li><strong>Tiered:</strong> Set items_total = 3 × number of draws (3 winners per draw)</li>
                                </ul>
                            </div>

                            <div class="alert alert-warning">
                                <i class="fas fa-calculator"></i> <strong>Break-Even Calculator:</strong>
                                <p class="mb-0">Item Value ÷ Ticket Price = Minimum Tickets Needed</p>
                                <p class="mb-0" id="breakeven-calc"><em>Enter item value and ticket price to calculate</em></p>
                            </div>
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
                                            <option value="">Select Platform</option>
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
// Campaign type toggle
function toggleCampaignType() {
    const campaignType = $('#campaign_type').val();
    if (campaignType === 'item') {
        $('#item-fields').slideDown();
        // Make item fields required
        $('#item_name, #item_value').attr('required', true);
        // For item campaigns, prize pool % should be 0
        $('#prize_pool_percent').val(0).prop('readonly', true);
    } else {
        $('#item-fields').slideUp();
        // Make item fields not required
        $('#item_name, #item_value').attr('required', false);
        // Restore prize pool % for cash campaigns
        $('#prize_pool_percent').val(40).prop('readonly', false);
    }
    validateRevenueSharing();
}

// Winner selection type toggle
function toggleWinnerSelectionType() {
    const selectionType = $('#winner_selection_type').val();
    
    if (selectionType === 'tiered') {
        // Show tiered prizes section, hide single item section
        $('#tiered-prizes-section').slideDown();
        $('#single-item-section').slideUp();
        $('#item-quantity-group').slideUp();
        
        // Make tier fields required
        $('input[name^="tier_"][name$="_item_name"]').attr('required', true);
        $('input[name^="tier_"][name$="_item_value"]').attr('required', true);
        
        // Make single item fields not required
        $('#item_name, #item_value, #item_description').attr('required', false);
    } else {
        // Show single item section, hide tiered prizes section
        $('#tiered-prizes-section').slideUp();
        $('#single-item-section').slideDown();
        
        // Make single item fields required
        $('#item_name, #item_value').attr('required', true);
        
        // Make tier fields not required
        $('input[name^="tier_"]').attr('required', false);
        
        // Show/hide quantity field based on selection type
        if (selectionType === 'multiple') {
            $('#item-quantity-group').slideDown();
            $('#item_quantity').attr('required', true);
        } else {
            $('#item-quantity-group').slideUp();
            $('#item_quantity').attr('required', false);
        }
    }
}

// Initialize on page load
$(document).ready(function() {
    // Attach event listener to winner selection dropdown
    $('#winner_selection_type').on('change', toggleWinnerSelectionType);
    
    // Initialize based on current selection
    toggleWinnerSelectionType();
});

// Break-even calculator
function calculateBreakEven() {
    const itemValue = parseFloat($('#item_value').val()) || 0;
    const ticketPrice = parseFloat($('#ticket_price').val()) || 0;
    
    if (itemValue > 0 && ticketPrice > 0) {
        const breakEven = Math.ceil(itemValue / ticketPrice);
        const revenue = breakEven * ticketPrice;
        const profit = revenue - itemValue;
        
        $('#breakeven-calc').html(`
            <strong>Break-Even: ${breakEven} tickets</strong><br>
            Revenue: GHS ${revenue.toFixed(2)} | Item Cost: GHS ${itemValue.toFixed(2)} | Profit: GHS ${profit.toFixed(2)}
        `);
        
        // Suggest minimum tickets
        if (!$('#min_tickets_for_draw').val()) {
            $('#min_tickets_for_draw').val(breakEven);
        }
    } else {
        $('#breakeven-calc').html('<em>Enter item value and ticket price to calculate</em>');
    }
}

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

// Image preview function
function previewImage(input, previewId) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            if (previewId) {
                $(previewId).attr('src', e.target.result).show();
                $(previewId).closest('#image-preview-container').show();
            }
        };
        reader.readAsDataURL(input.files[0]);
        
        // Update custom file label
        const fileName = input.files[0].name;
        $(input).next('.custom-file-label').text(fileName);
    }
}

// Attach event listeners
$(document).ready(function() {
    $('.revenue-percent').on('input change', validateRevenueSharing);
    $('.pool-percent').on('input change', validatePoolSplit);
    
    // Campaign type toggle
    $('#campaign_type').on('change', toggleCampaignType);
    toggleCampaignType(); // Initialize on page load
    
    // Break-even calculator
    $('#item_value, #ticket_price').on('input change', calculateBreakEven);
    
    // Image upload handlers
    $('#item_image_upload').on('change', function() {
        previewImage(this, '#image-preview');
    });
    
    // Tier image upload handlers
    $('.tier-image-upload').on('change', function() {
        const fileName = this.files[0] ? this.files[0].name : 'Choose image...';
        $(this).next('.custom-file-label').text(fileName);
    });
    
    // Inventory validation for tiered campaigns
    function validateInventory() {
        const selectionType = $('#winner_selection_type').val();
        const itemsTotal = parseInt($('#items_total').val()) || 0;
        const itemQuantity = parseInt($('#item_quantity').val()) || 1;
        const warningDiv = $('#inventory-warning');
        const warningText = $('#inventory-warning-text');
        
        if (itemsTotal === 0) {
            warningDiv.hide();
            return;
        }
        
        let itemsPerDraw = 1;
        let warning = '';
        
        if (selectionType === 'single') {
            itemsPerDraw = 1;
        } else if (selectionType === 'multiple') {
            itemsPerDraw = itemQuantity;
        } else if (selectionType === 'tiered') {
            itemsPerDraw = 3; // Always 3 for tiered (1st, 2nd, 3rd)
        }
        
        const remainder = itemsTotal % itemsPerDraw;
        const possibleDraws = Math.floor(itemsTotal / itemsPerDraw);
        
        if (itemsTotal < itemsPerDraw) {
            // Not enough items for even one draw
            warning = `Not enough items! You need at least ${itemsPerDraw} items for one draw, but only have ${itemsTotal}.`;
            warningDiv.removeClass('alert-success').addClass('alert-danger');
            warningDiv.find('i').removeClass('fa-check-circle').addClass('fa-times-circle');
            warningDiv.show();
            warningText.text(warning);
        } else if (remainder !== 0) {
            // Can do some draws but will waste items
            warning = `Items total (${itemsTotal}) is not evenly divisible by ${itemsPerDraw}. ` +
                     `You can conduct ${possibleDraws} draw(s), but ${remainder} item(s) will be left over and wasted!`;
            warningDiv.removeClass('alert-success alert-danger').addClass('alert-warning');
            warningDiv.find('i').removeClass('fa-check-circle fa-times-circle').addClass('fa-exclamation-triangle');
            warningDiv.show();
            warningText.text(warning);
        } else {
            // Perfect division
            warning = `Perfect! You can conduct ${possibleDraws} draw(s) with ${itemsTotal} items (${itemsPerDraw} items per draw).`;
            warningDiv.removeClass('alert-warning alert-danger').addClass('alert-success');
            warningDiv.find('i').removeClass('fa-exclamation-triangle fa-times-circle').addClass('fa-check-circle');
            warningDiv.show();
            warningText.text(warning);
        }
    }
    
    // Attach inventory validation
    $('#items_total, #winner_selection_type, #item_quantity').on('input change', validateInventory);
    
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
