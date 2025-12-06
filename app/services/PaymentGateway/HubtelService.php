<?php

namespace App\Services\PaymentGateway;

use App\Core\Database;

/**
 * Hubtel Payment Gateway Service
 * Handles Direct Receive Money API for mobile money payments
 */
class HubtelService
{
    private $config;
    private $db;
    private $baseUrl;
    private $statusCheckUrl;
    
    public function __construct()
    {
        $this->db = new Database();
        
        // Load configuration from environment or config file
        $isSandbox = strtolower(getenv('HUBTEL_MODE') ?: 'sandbox') === 'sandbox';
        
        $this->config = [
            'client_id' => getenv('HUBTEL_CLIENT_ID') ?: '',
            'client_secret' => getenv('HUBTEL_CLIENT_SECRET') ?: '',
            'merchant_account' => getenv('HUBTEL_MERCHANT_ACCOUNT') ?: '', // POS Sales ID
            'mode' => $isSandbox ? 'sandbox' : 'production',
            'base_url' => $isSandbox ? 'https://sandbox.hubtel.com/merchantaccount' : 'https://rmp.hubtel.com',
            'status_check_url' => $isSandbox ? 'https://sandbox.hubtel.com/merchantaccount' : 'https://api-txnstatus.hubtel.com',
            'ip_whitelist' => explode(',', getenv('HUBTEL_IP_WHITELIST') ?: '')
        ];
        
        $this->baseUrl = $this->config['base_url'];
        $this->statusCheckUrl = $this->config['status_check_url'];
    }

    /**
     * Initialize a mobile money payment
     * 
     * @param array $data Payment data (phone, amount, reference, player_name, email, description)
     * @return array Payment response
     */
    public function initiatePayment($data)
    {
        $required = ['amount', 'phone', 'reference'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                return [
                    'success' => false,
                    'message' => "Missing required field: {$field}",
                    'error_code' => 'MISSING_FIELD'
                ];
            }
        }
        
        // Validate IP whitelisting
        if (!$this->isIpWhitelisted()) {
            error_log("Hubtel: IP not whitelisted - " . $this->getClientIp());
        }
        
        // Format phone number and detect channel
        $phone = $this->formatPhoneNumber($data['phone']);
        $channel = $this->detectMobileMoneyChannel($phone);
        
        // Get merchant account (POS Sales ID)
        $merchantAccount = $this->config['merchant_account'];
        if (empty($merchantAccount)) {
            return [
                'success' => false,
                'message' => 'Hubtel merchant account (POS Sales ID) not configured',
                'error_code' => 'CONFIG_ERROR'
            ];
        }
        
        // Prepare payment data for Hubtel
        $paymentData = [
            'CustomerName' => $data['player_name'] ?? 'Raffle Player',
            'CustomerMsisdn' => $phone,
            'CustomerEmail' => $data['email'] ?? $this->generateEmailFromPhone($phone),
            'Channel' => $channel,
            'Amount' => (float) $data['amount'],
            'PrimaryCallbackUrl' => $data['callback_url'] ?? $this->getDefaultCallbackUrl(),
            'Description' => $data['description'] ?? 'Raffle Ticket Purchase',
            'ClientReference' => $data['reference']
        ];
        
        try {
            $endpoint = "/merchantaccount/merchants/{$merchantAccount}/receive/mobilemoney";
            
            // Debug log
            error_log("Hubtel Payment Init - Merchant: {$merchantAccount}, Phone: {$phone}, Channel: {$channel}");
            
            $response = $this->makeApiCall('POST', $endpoint, $paymentData);
            
            // Check response code
            $responseCode = $response['ResponseCode'] ?? '';
            $httpStatus = $response['_http_status'] ?? 200;
            
            // Log the request
            $this->logGatewayActivity(
                'initialize',
                $data['reference'],
                $paymentData,
                $response,
                $responseCode,
                $httpStatus,
                ($responseCode !== '0001' && $responseCode !== '0000') ? ($response['Message'] ?? 'Unknown error') : null
            );
            
            if ($responseCode === '0001' || $responseCode === '0000') {
                // Pending or immediate success
                $responseData = $response['Data'] ?? [];
                
                return [
                    'success' => true,
                    'gateway_reference' => $responseData['TransactionId'] ?? $data['reference'],
                    'client_reference' => $data['reference'],
                    'amount' => $responseData['Amount'] ?? $data['amount'],
                    'charges' => $responseData['Charges'] ?? 0,
                    'amount_charged' => $responseData['AmountCharged'] ?? $data['amount'],
                    'status' => $responseCode === '0000' ? 'success' : 'pending',
                    'message' => $response['Message'] ?? 'Payment initiated. Please approve on your phone.',
                    'channel' => $channel,
                    'requires_approval' => true,
                    'raw_response' => $response
                ];
            } else {
                // Payment failed or error
                $errorMessage = $this->getErrorMessage($responseCode, $response['Message'] ?? 'Payment initialization failed');
                
                return [
                    'success' => false,
                    'message' => $errorMessage,
                    'error_code' => $responseCode,
                    'raw_response' => $response
                ];
            }
        } catch (\Exception $e) {
            error_log("Hubtel initialization error: " . $e->getMessage());
            
            // Log the error
            $this->logGatewayActivity(
                'initialize',
                $data['reference'] ?? 'unknown',
                $paymentData ?? [],
                [],
                'ERROR',
                0,
                $e->getMessage()
            );
            
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'error_code' => 'INITIALIZATION_FAILED'
            ];
        }
    }

    /**
     * Verify a payment transaction
     * 
     * @param string $clientReference Client reference
     * @return array Verification result
     */
    public function verifyPayment($clientReference)
    {
        try {
            // Get merchant account
            $merchantAccount = $this->config['merchant_account'];
            if (empty($merchantAccount)) {
                return [
                    'success' => false,
                    'message' => 'Hubtel merchant account not configured',
                    'error_code' => 'CONFIG_ERROR'
                ];
            }
            
            // Call status check API
            $endpoint = "/transactions/{$merchantAccount}/status";
            $queryParams = ['clientReference' => $clientReference];
            
            $response = $this->makeApiCall('GET', $endpoint, null, $queryParams, true);
            
            $responseCode = $response['responseCode'] ?? '';
            $httpStatus = $response['_http_status'] ?? 200;
            
            // Log the verification request
            $this->logGatewayActivity(
                'verify',
                $clientReference,
                ['clientReference' => $clientReference],
                $response,
                $responseCode,
                $httpStatus,
                ($responseCode !== '0000') ? ($response['message'] ?? 'Verification failed') : null
            );
            
            if ($responseCode === '0000') {
                $data = $response['data'] ?? [];
                $status = strtolower($data['status'] ?? 'unknown');
                
                return [
                    'success' => true,
                    'reference' => $clientReference,
                    'transaction_id' => $data['transactionId'] ?? null,
                    'external_transaction_id' => $data['externalTransactionId'] ?? null,
                    'amount' => $data['amount'] ?? 0,
                    'charges' => $data['charges'] ?? 0,
                    'amount_after_charges' => $data['amountAfterCharges'] ?? 0,
                    'status' => $this->mapHubtelStatus($status),
                    'payment_method' => $data['paymentMethod'] ?? 'mobile_money',
                    'paid_at' => $data['date'] ?? null,
                    'raw_response' => $response
                ];
            } else {
                // If payment not found or still pending, return pending status
                $message = $response['message'] ?? 'Payment verification failed';
                if (stripos($message, 'not found') !== false || stripos($message, 'pending') !== false) {
                    return [
                        'success' => true,
                        'status' => 'pending',
                        'message' => 'Payment is still pending approval'
                    ];
                }
                
                return [
                    'success' => false,
                    'message' => $message,
                    'error_code' => 'VERIFICATION_FAILED'
                ];
            }
        } catch (\Exception $e) {
            error_log("Hubtel verification error: " . $e->getMessage());
            
            // Check if error is "payment record not found" - treat as pending
            $errorMessage = $e->getMessage();
            if (stripos($errorMessage, 'not found') !== false || stripos($errorMessage, 'record not found') !== false) {
                error_log("Hubtel: Payment record not found, treating as pending");
                
                // Log as pending, not error
                $this->logGatewayActivity(
                    'verify',
                    $clientReference,
                    ['clientReference' => $clientReference],
                    [],
                    'PENDING',
                    0,
                    'Payment record not found - still pending'
                );
                
                return [
                    'success' => true,
                    'status' => 'pending',
                    'message' => 'Payment is still pending approval'
                ];
            }
            
            // Log the error for other exceptions
            $this->logGatewayActivity(
                'verify',
                $clientReference,
                ['clientReference' => $clientReference],
                [],
                'ERROR',
                0,
                $e->getMessage()
            );
            
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'error_code' => 'VERIFICATION_ERROR'
            ];
        }
    }

    /**
     * Handle webhook notification from Hubtel
     * 
     * @param array $payload Webhook payload
     * @param string $signature Webhook signature (if available)
     * @return array Processing result
     */
    public function handleWebhook($payload, $signature = null)
    {
        // Validate IP whitelisting for webhooks
        if (!$this->isIpWhitelisted()) {
            error_log("Hubtel webhook: IP not whitelisted - " . $this->getClientIp());
            
            // Log rejected webhook
            $this->logGatewayActivity(
                'webhook',
                'unknown',
                $payload,
                [],
                'REJECTED',
                403,
                'IP not whitelisted'
            );
            
            return [
                'success' => false,
                'message' => 'IP not whitelisted',
                'error_code' => 'IP_NOT_WHITELISTED'
            ];
        }
        
        try {
            $responseCode = $payload['ResponseCode'] ?? '';
            $data = $payload['Data'] ?? [];
            
            $clientReference = $data['ClientReference'] ?? '';
            if (empty($clientReference)) {
                throw new \Exception('Missing ClientReference in webhook data');
            }
            
            // Log webhook received
            $this->logGatewayActivity(
                'webhook',
                $clientReference,
                $payload,
                ['processed' => true],
                $responseCode,
                200,
                null
            );
            
            // Determine status based on response code
            $status = 'pending';
            if ($responseCode === '0000') {
                $status = 'success';
            } elseif ($responseCode === '2001') {
                $status = 'failed';
            }
            
            return [
                'success' => true,
                'reference' => $clientReference,
                'status' => $status,
                'amount' => $data['Amount'] ?? 0,
                'transaction_id' => $data['TransactionId'] ?? null,
                'external_transaction_id' => $data['ExternalTransactionId'] ?? null,
                'message' => $payload['Message'] ?? 'Webhook processed',
                'raw_data' => $payload
            ];
        } catch (\Exception $e) {
            error_log("Hubtel webhook error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'error_code' => 'WEBHOOK_PROCESSING_ERROR'
            ];
        }
    }
    
    /**
     * Make API call to Hubtel
     */
    private function makeApiCall($method, $endpoint, $data = null, $queryParams = [], $useStatusCheckUrl = false, $customUrl = null)
    {
        // Use custom URL if provided, otherwise use configured base URLs
        if ($customUrl) {
            $url = $customUrl;
        } else {
            $baseUrl = $useStatusCheckUrl ? $this->statusCheckUrl : $this->baseUrl;
            $url = $baseUrl . $endpoint;
        }
        
        // Add query parameters for GET requests
        if (!empty($queryParams)) {
            $url .= '?' . http_build_query($queryParams);
        }
        
        // Prepare Basic Auth
        $clientId = $this->config['client_id'];
        $clientSecret = $this->config['client_secret'];
        
        // Validate credentials
        if (empty($clientId) || empty($clientSecret)) {
            throw new \Exception("Hubtel API credentials not configured. Please check HUBTEL_CLIENT_ID and HUBTEL_CLIENT_SECRET in .env file");
        }
        
        $authString = base64_encode("{$clientId}:{$clientSecret}");
        
        // Debug log (remove in production)
        error_log("Hubtel API Call: {$method} {$url}");
        error_log("Client ID: " . (empty($clientId) ? 'EMPTY' : substr($clientId, 0, 4) . '***'));
        
        $headers = [
            'Authorization: Basic ' . $authString,
            'Content-Type: application/json',
            'Accept: application/json',
            'Cache-Control: no-cache'
        ];
        
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_SSL_VERIFYPEER => true
        ]);
        
        if ($data && in_array($method, ['POST', 'PUT', 'PATCH'])) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            throw new \Exception("cURL error: {$error}");
        }
        
        $decodedResponse = json_decode($response, true);
        
        // Add HTTP status to response for logging
        if (is_array($decodedResponse)) {
            $decodedResponse['_http_status'] = $httpCode;
        }
        
        if ($httpCode >= 400) {
            $message = $decodedResponse['Message'] ?? $decodedResponse['message'] ?? "HTTP {$httpCode} error";
            
            // Add more context for common errors
            if ($httpCode === 403) {
                $message = "Authentication failed (403 Forbidden). Please verify your Hubtel API credentials are correct.";
            } elseif ($httpCode === 401) {
                $message = "Invalid credentials (401 Unauthorized). Please check HUBTEL_CLIENT_ID and HUBTEL_CLIENT_SECRET.";
            }
            
            // Log the full response for debugging
            error_log("Hubtel API Error {$httpCode}: " . json_encode($decodedResponse));
            
            throw new \Exception($message);
        }
        
        return $decodedResponse;
    }
    
    /**
     * Log gateway request/response
     */
    private function logGatewayActivity($requestType, $reference, $requestData, $responseData, $responseCode, $httpStatus, $errorMessage = null)
    {
        try {
            $sql = "INSERT INTO payment_gateway_logs 
                    (gateway_provider, transaction_reference, request_type, request_data, response_data, 
                     response_code, http_status, error_message, ip_address, created_at)
                    VALUES 
                    (:provider, :reference, :request_type, :request_data, :response_data, 
                     :response_code, :http_status, :error_message, :ip_address, NOW())";
            
            $this->db->query($sql);
            $this->db->bind(':provider', 'hubtel');
            $this->db->bind(':reference', $reference);
            $this->db->bind(':request_type', $requestType);
            $this->db->bind(':request_data', json_encode($requestData));
            $this->db->bind(':response_data', json_encode($responseData));
            $this->db->bind(':response_code', $responseCode);
            $this->db->bind(':http_status', $httpStatus);
            $this->db->bind(':error_message', $errorMessage);
            $this->db->bind(':ip_address', $this->getClientIp());
            $this->db->execute();
        } catch (\Exception $e) {
            error_log("Failed to log gateway activity: " . $e->getMessage());
        }
    }

    /**
     * Format phone number for Ghana
     */
    private function formatPhoneNumber($phone)
    {
        // Remove all non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Handle Ghana phone numbers
        if (strlen($phone) === 10 && substr($phone, 0, 1) === '0') {
            // Convert 0XXXXXXXXX to 233XXXXXXXXX
            $phone = '233' . substr($phone, 1);
        } elseif (strlen($phone) === 9) {
            // Convert XXXXXXXXX to 233XXXXXXXXX
            $phone = '233' . $phone;
        }
        
        return $phone;
    }
    
    /**
     * Detect mobile money channel from phone number
     */
    private function detectMobileMoneyChannel($phone)
    {
        $phone = $this->formatPhoneNumber($phone);
        $prefix = substr($phone, -9, 3); // Get first 3 digits after country code
        
        // Hubtel channel names (updated for current Ghana networks)
        $channels = [
            'mtn-gh' => ['024', '025', '053', '054', '055', '059'],
            'vodafone-gh' => ['020', '050'], // Telecel (formerly Vodafone)
            'tigo-gh' => ['026', '027', '056', '057'] // AirtelTigo
        ];
        
        foreach ($channels as $channel => $prefixes) {
            if (in_array($prefix, $prefixes)) {
                return $channel;
            }
        }
        
        return 'mtn-gh'; // Default to MTN
    }
    
    /**
     * Generate email from phone number
     */
    private function generateEmailFromPhone($phone)
    {
        $hash = substr(md5($phone), 0, 8);
        return "player{$hash}@raffle.com";
    }
    
    /**
     * Map Hubtel status to internal status
     */
    private function mapHubtelStatus($hubtelStatus)
    {
        $statusMap = [
            'paid' => 'success',
            'unpaid' => 'pending',
            'failed' => 'failed',
            'refunded' => 'refunded'
        ];
        
        return $statusMap[$hubtelStatus] ?? 'pending';
    }
    
    /**
     * Get user-friendly error message based on response code
     */
    private function getErrorMessage($responseCode, $defaultMessage)
    {
        $errorMessages = [
            '2001' => 'Payment failed. Please check your mobile money balance and try again.',
            '4000' => 'Invalid payment details. Please check and try again.',
            '4070' => 'Payment service temporarily unavailable. Please try again later.',
            '4101' => 'Payment gateway configuration error. Please contact support.',
            '4103' => 'Payment not allowed on this channel. Please try a different network.'
        ];
        
        return $errorMessages[$responseCode] ?? $defaultMessage;
    }
    
    /**
     * Check if client IP is whitelisted
     */
    private function isIpWhitelisted()
    {
        $whitelist = $this->config['ip_whitelist'] ?? [];
        
        // If no whitelist configured, allow all (for development)
        if (empty($whitelist) || (count($whitelist) === 1 && empty($whitelist[0]))) {
            return true;
        }
        
        $clientIp = $this->getClientIp();
        return in_array($clientIp, $whitelist);
    }
    
    /**
     * Get client IP address
     */
    private function getClientIp()
    {
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            return trim($ips[0]);
        }
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }
    
    /**
     * Get default callback URL
     */
    private function getDefaultCallbackUrl()
    {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        return "{$protocol}://{$host}/webhook/hubtel";
    }
    
    /**
     * Get supported networks
     */
    public function getSupportedNetworks()
    {
        return [
            'mtn-gh' => [
                'name' => 'MTN Mobile Money',
                'prefixes' => ['024', '025', '053', '054', '055', '059'],
                'logo' => '/assets/images/mtn-logo.png'
            ],
            'vodafone-gh' => [
                'name' => 'Telecel Cash',
                'prefixes' => ['020', '050'],
                'logo' => '/assets/images/telecel-logo.png'
            ],
            'tigo-gh' => [
                'name' => 'AirtelTigo Money',
                'prefixes' => ['026', '027', '056', '057'],
                'logo' => '/assets/images/airteltigo-logo.png'
            ]
        ];
    }
}
