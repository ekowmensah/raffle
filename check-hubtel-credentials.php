<!DOCTYPE html>
<html>
<head>
    <title>Hubtel Credentials Checker</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            border-bottom: 3px solid #667eea;
            padding-bottom: 10px;
        }
        .status {
            padding: 15px;
            margin: 10px 0;
            border-radius: 5px;
            font-weight: bold;
        }
        .success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .warning {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }
        .info {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }
        .credential {
            margin: 15px 0;
            padding: 10px;
            background: #f8f9fa;
            border-left: 4px solid #667eea;
        }
        .credential label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }
        .credential .value {
            font-family: monospace;
            color: #666;
        }
        .masked {
            color: #999;
        }
        code {
            background: #f4f4f4;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: monospace;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔐 Hubtel Credentials Checker</h1>
        
        <?php
        require_once 'app/config/config.php';
        
        $allGood = true;
        
        // Check Client ID
        echo '<div class="credential">';
        echo '<label>HUBTEL_CLIENT_ID:</label>';
        if (!empty(HUBTEL_CLIENT_ID)) {
            $masked = substr(HUBTEL_CLIENT_ID, 0, 4) . str_repeat('*', max(0, strlen(HUBTEL_CLIENT_ID) - 4));
            echo '<div class="value">' . htmlspecialchars($masked) . ' <span style="color: green;">✓</span></div>';
        } else {
            echo '<div class="value masked">Not set ✗</div>';
            $allGood = false;
        }
        echo '</div>';
        
        // Check Client Secret
        echo '<div class="credential">';
        echo '<label>HUBTEL_CLIENT_SECRET:</label>';
        if (!empty(HUBTEL_CLIENT_SECRET)) {
            $masked = str_repeat('*', strlen(HUBTEL_CLIENT_SECRET));
            echo '<div class="value">' . $masked . ' <span style="color: green;">✓</span></div>';
        } else {
            echo '<div class="value masked">Not set ✗</div>';
            $allGood = false;
        }
        echo '</div>';
        
        // Check Merchant Account
        echo '<div class="credential">';
        echo '<label>HUBTEL_MERCHANT_ACCOUNT:</label>';
        if (!empty(HUBTEL_MERCHANT_ACCOUNT)) {
            echo '<div class="value">' . htmlspecialchars(HUBTEL_MERCHANT_ACCOUNT) . ' <span style="color: green;">✓</span></div>';
        } else {
            echo '<div class="value masked">Not set ✗</div>';
            $allGood = false;
        }
        echo '</div>';
        
        // Check IP Whitelist
        echo '<div class="credential">';
        echo '<label>HUBTEL_IP_WHITELIST:</label>';
        if (!empty(HUBTEL_IP_WHITELIST)) {
            echo '<div class="value">' . htmlspecialchars(HUBTEL_IP_WHITELIST) . ' <span style="color: green;">✓</span></div>';
        } else {
            echo '<div class="value masked">Not set (optional for development)</div>';
        }
        echo '</div>';
        
        echo '<hr style="margin: 30px 0;">';
        
        // Overall Status
        if ($allGood) {
            echo '<div class="status success">';
            echo '✓ All required credentials are configured!';
            echo '</div>';
            
            echo '<div class="info" style="margin-top: 20px;">';
            echo '<strong>Next Steps:</strong><br>';
            echo '1. Test with USSD simulator<br>';
            echo '2. Check error logs if payment fails<br>';
            echo '3. Verify credentials are correct in Hubtel dashboard';
            echo '</div>';
        } else {
            echo '<div class="status error">';
            echo '✗ Some credentials are missing!';
            echo '</div>';
            
            echo '<div class="warning" style="margin-top: 20px;">';
            echo '<strong>How to Fix:</strong><br>';
            echo '1. Open <code>c:\\xampp\\htdocs\\raffle\\.env</code><br>';
            echo '2. Fill in your Hubtel credentials:<br>';
            echo '<pre style="margin: 10px 0; padding: 10px; background: white; border-radius: 5px;">';
            echo 'HUBTEL_CLIENT_ID=your_client_id_here' . "\n";
            echo 'HUBTEL_CLIENT_SECRET=your_client_secret_here' . "\n";
            echo 'HUBTEL_MERCHANT_ACCOUNT=your_merchant_account_number' . "\n";
            echo 'HUBTEL_IP_WHITELIST=';
            echo '</pre>';
            echo '3. Save the file<br>';
            echo '4. Restart Apache (XAMPP Control Panel)<br>';
            echo '5. Refresh this page';
            echo '</div>';
        }
        
        // Check .env file exists
        $envFile = __DIR__ . '/.env';
        echo '<hr style="margin: 30px 0;">';
        echo '<div class="info">';
        echo '<strong>.env File Status:</strong><br>';
        if (file_exists($envFile)) {
            echo '✓ File exists at: <code>' . htmlspecialchars($envFile) . '</code><br>';
            echo 'Last modified: ' . date('Y-m-d H:i:s', filemtime($envFile));
        } else {
            echo '✗ File not found at: <code>' . htmlspecialchars($envFile) . '</code><br>';
            echo 'Please create the .env file first!';
        }
        echo '</div>';
        
        // Show where to get credentials
        echo '<hr style="margin: 30px 0;">';
        echo '<div class="info">';
        echo '<strong>Where to Get Credentials:</strong><br>';
        echo '1. Login to <a href="https://hubtel.com" target="_blank">Hubtel Dashboard</a><br>';
        echo '2. <strong>Client ID & Secret:</strong> Settings → API Keys<br>';
        echo '3. <strong>Merchant Account:</strong> Receive Money → POS Sales<br>';
        echo '<br>';
        echo '<a href="HUBTEL_QUICK_SETUP.md" target="_blank">View Complete Setup Guide</a>';
        echo '</div>';
        ?>
        
        <hr style="margin: 30px 0;">
        
        <div style="text-align: center;">
            <a href="public/ussd-simulator.php" style="display: inline-block; padding: 15px 30px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; font-weight: bold;">
                Test USSD Simulator
            </a>
        </div>
    </div>
</body>
</html>
