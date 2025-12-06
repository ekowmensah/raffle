<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($campaign->name) ?> | Raffle System</title>
    
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700&display=swap">
    <link rel="stylesheet" href="<?= vendor('fontawesome-free/css/all.min.css') ?>">
    <link rel="stylesheet" href="<?= vendor('adminlte/css/adminlte.min.css') ?>">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .navbar-custom {
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .campaign-hero {
            background: white;
            border-radius: 20px;
            padding: 40px;
            margin-top: 100px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        .price-tag {
            font-size: 3rem;
            font-weight: 700;
            color: #667eea;
        }
        .btn-buy {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            border: none;
            color: white;
            padding: 20px 50px;
            font-size: 1.3rem;
            border-radius: 50px;
            font-weight: 600;
        }
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            margin-bottom: 20px;
        }
        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
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
                <h1 class="display-4"><?= htmlspecialchars($campaign->name) ?></h1>
                <p class="lead"><?= nl2br(htmlspecialchars($campaign->description ?? '')) ?></p>
                
                <div class="price-tag my-4">
                    <?= $campaign->currency ?> <?= number_format($campaign->ticket_price, 2) ?>
                    <small class="text-muted" style="font-size: 1rem;">per ticket</small>
                </div>

                <div class="mb-4">
                    <p><strong>Campaign Period:</strong> <?= formatDate($campaign->start_date, 'M d, Y') ?> - <?= formatDate($campaign->end_date, 'M d, Y') ?></p>
                    <p><strong>Prize Pool:</strong> <?= $campaign->prize_pool_percent ?>% of total revenue</p>
                    <?php if ($campaign->daily_draw_enabled): ?>
                        <p><i class="fas fa-check-circle text-success"></i> Daily draws enabled!</p>
                    <?php endif; ?>
                </div>

                <button class="btn btn-buy btn-lg" onclick="showPaymentOptions()">
                    <i class="fas fa-shopping-cart"></i> Buy Tickets Now
                </button>
            </div>

            <div class="col-md-4">
                <div class="stat-card">
                    <i class="fas fa-ticket-alt fa-3x mb-3"></i>
                    <div class="stat-number"><?= number_format($stats->total_tickets ?? 0) ?></div>
                    <div>Tickets Sold</div>
                </div>

                <div class="stat-card">
                    <i class="fas fa-users fa-3x mb-3"></i>
                    <div class="stat-number"><?= number_format($stats->total_players ?? 0) ?></div>
                    <div>Players</div>
                </div>

                <div class="stat-card">
                    <i class="fas fa-trophy fa-3x mb-3"></i>
                    <div class="stat-number"><?= $campaign->currency ?> <?= number_format($stats->total_prize_pool ?? 0, 2) ?></div>
                    <div>Prize Pool</div>
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
                            <label>Your Name (Optional)</label>
                            <input type="text" name="player_name" class="form-control" 
                                   placeholder="e.g., John Doe">
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

                        <div class="form-group">
                            <label>Select Programme <span class="text-danger">*</span></label>
                            <select name="programme_id" id="programmeSelect" class="form-control form-control-lg" required disabled>
                                <option value="">First select a station...</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Select Game/Campaign <span class="text-danger">*</span></label>
                            <select name="campaign_id" id="campaignSelect" class="form-control form-control-lg" required disabled>
                                <option value="">First select a programme...</option>
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

                        <hr>

                        <h6 class="mb-3">Choose Payment Method:</h6>
                        
                        <div class="list-group">
                            <label class="list-group-item">
                                <input type="radio" name="payment_method" value="manual" checked>
                                <i class="fas fa-hand-holding-usd fa-2x text-success ml-2"></i>
                                <strong class="ml-2">Manual Payment (Test)</strong>
                                <p class="mb-0 ml-5 text-muted small">Instant payment for testing</p>
                            </label>
                            <label class="list-group-item">
                                <input type="radio" name="payment_method" value="mtn">
                                <i class="fas fa-mobile-alt fa-2x text-warning ml-2"></i>
                                <strong class="ml-2">MTN Mobile Money</strong>
                                <p class="mb-0 ml-5 text-muted small">Pay with MTN MoMo</p>
                            </label>
                            <label class="list-group-item">
                                <input type="radio" name="payment_method" value="paystack">
                                <i class="fas fa-credit-card fa-2x text-primary ml-2"></i>
                                <strong class="ml-2">Card Payment</strong>
                                <p class="mb-0 ml-5 text-muted small">Pay with Visa, Mastercard</p>
                            </label>
                        </div>

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

// Load programmes when station is selected
$('#stationSelect').on('change', function() {
    const stationId = $(this).val();
    const programmeSelect = $('#programmeSelect');
    const campaignSelect = $('#campaignSelect');
    
    // Reset programme and campaign dropdowns
    programmeSelect.html('<option value="">First select a station...</option>').prop('disabled', true);
    campaignSelect.html('<option value="">First select a programme...</option>').prop('disabled', true);
    $('#ticketPriceInfo').text('');
    $('#totalAmount').text('GHS 0.00');
    
    if (!stationId) {
        return;
    }
    
    // Load programmes for this station via AJAX
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
});

// Load campaigns when programme is selected
$('#programmeSelect').on('change', function() {
    const programmeId = $(this).val();
    const campaignSelect = $('#campaignSelect');
    
    // Reset campaign dropdown
    campaignSelect.html('<option value="">First select a programme...</option>').prop('disabled', true);
    $('#ticketPriceInfo').text('');
    $('#totalAmount').text('GHS 0.00');
    
    if (!programmeId) {
        return;
    }
    
    // Load campaigns for this programme via AJAX
    $.get('<?= url('public/getCampaignsByProgramme') ?>/' + programmeId, function(response) {
        if (response.success && response.campaigns.length > 0) {
            campaignSelect.html('<option value="">Select a game/campaign...</option>');
            
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
