<!DOCTYPE html>
<html>
<head>
    <title>Hubtel API Test</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 900px;
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
        .test-result {
            padding: 15px;
            margin: 15px 0;
            border-radius: 5px;
            border-left: 4px solid #ccc;
        }
        .success {
            background: #d4edda;
            border-left-color: #28a745;
        }
        .error {
            background: #f8d7da;
            border-left-color: #dc3545;
        }
        .info {
            background: #d1ecf1;
            border-left-color: #17a2b8;
        }
        pre {
            background: #f4f4f4;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
            font-size: 12px;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🧪 Hubtel API Connection Test</h1>
        
        <?php
        require_once '../app/config/config.php';
        
        $mode = defined('HUBTEL_MODE') ? HUBTEL_MODE : 'sandbox';
        
        echo '<div class="info">';
        echo '<strong>Testing Hubtel API Connection...</strong><br>';
        echo 'Mode: <strong>' . strtoupper($mode) . '</strong><br>';
        echo 'This will attempt to connect to Hubtel using your credentials.';
        echo '</div>';
        
        // Test credentials
        $clientId = HUBTEL_CLIENT_ID;
        $clientSecret = HUBTEL_CLIENT_SECRET;
        $merchantAccount = HUBTEL_MERCHANT_ACCOUNT;
        
        if (empty($clientId) || empty($clientSecret) || empty($merchantAccount)) {
            echo '<div class="test-result error">';
            echo '<strong>❌ Credentials Missing</strong><br>';
            echo 'Please configure all Hubtel credentials first.';
            echo '</div>';
            exit;
        }
        
        // Test 1: Check Status API (simpler endpoint)
        echo '<h3>Test 1: Status Check API</h3>';
        
        $testReference = 'TEST' . time();
        $baseUrl = ($mode === 'sandbox') 
            ? 'https://sandbox.hubtel.com/merchantaccount' 
            : 'https://api-txnstatus.hubtel.com';
        $statusUrl = "{$baseUrl}/transactions/{$merchantAccount}/status?clientReference={$testReference}";
        
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $statusUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Basic ' . base64_encode("{$clientId}:{$clientSecret}"),
                'Content-Type: application/json',
                'Accept: application/json'
            ],
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => true
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);
        
        echo '<div class="test-result ' . ($httpCode === 200 || $httpCode === 404 ? 'success' : 'error') . '">';
        echo '<strong>HTTP Status Code:</strong> ' . $httpCode . '<br>';
        
        if ($curlError) {
            echo '<strong>cURL Error:</strong> ' . htmlspecialchars($curlError) . '<br>';
        }
        
        if ($httpCode === 200) {
            echo '<strong>✓ Authentication Successful!</strong><br>';
            echo 'Your credentials are valid and working.';
        } elseif ($httpCode === 404) {
            echo '<strong>✓ Authentication Successful!</strong><br>';
            echo 'Transaction not found (expected for test reference), but authentication worked.';
        } elseif ($httpCode === 401) {
            echo '<strong>❌ Authentication Failed (401 Unauthorized)</strong><br>';
            echo 'Your Client ID or Client Secret is incorrect.';
        } elseif ($httpCode === 403) {
            echo '<strong>❌ Authentication Failed (403 Forbidden)</strong><br>';
            echo 'Possible reasons:<br>';
            echo '• Credentials are for sandbox but you\'re using production URL (or vice versa)<br>';
            echo '• Account doesn\'t have permission for this API<br>';
            echo '• IP address not whitelisted in Hubtel dashboard';
        } else {
            echo '<strong>❌ Unexpected Response</strong><br>';
            echo 'HTTP Code: ' . $httpCode;
        }
        
        echo '</div>';
        
        echo '<details style="margin: 15px 0;">';
        echo '<summary style="cursor: pointer; font-weight: bold;">View Response Details</summary>';
        echo '<pre>' . htmlspecialchars($response) . '</pre>';
        echo '</details>';
        
        // Test 2: Payment Initialization (more comprehensive)
        echo '<h3>Test 2: Payment Initialization API</h3>';
        
        $paymentBaseUrl = ($mode === 'sandbox') 
            ? 'https://sandbox.hubtel.com/merchantaccount' 
            : 'https://rmp.hubtel.com';
        $paymentUrl = "{$paymentBaseUrl}/receive-money/direct-debit";
        $testPhone = '233241234567';
        $testAmount = 1.00;
        $testRef = 'TEST' . time() . rand(1000, 9999);
        
        $paymentData = [
            'CustomerMsisdn' => $testPhone,
            'Channel' => 'mtn-gh',
            'Amount' => $testAmount,
            'PrimaryCallbackUrl' => 'http://localhost/raffle/webhook/hubtel',
            'Description' => 'Test Payment',
            'ClientReference' => $testRef,
            'CustomerName' => 'Test User',
            'CustomerEmail' => 'test@example.com'
        ];
        
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $paymentUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($paymentData),
            CURLOPT_HTTPHEADER => [
                'Authorization: Basic ' . base64_encode("{$clientId}:{$clientSecret}"),
                'Content-Type: application/json',
                'Accept: application/json'
            ],
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => true
        ]);
        
        $response2 = curl_exec($ch);
        $httpCode2 = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError2 = curl_error($ch);
        curl_close($ch);
        
        echo '<div class="test-result ' . ($httpCode2 === 200 || $httpCode2 === 201 ? 'success' : 'error') . '">';
        echo '<strong>HTTP Status Code:</strong> ' . $httpCode2 . '<br>';
        
        if ($curlError2) {
            echo '<strong>cURL Error:</strong> ' . htmlspecialchars($curlError2) . '<br>';
        }
        
        if ($httpCode2 === 200 || $httpCode2 === 201) {
            echo '<strong>✓ Payment API Works!</strong><br>';
            echo 'Your credentials can initiate payments successfully.';
            
            $responseData = json_decode($response2, true);
            if (isset($responseData['ResponseCode'])) {
                echo '<br><strong>Response Code:</strong> ' . htmlspecialchars($responseData['ResponseCode']);
                echo '<br><strong>Message:</strong> ' . htmlspecialchars($responseData['Message'] ?? 'N/A');
            }
        } elseif ($httpCode2 === 401) {
            echo '<strong>❌ Authentication Failed (401)</strong><br>';
            echo 'Client ID or Client Secret is incorrect.';
        } elseif ($httpCode2 === 403) {
            echo '<strong>❌ Forbidden (403)</strong><br>';
            echo '<strong>Common Causes:</strong><br>';
            echo '• <strong>Sandbox vs Production:</strong> Your credentials might be for sandbox, but you\'re hitting production API<br>';
            echo '• <strong>Merchant Account:</strong> The account number might be incorrect<br>';
            echo '• <strong>API Access:</strong> Your account might not have "Direct Debit" enabled<br>';
            echo '• <strong>IP Whitelist:</strong> Your IP might need to be whitelisted in Hubtel dashboard<br><br>';
            echo '<strong>Action Required:</strong><br>';
            echo '1. Login to Hubtel Dashboard<br>';
            echo '2. Check if you\'re using Sandbox or Production credentials<br>';
            echo '3. Verify "Receive Money" → "Direct Debit" is enabled<br>';
            echo '4. Check API permissions for your account';
        } elseif ($httpCode2 === 400) {
            echo '<strong>⚠️ Bad Request (400)</strong><br>';
            echo 'Authentication worked, but request format has issues.';
            $responseData = json_decode($response2, true);
            if (isset($responseData['Message'])) {
                echo '<br><strong>Error:</strong> ' . htmlspecialchars($responseData['Message']);
            }
        } else {
            echo '<strong>❌ Unexpected Response</strong><br>';
            echo 'HTTP Code: ' . $httpCode2;
        }
        
        echo '</div>';
        
        echo '<details style="margin: 15px 0;">';
        echo '<summary style="cursor: pointer; font-weight: bold;">View Request Data</summary>';
        echo '<pre>' . htmlspecialchars(json_encode($paymentData, JSON_PRETTY_PRINT)) . '</pre>';
        echo '</details>';
        
        echo '<details style="margin: 15px 0;">';
        echo '<summary style="cursor: pointer; font-weight: bold;">View Response Details</summary>';
        echo '<pre>' . htmlspecialchars($response2) . '</pre>';
        echo '</details>';
        
        // Recommendations
        echo '<hr style="margin: 30px 0;">';
        echo '<h3>📋 Recommendations</h3>';
        
        if ($httpCode2 === 403) {
            echo '<div class="test-result error">';
            echo '<strong>Your credentials are set but Hubtel is rejecting them (403 Forbidden)</strong><br><br>';
            echo '<strong>Most Likely Issue: Sandbox vs Production Mismatch</strong><br>';
            echo '• If your credentials are from <strong>Sandbox</strong>, you need to use sandbox URLs<br>';
            echo '• If your credentials are from <strong>Production</strong>, you need production URLs<br><br>';
            echo '<strong>How to Check:</strong><br>';
            echo '1. Login to Hubtel Dashboard<br>';
            echo '2. Look at the top of the page - it should say "Sandbox" or "Production"<br>';
            echo '3. Make sure you\'re using credentials from the correct environment<br><br>';
            echo '<strong>Alternative: Contact Hubtel Support</strong><br>';
            echo 'Email: support@hubtel.com<br>';
            echo 'Phone: +233 30 281 0100<br>';
            echo 'Tell them: "Getting 403 Forbidden on Direct Debit API"';
            echo '</div>';
        } elseif ($httpCode2 === 200 || $httpCode2 === 201) {
            echo '<div class="test-result success">';
            echo '<strong>✓ Everything looks good!</strong><br>';
            echo 'Your Hubtel integration should work in the USSD system.';
            echo '</div>';
        }
        ?>
        
        <hr style="margin: 30px 0;">
        
        <div style="text-align: center;">
            <a href="ussd-simulator.php" class="btn">Test USSD Simulator</a>
            <a href="check-hubtel-credentials.php" class="btn">Check Credentials</a>
            <a href="javascript:location.reload()" class="btn" style="background: #28a745;">Refresh Test</a>
        </div>
    </div>
</body>
</html>
