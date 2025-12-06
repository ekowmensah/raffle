<?php
// Test payment SMS flow
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Load environment
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($key, $value) = explode('=', $line, 2);
        putenv(trim($key) . '=' . trim($value));
    }
}

// Database
$db = new mysqli('localhost', 'root', '', 'raffle');
if ($db->connect_error) {
    die("Database connection failed: " . $db->connect_error);
}

echo "<h2>Test Payment SMS Flow</h2>";
echo "<pre>";

// Create a test successful payment
$playerId = 3; // Change to your player ID
$campaignId = 1; // Change to your campaign ID
$stationId = 1; // Change to your station ID
$amount = 10.00;
$phone = '233545644749'; // Change to your phone number

echo "=== Creating Test Payment ===\n";
echo "Player ID: {$playerId}\n";
echo "Campaign ID: {$campaignId}\n";
echo "Amount: GHS {$amount}\n";
echo "Phone: {$phone}\n\n";

// Create payment
$reference = 'TEST-' . time();
$stmt = $db->prepare("INSERT INTO payments (player_id, campaign_id, station_id, amount, gateway, gateway_reference, internal_reference, status, channel, paid_at, created_at) VALUES (?, ?, ?, ?, 'manual', ?, ?, 'success', 'TEST', NOW(), NOW())");
$stmt->bind_param('iiidss', $playerId, $campaignId, $stationId, $amount, $reference, $reference);
$stmt->execute();
$paymentId = $db->insert_id;

echo "Payment ID: {$paymentId}\n";
echo "Reference: {$reference}\n\n";

// Get campaign details
$result = $db->query("SELECT name, ticket_price FROM campaigns WHERE id = {$campaignId}");
$campaign = $result->fetch_assoc();
$ticketCount = floor($amount / $campaign['ticket_price']);

echo "=== Campaign Details ===\n";
echo "Campaign: {$campaign['name']}\n";
echo "Ticket Price: GHS {$campaign['ticket_price']}\n";
echo "Tickets to generate: {$ticketCount}\n\n";

// Generate test tickets
echo "=== Generating Tickets ===\n";
$tickets = [];
for ($i = 0; $i < $ticketCount; $i++) {
    $ticketCode = rand(1000000000, 9999999999);
    $stmt = $db->prepare("INSERT INTO tickets (player_id, campaign_id, station_id, payment_id, ticket_code, status, quantity, created_at) VALUES (?, ?, ?, ?, ?, 'active', 1, NOW())");
    $stmt->bind_param('iiiis', $playerId, $campaignId, $stationId, $paymentId, $ticketCode);
    $stmt->execute();
    $tickets[] = $ticketCode;
    echo "Ticket {$i}: {$ticketCode}\n";
}
echo "\n";

// Now send SMS using the service
echo "=== Sending SMS ===\n";
require_once '../app/core/Database.php';
require_once '../app/services/SMS/HubtelSmsService.php';

$smsService = new \App\Services\SMS\HubtelSmsService();
$result = $smsService->sendTicketConfirmation($phone, $tickets, $campaign['name'], $amount);

echo "SMS Result:\n";
print_r($result);
echo "\n";

// Check SMS logs
$smsResult = $db->query("SELECT * FROM sms_logs ORDER BY created_at DESC LIMIT 1");
$smsLog = $smsResult->fetch_assoc();

echo "=== SMS Log ===\n";
echo "ID: {$smsLog['id']}\n";
echo "Phone: {$smsLog['phone_number']}\n";
echo "Status: {$smsLog['status']}\n";
echo "Message Type: {$smsLog['message_type']}\n";
echo "Gateway: {$smsLog['gateway']}\n";
echo "Created: {$smsLog['created_at']}\n\n";

echo "=== Message Content ===\n";
echo $smsLog['message'] . "\n\n";

echo "✅ Test completed! Check your phone for SMS.\n";
echo "</pre>";
