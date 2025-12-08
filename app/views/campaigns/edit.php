<?php require_once '../app/views/layouts/header.php'; ?>
<?php require_once '../app/views/layouts/sidebar.php'; ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        <i class="fas fa-edit"></i> Edit Campaign
                        <?php if ($campaign->campaign_type === 'item'): ?>
                            <span class="badge badge-success ml-2"><i class="fas fa-gift"></i> Item</span>
                        <?php else: ?>
                            <span class="badge badge-primary ml-2"><i class="fas fa-money-bill-wave"></i> Cash</span>
                        <?php endif; ?>
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= url('home') ?>">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= url('campaign') ?>">Campaigns</a></li>
                        <li class="breadcrumb-item"><a href="<?= url('campaign/show/' . $campaign->id) ?>">View</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            
            <?php if (flash('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <i class="icon fas fa-ban"></i> <strong>Error!</strong> <?= flash('error') ?>
                </div>
            <?php endif; ?>

            <?php if (flash('success')): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <i class="icon fas fa-check"></i> <strong>Success!</strong> <?= flash('success') ?>
                </div>
            <?php endif; ?>

            <?php if ($campaign->is_config_locked): ?>
                <div class="alert alert-warning">
                    <i class="fas fa-lock"></i> <strong>Configuration Locked:</strong> This campaign's configuration is locked. Some fields cannot be modified.
                </div>
            <?php endif; ?>

            <form action="<?= url('campaign/edit/' . $campaign->id) ?>" method="POST" id="campaign-form" enctype="multipart/form-data">
                <?= csrf_field() ?>
                
                <!-- Basic Information -->
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-info-circle"></i> Basic Information</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Campaign Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name" 
                                           value="<?= htmlspecialchars($campaign->name) ?>" required
                                           placeholder="Enter campaign name">
                                    <small class="form-text text-muted">A descriptive name for your campaign</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Campaign Code</label>
                                    <input type="text" class="form-control" value="<?= htmlspecialchars($campaign->code) ?>" disabled>
                                    <small class="form-text text-muted"><i class="fas fa-lock"></i> Campaign code cannot be changed</small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3" 
                                      placeholder="Enter campaign description..."><?= htmlspecialchars($campaign->description ?? '') ?></textarea>
                            <small class="form-text text-muted">Provide details about this campaign</small>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="campaign_type">Campaign Type <span class="text-danger">*</span></label>
                                    <select class="form-control" id="campaign_type" name="campaign_type" required>
                                        <option value="cash" <?= ($campaign->campaign_type ?? 'cash') == 'cash' ? 'selected' : '' ?>>
                                            <i class="fas fa-money-bill-wave"></i> Cash Prize Campaign
                                        </option>
                                        <option value="item" <?= ($campaign->campaign_type ?? 'cash') == 'item' ? 'selected' : '' ?>>
                                            <i class="fas fa-gift"></i> Item Prize Campaign (Phone, Car, TV, etc.)
                                        </option>
                                    </select>
                                    <small class="form-text text-muted">Choose whether winners receive cash or physical items</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status">Campaign Status <span class="text-danger">*</span></label>
                                    <select class="form-control" id="status" name="status" required>
                                        <option value="draft" <?= $campaign->status == 'draft' ? 'selected' : '' ?>>
                                            <i class="fas fa-file"></i> Draft
                                        </option>
                                        <option value="active" <?= $campaign->status == 'active' ? 'selected' : '' ?>>
                                            <i class="fas fa-play-circle"></i> Active
                                        </option>
                                        <option value="closed" <?= $campaign->status == 'closed' ? 'selected' : '' ?>>
                                            <i class="fas fa-stop-circle"></i> Closed
                                        </option>
                                        <option value="draw_done" <?= $campaign->status == 'draw_done' ? 'selected' : '' ?>>
                                            <i class="fas fa-check-circle"></i> Draw Done
                                        </option>
                                    </select>
                                    <small class="form-text text-muted">Current status of the campaign</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Item Campaign Fields -->
                <div id="item-fields" class="card card-success card-outline" style="display: <?= ($campaign->campaign_type ?? 'cash') == 'item' ? 'block' : 'none' ?>;">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-gift"></i> Item Prize Details</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> <strong>Item Campaign:</strong> Players compete to win physical prizes instead of cash. Configure the item details below.
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="item_name">Item Name <span class="text-danger item-required">*</span></label>
                                    <input type="text" class="form-control" id="item_name" name="item_name" 
                                           value="<?= htmlspecialchars($campaign->item_name ?? '') ?>"
                                           placeholder="e.g., iPhone 15 Pro Max 256GB"
                                           <?= ($campaign->campaign_type ?? 'cash') == 'item' ? 'required' : '' ?>>
                                    <small class="form-text text-muted">Full name of the prize item</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="item_value">Item Value (GHS) <span class="text-danger item-required">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-tag"></i></span>
                                        </div>
                                        <input type="number" class="form-control" id="item_value" name="item_value" 
                                               value="<?= $campaign->item_value ?? '' ?>" step="0.01" min="0"
                                               placeholder="5000.00"
                                               <?= ($campaign->campaign_type ?? 'cash') == 'item' ? 'required' : '' ?>>
                                    </div>
                                    <small class="form-text text-muted">Retail/market value of the item</small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="item_description">Item Description</label>
                            <textarea class="form-control" id="item_description" name="item_description" rows="4" 
                                      placeholder="Detailed description of the item, including features, condition, warranty, etc."><?= htmlspecialchars($campaign->item_description ?? '') ?></textarea>
                            <small class="form-text text-muted">Provide comprehensive details to entice players</small>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="winner_selection_type">Winner Selection Type <span class="text-danger item-required">*</span></label>
                                    <select class="form-control" id="winner_selection_type" name="winner_selection_type"
                                            <?= ($campaign->campaign_type ?? 'cash') == 'item' ? 'required' : '' ?>>
                                        <option value="single" <?= ($campaign->winner_selection_type ?? 'single') == 'single' ? 'selected' : '' ?>>
                                            <i class="fas fa-user"></i> Single Winner
                                        </option>
                                        <option value="multiple" <?= ($campaign->winner_selection_type ?? 'single') == 'multiple' ? 'selected' : '' ?>>
                                            <i class="fas fa-users"></i> Multiple Winners (Same Item)
                                        </option>
                                        <option value="tiered" <?= ($campaign->winner_selection_type ?? 'single') == 'tiered' ? 'selected' : '' ?>>
                                            <i class="fas fa-trophy"></i> Tiered Prizes (Different Items)
                                        </option>
                                    </select>
                                    <small class="form-text text-muted">How winners will be selected</small>
                                </div>
                            </div>
                            <div class="col-md-4" id="item-quantity-group">
                                <div class="form-group">
                                    <label for="item_quantity">Number of Items</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-hashtag"></i></span>
                                        </div>
                                        <input type="number" class="form-control" id="item_quantity" name="item_quantity" 
                                               value="<?= $campaign->item_quantity ?? 1 ?>" min="1" max="100">
                                    </div>
                                    <small class="form-text text-muted">For multiple winners only</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="min_tickets_for_draw">Minimum Tickets for Draw</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-ticket-alt"></i></span>
                                        </div>
                                        <input type="number" class="form-control" id="min_tickets_for_draw" name="min_tickets_for_draw" 
                                               value="<?= $campaign->min_tickets_for_draw ?? '' ?>" min="0" placeholder="Optional">
                                    </div>
                                    <small class="form-text text-muted">Draw won't happen until this threshold</small>
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
                                               value="<?= $campaign->items_total ?? 0 ?>" min="0">
                                    </div>
                                    <small class="form-text text-muted">Total number of items you have for this campaign</small>
                                    <div id="inventory-warning" class="alert alert-warning mt-2" style="display: none;">
                                        <i class="fas fa-exclamation-triangle"></i> <strong>Warning:</strong> <span id="inventory-warning-text"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><i class="fas fa-chart-line"></i> Inventory Status</label>
                                    <div class="alert alert-info mb-0">
                                        <strong>Items Awarded:</strong> <?= $campaign->items_awarded ?? 0 ?><br>
                                        <strong>Items Remaining:</strong> <?= ($campaign->items_total ?? 0) - ($campaign->items_awarded ?? 0) ?>
                                        <?php if (isset($campaign->items_awarded) && $campaign->items_awarded > 0): ?>
                                        <br><small class="text-muted">Cannot reduce total below awarded count</small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="track_inventory" name="track_inventory" 
                                           value="1" <?= (isset($campaign->track_inventory) && $campaign->track_inventory) ? 'checked' : '' ?>>
                                    <label class="custom-control-label" for="track_inventory">
                                        <i class="fas fa-check-circle text-success"></i> Enable Inventory Tracking
                                    </label>
                                    <br><small class="text-muted">When enabled, draws will be prevented if insufficient items remain</small>
                                </div>
                            </div>
                        </div>

                        <!-- Tiered Prize Configuration (shown only when tiered is selected) -->
                        <div id="tiered-prizes-section" style="display: none;">
                            <div class="alert alert-info">
                                <h6><i class="fas fa-trophy"></i> Configure Prize Tiers</h6>
                                <p class="mb-0">Define different prizes for each tier (1st, 2nd, 3rd place)</p>
                            </div>

                            <?php
                            // Load existing tiers if they exist
                            $tiers = [];
                            if ($campaign->winner_selection_type === 'tiered') {
                                require_once '../app/models/CampaignPrizeTier.php';
                                $tierModel = new \App\Models\CampaignPrizeTier();
                                $tiers = $tierModel->getByCampaign($campaign->id);
                                // Convert to associative array by rank
                                $tiersArray = [];
                                foreach ($tiers as $tier) {
                                    $tiersArray[$tier->tier_rank] = $tier;
                                }
                                $tiers = $tiersArray;
                            }
                            ?>

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
                                                       value="<?= $tiers[1]->item_name ?? '' ?>" placeholder="e.g., Toyota Corolla">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Prize Value (<?= $campaign->currency ?>)</label>
                                                <input type="number" step="0.01" class="form-control" name="tier_1_item_value" 
                                                       value="<?= $tiers[1]->item_value ?? '' ?>" placeholder="150000.00">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Prize Description</label>
                                        <textarea class="form-control" name="tier_1_item_description" rows="2" 
                                                  placeholder="Describe the 1st prize..."><?= $tiers[1]->item_description ?? '' ?></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label><i class="fas fa-image"></i> Prize Image</label>
                                        <?php if (!empty($tiers[1]->item_image ?? '')): ?>
                                        <div class="mb-2">
                                            <img src="<?= BASE_URL ?>/<?= $tiers[1]->item_image ?>" alt="Current" class="img-thumbnail" style="max-height: 100px;">
                                            <small class="d-block text-muted">Current image</small>
                                        </div>
                                        <?php endif; ?>
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input tier-image-upload" id="tier_1_image_upload" 
                                                   name="tier_1_image_upload" accept="image/*">
                                            <label class="custom-file-label" for="tier_1_image_upload">Choose new image...</label>
                                        </div>
                                        <small class="form-text text-muted">JPG, PNG, GIF, WEBP (Max 5MB)</small>
                                        <input type="hidden" name="tier_1_item_image" value="<?= $tiers[1]->item_image ?? '' ?>">
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
                                                       value="<?= $tiers[2]->item_name ?? '' ?>" placeholder="e.g., Honda Motorcycle">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Prize Value (<?= $campaign->currency ?>)</label>
                                                <input type="number" step="0.01" class="form-control" name="tier_2_item_value" 
                                                       value="<?= $tiers[2]->item_value ?? '' ?>" placeholder="15000.00">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Prize Description</label>
                                        <textarea class="form-control" name="tier_2_item_description" rows="2" 
                                                  placeholder="Describe the 2nd prize..."><?= $tiers[2]->item_description ?? '' ?></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label><i class="fas fa-image"></i> Prize Image</label>
                                        <?php if (!empty($tiers[2]->item_image ?? '')): ?>
                                        <div class="mb-2">
                                            <img src="<?= BASE_URL ?>/<?= $tiers[2]->item_image ?>" alt="Current" class="img-thumbnail" style="max-height: 100px;">
                                            <small class="d-block text-muted">Current image</small>
                                        </div>
                                        <?php endif; ?>
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input tier-image-upload" id="tier_2_image_upload" 
                                                   name="tier_2_image_upload" accept="image/*">
                                            <label class="custom-file-label" for="tier_2_image_upload">Choose new image...</label>
                                        </div>
                                        <small class="form-text text-muted">JPG, PNG, GIF, WEBP (Max 5MB)</small>
                                        <input type="hidden" name="tier_2_item_image" value="<?= $tiers[2]->item_image ?? '' ?>">
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
                                                       value="<?= $tiers[3]->item_name ?? '' ?>" placeholder="e.g., Mountain Bicycle">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Prize Value (<?= $campaign->currency ?>)</label>
                                                <input type="number" step="0.01" class="form-control" name="tier_3_item_value" 
                                                       value="<?= $tiers[3]->item_value ?? '' ?>" placeholder="1500.00">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Prize Description</label>
                                        <textarea class="form-control" name="tier_3_item_description" rows="2" 
                                                  placeholder="Describe the 3rd prize..."><?= $tiers[3]->item_description ?? '' ?></textarea>
                                    </div>
                                    <div class="form-group mb-0">
                                        <label><i class="fas fa-image"></i> Prize Image</label>
                                        <?php if (!empty($tiers[3]->item_image ?? '')): ?>
                                        <div class="mb-2">
                                            <img src="<?= BASE_URL ?>/<?= $tiers[3]->item_image ?>" alt="Current" class="img-thumbnail" style="max-height: 100px;">
                                            <small class="d-block text-muted">Current image</small>
                                        </div>
                                        <?php endif; ?>
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input tier-image-upload" id="tier_3_image_upload" 
                                                   name="tier_3_image_upload" accept="image/*">
                                            <label class="custom-file-label" for="tier_3_image_upload">Choose new image...</label>
                                        </div>
                                        <small class="form-text text-muted">JPG, PNG, GIF, WEBP (Max 5MB)</small>
                                        <input type="hidden" name="tier_3_item_image" value="<?= $tiers[3]->item_image ?? '' ?>">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Single/Multiple Item Image (shown when NOT tiered) -->
                        <div id="single-item-image-section">
                            <div class="form-group">
                                <label for="item_image_upload">Item Image</label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="item_image_upload" name="item_image_upload" 
                                           accept="image/jpeg,image/jpg,image/png,image/gif,image/webp">
                                    <label class="custom-file-label" for="item_image_upload">Choose image file...</label>
                                </div>
                                <small class="form-text text-muted">
                                    <i class="fas fa-info-circle"></i> Accepted formats: JPG, PNG, GIF, WEBP (Max 5MB)
                                </small>
                                <!-- Hidden field to store existing image path -->
                                <input type="hidden" id="item_image" name="item_image" value="<?= htmlspecialchars($campaign->item_image ?? '') ?>">
                            </div>

                            <!-- Image Preview -->
                            <div class="form-group" id="image-preview-container">
                            <label>
                                <?php if (!empty($campaign->item_image)): ?>
                                    Current Item Image
                                <?php else: ?>
                                    Image Preview
                                <?php endif; ?>
                            </label>
                            <div class="text-center p-3 border rounded" style="background-color: #f8f9fa;">
                                <img id="image-preview" 
                                     src="<?= !empty($campaign->item_image) ? BASE_URL . '/' . htmlspecialchars($campaign->item_image) : '' ?>" 
                                     alt="<?= htmlspecialchars($campaign->item_name ?? 'Item preview') ?>" 
                                     class="img-fluid img-thumbnail" 
                                     style="max-height: 300px; <?= empty($campaign->item_image) ? 'display: none;' : '' ?>">
                                <div id="no-image-placeholder" style="<?= !empty($campaign->item_image) ? 'display: none;' : '' ?>">
                                    <i class="fas fa-image fa-3x text-muted mb-2"></i>
                                    <p class="text-muted">No image selected</p>
                                </div>
                            </div>
                            <?php if (!empty($campaign->item_image)): ?>
                            <div class="mt-2">
                                <button type="button" class="btn btn-sm btn-danger" id="remove-image-btn">
                                    <i class="fas fa-trash"></i> Remove Current Image
                                </button>
                            </div>
                            <?php endif; ?>
                            </div>

                            <div class="alert alert-warning" id="breakeven-alert">
                                <h5><i class="fas fa-calculator"></i> Break-Even Analysis</h5>
                                <p class="mb-0" id="breakeven-calc">
                                    <em>Enter item value and ticket price to calculate break-even point</em>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pricing & Duration -->
                <div class="card card-warning card-outline">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-dollar-sign"></i> Pricing & Duration</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="ticket_price">Ticket Price <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">GHS</span>
                                        </div>
                                        <input type="number" class="form-control" id="ticket_price" name="ticket_price" 
                                               value="<?= $campaign->ticket_price ?>" step="0.01" min="0.01" required
                                               placeholder="1.00">
                                    </div>
                                    <small class="form-text text-muted">Price per ticket</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="start_date">Start Date <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                        </div>
                                        <input type="date" class="form-control" id="start_date" name="start_date" 
                                               value="<?= date('Y-m-d', strtotime($campaign->start_date)) ?>" required>
                                    </div>
                                    <small class="form-text text-muted">Campaign start date</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="end_date">End Date <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-calendar-check"></i></span>
                                        </div>
                                        <input type="date" class="form-control" id="end_date" name="end_date" 
                                               value="<?= date('Y-m-d', strtotime($campaign->end_date)) ?>" required>
                                    </div>
                                    <small class="form-text text-muted">Campaign end date</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Revenue Sharing -->
                <div class="card card-info card-outline">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-chart-pie"></i> Revenue Sharing Configuration</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="platform_percent">
                                        <i class="fas fa-building text-primary"></i> Platform %
                                    </label>
                                    <div class="input-group">
                                        <input type="number" class="form-control revenue-percent" id="platform_percent" 
                                               name="platform_percent" value="<?= $campaign->platform_percent ?>" 
                                               step="0.01" min="0" max="100" required>
                                        <div class="input-group-append">
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="station_percent">
                                        <i class="fas fa-broadcast-tower text-info"></i> Station %
                                    </label>
                                    <div class="input-group">
                                        <input type="number" class="form-control revenue-percent" id="station_percent" 
                                               name="station_percent" value="<?= $campaign->station_percent ?>" 
                                               step="0.01" min="0" max="100" required>
                                        <div class="input-group-append">
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="programme_percent">
                                        <i class="fas fa-microphone text-success"></i> Programme %
                                    </label>
                                    <div class="input-group">
                                        <input type="number" class="form-control revenue-percent" id="programme_percent" 
                                               name="programme_percent" value="<?= $campaign->programme_percent ?>" 
                                               step="0.01" min="0" max="100" required>
                                        <div class="input-group-append">
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="prize_pool_percent">
                                        <i class="fas fa-trophy text-warning"></i> Prize Pool %
                                    </label>
                                    <div class="input-group">
                                        <input type="number" class="form-control revenue-percent" id="prize_pool_percent" 
                                               name="prize_pool_percent" value="<?= $campaign->prize_pool_percent ?>" 
                                               step="0.01" min="0" max="100" required>
                                        <div class="input-group-append">
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="alert alert-info mb-0">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <strong><i class="fas fa-calculator"></i> Total Revenue Split: <span id="revenue-total">100</span>%</strong>
                                </div>
                                <div class="col-md-6">
                                    <span id="revenue-warning" class="text-danger" style="display:none;">
                                        <i class="fas fa-exclamation-triangle"></i> <strong>Must equal 100%!</strong>
                                    </span>
                                    <span id="revenue-success" class="text-success" style="display:none;">
                                        <i class="fas fa-check-circle"></i> <strong>Perfect split!</strong>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Draw Configuration -->
                <div class="card card-secondary card-outline">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-random"></i> Draw Configuration</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="custom-control custom-switch custom-switch-on-success">
                            <input type="checkbox" class="custom-control-input" id="daily_draw_enabled" 
                                   name="daily_draw_enabled" <?= $campaign->daily_draw_enabled ? 'checked' : '' ?>>
                            <label class="custom-control-label" for="daily_draw_enabled">
                                <strong>Enable Daily Draws</strong>
                            </label>
                        </div>
                        <small class="form-text text-muted">
                            <i class="fas fa-info-circle"></i> When enabled, draws will be conducted daily in addition to the final draw
                        </small>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="card">
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-save"></i> Update Campaign
                                </button>
                                <a href="<?= url('campaign/show/' . $campaign->id) ?>" class="btn btn-default btn-lg">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                            </div>
                            <div class="col-md-6 text-right">
                                <small class="text-muted">
                                    <i class="fas fa-clock"></i> Last updated: <?= formatDate($campaign->updated_at ?? $campaign->created_at, 'M d, Y h:i A') ?>
                                </small>
                            </div>
                        </div>
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
    const itemFields = $('#item-fields');
    const prizePoolField = $('#prize_pool_percent');
    
    if (campaignType === 'item') {
        itemFields.slideDown(300);
        // For item campaigns, prize pool should be 0
        prizePoolField.val(0).prop('readonly', true).addClass('bg-light');
        // Make item fields required
        $('.item-required').show();
        $('#item_name, #item_value, #winner_selection_type').attr('required', true);
    } else {
        itemFields.slideUp(300);
        // Restore prize pool for cash campaigns
        prizePoolField.prop('readonly', false).removeClass('bg-light');
        // Remove item field requirements
        $('.item-required').hide();
        $('#item_name, #item_value, #winner_selection_type').attr('required', false);
    }
    
    validateRevenueSharing();
    calculateBreakEven();
}

// Break-even calculator
function calculateBreakEven() {
    const campaignType = $('#campaign_type').val();
    const itemValue = parseFloat($('#item_value').val()) || 0;
    const ticketPrice = parseFloat($('#ticket_price').val()) || 0;
    const calcElement = $('#breakeven-calc');
    
    if (campaignType === 'item' && itemValue > 0 && ticketPrice > 0) {
        const breakEven = Math.ceil(itemValue / ticketPrice);
        const revenue = breakEven * ticketPrice;
        const profit = revenue - itemValue;
        const profitMargin = ((profit / revenue) * 100).toFixed(2);
        
        calcElement.html(`
            <div class="row">
                <div class="col-md-3">
                    <strong>Break-Even:</strong><br>
                    <span class="h4 text-primary">${breakEven.toLocaleString()} tickets</span>
                </div>
                <div class="col-md-3">
                    <strong>Revenue:</strong><br>
                    <span class="h4 text-success">GHS ${revenue.toLocaleString('en-US', {minimumFractionDigits: 2})}</span>
                </div>
                <div class="col-md-3">
                    <strong>Item Cost:</strong><br>
                    <span class="h4 text-danger">GHS ${itemValue.toLocaleString('en-US', {minimumFractionDigits: 2})}</span>
                </div>
                <div class="col-md-3">
                    <strong>Profit:</strong><br>
                    <span class="h4 text-info">GHS ${profit.toLocaleString('en-US', {minimumFractionDigits: 2})} (${profitMargin}%)</span>
                </div>
            </div>
        `);
        
        // Auto-suggest minimum tickets if not set
        const minTickets = $('#min_tickets_for_draw');
        if (!minTickets.val() || parseInt(minTickets.val()) === 0) {
            minTickets.val(breakEven);
        }
    } else if (campaignType === 'item') {
        calcElement.html('<em>Enter item value and ticket price to calculate break-even point</em>');
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
    
    const warningElement = $('#revenue-warning');
    const successElement = $('#revenue-success');
    
    if (Math.abs(total - 100) > 0.01) {
        warningElement.show();
        successElement.hide();
        $('.revenue-percent').addClass('is-invalid');
        return false;
    } else {
        warningElement.hide();
        successElement.show();
        $('.revenue-percent').removeClass('is-invalid').addClass('is-valid');
        return true;
    }
}

// Winner selection type change handler
function handleWinnerSelectionChange() {
    const selectionType = $('#winner_selection_type').val();
    const quantityGroup = $('#item-quantity-group');
    const tieredSection = $('#tiered-prizes-section');
    const singleImageSection = $('#single-item-image-section');
    
    if (selectionType === 'tiered') {
        // Show tiered prizes section, hide single item section
        tieredSection.slideDown();
        singleImageSection.slideUp();
        quantityGroup.slideUp();
        
        // Make tier fields required
        $('input[name^="tier_"][name$="_item_name"]').attr('required', true);
        $('input[name^="tier_"][name$="_item_value"]').attr('required', true);
        
        // Make single item fields not required
        $('#item_name, #item_value, #item_description').attr('required', false);
    } else {
        // Show single item section, hide tiered prizes section
        tieredSection.slideUp();
        singleImageSection.slideDown();
        
        // Make single item fields required ONLY if campaign type is item
        const campaignType = $('#campaign_type').val();
        if (campaignType === 'item') {
            $('#item_name, #item_value').attr('required', true);
        } else {
            $('#item_name, #item_value').attr('required', false);
        }
        
        // Make tier fields not required
        $('input[name^="tier_"]').attr('required', false);
        
        // Show/hide quantity field based on selection type
        if (selectionType === 'multiple') {
            quantityGroup.slideDown();
            if (campaignType === 'item') {
                $('#item_quantity').attr('required', true);
            }
        } else {
            quantityGroup.slideUp();
            $('#item_quantity').attr('required', false);
            if (selectionType === 'single') {
                $('#item_quantity').val(1);
            }
        }
    }
}

// Date validation
function validateDates() {
    const startDate = new Date($('#start_date').val());
    const endDate = new Date($('#end_date').val());
    
    if (endDate <= startDate) {
        alert('End date must be after start date');
        return false;
    }
    return true;
}

// Image upload preview
function previewImage(input) {
    const preview = $('#image-preview');
    const placeholder = $('#no-image-placeholder');
    const container = $('#image-preview-container label');
    
    if (input.files && input.files[0]) {
        const file = input.files[0];
        
        // Validate file size (5MB max)
        if (file.size > 5 * 1024 * 1024) {
            alert('File size must be less than 5MB');
            input.value = '';
            return;
        }
        
        // Validate file type
        const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        if (!validTypes.includes(file.type)) {
            alert('Please select a valid image file (JPG, PNG, GIF, or WEBP)');
            input.value = '';
            return;
        }
        
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.attr('src', e.target.result).show();
            placeholder.hide();
            container.text('Image Preview');
        };
        reader.readAsDataURL(file);
        
        // Update custom file label
        const fileName = file.name;
        $(input).next('.custom-file-label').text(fileName);
    }
}

// Remove image
function removeImage() {
    if (confirm('Are you sure you want to remove the current image?')) {
        $('#image-preview').attr('src', '').hide();
        $('#no-image-placeholder').show();
        $('#item_image').val('');
        $('#item_image_upload').val('');
        $('.custom-file-label').text('Choose image file...');
        $('#remove-image-btn').hide();
        $('#image-preview-container label').text('Image Preview');
    }
}

// Initialize on document ready
$(document).ready(function() {
    // Event listeners
    $('.revenue-percent').on('input change', validateRevenueSharing);
    $('#campaign_type').on('change', toggleCampaignType);
    $('#item_value, #ticket_price').on('input change', calculateBreakEven);
    $('#winner_selection_type').on('change', handleWinnerSelectionChange);
    
    // Image upload handler
    $('#item_image_upload').on('change', function() {
        previewImage(this);
    });
    
    // Tier image upload handlers
    $('.tier-image-upload').on('change', function() {
        const fileName = this.files[0] ? this.files[0].name : 'Choose new image...';
        $(this).next('.custom-file-label').text(fileName);
    });
    
    // Initialize campaign type on page load
    toggleCampaignType();
    
    // Initialize winner selection type on page load
    handleWinnerSelectionChange();
    
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
            warningDiv.removeClass('alert-success alert-warning').addClass('alert-danger');
            warningDiv.find('i').removeClass('fa-check-circle fa-exclamation-triangle').addClass('fa-times-circle');
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
    
    // Validate on page load
    validateInventory();
    
    // Remove image button
    $('#remove-image-btn').on('click', function() {
        removeImage();
    });
    
    // Custom file input label update
    $('.custom-file-input').on('change', function() {
        const fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').addClass('selected').html(fileName);
    });
    
    // Initialize states
    toggleCampaignType();
    handleWinnerSelectionChange();
    validateRevenueSharing();
    calculateBreakEven();
    
    // Form submission validation
    $('#campaign-form').on('submit', function(e) {
        const revenueValid = validateRevenueSharing();
        const datesValid = validateDates();
        
        if (!revenueValid) {
            e.preventDefault();
            alert('Please ensure revenue sharing percentages total 100%');
            $('html, body').animate({
                scrollTop: $('#platform_percent').offset().top - 100
            }, 500);
            return false;
        }
        
        if (!datesValid) {
            e.preventDefault();
            return false;
        }
        
        // Show loading state
        $(this).find('button[type="submit"]').html('<i class="fas fa-spinner fa-spin"></i> Updating...').prop('disabled', true);
    });
    
    // Card widget initialization
    $('[data-card-widget="collapse"]').on('click', function() {
        $(this).find('i').toggleClass('fa-minus fa-plus');
    });
});
</script>

<style>
.card-outline {
    border-top: 3px solid;
}

.card-primary.card-outline {
    border-top-color: #007bff;
}

.card-success.card-outline {
    border-top-color: #28a745;
}

.card-warning.card-outline {
    border-top-color: #ffc107;
}

.card-info.card-outline {
    border-top-color: #17a2b8;
}

.card-secondary.card-outline {
    border-top-color: #6c757d;
}

.form-control.is-valid {
    border-color: #28a745;
}

.form-control.is-invalid {
    border-color: #dc3545;
}

.custom-switch-on-success .custom-control-input:checked ~ .custom-control-label::before {
    background-color: #28a745;
    border-color: #28a745;
}

#breakeven-alert {
    background-color: #fff3cd;
    border-color: #ffc107;
}

.img-thumbnail {
    border: 3px solid #dee2e6;
    border-radius: 0.5rem;
}
</style>
