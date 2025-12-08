<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $data['title'] ?? 'Payment Pending' ?> | eTickets Raffle</title>
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
            padding: 20px;
            position: relative;
            overflow: hidden;
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
        
        /* Animated background particles */
        .particle {
            position: fixed;
            border-radius: 50%;
            pointer-events: none;
            opacity: 0.3;
            animation: float 20s infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0) translateX(0); }
            25% { transform: translateY(-100px) translateX(50px); }
            50% { transform: translateY(-50px) translateX(-50px); }
            75% { transform: translateY(-150px) translateX(100px); }
        }
        
        .container {
            background: rgba(15, 15, 35, 0.95);
            backdrop-filter: blur(20px);
            border: 3px solid rgba(102, 126, 234, 0.5);
            border-radius: 25px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.5), 0 0 100px rgba(102, 126, 234, 0.3);
            max-width: 1100px;
            width: 95%;
            padding: 30px;
            text-align: center;
            position: relative;
            overflow: hidden;
            animation: slideIn 0.5s ease-out;
            max-height: 95vh;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(90deg, #667eea, #764ba2, #f093fb);
        }
        
        /* Animated phone icon */
        .phone-animation {
            position: relative;
            width: 80px;
            height: 80px;
            margin: 0 auto 15px;
        }
        
        .phone-icon {
            font-size: 60px;
            background: linear-gradient(135deg, #667eea, #f093fb);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: pulse 2s ease-in-out infinite;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.1); opacity: 0.8; }
        }
        
        .ripple {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 80px;
            height: 80px;
            border: 2px solid #667eea;
            border-radius: 50%;
            animation: ripple 2s ease-out infinite;
        }
        
        .ripple:nth-child(2) {
            animation-delay: 0.5s;
        }
        
        .ripple:nth-child(3) {
            animation-delay: 1s;
        }
        
        @keyframes ripple {
            0% {
                width: 80px;
                height: 80px;
                opacity: 1;
            }
            100% {
                width: 140px;
                height: 140px;
                opacity: 0;
            }
        }
        
        h1 {
            background: linear-gradient(135deg, #fff, #f093fb);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 10px;
            font-size: 2rem;
            font-weight: 900;
            animation: glow 2s ease-in-out infinite;
        }
        
        @keyframes glow {
            0%, 100% { filter: drop-shadow(0 0 10px rgba(240, 147, 251, 0.5)); }
            50% { filter: drop-shadow(0 0 20px rgba(240, 147, 251, 0.8)); }
        }
        
        .subtitle {
            color: rgba(255,255,255,0.8);
            font-size: 1rem;
            margin-bottom: 20px;
            font-weight: 500;
        }
        
        .status {
            background: linear-gradient(135deg, rgba(255, 193, 7, 0.2), rgba(255, 152, 0, 0.2));
            border: 2px solid rgba(255, 193, 7, 0.5);
            color: #ffd700;
            padding: 15px;
            border-radius: 12px;
            margin: 15px 0;
            font-size: 1.1rem;
            font-weight: 700;
            animation: statusPulse 2s ease-in-out infinite;
        }
        
        @keyframes statusPulse {
            0%, 100% { box-shadow: 0 0 20px rgba(255, 193, 7, 0.3); }
            50% { box-shadow: 0 0 30px rgba(255, 193, 7, 0.6); }
        }
        
        .status i {
            margin-right: 10px;
            animation: spin 2s linear infinite;
        }
        
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        
        /* Horizontal layout for content */
        .content-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin: 15px 0;
        }
        
        .info {
            background: rgba(102, 126, 234, 0.1);
            border: 2px solid rgba(102, 126, 234, 0.3);
            padding: 15px;
            border-radius: 12px;
            text-align: left;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid rgba(102, 126, 234, 0.2);
        }
        
        .info-row:last-child {
            border-bottom: none;
        }
        
        .info-label {
            font-weight: 600;
            color: rgba(255,255,255,0.7);
            font-size: 0.85rem;
            flex-shrink: 0;
        }
        
        .info-value {
            color: #f093fb;
            font-weight: 700;
            font-size: 0.9rem;
            text-align: right;
            word-break: break-word;
        }
        
        .instructions {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.15), rgba(5, 150, 105, 0.15));
            border: 2px solid rgba(16, 185, 129, 0.4);
            border-left: 5px solid #10b981;
            padding: 20px;
            text-align: left;
            border-radius: 12px;
        }
        
        .instructions h3 {
            color: #10b981;
            margin-bottom: 15px;
            font-size: 1.1rem;
            font-weight: 700;
        }
        
        .instructions ol {
            margin-left: 20px;
            counter-reset: item;
            list-style: none;
        }
        
        .instructions li {
            margin: 10px 0;
            color: rgba(255,255,255,0.9);
            font-size: 0.9rem;
            line-height: 1.5;
            counter-increment: item;
            position: relative;
            padding-left: 30px;
        }
        
        .instructions li::before {
            content: counter(item);
            position: absolute;
            left: 0;
            top: 0;
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            width: 22px;
            height: 22px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.75rem;
        }
        
        .btn {
            display: inline-block;
            padding: 12px 30px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            text-decoration: none;
            border-radius: 50px;
            font-weight: 700;
            margin-top: 15px;
            transition: all 0.3s;
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 0.85rem;
        }
        
        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 30px rgba(102, 126, 234, 0.6);
            color: white;
            text-decoration: none;
        }
        
        .auto-check {
            color: rgba(255,255,255,0.6);
            font-size: 0.85rem;
            margin-top: 15px;
            padding: 10px;
            background: rgba(102, 126, 234, 0.1);
            border-radius: 10px;
            border: 1px solid rgba(102, 126, 234, 0.2);
        }
        
        .auto-check i {
            color: #10b981;
            margin-right: 8px;
            animation: spin 2s linear infinite;
        }
        
        /* Motivational messages */
        .motivation {
            background: linear-gradient(135deg, rgba(240, 147, 251, 0.15), rgba(118, 75, 162, 0.15));
            border: 2px solid rgba(240, 147, 251, 0.4);
            border-radius: 12px;
            padding: 15px;
            margin: 15px 0;
            animation: fadeIn 1s ease-in;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .motivation h4 {
            color: #f093fb;
            font-size: 1rem;
            font-weight: 700;
            margin-bottom: 8px;
        }
        
        .motivation p {
            color: rgba(255,255,255,0.8);
            line-height: 1.5;
            margin: 0;
            font-size: 0.9rem;
        }
        
        /* Progress dots */
        .progress-dots {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin: 12px 0;
        }
        
        .dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: rgba(102, 126, 234, 0.3);
            animation: dotPulse 1.5s ease-in-out infinite;
        }
        
        .dot:nth-child(2) { animation-delay: 0.3s; }
        .dot:nth-child(3) { animation-delay: 0.6s; }
        
        @keyframes dotPulse {
            0%, 100% { background: rgba(102, 126, 234, 0.3); transform: scale(1); }
            50% { background: #667eea; transform: scale(1.3); }
        }
        
        /* Mobile info grid - 3 columns */
        .info-grid-mobile {
            display: none;
        }
        
        @media (max-width: 992px) {
            .content-grid {
                grid-template-columns: 1fr;
                gap: 12px;
            }
        }
        
        @media (max-width: 768px) {
            body {
                padding: 10px;
            }
            
            .container {
                padding: 15px;
                max-height: 98vh;
                width: 100%;
            }
            
            h1 {
                font-size: 1.4rem;
                margin-bottom: 5px;
            }
            
            .subtitle {
                font-size: 0.85rem;
                margin-bottom: 10px;
            }
            
            .phone-animation {
                width: 50px;
                height: 50px;
                margin-bottom: 8px;
            }
            
            .phone-icon {
                font-size: 40px;
            }
            
            .ripple {
                width: 50px;
                height: 50px;
            }
            
            @keyframes ripple {
                0% {
                    width: 50px;
                    height: 50px;
                    opacity: 1;
                }
                100% {
                    width: 100px;
                    height: 100px;
                    opacity: 0;
                }
            }
            
            .status {
                padding: 10px;
                font-size: 0.95rem;
                margin: 10px 0;
            }
            
            .progress-dots {
                margin: 8px 0;
            }
            
            .dot {
                width: 10px;
                height: 10px;
            }
            
            .motivation {
                padding: 10px;
                margin: 10px 0;
            }
            
            .motivation h4 {
                font-size: 0.9rem;
                margin-bottom: 5px;
            }
            
            .motivation p {
                font-size: 0.8rem;
                line-height: 1.4;
            }
            
            /* Mobile: Show 3-column grid for payment info */
            .info {
                display: none;
            }
            
            .info-grid-mobile {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 8px;
                background: rgba(102, 126, 234, 0.1);
                border: 2px solid rgba(102, 126, 234, 0.3);
                padding: 12px;
                border-radius: 12px;
            }
            
            .info-item-mobile {
                background: rgba(15, 15, 35, 0.6);
                padding: 8px;
                border-radius: 8px;
                border: 1px solid rgba(102, 126, 234, 0.2);
            }
            
            .info-item-mobile.full-width {
                grid-column: 1 / -1;
            }
            
            .info-label-mobile {
                font-size: 0.7rem;
                color: rgba(255,255,255,0.6);
                text-transform: uppercase;
                letter-spacing: 0.5px;
                margin-bottom: 3px;
                font-weight: 600;
            }
            
            .info-value-mobile {
                font-size: 0.85rem;
                color: #f093fb;
                font-weight: 700;
                word-break: break-word;
            }
            
            .instructions {
                padding: 12px;
            }
            
            .instructions h3 {
                font-size: 0.95rem;
                margin-bottom: 10px;
            }
            
            .instructions ol {
                margin-left: 15px;
            }
            
            .instructions li {
                font-size: 0.8rem;
                margin: 8px 0;
                padding-left: 25px;
                line-height: 1.4;
            }
            
            .instructions li::before {
                width: 20px;
                height: 20px;
                font-size: 0.7rem;
            }
            
            .btn {
                padding: 10px 25px;
                font-size: 0.8rem;
                margin-top: 10px;
            }
            
            .auto-check {
                font-size: 0.75rem;
                padding: 8px;
                margin-top: 10px;
            }
        }
        
        @media (max-width: 480px) {
            h1 {
                font-size: 1.2rem;
            }
            
            .status {
                font-size: 0.9rem;
            }
            
            .info-grid-mobile {
                grid-template-columns: 1fr;
            }
            
            .info-item-mobile.full-width {
                grid-column: 1;
            }
        }
    </style>
</head>
<body>
    <!-- Animated background particles -->
    <div class="particle" style="width: 100px; height: 100px; background: rgba(102, 126, 234, 0.3); top: 10%; left: 10%;"></div>
    <div class="particle" style="width: 150px; height: 150px; background: rgba(240, 147, 251, 0.2); top: 60%; right: 15%; animation-delay: 5s;"></div>
    <div class="particle" style="width: 80px; height: 80px; background: rgba(118, 75, 162, 0.3); bottom: 20%; left: 20%; animation-delay: 10s;"></div>
    
    <div class="container">
        <div class="phone-animation">
            <div class="ripple"></div>
            <div class="ripple"></div>
            <div class="ripple"></div>
            <i class="fas fa-mobile-alt phone-icon"></i>
        </div>
        
        <h1>⏳ Payment Pending</h1>
        <p class="subtitle">Please check your phone to complete the payment</p>
        
        <div class="status">
            <i class="fas fa-spinner"></i> Waiting for payment confirmation...
        </div>
        
        <div class="progress-dots">
            <div class="dot"></div>
            <div class="dot"></div>
            <div class="dot"></div>
        </div>
        
        <div class="motivation">
            <h4><i class="fas fa-trophy"></i> You're Almost There!</h4>
            <p>Your tickets are reserved and waiting for you. Complete the payment now to secure your chance to win amazing prizes!</p>
        </div>
        
        <div class="content-grid">
            <!-- Desktop/Tablet View -->
            <div class="info">
                <div class="info-row">
                    <span class="info-label">Payment ID:</span>
                    <span class="info-value"><?= htmlspecialchars($data['payment']->id) ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Reference:</span>
                    <span class="info-value"><?= htmlspecialchars($data['payment']->internal_reference) ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Amount:</span>
                    <span class="info-value">GHS <?= number_format($data['payment']->amount, 2) ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Campaign:</span>
                    <span class="info-value"><?= htmlspecialchars($data['campaign']->name) ?></span>
                </div>
            </div>
            
            <!-- Mobile View - Grid Layout -->
            <div class="info-grid-mobile">
                <div class="info-item-mobile">
                    <div class="info-label-mobile">Payment ID</div>
                    <div class="info-value-mobile"><?= htmlspecialchars($data['payment']->id) ?></div>
                </div>
                <div class="info-item-mobile">
                    <div class="info-label-mobile">Amount</div>
                    <div class="info-value-mobile">GHS <?= number_format($data['payment']->amount, 2) ?></div>
                </div>
                <div class="info-item-mobile full-width">
                    <div class="info-label-mobile">Reference</div>
                    <div class="info-value-mobile"><?= htmlspecialchars($data['payment']->internal_reference) ?></div>
                </div>
                <div class="info-item-mobile full-width">
                    <div class="info-label-mobile">Campaign</div>
                    <div class="info-value-mobile"><?= htmlspecialchars($data['campaign']->name) ?></div>
                </div>
            </div>
            
            <div class="instructions">
                <h3><i class="fas fa-mobile-alt"></i> Complete Payment on Your Phone</h3>
                <ol>
                    <li>Check your phone for a mobile money prompt</li>
                    <li>Enter your mobile money PIN to approve the payment</li>
                    <li>Wait for the confirmation SMS</li>
                    <li>This page will automatically redirect when payment is confirmed</li>
                </ol>
            </div>
        </div>
        
        <a href="<?= BASE_URL ?>/public" class="btn">
            <i class="fas fa-home"></i> Back to Home
        </a>
        
        <div class="auto-check">
            <i class="fas fa-sync-alt"></i> Auto-checking payment status every 5 seconds...
        </div>
    </div>
    
    <script>
        // Auto-check payment status every 5 seconds
        const paymentId = <?= $data['payment']->id ?>;
        let checkCount = 0;
        
        // Motivational messages to cycle through
        const motivationalMessages = [
            { icon: 'fa-trophy', title: 'You\'re Almost There!', text: 'Your tickets are reserved and waiting for you. Complete the payment now to secure your chance to win!' },
            { icon: 'fa-star', title: 'Big Prizes Await!', text: 'Every ticket brings you closer to winning. Don\'t miss out on this opportunity!' },
            { icon: 'fa-rocket', title: 'Join the Winners!', text: 'Thousands have already won with us. You could be next!' },
            { icon: 'fa-gem', title: 'Your Lucky Moment!', text: 'This could be your winning ticket. Complete the payment to find out!' }
        ];
        
        function updateMotivationalMessage() {
            const messageIndex = Math.floor(checkCount / 4) % motivationalMessages.length;
            const message = motivationalMessages[messageIndex];
            const motivationDiv = document.querySelector('.motivation');
            
            if (motivationDiv) {
                motivationDiv.innerHTML = `
                    <h4><i class="fas ${message.icon}"></i> ${message.title}</h4>
                    <p>${message.text}</p>
                `;
            }
        }
        
        function checkPaymentStatus() {
            checkCount++;
            updateMotivationalMessage();
            
            fetch('<?= BASE_URL ?>/public/checkPaymentStatus/' + paymentId)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.status === 'success') {
                        // Payment successful, redirect to success page
                        document.querySelector('.status').innerHTML = '<i class="fas fa-check-circle"></i> Payment Confirmed! Redirecting...';
                        document.querySelector('.status').style.background = 'linear-gradient(135deg, rgba(16, 185, 129, 0.2), rgba(5, 150, 105, 0.2))';
                        document.querySelector('.status').style.borderColor = 'rgba(16, 185, 129, 0.5)';
                        document.querySelector('.status').style.color = '#10b981';
                        
                        setTimeout(() => {
                            window.location.href = '<?= BASE_URL ?>/public/paymentSuccess/' + paymentId;
                        }, 1500);
                    } else if (data.success && data.status === 'failed') {
                        // Payment failed
                        document.querySelector('.status').innerHTML = '<i class="fas fa-times-circle"></i> Payment Failed. Redirecting...';
                        document.querySelector('.status').style.background = 'linear-gradient(135deg, rgba(239, 68, 68, 0.2), rgba(220, 38, 38, 0.2))';
                        document.querySelector('.status').style.borderColor = 'rgba(239, 68, 68, 0.5)';
                        document.querySelector('.status').style.color = '#ef4444';
                        
                        setTimeout(() => {
                            window.location.href = '<?= BASE_URL ?>/public/buyTicket';
                        }, 2000);
                    }
                    // If still pending, continue checking
                })
                .catch(error => {
                    console.error('Error checking payment status:', error);
                });
        }
        
        // Check immediately
        checkPaymentStatus();
        
        // Then check every 5 seconds
        setInterval(checkPaymentStatus, 5000);
    </script>
</body>
</html>
