<?php

namespace App\Services;

class SmsNotificationService
{
    private $apiUrl;
    private $apiKey;
    private $senderId;

    public function __construct()
    {
        $this->apiUrl = getenv('SMS_API_URL') ?: 'https://sms.example.com/api';
        $this->apiKey = getenv('SMS_API_KEY') ?: 'your-api-key';
        $this->senderId = getenv('SMS_SENDER_ID') ?: 'RAFFLE';
    }

    public function sendTicketNotification($phone, $tickets, $campaignName)
    {
        $ticketCodes = array_column($tickets, 'ticket_code');
        $ticketList = implode(', ', array_slice($ticketCodes, 0, 3));
        
        if (count($ticketCodes) > 3) {
            $ticketList .= '...';
        }
        
        // Calculate total entries across all tickets
        $totalEntries = 0;
        foreach ($tickets as $ticket) {
            $quantity = is_array($ticket) ? ($ticket['quantity'] ?? 1) : ($ticket->quantity ?? 1);
            $totalEntries += $quantity;
        }

        $message = "Ticket for {$campaignName}: {$ticketList}. "
                 . "Entries: {$totalEntries}. Good luck!";

        return $this->sendSms($phone, $message);
    }

    public function sendWinnerNotification($phone, $ticketCode, $prizeAmount, $campaignName)
    {
        $message = "Congratulations! Your ticket {$ticketCode} won GHS {$prizeAmount} "
                 . "in {$campaignName}!";

        return $this->sendSms($phone, $message);
    }

    public function sendPaymentConfirmation($phone, $amount, $reference)
    {
        $message = "Payment of GHS {$amount} received. Reference: {$reference}. "
                 . "Your tickets will be sent shortly.";

        return $this->sendSms($phone, $message);
    }

    private function sendSms($phone, $message)
    {
        // Format phone number
        $phone = $this->formatPhoneNumber($phone);

        // Prepare API request
        $payload = [
            'sender' => $this->senderId,
            'recipient' => $phone,
            'message' => $message,
            'api_key' => $this->apiKey
        ];

        // Simulated SMS sending - implement actual API call
        // In production, use cURL or HTTP client to send to SMS gateway
        
        // Log SMS for debugging
        error_log("SMS to {$phone}: {$message}");

        return [
            'success' => true,
            'message_id' => 'SMS-' . time(),
            'status' => 'sent'
        ];
    }

    private function formatPhoneNumber($phone)
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        if (strlen($phone) === 10) {
            $phone = '233' . substr($phone, 1);
        }
        
        return $phone;
    }
}
