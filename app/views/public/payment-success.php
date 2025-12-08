<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful! 🎉 | <?= APP_NAME ?></title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
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
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 15px;
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
            background: linear-gradient(135deg, #10b981 0%, #059669 50%, #047857 100%);
            opacity: 0.1;
            z-index: -1;
        }
        
        /* Confetti animation */
        .confetti {
            position: fixed;
            width: 10px;
            height: 10px;
            background: #f093fb;
            position: absolute;
            animation: confetti-fall 3s linear infinite;
        }
        
        @keyframes confetti-fall {
            to {
                transform: translateY(100vh) rotate(360deg);
                opacity: 0;
            }
        }
        
        .success-card {
            background: rgba(15, 15, 35, 0.95);
            backdrop-filter: blur(20px);
            border: 3px solid rgba(16, 185, 129, 0.5);
            border-radius: 30px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.5), 0 0 100px rgba(16, 185, 129, 0.3);
            padding: 30px;
            max-width: 1000px;
            width: 100%;
            text-align: center;
            position: relative;
            overflow: hidden;
            animation: slideUp 0.6s ease-out;
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .success-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(90deg, #10b981, #059669, #10b981);
            animation: shimmer 2s linear infinite;
        }
        
        @keyframes shimmer {
            0% { background-position: -100% 0; }
            100% { background-position: 200% 0; }
        }
        
        .success-icon {
            font-size: 80px;
            background: linear-gradient(135deg, #10b981, #34d399);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: scaleIn 0.6s ease-out, pulse 2s ease-in-out infinite 1s;
            display: inline-block;
        }
        
        @keyframes scaleIn {
            0% { transform: scale(0) rotate(-180deg); }
            50% { transform: scale(1.2) rotate(10deg); }
            100% { transform: scale(1) rotate(0deg); }
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        
        h1 {
            background: linear-gradient(135deg, #fff, #10b981);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-size: 2.2rem;
            font-weight: 900;
            margin: 15px 0 8px;
            animation: fadeIn 0.8s ease-out 0.3s both;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .lead {
            color: rgba(255,255,255,0.9);
            font-size: 1.1rem;
            margin-bottom: 20px;
            animation: fadeIn 0.8s ease-out 0.5s both;
        }
        
        .payment-summary {
            background: rgba(16, 185, 129, 0.1);
            border: 2px solid rgba(16, 185, 129, 0.3);
            border-radius: 15px;
            padding: 15px;
            margin: 20px 0;
            animation: fadeIn 0.8s ease-out 0.7s both;
        }
        
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            margin-top: 10px;
        }
        
        .summary-item {
            background: rgba(15, 15, 35, 0.6);
            padding: 15px;
            border-radius: 12px;
            border: 1px solid rgba(16, 185, 129, 0.2);
        }
        
        .summary-label {
            font-size: 0.8rem;
            color: rgba(255,255,255,0.6);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
            font-weight: 600;
        }
        
        .summary-value {
            font-size: 1.1rem;
            color: #10b981;
            font-weight: 700;
        }
        
        .tickets-section {
            margin: 20px 0;
            animation: fadeIn 0.8s ease-out 0.9s both;
        }
        
        .tickets-title {
            color: #10b981;
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 15px;
        }
        
        .tickets-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 12px;
            margin-top: 15px;
        }
        
        .ticket-card {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.15), rgba(5, 150, 105, 0.15));
            border: 2px solid rgba(16, 185, 129, 0.4);
            border-radius: 15px;
            padding: 15px;
            transition: all 0.3s;
            animation: slideIn 0.5s ease-out;
            animation-fill-mode: both;
        }
        
        .ticket-card:hover {
            transform: translateY(-5px);
            border-color: #10b981;
            box-shadow: 0 10px 30px rgba(16, 185, 129, 0.3);
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        .ticket-icon {
            font-size: 1.5rem;
            color: #10b981;
            margin-bottom: 8px;
        }
        
        .ticket-code {
            background: rgba(15, 15, 35, 0.8);
            padding: 10px;
            border-radius: 10px;
            font-family: 'Courier New', monospace;
            font-size: 1.1rem;
            font-weight: 700;
            color: #10b981;
            margin: 8px 0;
            letter-spacing: 2px;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }
        
        .ticket-badge {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 700;
            display: inline-block;
        }
        
        .info-box {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.15), rgba(118, 75, 162, 0.15));
            border: 2px solid rgba(102, 126, 234, 0.4);
            border-radius: 15px;
            padding: 15px;
            margin: 20px 0;
            animation: fadeIn 0.8s ease-out 1.1s both;
        }
        
        .info-box i {
            color: #667eea;
            margin-right: 10px;
        }
        
        .info-box p {
            color: rgba(255,255,255,0.9);
            margin: 0;
            line-height: 1.6;
        }
        
        .action-buttons {
            display: flex;
            gap: 12px;
            justify-content: center;
            flex-wrap: wrap;
            margin: 20px 0 15px;
            animation: fadeIn 0.8s ease-out 1.3s both;
        }
        
        .btn {
            padding: 15px 35px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 1rem;
            text-decoration: none;
            transition: all 0.3s;
            text-transform: uppercase;
            letter-spacing: 1px;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            border: none;
            box-shadow: 0 8px 20px rgba(16, 185, 129, 0.4);
        }
        
        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 30px rgba(16, 185, 129, 0.6);
            color: white;
        }
        
        .btn-secondary {
            background: rgba(102, 126, 234, 0.2);
            color: #667eea;
            border: 2px solid rgba(102, 126, 234, 0.5);
        }
        
        .btn-secondary:hover {
            background: rgba(102, 126, 234, 0.3);
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
            color: #667eea;
        }
        
        .good-luck {
            color: rgba(255,255,255,0.7);
            font-size: 0.9rem;
            margin-top: 15px;
            animation: fadeIn 0.8s ease-out 1.5s both;
        }
        
        .good-luck i {
            color: #ffd700;
            margin-right: 8px;
        }
        
        /* Mobile Responsive */
        @media (max-width: 768px) {
            body {
                padding: 10px;
            }
            
            .success-card {
                padding: 25px 20px;
            }
            
            .success-icon {
                font-size: 70px;
            }
            
            h1 {
                font-size: 1.8rem;
            }
            
            .lead {
                font-size: 1rem;
            }
            
            .summary-grid {
                grid-template-columns: 1fr;
                gap: 10px;
            }
            
            .tickets-grid {
                grid-template-columns: 1fr;
            }
            
            .action-buttons {
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
                justify-content: center;
                padding: 12px 25px;
                font-size: 0.9rem;
            }
        }
        
        @media (max-width: 480px) {
            h1 {
                font-size: 1.5rem;
            }
            
            .success-icon {
                font-size: 60px;
            }
        }
    </style>
</head>
<body>
    <!-- Confetti elements -->
    <script>
        // Create confetti
        for(let i = 0; i < 50; i++) {
            const confetti = document.createElement('div');
            confetti.className = 'confetti';
            confetti.style.left = Math.random() * 100 + '%';
            confetti.style.animationDelay = Math.random() * 3 + 's';
            confetti.style.backgroundColor = ['#10b981', '#667eea', '#f093fb', '#ffd700'][Math.floor(Math.random() * 4)];
            document.body.appendChild(confetti);
        }
    </script>
    
    <div class="success-card">
        <i class="fas fa-check-circle success-icon"></i>
        <h1>🎉 Payment Successful!</h1>
        <p class="lead">Your tickets have been generated successfully</p>
        
        <div class="payment-summary">
            <div class="summary-grid">
                <div class="summary-item">
                    <div class="summary-label">Reference</div>
                    <div class="summary-value"><?= htmlspecialchars($payment->internal_reference ?? $payment->gateway_reference ?? 'N/A') ?></div>
                </div>
                <div class="summary-item">
                    <div class="summary-label">Amount Paid</div>
                    <div class="summary-value"><?= $payment->currency ?> <?= number_format($payment->amount, 2) ?></div>
                </div>
                <div class="summary-item">
                    <div class="summary-label">Tickets</div>
                    <div class="summary-value"><?= count($tickets) ?> Ticket<?= count($tickets) > 1 ? 's' : '' ?></div>
                </div>
            </div>
        </div>

        <div class="tickets-section">
            <h2 class="tickets-title">
                <i class="fas fa-ticket-alt"></i> Your Ticket Codes
            </h2>
            
            <?php if (!empty($tickets)): ?>
                <div class="tickets-grid">
                    <?php foreach ($tickets as $index => $ticket): ?>
                        <div class="ticket-card" style="animation-delay: <?= $index * 0.1 ?>s;">
                            <div class="ticket-icon">
                                <i class="fas fa-ticket-alt"></i>
                            </div>
                            <div class="ticket-code">
                                <?= htmlspecialchars($ticket->ticket_code) ?>
                            </div>
                            <div class="ticket-badge">
                                <?= $ticket->quantity ?? 1 ?> <?= ($ticket->quantity ?? 1) > 1 ? 'Entries' : 'Entry' ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="info-box" style="border-color: rgba(239, 68, 68, 0.5); background: rgba(239, 68, 68, 0.1);">
                    <i class="fas fa-exclamation-triangle" style="color: #ef4444;"></i>
                    <p style="color: #ef4444;">
                        <strong>No tickets found.</strong> Please contact support with reference: <?= htmlspecialchars($payment->internal_reference ?? $payment->gateway_reference ?? 'N/A') ?>
                    </p>
                </div>
            <?php endif; ?>
        </div>

        <?php if (!empty($tickets)): ?>
        <div class="info-box">
            <p>
                <i class="fas fa-sms"></i>
                <strong>SMS Sent!</strong> Your ticket codes have been sent to your mobile number.
            </p>
        </div>
        <?php endif; ?>

        <div class="action-buttons">
            <a href="<?= url('public/buyTicket') ?>" class="btn btn-primary">
                <i class="fas fa-plus-circle"></i>
                Buy More Tickets
            </a>
            <a href="<?= url('public') ?>" class="btn btn-secondary">
                <i class="fas fa-home"></i>
                Back to Home
            </a>
        </div>

        <div class="good-luck">
            <i class="fas fa-trophy"></i> <strong>Good luck in the draw!</strong><br>
            <small>Winners will be announced on the draw date. Keep your tickets safe!</small>
        </div>
    </div>
</body>
</html>
