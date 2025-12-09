<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    
    <?php if (isset($seo_meta)): ?>
        <?php 
        require_once '../app/helpers/SeoHelper.php';
        echo \App\Helpers\SeoHelper::renderMetaTags($seo_meta);
        echo \App\Helpers\SeoHelper::generateStructuredData($seo_meta['campaign']);
        ?>
    <?php else: ?>
        <title>Buy Tickets | eTickets Raffle</title>
        <meta name="description" content="Buy raffle tickets online and win amazing prizes! Transparent draws, instant winners, secure payments.">
        <meta name="keywords" content="raffle, lottery, win prizes, Ghana raffle, online raffle, buy tickets">
    <?php endif; ?>
    
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
                <h4 class="mb-4">Step 1: Select Platform</h4>
                
                <div class="form-group">
                    <label for="station_id">Choose a Platform <span class="text-danger">*</span></label>
                    <select name="station_id" id="station_id" class="form-control form-control-lg" required>
                        <option value="">-- Select a Platform --</option>
                        <?php foreach ($stations as $station): ?>
                            <option value="<?= $station->id ?>" data-name="<?= htmlspecialchars($station->name) ?>">
                                <?= htmlspecialchars($station->name) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="mt-4">
                    <button type="button" class="btn btn-next float-right" id="station-next-btn" disabled>
                        Continue <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
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
                    <h5 class="mb-3">Select Campaign:</h5>
                    <div class="form-group">
                        <select name="station_campaign_select" id="station_campaign_select" class="form-control form-control-lg">
                            <option value="">-- Select a campaign --</option>
                        </select>
                        <small class="form-text text-muted" id="station-campaign-price"></small>
                    </div>
                </div>
                
                <!-- Programme campaigns section -->
                <div id="programme-campaigns-section" class="hidden">
                    <div id="programme-selection">
                        <h5 class="mb-3">Select Programme:</h5>
                        <div class="form-group">
                            <select name="programme_id_select" id="programme_id_select" class="form-control form-control-lg">
                                <option value="">-- Select a programme --</option>
                            </select>
                        </div>
                    </div>
                    
                    <div id="programme-campaigns-container" class="hidden mt-4">
                        <h5 class="mb-3">Select Campaign:</h5>
                        <div class="form-group">
                            <select name="programme_campaign_select" id="programme_campaign_select" class="form-control form-control-lg">
                                <option value="">-- First select a programme --</option>
                            </select>
                            <small class="form-text text-muted" id="programme-campaign-price"></small>
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
                    <label for="phone">Phone Number <span class="text-danger">*</span></label>
                    <input type="tel" class="form-control form-control-lg" id="phone" name="phone" placeholder="0241234567" required pattern="[0-9]{10}" minlength="10" maxlength="10">
                    <small class="form-text text-muted">Enter your 10-digit mobile money number (e.g., 0241234567)</small>
                </div>
                
                <div class="form-group">
                    <label for="ticket_count">Number of Tickets</label>
                    <input type="number" class="form-control form-control-lg" id="ticket_count" name="ticket_count" min="1" value="1" required>
                    <small class="form-text text-muted">Price per ticket: <span id="ticket-price-display">GHS 0.00</span></small>
                </div>
                
                <div class="alert alert-warning">
                    <i class="fas fa-trophy"></i> <strong>Pro Tip:</strong> Buy more tickets to increase your chances of winning! The more tickets you have, the higher your odds!
                </div>
                
                <div class="alert alert-success">
                    <strong>Total Amount:</strong> <span id="total-amount-display" class="h4">GHS 0.00</span>
                </div>
                
                <div class="mt-4">
                    <button type="button" class="btn btn-back" onclick="goToStep(2)">
                        <i class="fas fa-arrow-left"></i> Back
                    </button>
                    <button type="button" class="btn btn-next float-right" onclick="validateStep3()">
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

<!-- Validation Modal -->
<div class="modal fade" id="validationModal" tabindex="-1" role="dialog" aria-labelledby="validationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="background: rgba(15, 15, 35, 0.95); border: 2px solid rgba(102, 126, 234, 0.5); border-radius: 20px;">
            <div class="modal-header" style="border-bottom: 1px solid rgba(102, 126, 234, 0.3);">
                <h5 class="modal-title" id="validationModalLabel" style="color: #f093fb;">
                    <i class="fas fa-exclamation-circle"></i> <span id="modalTitle">Validation Error</span>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: #fff; opacity: 0.8;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="color: rgba(255,255,255,0.9);">
                <p id="modalMessage"></p>
            </div>
            <div class="modal-footer" style="border-top: 1px solid rgba(102, 126, 234, 0.3);">
                <button type="button" class="btn btn-primary" data-dismiss="modal" style="background: linear-gradient(135deg, #667eea, #764ba2); border: none;">
                    <i class="fas fa-check"></i> Got it!
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Confirmation Modal -->
<div class="modal fade" id="confirmationModal" tabindex="-1" role="dialog" aria-labelledby="confirmationModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content" style="background: linear-gradient(135deg, rgba(15, 15, 35, 0.98), rgba(30, 20, 50, 0.98)); border: 3px solid rgba(240, 147, 251, 0.6); border-radius: 25px; box-shadow: 0 0 40px rgba(240, 147, 251, 0.4);">
            <div class="modal-header" style="border-bottom: 2px solid rgba(240, 147, 251, 0.3); background: rgba(102, 126, 234, 0.1);">
                <h4 class="modal-title" id="confirmationModalLabel" style="color: #f093fb; font-weight: 800;">
                    <i class="fas fa-hand-paper" style="color: #ffd700;"></i> <span id="confirmTitle">Wait! Before You Continue...</span>
                </h4>
            </div>
            <div class="modal-body text-center" style="color: rgba(255,255,255,0.95); padding: 2rem;">
                <div id="confirmMessage"></div>
            </div>
            <div class="modal-footer" style="border-top: 2px solid rgba(240, 147, 251, 0.3); justify-content: center; padding: 1.5rem;">
                <button type="button" class="btn btn-lg" id="goBackBtn" style="background: linear-gradient(135deg, #10b981, #059669); border: none; padding: 12px 30px; font-weight: 700; box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4);">
                    <i class="fas fa-plus-circle"></i> Yes, Add More Tickets!
                </button>
                <button type="button" class="btn btn-lg" id="continueBtn" style="background: linear-gradient(135deg, #667eea, #764ba2); border: none; padding: 12px 30px; font-weight: 700; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);">
                    <i class="fas fa-arrow-right"></i> No, Continue to Payment
                </button>
            </div>
        </div>
    </div>
</div>

<script src="<?= vendor('jquery/jquery.min.js') ?>"></script>
<script src="<?= vendor('bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
<script>
let selectedStationId = null;
let selectedStationName = null;
let selectedCampaign = null;
let currentStep = 1;
let currentCampaignType = 'station';

// Helper function to escape HTML for safe display
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Helper function to format currency
function formatCurrency(amount, currency = 'GHS') {
    const numAmount = parseFloat(amount);
    if (isNaN(numAmount)) return `${currency} 0.00`;
    return `${currency} ${numAmount.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
}

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

// Handle station selection
$('#station_id').on('change', function() {
    const stationId = $(this).val();
    const stationName = $(this).find('option:selected').data('name');
    
    if (stationId) {
        $('#station-next-btn').prop('disabled', false);
        selectedStationId = stationId;
        selectedStationName = stationName;
    } else {
        $('#station-next-btn').prop('disabled', true);
        selectedStationId = null;
        selectedStationName = null;
    }
});

// Handle station next button
$('#station-next-btn').on('click', function() {
    if (selectedStationId) {
        // Reset campaign type to station-wide
        currentCampaignType = 'station';
        $('input[name="campaign_type_choice"][value="station"]').prop('checked', true).parent().addClass('active');
        $('input[name="campaign_type_choice"][value="programme"]').prop('checked', false).parent().removeClass('active');
        $('#station-campaigns-section').show();
        $('#programme-campaigns-section').hide();
        
        // Load station-wide campaigns
        loadStationCampaigns();
        goToStep(2);
    }
});

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
        if (selectedStationId && $('#programme_id_select option').length <= 1) {
            loadProgrammes();
        }
    }
});

function loadStationCampaigns(callback) {
    $.ajax({
        url: '<?= url('public/getCampaignsByStation') ?>/' + selectedStationId,
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                displayStationCampaigns(response.campaigns, selectedStationName);
                if (callback) callback();
            }
        },
        error: function() {
            alert('Error loading campaigns');
        }
    });
}

function displayStationCampaigns(campaigns, stationName) {
    const select = $('#station_campaign_select');
    select.html('<option value="">-- Select a campaign --</option>');
    
    if (campaigns.length === 0) {
        select.html('<option value="">No station-wide campaigns available</option>');
    } else {
        campaigns.forEach(function(campaign) {
            const optionText = `${campaign.name} - ${formatCurrency(campaign.ticket_price, campaign.currency)}`;
            const option = $('<option></option>')
                .val(campaign.id)
                .text(optionText)
                .data('name', campaign.name)
                .data('price', campaign.ticket_price)
                .data('currency', campaign.currency);
            select.append(option);
        });
    }
}

// Handle station campaign selection from dropdown
$('#station_campaign_select').on('change', function() {
    const campaignId = $(this).val();
    const option = $(this).find('option:selected');
    
    if (campaignId) {
        const campaignName = option.data('name');
        const ticketPrice = option.data('price');
        const currency = option.data('currency');
        
        $('#station-campaign-price').text(`Price per ticket: ${formatCurrency(ticketPrice, currency)}`);
        selectCampaign(campaignId, campaignName, ticketPrice, currency, null);
    } else {
        $('#station-campaign-price').text('');
    }
});

function loadProgrammes(callback) {
    $.ajax({
        url: '<?= url('public/getProgrammesByStation') ?>/' + selectedStationId,
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                displayProgrammes(response.programmes);
                if (callback) callback();
            }
        },
        error: function() {
            alert('Error loading programmes');
        }
    });
}

// Handle programme selection from dropdown
$('#programme_id_select').on('change', function() {
    const programmeId = $(this).val();
    const programmeName = $(this).find('option:selected').text();
    
    if (programmeId) {
        loadProgrammeCampaigns(programmeId, programmeName);
    } else {
        $('#programme-campaigns-container').addClass('hidden');
        $('#programme_campaign_select').html('<option value="">-- First select a programme --</option>');
        $('#programme-campaign-price').text('');
    }
});

function loadProgrammeCampaigns(programmeId, programmeName) {
    $.ajax({
        url: '<?= url('public/getCampaignsByProgramme') ?>/' + programmeId,
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                displayProgrammeCampaigns(response.campaigns, programmeName, programmeId);
                $('#programme-campaigns-container').removeClass('hidden');
            }
        },
        error: function() {
            alert('Error loading campaigns');
        }
    });
}

function displayProgrammes(programmes) {
    const select = $('#programme_id_select');
    select.html('<option value="">-- Select a programme --</option>');
    
    if (programmes.length === 0) {
        select.html('<option value="">No programmes available</option>');
    } else {
        programmes.forEach(function(programme) {
            select.append(`<option value="${programme.id}">${programme.name}</option>`);
        });
    }
}


function displayProgrammeCampaigns(campaigns, programmeName, programmeId) {
    const select = $('#programme_campaign_select');
    select.html('<option value="">-- Select a campaign --</option>');
    
    if (campaigns.length === 0) {
        select.html('<option value="">No campaigns available for this programme</option>');
    } else {
        campaigns.forEach(function(campaign) {
            const optionText = `${campaign.name} - ${formatCurrency(campaign.ticket_price, campaign.currency)}`;
            const option = $('<option></option>')
                .val(campaign.id)
                .text(optionText)
                .data('name', campaign.name)
                .data('price', campaign.ticket_price)
                .data('currency', campaign.currency)
                .data('programme', programmeId);
            select.append(option);
        });
    }
    
    $('#programme-campaigns-container').removeClass('hidden');
}

// Handle programme campaign selection from dropdown
$('#programme_campaign_select').on('change', function() {
    const campaignId = $(this).val();
    const option = $(this).find('option:selected');
    
    if (campaignId) {
        const campaignName = option.data('name');
        const ticketPrice = option.data('price');
        const currency = option.data('currency');
        const programmeId = option.data('programme');
        
        $('#programme-campaign-price').text(`Price per ticket: ${formatCurrency(ticketPrice, currency)}`);
        selectCampaign(campaignId, campaignName, ticketPrice, currency, programmeId);
    } else {
        $('#programme-campaign-price').text('');
    }
});

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
    document.getElementById('ticket-price-display').textContent = formatCurrency(ticketPrice, currency);
    
    // Display selected campaign info
    let campaignType = programmeId ? '<span class="badge badge-info">Programme Campaign</span>' : '<span class="badge badge-primary">Station-Wide Campaign</span>';
    document.getElementById('selected-campaign-info').innerHTML = `
        <h5>${campaignType}</h5>
        <p class="mb-0"><strong>${escapeHtml(campaignName)}</strong> - ${formatCurrency(ticketPrice, currency)} per ticket</p>
    `;
    
    updateTotalAmount();
    goToStep(3);
}

function updateTotalAmount() {
    if (selectedCampaign) {
        let quantity = parseInt(document.getElementById('ticket_count').value) || 1;
        let total = selectedCampaign.price * quantity;
        document.getElementById('total-amount-display').textContent = formatCurrency(total, selectedCampaign.currency);
        
        // Update payment summary
        document.getElementById('payment-summary').innerHTML = `
            <h5>Payment Summary</h5>
            <p><strong>Campaign:</strong> ${escapeHtml(selectedCampaign.name)}</p>
            <p><strong>Tickets:</strong> ${quantity} × ${formatCurrency(selectedCampaign.price, selectedCampaign.currency)}</p>
            <p class="mb-0"><strong>Total:</strong> <span class="h4">${formatCurrency(total, selectedCampaign.currency)}</span></p>
        `;
    }
}

// Update total when quantity changes
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('ticket_count').addEventListener('input', updateTotalAmount);
});

// Show validation modal
function showValidationModal(title, message, focusElement) {
    $('#modalTitle').text(title);
    $('#modalMessage').html(message);
    $('#validationModal').modal('show');
    
    // Focus on the element when modal is closed
    $('#validationModal').on('hidden.bs.modal', function () {
        if (focusElement) {
            document.getElementById(focusElement).focus();
        }
    });
}

// Show confirmation modal with dynamic messaging based on ticket count
function showConfirmationModal(ticketCount) {
    let message = '';
    let title = 'Wait! Before You Continue...';
    
    if (ticketCount === 1) {
        title = '⚠️ Only 1 Ticket? Think Again!';
        message = `
            <div style="animation: pulse 2s infinite;">
                <i class="fas fa-exclamation-triangle fa-4x mb-3" style="color: #ffd700;"></i>
            </div>
            <h3 style="color: #f093fb; font-weight: 800; margin-bottom: 20px;">You're Buying Only 1 Ticket!</h3>
            <p style="font-size: 1.1em; line-height: 1.8;">With just <strong style="color: #ff6b6b;">1 ticket</strong>, your chances of winning are <strong style="color: #ff6b6b;">very low</strong>.</p>
            <div class="alert" style="background: rgba(255, 107, 107, 0.2); border: 2px solid rgba(255, 107, 107, 0.5); margin: 20px 0; padding: 20px;">
                <i class="fas fa-chart-line fa-2x mb-2" style="color: #ffd700;"></i>
                <h5 style="color: #fff; margin-top: 10px;">Increase Your Odds!</h5>
                <p style="margin: 10px 0;">📊 <strong>5 tickets</strong> = 5x better chance<br>📊 <strong>10 tickets</strong> = 10x better chance<br>📊 <strong>20 tickets</strong> = 20x better chance</p>
            </div>
            <p style="font-size: 1.2em; color: #10b981; font-weight: 700; margin-top: 20px;">
                <i class="fas fa-trophy"></i> Winners usually buy multiple tickets!
            </p>
            <style>
                @keyframes pulse {
                    0%, 100% { transform: scale(1); }
                    50% { transform: scale(1.1); }
                }
            </style>
        `;
    } else if (ticketCount >= 2 && ticketCount <= 4) {
        title = '💡 Good Start! Want Even Better Odds?';
        message = `
            <i class="fas fa-thumbs-up fa-4x mb-3" style="color: #667eea;"></i>
            <h3 style="color: #f093fb; font-weight: 800; margin-bottom: 20px;">You're Buying ${ticketCount} Tickets</h3>
            <p style="font-size: 1.1em; line-height: 1.8;">That's a good start! But you could <strong style="color: #10b981;">double or triple</strong> your chances with just a few more tickets.</p>
            <div class="alert" style="background: rgba(102, 126, 234, 0.2); border: 2px solid rgba(102, 126, 234, 0.5); margin: 20px 0; padding: 20px;">
                <i class="fas fa-lightbulb fa-2x mb-2" style="color: #ffd700;"></i>
                <h5 style="color: #fff; margin-top: 10px;">Smart Players Buy More!</h5>
                <p style="margin: 10px 0;">🎯 Current odds: <strong>${ticketCount}x</strong><br>🎯 With 10 tickets: <strong>10x better!</strong><br>🎯 With 20 tickets: <strong>20x better!</strong></p>
            </div>
            <p style="font-size: 1.1em; color: #ffd700; font-weight: 700;">
                <i class="fas fa-star"></i> Small investment, BIG potential return!
            </p>
        `;
    } else if (ticketCount >= 5 && ticketCount <= 9) {
        title = '🎉 Great Choice! Go Even Bigger?';
        message = `
            <i class="fas fa-fire fa-4x mb-3" style="color: #ff6b6b;"></i>
            <h3 style="color: #f093fb; font-weight: 800; margin-bottom: 20px;">You're Buying ${ticketCount} Tickets - Nice!</h3>
            <p style="font-size: 1.1em; line-height: 1.8;">You're on the right track! Many winners bought <strong style="color: #10b981;">10-20 tickets</strong> to maximize their chances.</p>
            <div class="alert" style="background: rgba(16, 185, 129, 0.2); border: 2px solid rgba(16, 185, 129, 0.5); margin: 20px 0; padding: 20px;">
                <i class="fas fa-trophy fa-2x mb-2" style="color: #ffd700;"></i>
                <h5 style="color: #fff; margin-top: 10px;">You're Almost There!</h5>
                <p style="margin: 10px 0;">🏆 Round up to <strong>10 tickets</strong> for double-digit odds!<br>🏆 Or go for <strong>20 tickets</strong> to be a serious contender!</p>
            </div>
            <p style="font-size: 1.1em; color: #10b981; font-weight: 700;">
                <i class="fas fa-gem"></i> You're so close to optimal odds!
            </p>
        `;
    } else {
        title = '🔥 Excellent! You\'re a Serious Player!';
        message = `
            <i class="fas fa-crown fa-4x mb-3" style="color: #ffd700;"></i>
            <h3 style="color: #10b981; font-weight: 800; margin-bottom: 20px;">You're Buying ${ticketCount} Tickets - Fantastic!</h3>
            <p style="font-size: 1.1em; line-height: 1.8;">You're playing smart! With <strong style="color: #10b981;">${ticketCount} tickets</strong>, you have a <strong>serious chance</strong> of winning!</p>
            <div class="alert" style="background: rgba(255, 215, 0, 0.2); border: 2px solid rgba(255, 215, 0, 0.5); margin: 20px 0; padding: 20px;">
                <i class="fas fa-medal fa-2x mb-2" style="color: #ffd700;"></i>
                <h5 style="color: #ffd700; margin-top: 10px;">Winner's Mindset!</h5>
                <p style="margin: 10px 0; color: #fff;">✨ You're in the <strong>top tier</strong> of players<br>✨ Your odds are <strong>${ticketCount}x better</strong> than single-ticket buyers<br>✨ This is how champions play!</p>
            </div>
            <p style="font-size: 1.1em; color: #ffd700; font-weight: 700;">
                <i class="fas fa-rocket"></i> Ready to claim your prize? Let's go!
            </p>
        `;
    }
    
    $('#confirmTitle').html(title);
    $('#confirmMessage').html(message);
    
    // Handle button clicks
    $('#goBackBtn').off('click').on('click', function() {
        $('#confirmationModal').modal('hide');
        document.getElementById('ticket_count').focus();
        document.getElementById('ticket_count').select();
    });
    
    $('#continueBtn').off('click').on('click', function() {
        $('#confirmationModal').modal('hide');
        goToStep(4);
    });
    
    $('#confirmationModal').modal('show');
}

// Validate Step 3 before proceeding to Step 4
function validateStep3() {
    const phone = document.getElementById('phone').value.trim();
    const ticketCount = parseInt(document.getElementById('ticket_count').value);
    
    // Check if phone is empty
    if (!phone) {
        showValidationModal(
            'Phone Number Required',
            '<i class="fas fa-phone fa-2x mb-3" style="color: #f093fb;"></i><br>Please enter your mobile money number for payments and notifications!',
            'phone'
        );
        return false;
    }
    
    // Validate phone number format (10 digits)
    const phonePattern = /^[0-9]{10}$/;
    if (!phonePattern.test(phone)) {
        showValidationModal(
            'Invalid Phone Number',
            '<i class="fas fa-mobile-alt fa-2x mb-3" style="color: #f093fb;"></i><br>Please enter a valid 10-digit Mobile Money phone number.<br><small class="text-muted">Example: 0241234567</small>',
            'phone'
        );
        return false;
    }
    
    // Validate ticket count
    if (!ticketCount || ticketCount < 1) {
        showValidationModal(
            'Ticket Count Required',
            '<i class="fas fa-ticket-alt fa-2x mb-3" style="color: #f093fb;"></i><br>Please enter the number of tickets you want to purchase. Remember, more tickets = higher chances of winning!',
            'ticket_count'
        );
        return false;
    }
    
    // Show confirmation modal before proceeding to payment
    showConfirmationModal(ticketCount);
}

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
    
    // Validate phone number format (10 digits)
    const phonePattern = /^[0-9]{10}$/;
    if (!phonePattern.test(phone)) {
        alert('Please enter a valid 10-digit phone number');
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

// Get URL parameter
function getUrlParameter(name) {
    name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
    const regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
    const results = regex.exec(location.search);
    return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
}

// Pre-select campaign if campaign ID is in URL
function preselectCampaign(campaignId) {
    if (!campaignId) return;
    
    // Fetch campaign details to get station info
    $.ajax({
        url: '<?= url('public/getCampaignDetails') ?>/' + campaignId,
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success && response.campaign) {
                const campaign = response.campaign;
                
                // Select the station
                $('#station_id').val(campaign.station_id);
                selectedStationId = campaign.station_id;
                selectedStationName = campaign.station_name;
                $('#station-next-btn').prop('disabled', false);
                
                // Automatically go to step 2
                setTimeout(function() {
                    // Determine if it's a station-wide or programme campaign
                    if (campaign.programme_id) {
                        // Programme campaign
                        currentCampaignType = 'programme';
                        $('input[name="campaign_type_choice"][value="programme"]').prop('checked', true).parent().addClass('active');
                        $('input[name="campaign_type_choice"][value="station"]').prop('checked', false).parent().removeClass('active');
                        $('#station-campaigns-section').hide();
                        $('#programme-campaigns-section').show();
                        
                        // Load programmes and then select the campaign
                        loadProgrammes(function() {
                            $('#programme_id_select').val(campaign.programme_id).trigger('change');
                            
                            // Wait for campaigns to load, then select the specific campaign
                            setTimeout(function() {
                                $('#programme_campaign_select').val(campaignId).trigger('change');
                            }, 500);
                        });
                    } else {
                        // Station-wide campaign
                        currentCampaignType = 'station';
                        $('input[name="campaign_type_choice"][value="station"]').prop('checked', true).parent().addClass('active');
                        $('input[name="campaign_type_choice"][value="programme"]').prop('checked', false).parent().removeClass('active');
                        $('#station-campaigns-section').show();
                        $('#programme-campaigns-section').hide();
                        
                        // Load station campaigns and then select the specific one
                        loadStationCampaigns(function() {
                            $('#station_campaign_select').val(campaignId).trigger('change');
                        });
                    }
                    
                    goToStep(2);
                }, 300);
            }
        },
        error: function() {
            console.error('Error loading campaign details for pre-selection');
        }
    });
}

// Initialize on page load
$(document).ready(function() {
    // Enable/disable station next button based on selection
    if ($('#station_id').val()) {
        $('#station-next-btn').prop('disabled', false);
    }
    
    // Check for campaign parameter and pre-select
    const campaignId = getUrlParameter('campaign');
    if (campaignId) {
        preselectCampaign(campaignId);
    }
});
</script>

</body>
</html>
