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
            align-items: flex-start;
            justify-content: center;
            padding: 40px 20px;
            position: relative;
            overflow-y: auto;
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
            border-radius: 30px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.5), 0 0 100px rgba(102, 126, 234, 0.3);
            padding: 50px 40px;
            max-width: 900px;
            width: 100%;
            text-align: center;
            position: relative;
            overflow: visible;
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
            width: 100px;
            height: 100px;
            margin: 0 auto 30px;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .phone-icon {
            font-size: 80px;
            color: #667eea;
            z-index: 2;
            animation: phonePulse 2s ease-in-out infinite;
        }
        
        @keyframes phonePulse {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.1); opacity: 0.8; }
        }
        
        .ripple {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 100px;
            height: 100px;
            border: 3px solid #667eea;
            border-radius: 50%;
            animation: ripple 1.5s ease-out infinite;
        }
        
        .ripple:nth-child(2) {
            animation-delay: 0.5s;
        }
        
        .ripple:nth-child(3) {
            animation-delay: 1s;
        }
        
        @keyframes ripple {
            0% {
                width: 100px;
                height: 100px;
                opacity: 1;
            }
            100% {
                width: 200px;
                height: 200px;
                opacity: 0;
            }
        }
        
        h1 {
            background: linear-gradient(135deg, #fff, #667eea);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-size: 2.5rem;
            font-weight: 900;
            margin-bottom: 15px;
        }
        
        @keyframes glow {
            0%, 100% { filter: drop-shadow(0 0 10px rgba(240, 147, 251, 0.5)); }
            50% { filter: drop-shadow(0 0 20px rgba(240, 147, 251, 0.8)); }
        }
        
        .subtitle {
            color: rgba(255,255,255,0.7);
            font-size: 1.1rem;
            margin-bottom: 30px;
        }
        
        .status {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.2), rgba(118, 75, 162, 0.2));
            border: 2px solid rgba(102, 126, 234, 0.5);
            padding: 20px;
            border-radius: 15px;
            margin: 25px 0;
            font-size: 1.2rem;
            font-weight: 600;
            color: #f093fb;
        }
        
        .status i {
            margin-right: 10px;
            animation: spin 2s linear infinite;
        }
        
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        
        /* Vertical layout for content */
        .content-section {
            margin: 30px 0;
        }
        
        .info {
            background: rgba(102, 126, 234, 0.1);
            border: 2px solid rgba(102, 126, 234, 0.3);
            padding: 25px;
            border-radius: 15px;
            text-align: left;
            margin-bottom: 30px;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid rgba(102, 126, 234, 0.2);
        }
        
        .info-row:last-child {
            border-bottom: none;
        }
        
        .info-label {
            font-weight: 600;
            color: rgba(255,255,255,0.7);
            font-size: 1rem;
            flex-shrink: 0;
        }
        
        .info-value {
            color: #f093fb;
            font-weight: 700;
            font-size: 1.1rem;
            text-align: right;
            word-break: break-word;
        }
        
        .instructions {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.15), rgba(5, 150, 105, 0.15));
            border: 2px solid rgba(16, 185, 129, 0.4);
            border-left: 5px solid #10b981;
            padding: 30px;
            text-align: left;
            border-radius: 15px;
        }
        
        .instructions h3 {
            color: #10b981;
            font-size: 1.3rem;
            margin-bottom: 20px;
            font-weight: 700;
        }
        
        .instructions ol {
            margin: 0;
            padding-left: 0;
            list-style: none;
        }
        
        .instructions li {
            color: rgba(255,255,255,0.9);
            margin: 18px 0;
            font-size: 1rem;
            line-height: 1.7;
            position: relative;
            padding-left: 45px;
        }
        
        .instructions li::before {
            content: attr(data-number);
            position: absolute;
            left: 0;
            top: 0;
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.95rem;
        }
        
        .btn {
            display: inline-block;
            padding: 15px 40px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
            border-radius: 50px;
            font-weight: 700;
            font-size: 1rem;
            text-decoration: none;
            display: inline-block;
            margin-top: 30px;
            transition: all 0.3s;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 30px rgba(102, 126, 234, 0.6);
            color: white;
            text-decoration: none;
        }
        
        .auto-check {
            background: rgba(102, 126, 234, 0.1);
            border: 1px solid rgba(102, 126, 234, 0.3);
            padding: 15px;
            border-radius: 10px;
            margin-top: 25px;
            font-size: 0.95rem;
            color: rgba(255,255,255,0.7);
        }
        
        .auto-check i {
            color: #10b981;
            margin-right: 8px;
            animation: spin 2s linear infinite;
        }
        
        /* Motivational messages */
        .motivation {
            background: linear-gradient(135deg, rgba(240, 147, 251, 0.15), rgba(102, 126, 234, 0.15));
            border: 2px solid rgba(240, 147, 251, 0.4);
            border-radius: 15px;
            padding: 25px;
            margin: 30px 0;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .motivation h4 {
            color: #f093fb;
            font-size: 1.2rem;
            margin-bottom: 12px;
            font-weight: 700;
        }
        
        .motivation p {
            color: rgba(255,255,255,0.8);
            line-height: 1.5;
            margin: 0;
            font-size: 1rem;
        }
        
        /* Progress dots */
        .progress-dots {
            display: flex;
            justify-content: center;
            gap: 12px;
            margin: 20px 0;
        }
        
        .dot {
            width: 14px;
            height: 14px;
            background: rgba(102, 126, 234, 0.3);
            border-radius: 50%;
            animation: dotPulse 1.5s ease-in-out infinite;
        }
        
        .dot:nth-child(2) { animation-delay: 0.3s; }
        .dot:nth-child(3) { animation-delay: 0.6s; }
        
        @keyframes dotPulse {
            0%, 100% { background: rgba(102, 126, 234, 0.3); transform: scale(1); }
            50% { background: #667eea; transform: scale(1.3); }
        }
        
        @media (max-width: 992px) {
            body {
                padding: 20px 15px;
            }
        }
        
        @media (max-width: 768px) {
            body {
                padding: 20px 15px;
            }
            
            .container {
                padding: 30px 20px;
            }
            
            h1 {
                font-size: 1.8rem;
            }
            
            .subtitle {
                font-size: 1rem;
            }
            
            .phone-animation {
                width: 80px;
                height: 80px;
            }
            
            .phone-icon {
                font-size: 60px;
            }
            
            .info {
                padding: 20px;
            }
            
            .instructions {
                padding: 20px;
            }
        }
        
        @media (max-width: 480px) {
            .container {
                padding: 25px 15px;
            }
            
            h1 {
                font-size: 1.5rem;
            }
            
            .phone-animation {
                width: 70px;
                height: 70px;
            }
            
            .phone-icon {
                font-size: 50px;
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
        
        <div class="content-section">
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
            
            <div class="instructions">
                <h3><i class="fas fa-mobile-alt"></i> Complete Payment on Your Phone</h3>
                <ol>
                    <li data-number="1">Check your phone for a mobile money prompt</li>
                    <li data-number="2">Enter your mobile money PIN to approve the payment</li>
                    <li data-number="3">Wait for the confirmation SMS</li>
                    <li data-number="4">This page will automatically redirect when payment is confirmed</li>
                </ol>
            </div>
        </div>
        
        <a href="<?= url('public/campaign/' . $campaign->id) ?>" class="btn">
            <i class="fas fa-home"></i> Cancel
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
