<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title><?= htmlspecialchars($campaign->name) ?> | eTickets Raffle</title>
    
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding-bottom: 80px;
        }
        
        /* Navbar */
        .navbar-custom {
            background: rgba(255,255,255,0.98);
            backdrop-filter: blur(20px);
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            padding: 1rem 0;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
        }
        
        .navbar-custom .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: #667eea;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .back-link {
            color: #4b5563;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }
        
        .back-link:hover {
            color: #667eea;
        }
        
        /* Campaign hero section */
        .campaign-hero {
            background: white;
            border-radius: 30px;
            margin: 100px auto 40px;
            max-width: 1200px;
            padding: 3rem;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            position: relative;
            overflow: hidden;
        }
        
        .campaign-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 8px;
            background: linear-gradient(90deg, #667eea, #764ba2, #f093fb);
        }
        
        @media (max-width: 768px) {
            .campaign-hero {
                margin: 80px 1rem 2rem;
                padding: 2rem 1.5rem;
                border-radius: 20px;
            }
        }
        
        h1 {
            font-size: 2.5rem;
            font-weight: 900;
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 1rem;
            line-height: 1.2;
        }
        
        @media (min-width: 768px) {
            h1 {
                font-size: 3.5rem;
            }
        }
        
        @media (max-width: 768px) {
            h1 {
                font-size: 2rem;
            }
        }
        
        .lead {
            font-size: 1.2rem;
            color: #4b5563;
            line-height: 1.8;
            margin-bottom: 2rem;
        }
        
        @media (max-width: 768px) {
            .lead {
                font-size: 1rem;
            }
        }
        
        /* Price tag */
        .price-section {
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 20px;
            padding: 2rem;
            margin: 2rem 0;
            text-align: center;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
        }
        
        .price-tag {
            font-size: 4rem;
            font-weight: 900;
            color: white;
            display: block;
            text-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }
        
        .price-label {
            font-size: 1.1rem;
            color: rgba(255,255,255,0.9);
            font-weight: 500;
            margin-top: 0.5rem;
        }
        
        @media (max-width: 768px) {
            .price-tag {
                font-size: 2.5rem;
            }
        }
        
        /* Info section */
        .campaign-info {
            background: linear-gradient(135deg, #f9fafb, #f3f4f6);
            border-radius: 20px;
            padding: 2rem;
            margin: 2rem 0;
            border: 2px solid #e5e7eb;
        }
        
        .info-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1.5rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .info-item:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }
        
        .info-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea, #764ba2);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            flex-shrink: 0;
        }
        
        .info-content {
            flex: 1;
        }
        
        .info-label {
            font-size: 0.85rem;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
            margin-bottom: 0.25rem;
        }
        
        .info-value {
            font-size: 1.1rem;
            color: #111827;
            font-weight: 600;
        }
        
        /* Buy button */
        .btn-buy {
            background: linear-gradient(135deg, #10b981, #059669);
            border: none;
            color: white;
            padding: 1.5rem 3rem;
            font-size: 1.3rem;
            border-radius: 50px;
            font-weight: 700;
            width: 100%;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 8px 25px rgba(16, 185, 129, 0.4);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .btn-buy:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(16, 185, 129, 0.6);
        }
        
        .btn-buy:active {
            transform: translateY(-1px);
        }
        
        @media (min-width: 768px) {
            .btn-buy {
                width: auto;
                min-width: 320px;
            }
        }
        
        @media (max-width: 768px) {
            .btn-buy {
                font-size: 1.1rem;
                padding: 1.2rem 2rem;
            }
        }
        
        /* Features section */
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-top: 3rem;
        }
        
        .feature-card {
            background: white;
            border: 2px solid #e5e7eb;
            border-radius: 20px;
            padding: 2rem;
            text-align: center;
            transition: all 0.3s;
        }
        
        .feature-card:hover {
            border-color: #667eea;
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.2);
        }
        
        .feature-icon {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea, #764ba2);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 2rem;
            color: white;
        }
        
        .feature-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: #111827;
            margin-bottom: 0.5rem;
        }
        
        .feature-desc {
            font-size: 0.9rem;
            color: #6b7280;
            line-height: 1.6;
        }
        
        /* Modal improvements */
        .modal-content {
            border-radius: 20px;
            border: none;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        
        .modal-header {
            background: linear-gradient(135deg, #667eea, #764ba2) !important;
            border-radius: 20px 20px 0 0;
            padding: 1.5rem;
            border-bottom: none;
        }
        
        .modal-header .modal-title {
            color: white;
            font-weight: 700;
            font-size: 1.3rem;
        }
        
        .modal-header .close {
            color: white;
            opacity: 1;
            text-shadow: none;
            font-size: 2rem;
        }
        
        .modal-body {
            padding: 2rem;
        }
        
        .modal-backdrop {
            background-color: rgba(0, 0, 0, 0.7);
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
        
        /* Submit button in modal */
        .btn-success {
            background: linear-gradient(135deg, #10b981, #059669) !important;
            border: none !important;
            padding: 1rem 2rem;
            font-size: 1.1rem;
            font-weight: 700;
            border-radius: 12px;
            transition: all 0.3s;
        }
        
        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(16, 185, 129, 0.4);
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
<nav class="navbar-custom">
    <div class="container">
        <a class="navbar-brand" href="<?= url('public') ?>">
            <i class="fas fa-ticket"></i>
            <strong>eTickets Raffle</strong>
        </a>
        <a href="<?= url('public') ?>" class="back-link">
            <i class="fas fa-arrow-left"></i> Back to Campaigns
        </a>
    </div>
</nav>

<div class="container">
    <div class="campaign-hero">
        <h1><?= htmlspecialchars($campaign->name) ?></h1>
        <p class="lead"><?= nl2br(htmlspecialchars($campaign->description ?? 'Join this exciting raffle campaign and stand a chance to win amazing prizes!')) ?></p>
        
        <div class="price-section">
            <div class="price-tag"><?= $campaign->currency ?> <?= number_format($campaign->ticket_price, 2) ?></div>
            <div class="price-label">Per Ticket</div>
        </div>

        <div class="campaign-info">
            <div class="info-item">
                <div class="info-icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="info-content">
                    <div class="info-label">Campaign Period</div>
                    <div class="info-value"><?= formatDate($campaign->start_date, 'M d, Y') ?> - <?= formatDate($campaign->end_date, 'M d, Y') ?></div>
                </div>
            </div>

            <div class="info-item">
                <div class="info-icon">
                    <i class="fas fa-trophy"></i>
                </div>
                <div class="info-content">
                    <div class="info-label">Prize Pool</div>
                    <div class="info-value"><?= $campaign->prize_pool_percent ?>% of Total Revenue</div>
                </div>
            </div>

            <?php if ($campaign->daily_draw_enabled): ?>
            <div class="info-item">
                <div class="info-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="info-content">
                    <div class="info-label">Draw Frequency</div>
                    <div class="info-value">Daily Draws Enabled! 🎉</div>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <div style="text-align: center; margin: 3rem 0;">
            <button class="btn btn-buy" onclick="showPaymentOptions()">
                <i class="fas fa-ticket"></i> Buy Tickets Now
            </button>
        </div>

        <!-- Features Grid -->
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-shield-check"></i>
                </div>
                <div class="feature-title">100% Secure</div>
                <div class="feature-desc">Your transactions are protected with bank-level security</div>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-bolt"></i>
                </div>
                <div class="feature-title">Instant Tickets</div>
                <div class="feature-desc">Receive your ticket codes immediately via SMS</div>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-trophy"></i>
                </div>
                <div class="feature-title">Big Prizes</div>
                <div class="feature-desc">Win amazing cash prizes and rewards</div>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-mobile-alt"></i>
                </div>
                <div class="feature-title">Play via USSD</div>
                <div class="feature-desc">No internet? Dial our USSD code to play</div>
            </div>
        </div>
    </div>

    <!-- Payment Options Modal -->
    <div class="modal fade" id="paymentModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Buy Tickets - <?= htmlspecialchars($campaign->name) ?></h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
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
