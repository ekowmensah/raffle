<?php

namespace App\Services\SMS;

use App\Core\Database;

class HubtelSmsService
{
    private $clientId;
    private $clientSecret;
    private $senderId;
    private $apiUrl;
    private $db;
    
    public function __construct()
    {
        // SMS has separate credentials from payment gateway
        $this->clientId = getenv('SMS_CLIENT_ID') ?: '';
        $this->clientSecret = getenv('SMS_CLIENT_SECRET') ?: '';
        $this->senderId = getenv('SMS_SENDER_ID') ?: 'MENSWEB';
        $this->apiUrl = 'https://sms.hubtel.com/v1/messages/send';
        $this->db = new Database();
    }
    
    /**
     * Send SMS via Hubtel
     */
    public function send($phoneNumber, $message, $type = 'general')
    {
        // Normalize phone number
        $phoneNumber = $this->normalizePhone($phoneNumber);
        
        // Validate
        if (empty($phoneNumber) || empty($message)) {
            return ['success' => false, 'message' => 'Invalid phone or message'];
        }
        
        // Log as pending
        $logId = $this->logSms($phoneNumber, $message, $type, 'pending');
        
        // Send via Hubtel API
        $result = $this->sendViaHubtel($phoneNumber, $message);
        
        // Update log
        $this->updateSmsLog($logId, $result);
        
        return $result;
    }
    
    /**
     * Send ticket purchase confirmation SMS
     */
    public function sendTicketConfirmation($phoneNumber, $tickets, $campaignName, $totalAmount)
    {
        $ticketCount = count($tickets);
        $ticketCodes = array_column($tickets, 'ticket_code');
        
        // Show first 3 codes
        $codesList = implode(', ', array_slice($ticketCodes, 0, 3));
        if ($ticketCount > 3) {
            $codesList .= '...';
        }
        
        $message = "Successful! GHS " . number_format($totalAmount, 2) . "\n";
        $message .= "Campaign: {$campaignName}\n";
        $message .= "Tickets ({$ticketCount}): {$codesList}\n";
        $message .= "Good luck!";
        
        return $this->send($phoneNumber, $message, 'ticket');
    }
    
    /**
     * Send winner notification SMS
     */
    public function sendWinnerNotification($phoneNumber, $ticketCode, $prizeAmount, $prizeRank, $campaignName)
    {
        $message = "CONGRATULATIONS!\n";
        $message .= "You WON in {$campaignName}!\n";
        $message .= "Ticket: {$ticketCode}\n";
        $message .= "Prize: GHS " . number_format($prizeAmount, 2) . " ({$prizeRank})\n";
        $message .= "Your Prize will be Credited to you Soon!";
        
        return $this->send($phoneNumber, $message, 'winner');
    }
    
    /**
     * Send prize paid notification SMS
     */
    public function sendPrizePaidNotification($phoneNumber, $ticketCode, $prizeAmount, $campaignName)
    {
        $message = "PRIZE PAID!\n";
        $message .= "Campaign: {$campaignName}\n";
        $message .= "Ticket: {$ticketCode}\n";
        $message .= "Amount: GHS " . number_format($prizeAmount, 2) . "\n";
        $message .= "Your prize has been credited. Thank you for playing!";
        
        return $this->send($phoneNumber, $message, 'prize_paid');
    }
    
    /**
     * Send draw results notification SMS
     */
    public function sendDrawResultsNotification($phoneNumber, $campaignName, $drawDate, $drawType, $hasWon = false)
    {
        if ($hasWon) {
            $message = "🎉 Draw Results for {$campaignName}\n";
            $message .= "Date: {$drawDate}\n";
            $message .= "Type: " . strtoupper($drawType) . "\n";
            $message .= "YOU WON! Check your tickets for details.";
        } else {
            $message = "Draw completed for {$campaignName}\n";
            $message .= "Date: {$drawDate}\n";
            $message .= "Type: " . strtoupper($drawType) . "\n";
            $message .= "Better luck next time!";
        }
        
        return $this->send($phoneNumber, $message, 'draw');
    }
    
    /**
     * Send payment confirmation SMS
     */
    public function sendPaymentConfirmation($phoneNumber, $amount, $reference)
    {
        $message = "Payment received!\n";
        $message .= "Amount: GHS " . number_format($amount, 2) . "\n";
        $message .= "Ref: {$reference}\n";
        $message .= "Your tickets will be sent shortly.";
        
        return $this->send($phoneNumber, $message, 'payment');
    }
    
    /**
     * Send balance inquiry SMS
     */
    public function sendBalanceInquiry($phoneNumber, $playerName, $totalTickets, $totalWinnings)
    {
        $message = "Account Summary\n";
        $message .= "Name: {$playerName}\n";
        $message .= "Total Tickets: {$totalTickets}\n";
        $message .= "Total Winnings: GHS " . number_format($totalWinnings, 2) . "\n";
        $message .= "Thank you for playing!";
        
        return $this->send($phoneNumber, $message, 'balance');
    }
    
    /**
     * Send OTP SMS
     */
    public function sendOTP($phoneNumber, $otp, $expiryMinutes = 10)
    {
        $message = "Your verification code is: {$otp}\n";
        $message .= "Valid for {$expiryMinutes} minutes.\n";
        $message .= "Do not share this code.";
        
        return $this->send($phoneNumber, $message, 'otp');
    }
    
    /**
     * Send bulk SMS
     */
    public function sendBulk($phoneNumbers, $message, $type = 'general')
    {
        $results = [];
        
        foreach ($phoneNumbers as $phone) {
            $results[$phone] = $this->send($phone, $message, $type);
            // Small delay to avoid rate limiting
            usleep(100000); // 100ms
        }
        
        return $results;
    }
    
    /**
     * Send via Hubtel API
     */
    private function sendViaHubtel($phoneNumber, $message)
    {
        $url = $this->apiUrl;
        
        $data = [
            'From' => $this->senderId,
            'To' => $phoneNumber,
            'Content' => $message
        ];
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Basic ' . base64_encode($this->clientId . ':' . $this->clientSecret)
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($httpCode == 200 || $httpCode == 201) {
            $responseData = json_decode($response, true);
            return [
                'success' => true,
                'message_id' => $responseData['MessageId'] ?? null,
                'status' => 'sent',
                'response' => $response
            ];
        } else {
            return [
                'success' => false,
                'status' => 'failed',
                'error' => $error ?: $response,
                'response' => $response
            ];
        }
    }
    
    /**
     * Log SMS to database
     */
    private function logSms($phoneNumber, $message, $type, $status)
    {
        $this->db->query("INSERT INTO sms_logs 
            (phone_number, message, message_type, status, gateway, created_at) 
            VALUES (:phone, :message, :type, :status, 'hubtel', NOW())");
        
        $this->db->bind(':phone', $phoneNumber);
        $this->db->bind(':message', $message);
        $this->db->bind(':type', $type);
        $this->db->bind(':status', $status);
        $this->db->execute();
        
        return $this->db->lastInsertId();
    }
    
    /**
     * Update SMS log
     */
    private function updateSmsLog($logId, $result)
    {
        $this->db->query("UPDATE sms_logs 
            SET status = :status, 
                gateway_response = :response, 
                message_id = :message_id,
                sent_at = NOW()
            WHERE id = :id");
        
        $this->db->bind(':id', $logId);
        $this->db->bind(':status', $result['status']);
        $this->db->bind(':response', $result['response'] ?? null);
        $this->db->bind(':message_id', $result['message_id'] ?? null);
        $this->db->execute();
    }
    
    /**
     * Normalize phone number to Ghana format
     */
    private function normalizePhone($phone)
    {
        // Remove all non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Convert to international format
        if (strlen($phone) == 10 && $phone[0] == '0') {
            $phone = '233' . substr($phone, 1);
        } elseif (strlen($phone) == 9) {
            $phone = '233' . $phone;
        }
        
        return $phone;
    }
    
    /**
     * Get SMS statistics
     */
    public function getStats($startDate = null, $endDate = null)
    {
        $query = "SELECT 
            COUNT(*) as total,
            SUM(CASE WHEN status = 'sent' THEN 1 ELSE 0 END) as sent,
            SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed,
            SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
            message_type,
            DATE(created_at) as date
            FROM sms_logs";
        
        if ($startDate && $endDate) {
            $query .= " WHERE created_at BETWEEN :start AND :end";
        }
        
        $query .= " GROUP BY message_type, DATE(created_at)";
        
        $this->db->query($query);
        
        if ($startDate && $endDate) {
            $this->db->bind(':start', $startDate);
            $this->db->bind(':end', $endDate);
        }
        
        return $this->db->resultSet();
    }
}
