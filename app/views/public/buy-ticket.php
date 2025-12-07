<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>Buy Tickets | eTickets Raffle</title>
    
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
            background: #0f0f23;
            min-height: 100vh;
            padding-bottom: 80px;
            color: #fff;
            position: relative;
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
            opacity: 0.1;
            z-index: -1;
        }
        
        /* Navbar */
        .navbar-custom {
            background: rgba(15, 15, 35, 0.95);
            backdrop-filter: blur(20px);
            border-bottom: 2px solid rgba(102, 126, 234, 0.3);
            padding: 1rem 0;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
        }
        
        .navbar-brand {
            font-size: 1.8rem;
            font-weight: 900;
            background: linear-gradient(135deg, #667eea, #f093fb);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .nav-link {
            color: rgba(255,255,255,0.8) !important;
            font-weight: 500;
            transition: all 0.3s;
        }

        .nav-link:hover {
            color: #f093fb !important;
        }
        
        /* Container */
        .buy-ticket-card {
            background: rgba(15, 15, 35, 0.8);
            backdrop-filter: blur(20px);
            border: 2px solid rgba(102, 126, 234, 0.3);
            border-radius: 25px;
            margin: 100px 16px 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
            position: relative;
        }

        .buy-ticket-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(90deg, #667eea, #764ba2, #f093fb);
        }
        
        @media (min-width: 768px) {
            .buy-ticket-card {
                max-width: 700px;
                margin: 120px auto 40px;
            }
        }

        .buy-ticket-card h2 {
            background: linear-gradient(135deg, #fff, #f093fb);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-weight: 900;
            padding: 2rem 1rem 1rem;
        }
        
        /* Step indicator */
        .step-indicator {
            display: flex;
            padding: 20px 16px;
            background: rgba(102, 126, 234, 0.1);
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            border-bottom: 1px solid rgba(102, 126, 234, 0.2);
        }
        
        .step {
            flex: 1;
            min-width: 60px;
            text-align: center;
            padding: 8px 4px;
            position: relative;
        }
        
        .step-number {
            width: 40px;
            height: 40px;
            line-height: 40px;
            border-radius: 50%;
            background: rgba(255,255,255,0.1);
            color: rgba(255,255,255,0.5);
            font-weight: 700;
            font-size: 1rem;
            margin: 0 auto 8px;
            display: block;
            border: 2px solid rgba(255,255,255,0.2);
            transition: all 0.3s;
        }
        
        .step-label {
            font-size: 0.75rem;
            color: rgba(255,255,255,0.6);
            font-weight: 500;
        }
        
        .step.active .step-number {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border-color: #f093fb;
            box-shadow: 0 0 20px rgba(102, 126, 234, 0.5);
            transform: scale(1.1);
        }
        
        .step.active .step-label {
            color: #f093fb;
            font-weight: 700;
        }
        
        .step.completed .step-number {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            border-color: #10b981;
        }
        
        .step.completed .step-label {
            color: #10b981;
        }
        
        /* Content area */
        .step-content {
            padding: 24px 16px;
        }
        
        @media (min-width: 768px) {
            .step-content {
                padding: 32px 40px;
            }
        }
        
        h4 {
            font-size: 1.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, #fff, #f093fb);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 20px;
        }

        h5 {
            color: #fff;
            font-weight: 600;
        }
        
        /* Campaign options */
        .campaign-option {
            border: 2px solid rgba(102, 126, 234, 0.3);
            border-radius: 15px;
            padding: 1.25rem;
            margin-bottom: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            background: rgba(102, 126, 234, 0.05);
            backdrop-filter: blur(10px);
        }
        
        .campaign-option:active {
            transform: scale(0.98);
        }
        
        .campaign-option:hover {
            border-color: #f093fb;
            box-shadow: 0 8px 25px rgba(240, 147, 251, 0.3);
            transform: translateY(-3px);
            background: rgba(102, 126, 234, 0.1);
        }
        
        .campaign-option.selected {
            border-color: #f093fb;
            background: rgba(240, 147, 251, 0.15);
            box-shadow: 0 0 30px rgba(240, 147, 251, 0.4);
        }
        
        /* Badges */
        .campaign-type-badge {
            display: inline-block;
            padding: 0.4rem 1rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .badge-station-wide {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }
        
        .badge-programme {
            background: linear-gradient(135deg, #f093fb, #f5576c);
            color: white;
            box-shadow: 0 4px 12px rgba(240, 147, 251, 0.3);
        }
        
        /* Form elements */
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            font-weight: 600;
            font-size: 0.9rem;
            color: rgba(255,255,255,0.9);
            margin-bottom: 0.5rem;
            display: block;
        }
        
        .form-control-lg {
            height: 52px;
            font-size: 1rem;
            border-radius: 12px;
            border: 2px solid rgba(102, 126, 234, 0.3);
            padding: 12px 16px;
            background: rgba(255,255,255,0.05);
            color: #fff;
            transition: all 0.3s;
        }

        .form-control-lg::placeholder {
            color: rgba(255,255,255,0.4);
        }
        
        .form-control-lg:focus {
            border-color: #f093fb;
            box-shadow: 0 0 0 3px rgba(240, 147, 251, 0.2);
            background: rgba(255,255,255,0.08);
            outline: none;
        }
        
        /* Buttons */
        .btn-next, .btn-back, .btn-success {
            border: none;
            padding: 1rem 2rem;
            font-size: 1rem;
            border-radius: 12px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
            margin-top: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        @media (min-width: 768px) {
            .btn-next, .btn-back, .btn-success {
                width: auto;
                min-width: 160px;
            }
        }
        
        .btn-next {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
        }

        .btn-next:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 30px rgba(102, 126, 234, 0.5);
        }
        
        .btn-next:active {
            transform: scale(0.98);
        }
        
        .btn-back {
            background: rgba(255,255,255,0.1);
            color: white;
            border: 2px solid rgba(255,255,255,0.2);
        }

        .btn-back:hover {
            background: rgba(255,255,255,0.15);
            transform: translateY(-2px);
        }
        
        .btn-success {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            box-shadow: 0 8px 20px rgba(16, 185, 129, 0.4);
        }

        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 30px rgba(16, 185, 129, 0.5);
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
            border: 2px solid rgba(102, 126, 234, 0.3);
            border-radius: 12px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 600;
            font-size: 0.875rem;
            background: rgba(255,255,255,0.05);
            color: rgba(255,255,255,0.7);
        }

        .btn-group-toggle label:hover {
            border-color: #f093fb;
            background: rgba(255,255,255,0.1);
        }
        
        .btn-group-toggle label.active {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border-color: #f093fb;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }
        
        /* Alert boxes */
        .alert {
            border-radius: 15px;
            padding: 1.25rem;
            margin-bottom: 1.25rem;
            border: 2px solid;
            backdrop-filter: blur(10px);
        }
        
        .alert-info {
            background: rgba(102, 126, 234, 0.15);
            color: #a5b4fc;
            border-color: rgba(102, 126, 234, 0.3);
        }
        
        .alert-success {
            background: rgba(16, 185, 129, 0.15);
            color: #6ee7b7;
            border-color: rgba(16, 185, 129, 0.3);
        }

        .alert-warning {
            background: rgba(251, 191, 36, 0.15);
            color: #fcd34d;
            border-color: rgba(251, 191, 36, 0.3);
        }

        .alert-primary {
            background: rgba(240, 147, 251, 0.15);
            color: #f9a8d4;
            border-color: rgba(240, 147, 251, 0.3);
        }
        
        /* Utility */
        .hidden {
            display: none;
        }
        
        .text-muted {
            color: rgba(255,255,255,0.6) !important;
            font-size: 0.875rem;
        }

        .text-primary {
            color: #667eea !important;
        }

        .form-text {
            color: rgba(255,255,255,0.6);
        }
        
        /* Loading state */
        .loading {
            opacity: 0.6;
            pointer-events: none;
        }

        /* Navbar toggler for dark theme */
        .navbar-toggler {
            border-color: rgba(255,255,255,0.3);
        }

        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba(255, 255, 255, 0.8)' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light navbar-custom fixed-top">
    <div class="container">
        <a class="navbar-brand" href="<?= url('public') ?>">
            <i class="fas fa-ticket"></i>
            eTickets Raffle
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
                <div class="step-label">Station</div>
            </div>
            <div class="step" id="step2-indicator">
                <div class="step-number">2</div>
                <div class="step-label">Campaign</div>
            </div>
            <div class="step" id="step3-indicator">
                <div class="step-number">3</div>
                <div class="step-label">Details</div>
            </div>
            <div class="step" id="step4-indicator">
                <div class="step-number">4</div>
                <div class="step-label">Payment</div>
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
                <h4 class="mb-4">Step 4: Confirm Payment</h4>
                
                <div id="payment-summary" class="alert alert-primary">
                    <!-- Payment summary will be displayed here -->
                </div>
                
                <div class="alert alert-info">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-mobile-alt fa-2x text-primary mr-3"></i>
                        <div>
                            <h5 class="mb-1">Mobile Money Payment</h5>
                            <small class="text-muted">You will receive a prompt on your phone to approve the payment</small>
                        </div>
                    </div>
                </div>
                
                <!-- Payment method auto-selected -->
                <input type="hidden" name="payment_method" id="payment_method" value="hubtel" required>
                
                <div class="mt-4">
                    <button type="button" class="btn btn-back" onclick="goToStep(3)">
                        <i class="fas fa-arrow-left"></i> Back
                    </button>
                    <button type="submit" class="btn btn-success btn-lg float-right" onclick="return validateForm()">
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

// Update total when quantity changes
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('ticket_count').addEventListener('input', updateTotalAmount);
});

// Validate form before submission
function validateForm() {
    const stationId = document.getElementById('station_id').value;
    const campaignId = document.getElementById('campaign_id').value;
    const phone = document.getElementById('phone').value;
    const ticketCount = document.getElementById('ticket_count').value;
    
    if (!stationId) {
        alert('Please select a station');
        goToStep(1);
        return false;
    }
    
    if (!campaignId) {
        alert('Please select a campaign');
        goToStep(2);
        return false;
    }
    
    if (!phone) {
        alert('Please enter your phone number');
        goToStep(3);
        return false;
    }
    
    if (!ticketCount || ticketCount < 1) {
        alert('Please enter a valid number of tickets');
        goToStep(3);
        return false;
    }
    
    return true;
}
</script>

</body>
</html>
