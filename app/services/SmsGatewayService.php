<?php

namespace App\Services;

class SmsGatewayService
{
    private $apiKey;
    private $senderId;
    private $gateway;
    
    public function __construct()
    {
        // Load from config or environment
        $this->apiKey = getenv('SMS_API_KEY') ?: 'your-api-key';
        $this->senderId = getenv('SMS_SENDER_ID') ?: 'RAFFLE';
        $this->gateway = getenv('SMS_GATEWAY') ?: 'hubtel'; // hubtel, arkesel, mnotify
    }
    
    /**
     * Send SMS message
     */
    public function send($phoneNumber, $message)
    {
        switch ($this->gateway) {
            case 'hubtel':
                return $this->sendViaHubtel($phoneNumber, $message);
            case 'arkesel':
                return $this->sendViaArkesel($phoneNumber, $message);
            case 'mnotify':
                return $this->sendViaMNotify($phoneNumber, $message);
            default:
                return $this->logMessage($phoneNumber, $message);
        }
    }
    
    /**
     * Send payment confirmation SMS
     */
    public function sendPaymentConfirmation($phoneNumber, $amount, $ticketCodes)
    {
        $message = "Payment confirmed! Amount: GHS " . number_format($amount, 2) . "\n";
        $message .= "Ticket(s): " . implode(', ', $ticketCodes) . "\n";
        $message .= "Good luck!";
        
        return $this->send($phoneNumber, $message);
    }
    
    /**
     * Send ticket delivery SMS
     */
    public function sendTicketDelivery($phoneNumber, $ticketCode, $campaignName)
    {
        $message = "Your raffle ticket:\n";
        $message .= "Code: {$ticketCode}\n";
        $message .= "Campaign: {$campaignName}\n";
        $message .= "Keep this code safe. Good luck!";
        
        return $this->send($phoneNumber, $message);
    }
    
    /**
     * Send draw notification SMS
     */
    public function sendDrawNotification($phoneNumber, $campaignName, $drawDate, $drawType)
    {
        $message = "Draw Alert!\n";
        $message .= "Campaign: {$campaignName}\n";
        $message .= "Type: " . strtoupper($drawType) . "\n";
        $message .= "Date: {$drawDate}\n";
        $message .= "Check results soon!";
        
        return $this->send($phoneNumber, $message);
    }
    
    /**
     * Send winner notification SMS
     */
    public function sendWinnerNotification($phoneNumber, $prizeAmount, $prizeRank, $campaignName)
    {
        $message = "CONGRATULATIONS!\n";
        $message .= "You WON in {$campaignName}!\n";
        $message .= "Prize: GHS " . number_format($prizeAmount, 2) . "\n";
        $message .= "Rank: {$prizeRank}\n";
        $message .= "Contact us to claim your prize!";
        
        return $this->send($phoneNumber, $message);
    }
    
    /**
     * Send balance alert SMS
     */
    public function sendBalanceAlert($phoneNumber, $playerName, $totalTickets, $totalWinnings)
    {
        $message = "Account Summary for {$playerName}:\n";
        $message .= "Total Tickets: {$totalTickets}\n";
        $message .= "Total Winnings: GHS " . number_format($totalWinnings, 2) . "\n";
        $message .= "Thank you for playing!";
        
        return $this->send($phoneNumber, $message);
    }
    
    /**
     * Send via Hubtel SMS API
     */
    private function sendViaHubtel($phoneNumber, $message)
    {
        $url = 'https://sms.hubtel.com/v1/messages/send';
        
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
            'Authorization: Basic ' . base64_encode($this->apiKey)
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        $this->logSms($phoneNumber, $message, $httpCode == 200 ? 'sent' : 'failed', $response);
        
        return $httpCode == 200;
    }
    
    /**
     * Send via Arkesel SMS API
     */
    private function sendViaArkesel($phoneNumber, $message)
    {
        $url = 'https://sms.arkesel.com/api/v2/sms/send';
        
        $data = [
            'sender' => $this->senderId,
            'recipients' => [$phoneNumber],
            'message' => $message
        ];
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'api-key: ' . $this->apiKey
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        $this->logSms($phoneNumber, $message, $httpCode == 200 ? 'sent' : 'failed', $response);
        
        return $httpCode == 200;
    }
    
    /**
     * Send via MNotify SMS API
     */
    private function sendViaMNotify($phoneNumber, $message)
    {
        $url = 'https://api.mnotify.com/api/sms/quick';
        
        $data = [
            'key' => $this->apiKey,
            'to' => $phoneNumber,
            'msg' => $message,
            'sender_id' => $this->senderId
        ];
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        $this->logSms($phoneNumber, $message, $httpCode == 200 ? 'sent' : 'failed', $response);
        
        return $httpCode == 200;
    }
    
    /**
     * Log message (for development/testing)
     */
    private function logMessage($phoneNumber, $message)
    {
        $logFile = '../storage/logs/sms_' . date('Y-m-d') . '.log';
        $logEntry = date('Y-m-d H:i:s') . " | To: {$phoneNumber} | Message: {$message}\n";
        
        file_put_contents($logFile, $logEntry, FILE_APPEND);
        
        $this->logSms($phoneNumber, $message, 'logged', 'Development mode');
        
        return true;
    }
    
    /**
     * Log SMS to database
     */
    private function logSms($phoneNumber, $message, $status, $response)
    {
        $db = new \App\Core\Database();
        
        $db->query("INSERT INTO sms_logs (phone_number, message, status, gateway_response, created_at) 
                   VALUES (:phone, :message, :status, :response, NOW())");
        $db->bind(':phone', $phoneNumber);
        $db->bind(':message', $message);
        $db->bind(':status', $status);
        $db->bind(':response', $response);
        $db->execute();
    }
    
    /**
     * Send bulk SMS
     */
    public function sendBulk($phoneNumbers, $message)
    {
        $results = [];
        
        foreach ($phoneNumbers as $phone) {
            $results[$phone] = $this->send($phone, $message);
        }
        
        return $results;
    }
    
    /**
     * Get SMS balance (if supported by gateway)
     */
    public function getBalance()
    {
        // Implementation depends on gateway
        return 0;
    }
}
