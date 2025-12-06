<?php
/**
 * Debug payment and ticket generation
 */

require_once '../app/core/Database.php';

$db = new \App\Core\Database();

// Get the latest payment
$db->query("SELECT * FROM payments ORDER BY id DESC LIMIT 1");
$payment = $db->single();

echo "<h2>Latest Payment</h2>";
echo "<pre>";
print_r($payment);
echo "</pre>";

if ($payment) {
    // Get tickets for this payment
    $db->query("SELECT * FROM tickets WHERE payment_id = :payment_id");
    $db->bind(':payment_id', $payment->id);
    $tickets = $db->resultSet();
    
    echo "<h2>Tickets for Payment #{$payment->id}</h2>";
    echo "<p>Count: " . count($tickets) . "</p>";
    echo "<pre>";
    print_r($tickets);
    echo "</pre>";
    
    // Get campaign details
    $db->query("SELECT * FROM raffle_campaigns WHERE id = :id");
    $db->bind(':id', $payment->campaign_id);
    $campaign = $db->single();
    
    echo "<h2>Campaign Details</h2>";
    echo "<pre>";
    print_r($campaign);
    echo "</pre>";
    
    echo "<h2>Calculation</h2>";
    echo "<p>Payment Amount: {$payment->amount}</p>";
    echo "<p>Campaign Ticket Price: {$campaign->ticket_price}</p>";
    echo "<p>Calculated Tickets: " . floor($payment->amount / $campaign->ticket_price) . "</p>";
}
?>
