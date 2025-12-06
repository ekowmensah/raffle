<?php
/**
 * Test if routing is working
 */

echo "<h1>Routing Test</h1>";

echo "<h3>1. Direct PHP File Test</h3>";
echo "<p>✅ This file works (you're seeing this)</p>";

echo "<h3>2. Test USSD Endpoint</h3>";
echo "<p>Testing: <code>index.php?url=ussd</code></p>";

// Test the USSD endpoint
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$scriptPath = dirname($_SERVER['SCRIPT_NAME']);
$testUrl = "{$protocol}://{$host}{$scriptPath}/index.php?url=ussd";

echo "<p>Full URL: <code>" . htmlspecialchars($testUrl) . "</code></p>";

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => $testUrl,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => http_build_query([
        'sessionId' => 'TEST123',
        'phoneNumber' => '0241234567',
        'text' => ''
    ])
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

if ($error) {
    echo "<p style='color: red;'>❌ cURL Error: " . htmlspecialchars($error) . "</p>";
} else {
    echo "<p>HTTP Code: <strong>{$httpCode}</strong></p>";
    
    if ($httpCode === 200) {
        echo "<p style='color: green;'>✅ USSD endpoint is working!</p>";
        echo "<h4>Response:</h4>";
        echo "<pre>" . htmlspecialchars($response) . "</pre>";
    } else {
        echo "<p style='color: red;'>❌ USSD endpoint returned error</p>";
        echo "<h4>Response:</h4>";
        echo "<pre>" . htmlspecialchars($response) . "</pre>";
    }
}

echo "<h3>3. Environment Info</h3>";
echo "<p>Host: " . ($_SERVER['HTTP_HOST'] ?? 'N/A') . "</p>";
echo "<p>Document Root: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'N/A') . "</p>";
echo "<p>Script Filename: " . (__FILE__) . "</p>";
echo "<p>BASE_URL: " . (defined('BASE_URL') ? BASE_URL : 'Not defined') . "</p>";

echo "<h3>4. Test .htaccess</h3>";
echo "<p>Try accessing: <a href='ussd'>ussd</a> (should route to index.php?url=ussd)</p>";
?>
