<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title><?= htmlspecialchars($campaign->name) ?> | Raffle System</title>
    
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap">
    <link rel="stylesheet" href="<?= vendor('fontawesome-free/css/all.min.css') ?>">
    <link rel="stylesheet" href="<?= vendor('adminlte/css/adminlte.min.css') ?>">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: #f8f9fa;
            min-height: 100vh;
            padding-bottom: 80px;
        }
        
        /* Mobile-first navbar */
        .navbar-custom {
            background: white;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            padding: 12px 0;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
        }
        
        .navbar-brand {
            font-weight: 700;
            font-size: 1.25rem;
            color: #667eea !important;
        }
        
        /* Campaign hero section */
        .campaign-hero {
            background: white;
            border-radius: 0;
            margin-top: 60px;
            padding: 24px 16px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        
        @media (min-width: 768px) {
            .campaign-hero {
                border-radius: 16px;
                margin: 80px 16px 20px;
                padding: 40px;
            }
        }
        
        @media (min-width: 992px) {
            .campaign-hero {
                margin: 100px auto 40px;
                max-width: 1200px;
            }
        }
        
        h1 {
            font-size: 1.75rem;
            font-weight: 800;
            color: #212529;
            margin-bottom: 12px;
            line-height: 1.2;
        }
        
        @media (min-width: 768px) {
            h1 {
                font-size: 2.5rem;
            }
        }
        
        .lead {
            font-size: 1rem;
            color: #6c757d;
            line-height: 1.6;
            margin-bottom: 20px;
        }
        
        /* Price tag */
        .price-tag {
            font-size: 2.5rem;
            font-weight: 800;
            color: #667eea;
            margin: 20px 0;
            display: flex;
            align-items: baseline;
            gap: 8px;
        }
        
        @media (min-width: 768px) {
            .price-tag {
                font-size: 3.5rem;
            }
        }
        
        .price-tag small {
            font-size: 1rem;
            color: #6c757d;
            font-weight: 500;
        }
        
        /* Info section */
        .campaign-info {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 16px;
            margin: 20px 0;
        }
        
        .campaign-info p {
            margin-bottom: 8px;
            font-size: 0.875rem;
            color: #495057;
        }
        
        .campaign-info strong {
            color: #212529;
            font-weight: 600;
        }
        
        /* Buy button */
        .btn-buy {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 16px 32px;
            font-size: 1.125rem;
            border-radius: 12px;
            font-weight: 700;
            width: 100%;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }
        
        .btn-buy:active {
            transform: scale(0.98);
        }
        
        @media (min-width: 768px) {
            .btn-buy {
                width: auto;
                min-width: 240px;
            }
        }
        
        /* Stat cards */
        .stats-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 12px;
            margin-top: 24px;
        }
        
        @media (min-width: 768px) {
            .stats-grid {
                grid-template-columns: repeat(3, 1fr);
                gap: 16px;
            }
        }
        
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
        }
        
        .stat-card i {
            font-size: 2rem;
            margin-bottom: 12px;
            opacity: 0.9;
        }
        
        .stat-number {
            font-size: 1.75rem;
            font-weight: 800;
            margin-bottom: 4px;
        }
        
        .stat-label {
            font-size: 0.875rem;
            opacity: 0.9;
            font-weight: 500;
        }
        
        /* Modal improvements */
        .modal-content {
            border-radius: 16px;
            border: none;
        }
        
        .modal-header {
            border-radius: 16px 16px 0 0;
            padding: 20px 24px;
        }
        
        .modal-body {
            padding: 24px;
        }
        
        /* Form elements */
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            font-weight: 600;
            font-size: 0.875rem;
            color: #495057;
            margin-bottom: 8px;
            display: block;
        }
        
        .form-control-lg {
            height: 48px;
            font-size: 1rem;
            border-radius: 12px;
            border: 2px solid #e9ecef;
            padding: 12px 16px;
        }
        
        .form-control-lg:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        /* Toggle buttons */
        .btn-group-toggle {
            display: flex;
            gap: 8px;
            margin-bottom: 20px;
        }
        
        .btn-group-toggle label {
            flex: 1;
            padding: 12px 16px;
            border: 2px solid #e9ecef;
            border-radius: 12px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s ease;
            font-weight: 600;
            font-size: 0.875rem;
        }
        
        .btn-group-toggle label.active {
            background: #667eea;
            color: white;
            border-color: #667eea;
        }
        
        /* Alert */
        .alert {
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 20px;
            border: none;
        }
        
        /* Utility */
        .text-muted {
            color: #6c757d;
            font-size: 0.875rem;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light navbar-custom fixed-top">
    <div class="container">
        <a class="navbar-brand" href="<?= url('public') ?>">
            <i class="fas fa-trophy text-warning"></i>
            <strong>Raffle System</strong>
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="<?= url('public') ?>">
                        <i class="fas fa-arrow-left"></i> Back to Campaigns
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container">
    <div class="campaign-hero">
        <div class="row">
            <div class="col-md-8">
                <h1><?= htmlspecialchars($campaign->name) ?></h1>
                <p class="lead"><?= nl2br(htmlspecialchars($campaign->description ?? '')) ?></p>
                
                <div class="price-tag">
                    <?= $campaign->currency ?> <?= number_format($campaign->ticket_price, 2) ?>
                    <small>per ticket</small>
                </div>

                <div class="campaign-info">
                    <p><strong>Campaign Period:</strong> <?= formatDate($campaign->start_date, 'M d, Y') ?> - <?= formatDate($campaign->end_date, 'M d, Y') ?></p>
                    <p><strong>Prize Pool:</strong> <?= $campaign->prize_pool_percent ?>% of total revenue</p>
                    <?php if ($campaign->daily_draw_enabled): ?>
                        <p><i class="fas fa-check-circle text-success"></i> Daily draws enabled!</p>
                    <?php endif; ?>
                </div>

                <button class="btn btn-buy" onclick="showPaymentOptions()">
                    <i class="fas fa-shopping-cart"></i> Buy Tickets Now
                </button>
                
                <!-- Stats Grid - Mobile -->
                <div class="stats-grid d-md-none">
                    <div class="stat-card">
                        <i class="fas fa-ticket-alt"></i>
                        <div class="stat-number"><?= number_format($stats->total_tickets ?? 0) ?></div>
                        <div class="stat-label">Tickets Sold</div>
                    </div>

                    <div class="stat-card">
                        <i class="fas fa-users"></i>
                        <div class="stat-number"><?= number_format($stats->total_players ?? 0) ?></div>
                        <div class="stat-label">Players</div>
                    </div>

                    <div class="stat-card">
                        <i class="fas fa-trophy"></i>
                        <div class="stat-number"><?= $campaign->currency ?> <?= number_format($stats->total_prize_pool ?? 0, 2) ?></div>
                        <div class="stat-label">Prize Pool</div>
                    </div>
                </div>
            </div>

            <!-- Stats Grid - Desktop -->
            <div class="col-md-4 d-none d-md-block">
                <div class="stat-card">
                    <i class="fas fa-ticket-alt"></i>
                    <div class="stat-number"><?= number_format($stats->total_tickets ?? 0) ?></div>
                    <div class="stat-label">Tickets Sold</div>
                </div>

                <div class="stat-card" style="margin-top: 16px;">
                    <i class="fas fa-users"></i>
                    <div class="stat-number"><?= number_format($stats->total_players ?? 0) ?></div>
                    <div class="stat-label">Players</div>
                </div>

                <div class="stat-card" style="margin-top: 16px;">
                    <i class="fas fa-trophy"></i>
                    <div class="stat-number"><?= $campaign->currency ?> <?= number_format($stats->total_prize_pool ?? 0, 2) ?></div>
                    <div class="stat-label">Prize Pool</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Options Modal -->
    <div class="modal fade" id="paymentModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Buy Tickets - <?= htmlspecialchars($campaign->name) ?></h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <?php if (flash('error')): ?>
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            <?= flash('error') ?>
                        </div>
                    <?php endif; ?>
                    
                    <form action="<?= url('public/processPayment') ?>" method="POST" id="paymentForm">
                        <div class="form-group">
                            <label>Your Phone Number <span class="text-danger">*</span></label>
                            <input type="text" name="phone" class="form-control form-control-lg" 
                                   placeholder="e.g., 0244123456" required>
                            <small class="text-muted">We'll send your ticket codes to this number</small>
                        </div>

                        <div class="form-group">
                            <label>Select Station <span class="text-danger">*</span></label>
                            <select name="station_id" id="stationSelect" class="form-control form-control-lg" required>
                                <option value="">Choose a station...</option>
                                <?php
                                $stationModel = new \App\Models\Station();
                                $stations = $stationModel->getActive();
                                foreach ($stations as $station):
                                ?>
                                    <option value="<?= $station->id ?>"><?= htmlspecialchars($station->name) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group" id="campaignTypeGroup">
                            <label>Campaign Type</label>
                            <div class="btn-group btn-group-toggle d-flex" data-toggle="buttons">
                                <label class="btn btn-outline-primary active" id="stationWideBtn">
                                    <input type="radio" name="campaign_type" value="station" checked> 
                                    <i class="fas fa-broadcast-tower"></i> Station-Wide
                                </label>
                                <label class="btn btn-outline-primary" id="programmeBtn">
                                    <input type="radio" name="campaign_type" value="programme"> 
                                    <i class="fas fa-microphone"></i> Programme
                                </label>
                            </div>
                        </div>

                        <div class="form-group" id="programmeGroup" style="display: none;">
                            <label>Select Programme <span class="text-danger">*</span></label>
                            <select name="programme_id" id="programmeSelect" class="form-control form-control-lg" disabled>
                                <option value="">First select a station...</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Select Campaign <span class="text-danger">*</span></label>
                            <select name="campaign_id" id="campaignSelect" class="form-control form-control-lg" required disabled>
                                <option value="">First select a station...</option>
                            </select>
                            <small class="text-muted" id="ticketPriceInfo"></small>
                        </div>

                        <div class="form-group">
                            <label>Number of Tickets <span class="text-danger">*</span></label>
                            <input type="number" name="ticket_count" class="form-control form-control-lg" 
                                   min="1" value="1" id="ticketCountInput" required>
                            <small class="text-muted">
                                Total Amount: <strong id="totalAmount">GHS 0.00</strong>
                            </small>
                        </div>

                        <div class="form-group">
                            <label>Promo Code (Optional)</label>
                            <input type="text" name="promo_code" class="form-control" 
                                   placeholder="Enter promo code if you have one">
                        </div>

                        <!-- Payment method auto-selected -->
                        <input type="hidden" name="payment_method" value="hubtel">

                        <div class="mt-4">
                            <button type="submit" class="btn btn-success btn-lg btn-block">
                                <i class="fas fa-check-circle"></i> Proceed to Payment
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?= vendor('jquery/jquery.min.js') ?>"></script>
<script src="<?= vendor('bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
<script>
function showPaymentOptions() {
    $('#paymentModal').modal('show');
}

// Store campaigns data
let campaignsData = {};
let currentTicketPrice = 0;
let currentCampaignType = 'station';

// Handle campaign type toggle
$('input[name="campaign_type"]').on('change', function() {
    currentCampaignType = $(this).val();
    const stationId = $('#stationSelect').val();
    
    if (currentCampaignType === 'programme') {
        $('#programmeGroup').show();
        $('#programmeSelect').prop('required', true);
    } else {
        $('#programmeGroup').hide();
        $('#programmeSelect').prop('required', false).val('');
    }
    
    // Reload campaigns if station is selected
    if (stationId) {
        loadCampaigns(stationId);
    }
});

// Load campaigns when station is selected
$('#stationSelect').on('change', function() {
    const stationId = $(this).val();
    const programmeSelect = $('#programmeSelect');
    const campaignSelect = $('#campaignSelect');
    
    // Reset dropdowns
    programmeSelect.html('<option value="">Select a programme...</option>').prop('disabled', true);
    campaignSelect.html('<option value="">First select a station...</option>').prop('disabled', true);
    $('#ticketPriceInfo').text('');
    $('#totalAmount').text('GHS 0.00');
    
    if (!stationId) {
        return;
    }
    
    // Load programmes for this station
    $.get('<?= url('public/getProgrammesByStation') ?>/' + stationId, function(response) {
        if (response.success && response.programmes.length > 0) {
            programmeSelect.html('<option value="">Select a programme...</option>');
            
            response.programmes.forEach(function(programme) {
                programmeSelect.append(
                    $('<option></option>')
                        .val(programme.id)
                        .text(programme.name)
                );
            });
            
            programmeSelect.prop('disabled', false);
        } else {
            programmeSelect.html('<option value="">No active programmes for this station</option>');
        }
    });
    
    // Load campaigns based on type
    loadCampaigns(stationId);
});

function loadCampaigns(stationId) {
    const campaignSelect = $('#campaignSelect');
    
    if (currentCampaignType === 'station') {
        // Load station-wide campaigns
        $.get('<?= url('public/getCampaignsByStation') ?>/' + stationId, function(response) {
            if (response.success && response.campaigns.length > 0) {
                campaignSelect.html('<option value="">Select a campaign...</option>');
                
                response.campaigns.forEach(function(campaign) {
                    campaignsData[campaign.id] = campaign;
                    campaignSelect.append(
                        $('<option></option>')
                            .val(campaign.id)
                            .text(campaign.name + ' - ' + campaign.currency + ' ' + parseFloat(campaign.ticket_price).toFixed(2) + ' per ticket')
                    );
                });
                
                campaignSelect.prop('disabled', false);
            } else {
                campaignSelect.html('<option value="">No station-wide campaigns available</option>');
            }
        });
    } else {
        // For programme campaigns, wait for programme selection
        campaignSelect.html('<option value="">First select a programme...</option>').prop('disabled', true);
    }
}

// Load campaigns when programme is selected
$('#programmeSelect').on('change', function() {
    const programmeId = $(this).val();
    const campaignSelect = $('#campaignSelect');
    
    // Reset campaign dropdown
    campaignSelect.html('<option value="">Select a campaign...</option>').prop('disabled', true);
    $('#ticketPriceInfo').text('');
    $('#totalAmount').text('GHS 0.00');
    
    if (!programmeId) {
        return;
    }
    
    // Load campaigns for this programme via AJAX
    $.get('<?= url('public/getCampaignsByProgramme') ?>/' + programmeId, function(response) {
        if (response.success && response.campaigns.length > 0) {
            campaignSelect.html('<option value="">Select a campaign...</option>');
            
            response.campaigns.forEach(function(campaign) {
                campaignsData[campaign.id] = campaign;
                campaignSelect.append(
                    $('<option></option>')
                        .val(campaign.id)
                        .text(campaign.name + ' - ' + campaign.currency + ' ' + parseFloat(campaign.ticket_price).toFixed(2) + ' per ticket')
                );
            });
            
            campaignSelect.prop('disabled', false);
        } else {
            campaignSelect.html('<option value="">No active campaigns for this programme</option>');
        }
    });
});

// Update price info when campaign is selected
$('#campaignSelect').on('change', function() {
    const campaignId = $(this).val();
    
    if (campaignId && campaignsData[campaignId]) {
        const campaign = campaignsData[campaignId];
        currentTicketPrice = parseFloat(campaign.ticket_price);
        $('#ticketPriceInfo').text('Price per ticket: ' + campaign.currency + ' ' + currentTicketPrice.toFixed(2));
        calculateTotal();
    } else {
        currentTicketPrice = 0;
        $('#ticketPriceInfo').text('');
        $('#totalAmount').text('GHS 0.00');
    }
});

// Calculate total amount based on ticket count
$('#ticketCountInput').on('input', calculateTotal);

function calculateTotal() {
    const ticketCount = parseInt($('#ticketCountInput').val()) || 0;
    const total = ticketCount * currentTicketPrice;
    $('#totalAmount').text('GHS ' + total.toFixed(2));
}

// Reopen modal if there's an error
<?php if (flash('error') || flash('info')): ?>
$(document).ready(function() {
    $('#paymentModal').modal('show');
});
<?php endif; ?>
</script>
</body>
</html>
