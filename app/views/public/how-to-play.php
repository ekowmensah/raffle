<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>How to Play | Raffle System</title>
    
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
        .content-section {
            background: white;
            border-radius: 20px;
            padding: 50px;
            margin-top: 100px;
            margin-bottom: 50px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        .step-card {
            background: #f8f9fa;
            border-left: 5px solid #667eea;
            padding: 30px;
            margin-bottom: 30px;
            border-radius: 10px;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light navbar-custom fixed-top">
    <div class="container">
        <a class="navbar-brand" href="<?= url('public') ?>">
            <i class="fas fa-trophy text-warning"></i>
            <strong>Raffle System</strong>
        </a>
        <div class="ml-auto">
            <a href="<?= url('public') ?>" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left"></i> Back to Home
            </a>
        </div>
    </div>
</nav>

<div class="container">
    <div class="content-section">
        <h1 class="text-center mb-5"><i class="fas fa-question-circle text-primary"></i> How to Play</h1>

        <div class="step-card">
            <h3><i class="fas fa-search text-primary"></i> Step 1: Choose a Campaign</h3>
            <p>Browse through our active raffle campaigns and select the one you want to participate in. Each campaign has different ticket prices and prize pools.</p>
        </div>

        <div class="step-card">
            <h3><i class="fas fa-shopping-cart text-success"></i> Step 2: Buy Tickets</h3>
            <p>Purchase raffle tickets using any of these methods:</p>
            <ul>
                <li><strong>Mobile Money:</strong> Dial the USSD code displayed for your network</li>
                <li><strong>Online Payment:</strong> Use your debit/credit card</li>
                <li><strong>Bank Transfer:</strong> Transfer to the provided account</li>
            </ul>
            <p>You'll receive an SMS confirmation with your unique ticket number(s).</p>
        </div>

        <div class="step-card">
            <h3><i class="fas fa-clock text-warning"></i> Step 3: Wait for the Draw</h3>
            <p>Draws are conducted:</p>
            <ul>
                <li><strong>Daily Draws:</strong> If enabled, daily winners are selected</li>
                <li><strong>Final Draw:</strong> Grand prize winner is selected at campaign end</li>
            </ul>
            <p>All draws are conducted transparently and winners are notified immediately.</p>
        </div>

        <div class="step-card">
            <h3><i class="fas fa-trophy text-danger"></i> Step 4: Claim Your Prize</h3>
            <p>If you win:</p>
            <ul>
                <li>You'll receive an SMS notification</li>
                <li>Follow the instructions to claim your prize</li>
                <li>Prizes are paid directly to your mobile money account or bank</li>
            </ul>
        </div>

        <div class="alert alert-info mt-5">
            <h4><i class="fas fa-info-circle"></i> Important Information</h4>
            <ul>
                <li>You must be 18 years or older to participate</li>
                <li>Keep your ticket numbers safe</li>
                <li>Winners are selected randomly using a certified system</li>
                <li>Prize distribution is automatic and transparent</li>
                <li>Check our Terms & Conditions for full details</li>
            </ul>
        </div>

        <div class="text-center mt-5">
            <a href="<?= url('public') ?>" class="btn btn-primary btn-lg">
                <i class="fas fa-play-circle"></i> Start Playing Now
            </a>
        </div>
    </div>
</div>

<script src="<?= vendor('jquery/jquery.min.js') ?>"></script>
<script src="<?= vendor('bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
</body>
</html>
