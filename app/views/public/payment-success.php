<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful - <?= APP_NAME ?></title>
    <link rel="stylesheet" href="<?= vendor('bootstrap/css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= vendor('fontawesome-free/css/all.min.css') ?>">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .success-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            padding: 40px;
            max-width: 600px;
            text-align: center;
        }
        .success-icon {
            font-size: 80px;
            color: #28a745;
            animation: scaleIn 0.5s ease-in-out;
        }
        @keyframes scaleIn {
            from { transform: scale(0); }
            to { transform: scale(1); }
        }
        .ticket-code {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            font-family: 'Courier New', monospace;
            font-size: 18px;
            font-weight: bold;
            color: #667eea;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="success-card">
        <i class="fas fa-check-circle success-icon"></i>
        <h1 class="mt-4 mb-3">Payment Successful!</h1>
        <p class="lead">Your tickets have been generated successfully.</p>
        
        <div class="alert alert-success">
            <strong>Payment Reference:</strong> <?= htmlspecialchars($payment->internal_reference ?? $payment->gateway_reference ?? 'N/A') ?><br>
            <strong>Amount Paid:</strong> <?= $payment->currency ?> <?= number_format($payment->amount, 2) ?><br>
            <strong>Tickets Generated:</strong> <?= count($tickets) ?>
        </div>

        <h4 class="mt-4 mb-3">Your Ticket Codes:</h4>
        <?php if (!empty($tickets)): ?>
            <div class="row">
                <?php foreach ($tickets as $ticket): ?>
                    <div class="col-md-12 mb-3">
                        <div class="card border-success">
                            <div class="card-body text-center">
                                <h4 class="text-success mb-2">
                                    <i class="fas fa-ticket-alt"></i>
                                </h4>
                                <h5 class="mb-2">
                                    <code><?= htmlspecialchars($ticket->ticket_code) ?></code>
                                </h5>
                                <p class="mb-0">
                                    <span class="badge badge-primary badge-lg">
                                        <?= $ticket->quantity ?? 1 ?> <?= ($ticket->quantity ?? 1) > 1 ? 'Entries' : 'Entry' ?>
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i> 
                No tickets found. Please contact support with reference: <?= htmlspecialchars($payment->internal_reference ?? $payment->gateway_reference ?? 'N/A') ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($tickets)): ?>
        <div class="alert alert-info mt-4">
            <i class="fas fa-info-circle"></i> 
            Your ticket codes have been sent via SMS
        </div>
        <?php endif; ?>

        <div class="mt-4">
            <a href="<?= url('public/buyTicket') ?>" class="btn btn-primary btn-lg mr-2">
                <i class="fas fa-ticket-alt"></i> Buy More Tickets
            </a>
            <a href="<?= url('public') ?>" class="btn btn-outline-primary btn-lg">
                <i class="fas fa-home"></i> Home
            </a>
        </div>

        <div class="mt-4 text-muted">
            <small>
                <i class="fas fa-trophy"></i> Good luck in the draw!<br>
                Winners will be announced on the draw date.
            </small>
        </div>
    </div>

    <script src="<?= vendor('jquery/jquery.min.js') ?>"></script>
    <script src="<?= vendor('bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
</body>
</html>
