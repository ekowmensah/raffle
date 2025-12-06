<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $data['title'] ?? 'Payment Pending' ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 600px;
            width: 100%;
            padding: 40px;
            text-align: center;
        }
        
        .spinner {
            width: 80px;
            height: 80px;
            border: 8px solid #f3f3f3;
            border-top: 8px solid #667eea;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 30px;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        h1 {
            color: #333;
            margin-bottom: 20px;
            font-size: 32px;
        }
        
        .status {
            background: #fff3cd;
            color: #856404;
            padding: 15px;
            border-radius: 10px;
            margin: 20px 0;
            font-size: 18px;
            font-weight: bold;
        }
        
        .info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
            text-align: left;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #dee2e6;
        }
        
        .info-row:last-child {
            border-bottom: none;
        }
        
        .info-label {
            font-weight: bold;
            color: #666;
        }
        
        .info-value {
            color: #333;
        }
        
        .instructions {
            background: #e7f3ff;
            border-left: 4px solid #2196F3;
            padding: 20px;
            margin: 20px 0;
            text-align: left;
        }
        
        .instructions h3 {
            color: #2196F3;
            margin-bottom: 15px;
        }
        
        .instructions ol {
            margin-left: 20px;
        }
        
        .instructions li {
            margin: 10px 0;
            color: #555;
        }
        
        .btn {
            display: inline-block;
            padding: 15px 40px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 10px;
            font-weight: bold;
            margin-top: 20px;
            transition: all 0.3s;
        }
        
        .btn:hover {
            background: #764ba2;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        .auto-check {
            color: #666;
            font-size: 14px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="spinner"></div>
        
        <h1>⏳ Payment Pending</h1>
        
        <div class="status">
            Waiting for payment confirmation...
        </div>
        
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
            <h3>📱 Complete Payment on Your Phone</h3>
            <ol>
                <li>Check your phone for a mobile money prompt</li>
                <li>Enter your mobile money PIN to approve</li>
                <li>Wait for confirmation SMS</li>
                <li>This page will automatically update when payment is confirmed</li>
            </ol>
        </div>
        
        <a href="<?= BASE_URL ?>/public" class="btn">← Back to Home</a>
        
        <div class="auto-check">
            🔄 Auto-checking payment status every 5 seconds...
        </div>
    </div>
    
    <script>
        // Auto-check payment status every 5 seconds
        const paymentId = <?= $data['payment']->id ?>;
        
        function checkPaymentStatus() {
            fetch('<?= BASE_URL ?>/public/checkPaymentStatus/' + paymentId)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.status === 'success') {
                        // Payment successful, redirect to success page
                        window.location.href = '<?= BASE_URL ?>/public/paymentSuccess/' + paymentId;
                    } else if (data.success && data.status === 'failed') {
                        // Payment failed
                        alert('Payment failed. Please try again.');
                        window.location.href = '<?= BASE_URL ?>/public/campaign/<?= $data['campaign']->id ?>';
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
