<?php
require_once '../app/core/bootstrap.php';
require_once '../app/services/SMS/HubtelSmsService.php';

$smsService = new \App\Services\SMS\HubtelSmsService();

// Test SMS
$phone = '0241234567'; // Change to your phone number
$message = 'Test SMS from Raffle System. Time: ' . date('H:i:s');

echo "Sending test SMS to {$phone}...\n\n";

$result = $smsService->send($phone, $message, 'general');

echo "Result:\n";
print_r($result);

echo "\n\nChecking SMS logs...\n";

// Check database
$db = new \App\Core\Database();
$db->query("SELECT * FROM sms_logs ORDER BY created_at DESC LIMIT 1");
$log = $db->single();

echo "\nLatest SMS Log:\n";
print_r($log);

echo "\n\nCredentials being used:\n";
echo "SMS_CLIENT_ID: " . (getenv('SMS_CLIENT_ID') ?: 'NOT SET') . "\n";
echo "SMS_CLIENT_SECRET: " . (getenv('SMS_CLIENT_SECRET') ? '***SET***' : 'NOT SET') . "\n";
echo "SMS_SENDER_ID: " . (getenv('SMS_SENDER_ID') ?: 'NOT SET') . "\n";
