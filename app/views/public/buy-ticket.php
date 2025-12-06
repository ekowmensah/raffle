<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Buy Tickets | Raffle System</title>
    
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
        .buy-ticket-card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            margin-top: 100px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            max-width: 800px;
            margin-left: auto;
            margin-right: auto;
        }
        .step-indicator {
            display: flex;
            justify-content: space-between;
            margin-bottom: 40px;
        }
        .step {
            flex: 1;
            text-align: center;
            padding: 15px;
            border-radius: 10px;
            background: #f8f9fa;
            margin: 0 5px;
            position: relative;
        }
        .step.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .step.completed {
            background: #28a745;
            color: white;
        }
        .step-number {
            display: inline-block;
            width: 30px;
            height: 30px;
            line-height: 30px;
            border-radius: 50%;
            background: white;
            color: #667eea;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .step.active .step-number {
            background: white;
            color: #667eea;
        }
        .step.completed .step-number {
            background: white;
            color: #28a745;
        }
        .campaign-option {
            border: 2px solid #e9ecef;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 15px;
            cursor: pointer;
            transition: all 0.3s;
        }
        .campaign-option:hover {
            border-color: #667eea;
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.2);
        }
        .campaign-option.selected {
            border-color: #667eea;
            background: #f0f3ff;
        }
        .campaign-type-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        .badge-station-wide {
            background: #667eea;
            color: white;
        }
        .badge-programme {
            background: #f093fb;
            color: white;
        }
        .btn-next {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 15px 40px;
            font-size: 1.1rem;
            border-radius: 50px;
            font-weight: 600;
        }
        .btn-back {
            background: #6c757d;
            border: none;
            color: white;
            padding: 15px 40px;
            font-size: 1.1rem;
            border-radius: 50px;
            font-weight: 600;
        }
        .hidden {
            display: none;
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
                        <i class="fas fa-arrow-left"></i> Back to Home
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container">
    <div class="buy-ticket-card">
        <h2 class="text-center mb-4">
            <i class="fas fa-ticket-alt text-primary"></i> Buy Raffle Tickets
        </h2>

        <!-- Step Indicator -->
        <div class="step-indicator">
            <div class="step active" id="step1-indicator">
                <div class="step-number">1</div>
                <div>Select Station</div>
            </div>
            <div class="step" id="step2-indicator">
                <div class="step-number">2</div>
                <div>Choose Campaign</div>
            </div>
            <div class="step" id="step3-indicator">
                <div class="step-number">3</div>
                <div>Enter Details</div>
            </div>
            <div class="step" id="step4-indicator">
                <div class="step-number">4</div>
                <div>Payment</div>
            </div>
        </div>

        <form action="<?= url('public/processPayment') ?>" method="POST" id="ticketForm">
            <?= csrf_field() ?>
            
            <!-- Step 1: Select Station -->
            <div id="step1" class="step-content">
                <h4 class="mb-4">Step 1: Select Your Station</h4>
                
                <?php foreach ($stations as $station): ?>
                    <div class="campaign-option" onclick="selectStation(<?= $station->id ?>, '<?= htmlspecialchars($station->name) ?>')">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-1">
                                    <i class="fas fa-broadcast-tower text-primary"></i>
                                    <?= htmlspecialchars($station->name) ?>
                                </h5>
                                <p class="text-muted mb-0">
                                    <small><?= htmlspecialchars($station->location ?? '') ?></small>
                                </p>
                            </div>
                            <i class="fas fa-chevron-right text-muted"></i>
                        </div>
                    </div>
                <?php endforeach; ?>
                
                <input type="hidden" name="station_id" id="station_id" required>
            </div>

            <!-- Step 2: Choose Campaign Type -->
            <div id="step2" class="step-content hidden">
                <h4 class="mb-4">Step 2: Choose Campaign</h4>
                
                <!-- Campaign Type Selection -->
                <div class="mb-4" id="campaign-type-selector">
                    <div class="btn-group btn-group-toggle d-flex" data-toggle="buttons">
                        <label class="btn btn-outline-primary btn-lg active" id="station-wide-type-btn">
                            <input type="radio" name="campaign_type_choice" value="station" checked> 
                            <i class="fas fa-broadcast-tower"></i> Station-Wide Campaigns
                        </label>
                        <label class="btn btn-outline-primary btn-lg" id="programme-type-btn">
                            <input type="radio" name="campaign_type_choice" value="programme"> 
                            <i class="fas fa-microphone"></i> Programme Campaigns
                        </label>
                    </div>
                </div>
                
                <!-- Station-wide campaigns section -->
                <div id="station-campaigns-section">
                    <div id="station-campaigns-container">
                        <!-- Station-wide campaigns will be loaded here -->
                    </div>
                </div>
                
                <!-- Programme campaigns section -->
                <div id="programme-campaigns-section" class="hidden">
                    <div id="programme-selection">
                        <h5 class="mb-3">Select Programme:</h5>
                        <div id="programmes-container">
                            <!-- Programmes will be loaded here -->
                        </div>
                    </div>
                    
                    <div id="programme-campaigns-container" class="hidden mt-4">
                        <h5 class="mb-3">Programme Campaigns:</h5>
                        <div id="programme-campaigns-list">
                            <!-- Programme-specific campaigns will be loaded here -->
                        </div>
                    </div>
                </div>
                
                <input type="hidden" name="campaign_id" id="campaign_id" required>
                <input type="hidden" name="programme_id" id="programme_id">
                
                <div class="mt-4">
                    <button type="button" class="btn btn-back" onclick="goToStep(1)">
                        <i class="fas fa-arrow-left"></i> Back
                    </button>
                </div>
            </div>

            <!-- Step 3: Enter Details -->
            <div id="step3" class="step-content hidden">
                <h4 class="mb-4">Step 3: Enter Your Details</h4>
                
                <div id="selected-campaign-info" class="alert alert-info">
                    <!-- Selected campaign info will be displayed here -->
                </div>
                
                <div class="form-group">
                    <label for="player_name">Your Name</label>
                    <input type="text" class="form-control form-control-lg" id="player_name" name="player_name" required>
                </div>
                
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" class="form-control form-control-lg" id="phone" name="phone" placeholder="0241234567" required>
                    <small class="form-text text-muted">Enter your mobile money number</small>
                </div>
                
                <div class="form-group">
                    <label for="ticket_count">Number of Tickets</label>
                    <input type="number" class="form-control form-control-lg" id="ticket_count" name="ticket_count" min="1" value="1" required>
                    <small class="form-text text-muted">Price per ticket: <span id="ticket-price-display">GHS 0.00</span></small>
                </div>
                
                <div class="alert alert-success">
                    <strong>Total Amount:</strong> <span id="total-amount-display" class="h4">GHS 0.00</span>
                </div>
                
                <div class="mt-4">
                    <button type="button" class="btn btn-back" onclick="goToStep(2)">
                        <i class="fas fa-arrow-left"></i> Back
                    </button>
                    <button type="button" class="btn btn-next float-right" onclick="goToStep(4)">
                        Continue <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            </div>

            <!-- Step 4: Payment -->
            <div id="step4" class="step-content hidden">
                <h4 class="mb-4">Step 4: Select Payment Method</h4>
                
                <div id="payment-summary" class="alert alert-primary">
                    <!-- Payment summary will be displayed here -->
                </div>
                
                <div class="form-group">
                    <label>Payment Method</label>
                    <div class="campaign-option" onclick="selectPaymentMethod('manual')">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-hand-holding-usd fa-2x text-success mr-3"></i>
                            <div>
                                <h5 class="mb-0">Manual Payment</h5>
                                <small class="text-muted">Pay via mobile money or cash</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="campaign-option" onclick="selectPaymentMethod('mtn')">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-mobile-alt fa-2x text-warning mr-3"></i>
                            <div>
                                <h5 class="mb-0">MTN Mobile Money</h5>
                                <small class="text-muted">Instant payment via MTN MoMo</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="campaign-option" onclick="selectPaymentMethod('hubtel')">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-credit-card fa-2x text-primary mr-3"></i>
                            <div>
                                <h5 class="mb-0">Hubtel (All Networks)</h5>
                                <small class="text-muted">MTN, Telecel, AirtelTigo</small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <input type="hidden" name="payment_method" id="payment_method" value="manual" required>
                
                <div class="mt-4">
                    <button type="button" class="btn btn-back" onclick="goToStep(3)">
                        <i class="fas fa-arrow-left"></i> Back
                    </button>
                    <button type="submit" class="btn btn-success btn-lg float-right">
                        <i class="fas fa-check-circle"></i> Complete Purchase
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="<?= vendor('jquery/jquery.min.js') ?>"></script>
<script src="<?= vendor('bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
<script>
let selectedStationId = null;
let selectedCampaign = null;
let currentStep = 1;
let currentCampaignType = 'station';

function goToStep(step) {
    // Hide all steps
    for (let i = 1; i <= 4; i++) {
        document.getElementById('step' + i).classList.add('hidden');
        document.getElementById('step' + i + '-indicator').classList.remove('active', 'completed');
    }
    
    // Show current step
    document.getElementById('step' + step).classList.remove('hidden');
    document.getElementById('step' + step + '-indicator').classList.add('active');
    
    // Mark previous steps as completed
    for (let i = 1; i < step; i++) {
        document.getElementById('step' + i + '-indicator').classList.add('completed');
    }
    
    currentStep = step;
}

// Handle campaign type toggle
$('input[name="campaign_type_choice"]').on('change', function() {
    currentCampaignType = $(this).val();
    
    if (currentCampaignType === 'station') {
        $('#station-campaigns-section').show();
        $('#programme-campaigns-section').hide();
        $('#programme_id').val('');
    } else {
        $('#station-campaigns-section').hide();
        $('#programme-campaigns-section').show();
        
        // Load programmes if not already loaded
        if (selectedStationId && $('#programmes-container').children().length === 0) {
            loadProgrammes();
        }
    }
});

function selectStation(stationId, stationName) {
    selectedStationId = stationId;
    document.getElementById('station_id').value = stationId;
    
    // Reset campaign type to station-wide
    currentCampaignType = 'station';
    $('input[name="campaign_type_choice"][value="station"]').prop('checked', true).parent().addClass('active');
    $('input[name="campaign_type_choice"][value="programme"]').prop('checked', false).parent().removeClass('active');
    $('#station-campaigns-section').show();
    $('#programme-campaigns-section').hide();
    
    // Load station-wide campaigns
    $.ajax({
        url: '<?= url('public/getCampaignsByStation') ?>/' + stationId,
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                displayStationCampaigns(response.campaigns, stationName);
                goToStep(2);
            }
        },
        error: function() {
            alert('Error loading campaigns');
        }
    });
}

function displayStationCampaigns(campaigns, stationName) {
    let html = '<h5 class="mb-3"><i class="fas fa-broadcast-tower"></i> ' + stationName + ' Campaigns</h5>';
    
    if (campaigns.length === 0) {
        html += '<div class="alert alert-warning">No station-wide campaigns available. Please browse by programme.</div>';
    } else {
        campaigns.forEach(function(campaign) {
            html += `
                <div class="campaign-option" onclick="selectCampaign(${campaign.id}, '${campaign.name}', ${campaign.ticket_price}, '${campaign.currency}', null)">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="campaign-type-badge badge-station-wide">
                                <i class="fas fa-star"></i> Station-Wide
                            </span>
                            <h5 class="mt-2 mb-1">${campaign.name}</h5>
                            <p class="text-muted mb-0">
                                <strong>${campaign.currency} ${parseFloat(campaign.ticket_price).toFixed(2)}</strong> per ticket
                            </p>
                        </div>
                        <i class="fas fa-chevron-right text-muted"></i>
                    </div>
                </div>
            `;
        });
    }
    
    document.getElementById('station-campaigns-container').innerHTML = html;
}

function loadProgrammes() {
    $.ajax({
        url: '<?= url('public/getProgrammesByStation') ?>/' + selectedStationId,
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                displayProgrammes(response.programmes);
            }
        },
        error: function() {
            alert('Error loading programmes');
        }
    });
}

function displayProgrammes(programmes) {
    let html = '';
    
    if (programmes.length === 0) {
        html = '<div class="alert alert-warning">No programmes available for this station.</div>';
    } else {
        programmes.forEach(function(programme) {
            html += `
                <div class="campaign-option" onclick="selectProgramme(${programme.id}, '${programme.name}')">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1">
                                <i class="fas fa-microphone text-primary"></i>
                                ${programme.name}
                            </h5>
                        </div>
                        <i class="fas fa-chevron-right text-muted"></i>
                    </div>
                </div>
            `;
        });
    }
    
    document.getElementById('programmes-container').innerHTML = html;
}

function selectProgramme(programmeId, programmeName) {
    $.ajax({
        url: '<?= url('public/getCampaignsByProgramme') ?>/' + programmeId,
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                displayProgrammeCampaigns(response.campaigns, programmeName, programmeId);
                document.getElementById('programme-campaigns-container').classList.remove('hidden');
            }
        },
        error: function() {
            alert('Error loading campaigns');
        }
    });
}

function displayProgrammeCampaigns(campaigns, programmeName, programmeId) {
    let html = '<h6><i class="fas fa-microphone"></i> ' + programmeName + '</h6>';
    
    if (campaigns.length === 0) {
        html += '<div class="alert alert-warning">No campaigns available for this programme.</div>';
    } else {
        campaigns.forEach(function(campaign) {
            html += `
                <div class="campaign-option" onclick="selectCampaign(${campaign.id}, '${campaign.name}', ${campaign.ticket_price}, '${campaign.currency}', ${programmeId})">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="campaign-type-badge badge-programme">
                                <i class="fas fa-microphone"></i> Programme
                            </span>
                            <h5 class="mt-2 mb-1">${campaign.name}</h5>
                            <p class="text-muted mb-0">
                                <strong>${campaign.currency} ${parseFloat(campaign.ticket_price).toFixed(2)}</strong> per ticket
                            </p>
                        </div>
                        <i class="fas fa-chevron-right text-muted"></i>
                    </div>
                </div>
            `;
        });
    }
    
    document.getElementById('programme-campaigns-list').innerHTML = html;
}

function selectCampaign(campaignId, campaignName, ticketPrice, currency, programmeId) {
    selectedCampaign = {
        id: campaignId,
        name: campaignName,
        price: parseFloat(ticketPrice),
        currency: currency,
        programmeId: programmeId
    };
    
    document.getElementById('campaign_id').value = campaignId;
    document.getElementById('programme_id').value = programmeId || '';
    document.getElementById('ticket-price-display').textContent = currency + ' ' + ticketPrice;
    
    // Display selected campaign info
    let campaignType = programmeId ? '<span class="badge badge-info">Programme Campaign</span>' : '<span class="badge badge-primary">Station-Wide Campaign</span>';
    document.getElementById('selected-campaign-info').innerHTML = `
        <h5>${campaignType}</h5>
        <p class="mb-0"><strong>${campaignName}</strong> - ${currency} ${parseFloat(ticketPrice).toFixed(2)} per ticket</p>
    `;
    
    updateTotalAmount();
    goToStep(3);
}

function updateTotalAmount() {
    if (selectedCampaign) {
        let quantity = parseInt(document.getElementById('ticket_count').value) || 1;
        let total = selectedCampaign.price * quantity;
        document.getElementById('total-amount-display').textContent = selectedCampaign.currency + ' ' + total.toFixed(2);
        
        // Update payment summary
        document.getElementById('payment-summary').innerHTML = `
            <h5>Payment Summary</h5>
            <p><strong>Campaign:</strong> ${selectedCampaign.name}</p>
            <p><strong>Tickets:</strong> ${quantity} × ${selectedCampaign.currency} ${selectedCampaign.price.toFixed(2)}</p>
            <p class="mb-0"><strong>Total:</strong> <span class="h4">${selectedCampaign.currency} ${total.toFixed(2)}</span></p>
        `;
    }
}

function selectPaymentMethod(method) {
    document.getElementById('payment_method').value = method;
    
    // Visual feedback
    document.querySelectorAll('.campaign-option').forEach(function(el) {
        el.classList.remove('selected');
    });
    event.currentTarget.classList.add('selected');
}

// Update total when quantity changes
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('ticket_count').addEventListener('input', updateTotalAmount);
});
</script>

</body>
</html>
