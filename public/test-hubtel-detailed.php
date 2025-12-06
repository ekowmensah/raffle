<!DOCTYPE html>
<html>
<head>
    <title>Hubtel Detailed Diagnostic</title>
    <style>
        body { font-family: monospace; padding: 20px; background: #1e1e1e; color: #d4d4d4; }
        .section { background: #252526; padding: 15px; margin: 10px 0; border-left: 3px solid #007acc; }
        .success { color: #4ec9b0; }
        .error { color: #f48771; }
        .warning { color: #dcdcaa; }
        pre { background: #1e1e1e; padding: 10px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>🔍 Hubtel Detailed Diagnostic</h1>
    
    <?php
    require_once '../app/config/config.php';
    
    echo "<div class='section'>";
    echo "<h3>1. Configuration Check</h3>";
    
    $clientId = HUBTEL_CLIENT_ID;
    $clientSecret = HUBTEL_CLIENT_SECRET;
    $merchantAccount = HUBTEL_MERCHANT_ACCOUNT;
    $mode = HUBTEL_MODE;
    
    echo "Mode: <span class='warning'>{$mode}</span><br>";
    echo "Client ID: " . ($clientId ? "<span class='success'>SET (" . substr($clientId, 0, 4) . "***)</span>" : "<span class='error'>NOT SET</span>") . "<br>";
    echo "Client Secret: " . ($clientSecret ? "<span class='success'>SET (" . strlen($clientSecret) . " chars)</span>" : "<span class='error'>NOT SET</span>") . "<br>";
    echo "Merchant Account: " . ($merchantAccount ? "<span class='success'>{$merchantAccount}</span>" : "<span class='error'>NOT SET</span>") . "<br>";
    echo "</div>";
    
    if (!$clientId || !$clientSecret || !$merchantAccount) {
        echo "<div class='section error'>❌ Cannot proceed - credentials missing</div>";
        exit;
    }
    
    // Test 1: Basic Auth Test
    echo "<div class='section'>";
    echo "<h3>2. Authentication Test</h3>";
    
    $authString = base64_encode("{$clientId}:{$clientSecret}");
    echo "Auth String Length: " . strlen($authString) . " chars<br>";
    echo "Auth Header: <code>Authorization: Basic " . substr($authString, 0, 20) . "...</code><br>";
    echo "</div>";
    
    // Test 2: Try different endpoint formats
    echo "<div class='section'>";
    echo "<h3>3. Testing Different Endpoints</h3>";
    
    $baseUrl = ($mode === 'sandbox') ? 'https://sandbox.hubtel.com/merchantaccount' : 'https://rmp.hubtel.com';
    
    $endpoints = [
        "Status Check (Old)" => "https://api-txnstatus.hubtel.com/transactions/{$merchantAccount}/status?clientReference=TEST123",
        "Direct Debit (Old)" => "https://rmp.hubtel.com/receive-money/direct-debit",
        "Merchant Account API" => "{$baseUrl}/merchantaccount/merchants/{$merchantAccount}/receive/mobilemoney",
        "Alternative Format" => "{$baseUrl}/merchants/{$merchantAccount}/receive/mobilemoney",
    ];
    
    foreach ($endpoints as $name => $url) {
        echo "<br><strong>{$name}:</strong><br>";
        echo "<code>{$url}</code><br>";
        
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_HTTPHEADER => [
                'Authorization: Basic ' . $authString,
                'Content-Type: application/json'
            ],
            CURLOPT_POSTFIELDS => json_encode([
                'CustomerMsisdn' => '233241234567',
                'Channel' => 'mtn-gh',
                'Amount' => 1.00,
                'Description' => 'Test',
                'ClientReference' => 'TEST' . time()
            ]),
            CURLOPT_TIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => true
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);
        
        if ($curlError) {
            echo "<span class='error'>❌ cURL Error: {$curlError}</span><br>";
        } else {
            if ($httpCode === 200 || $httpCode === 201) {
                echo "<span class='success'>✓ HTTP {$httpCode} - SUCCESS!</span><br>";
                echo "<pre>" . htmlspecialchars(substr($response, 0, 500)) . "</pre>";
            } elseif ($httpCode === 401) {
                echo "<span class='error'>❌ HTTP 401 - Invalid Credentials</span><br>";
            } elseif ($httpCode === 403) {
                echo "<span class='error'>❌ HTTP 403 - Forbidden (Wrong endpoint or account)</span><br>";
            } elseif ($httpCode === 404) {
                echo "<span class='warning'>⚠ HTTP 404 - Endpoint not found</span><br>";
            } else {
                echo "<span class='error'>❌ HTTP {$httpCode}</span><br>";
            }
            
            if ($response) {
                $decoded = json_decode($response, true);
                if ($decoded) {
                    echo "<pre>" . htmlspecialchars(json_encode($decoded, JSON_PRETTY_PRINT)) . "</pre>";
                }
            }
        }
    }
    echo "</div>";
    
    // Test 3: Check Hubtel Documentation
    echo "<div class='section'>";
    echo "<h3>4. Recommendations</h3>";
    echo "<ul>";
    echo "<li>If all endpoints return 403: Your credentials might be invalid or expired</li>";
    echo "<li>If some endpoints return 404: The API structure might have changed</li>";
    echo "<li>If you see 401: Client ID or Secret is wrong</li>";
    echo "<li>Contact Hubtel Support: support@hubtel.com or +233 30 281 0100</li>";
    echo "<li>Check Hubtel Dashboard for API documentation updates</li>";
    echo "</ul>";
    echo "</div>";
    
    // Test 4: Raw cURL command
    echo "<div class='section'>";
    echo "<h3>5. Test with cURL Command</h3>";
    echo "<p>Copy and run this in your terminal:</p>";
    echo "<pre>";
    echo "curl -X POST '{$baseUrl}/merchantaccount/merchants/{$merchantAccount}/receive/mobilemoney' \\\n";
    echo "  -u '{$clientId}:{$clientSecret}' \\\n";
    echo "  -H 'Content-Type: application/json' \\\n";
    echo "  -d '{\n";
    echo "    \"CustomerMsisdn\": \"233241234567\",\n";
    echo "    \"Channel\": \"mtn-gh\",\n";
    echo "    \"Amount\": 1.00,\n";
    echo "    \"Description\": \"Test Payment\",\n";
    echo "    \"ClientReference\": \"TEST" . time() . "\"\n";
    echo "  }'";
    echo "</pre>";
    echo "</div>";
    ?>
    
    <div class="section">
        <h3>6. Next Steps</h3>
        <ol>
            <li>Check which endpoint (if any) returned HTTP 200</li>
            <li>If none work, verify credentials in Hubtel Dashboard</li>
            <li>Check if your account has "Direct Debit" or "Receive Money" enabled</li>
            <li>Confirm you're using the right credentials (Production vs Sandbox)</li>
        </ol>
    </div>
</body>
</html>
