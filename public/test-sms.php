<?php
// Standalone SMS test - bypass routing
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Load environment variables
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($key, $value) = explode('=', $line, 2);
        putenv(trim($key) . '=' . trim($value));
    }
}

// Database connection
$db = new mysqli('localhost', 'root', '', 'raffle');
if ($db->connect_error) {
    die("Database connection failed: " . $db->connect_error);
}

// Get SMS credentials
$smsClientId = getenv('SMS_CLIENT_ID') ?: '';
$smsClientSecret = getenv('SMS_CLIENT_SECRET') ?: '';
$smsSenderId = getenv('SMS_SENDER_ID') ?: 'MENSWEB';

echo "<h2>SMS Test</h2>";
echo "<pre>";

echo "=== SMS Configuration ===\n";
echo "SMS_CLIENT_ID: " . ($smsClientId ?: 'NOT SET') . "\n";
echo "SMS_CLIENT_SECRET: " . ($smsClientSecret ? '***SET***' : 'NOT SET') . "\n";
echo "SMS_SENDER_ID: " . $smsSenderId . "\n\n";

// Test phone number - CHANGE THIS TO YOUR NUMBER
$phone = '233241234567'; // Change to your phone number
$message = 'Test SMS from Raffle System. Time: ' . date('H:i:s');

echo "=== Sending SMS ===\n";
echo "To: {$phone}\n";
echo "Message: {$message}\n\n";

// Normalize phone
if (strlen($phone) == 10 && $phone[0] == '0') {
    $phone = '233' . substr($phone, 1);
}

// Send via Hubtel
$url = 'https://sms.hubtel.com/v1/messages/send';
$data = [
    'From' => $smsSenderId,
    'To' => $phone,
    'Content' => $message
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Basic ' . base64_encode($smsClientId . ':' . $smsClientSecret)
]);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "=== Response ===\n";
echo "HTTP Code: {$httpCode}\n";
if ($error) {
    echo "cURL Error: {$error}\n";
}
echo "Response: {$response}\n\n";

// Log to database
$status = ($httpCode == 200 || $httpCode == 201) ? 'sent' : 'failed';
$stmt = $db->prepare("INSERT INTO sms_logs (phone_number, message, message_type, status, gateway, gateway_response, created_at) VALUES (?, ?, 'general', ?, 'hubtel', ?, NOW())");
$stmt->bind_param('ssss', $phone, $message, $status, $response);
$stmt->execute();
$logId = $db->insert_id;

echo "=== Database Log ===\n";
echo "Log ID: {$logId}\n";
echo "Status: {$status}\n\n";

// Check recent SMS logs
$result = $db->query("SELECT * FROM sms_logs ORDER BY created_at DESC LIMIT 3");
echo "=== Recent SMS Logs ===\n";
while ($row = $result->fetch_assoc()) {
    echo "ID: {$row['id']} | Phone: {$row['phone_number']} | Status: {$row['status']} | Time: {$row['created_at']}\n";
}

echo "</pre>";
