<?php

namespace App\Services;

/**
 * Hubtel Transaction Status Check Service
 * 
 * Implements the Transaction Status Check API to verify payment status
 * when Service Fulfillment callback is not received within 5 minutes
 * 
 * API Endpoint: https://api-txnstatus.hubtel.com/transactions/{POS_Sales_ID}/status
 * Requires: IP whitelisting and Basic Authentication
 */
class HubtelTransactionStatusService
{
    private $apiBaseUrl = 'https://api-txnstatus.hubtel.com/transactions';
    private $posSalesId;
    private $username;
    private $password;
    
    public function __construct()
    {
        // Load configuration from environment or config file
        $this->posSalesId = $_ENV['HUBTEL_POS_SALES_ID'] ?? '';
        $this->username = $_ENV['HUBTEL_API_USERNAME'] ?? '';
        $this->password = $_ENV['HUBTEL_API_PASSWORD'] ?? '';
    }
    
    /**
     * Check transaction status by client reference (SessionId)
     * This is the recommended method according to API documentation
     * 
     * @param string $sessionId - The SessionId used in the transaction
     * @return array - Transaction status details
     */
    public function checkBySessionId($sessionId)
    {
        return $this->checkStatus(['clientReference' => $sessionId]);
    }
    
    /**
     * Check transaction status by Hubtel transaction ID
     * 
     * @param string $hubtelTransactionId - Transaction ID from Hubtel
     * @return array - Transaction status details
     */
    public function checkByHubtelTransactionId($hubtelTransactionId)
    {
        return $this->checkStatus(['hubtelTransactionId' => $hubtelTransactionId]);
    }
    
    /**
     * Check transaction status by network transaction ID
     * 
     * @param string $networkTransactionId - Transaction reference from mobile money provider
     * @return array - Transaction status details
     */
    public function checkByNetworkTransactionId($networkTransactionId)
    {
        return $this->checkStatus(['networkTransactionId' => $networkTransactionId]);
    }
    
    /**
     * Check transaction status
     * 
     * @param array $params - Query parameters (clientReference, hubtelTransactionId, or networkTransactionId)
     * @return array - Response with status and transaction details
     */
    private function checkStatus($params)
    {
        if (empty($this->posSalesId)) {
            error_log('Transaction Status Check Error: POS Sales ID not configured');
            return [
                'success' => false,
                'message' => 'POS Sales ID not configured',
                'data' => null
            ];
        }
        
        // Build URL with query parameters
        $url = "{$this->apiBaseUrl}/{$this->posSalesId}/status?" . http_build_query($params);
        
        error_log("=== TRANSACTION STATUS CHECK ===" . PHP_EOL . "URL: $url");
        
        try {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Accept: application/json'
            ]);
            
            // Add Basic Authentication if credentials are available
            if (!empty($this->username) && !empty($this->password)) {
                curl_setopt($ch, CURLOPT_USERPWD, "{$this->username}:{$this->password}");
                curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            }
            
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);
            
            if ($error) {
                error_log("Transaction Status Check Error: $error");
                return [
                    'success' => false,
                    'message' => "cURL Error: $error",
                    'data' => null
                ];
            }
            
            error_log("Transaction Status Check Response - HTTP $httpCode: $response");
            
            $responseData = json_decode($response, true);
            
            if ($httpCode === 200 && $responseData) {
                // Parse response according to API spec
                $message = $responseData['message'] ?? '';
                $responseCode = $responseData['responseCode'] ?? '';
                $data = $responseData['data'] ?? null;
                
                return [
                    'success' => $responseCode === '0000',
                    'message' => $message,
                    'response_code' => $responseCode,
                    'data' => $data
                ];
            } else {
                return [
                    'success' => false,
                    'message' => "HTTP $httpCode: " . ($responseData['message'] ?? 'Unknown error'),
                    'data' => null
                ];
            }
            
        } catch (\Exception $e) {
            error_log("Transaction Status Check Exception: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Exception: ' . $e->getMessage(),
                'data' => null
            ];
        }
    }
    
    /**
     * Parse transaction status data
     * 
     * @param array $data - Transaction data from API response
     * @return array - Parsed transaction details
     */
    public function parseTransactionData($data)
    {
        if (!$data) {
            return null;
        }
        
        return [
            'date' => $data['date'] ?? null,
            'status' => $data['status'] ?? null, // 'Paid', 'Unpaid', 'Refunded'
            'transaction_id' => $data['transactionId'] ?? null,
            'external_transaction_id' => $data['externalTransactionId'] ?? null,
            'payment_method' => $data['paymentMethod'] ?? null,
            'client_reference' => $data['clientReference'] ?? null,
            'currency_code' => $data['currencyCode'] ?? null,
            'amount' => $data['amount'] ?? 0,
            'charges' => $data['charges'] ?? 0,
            'amount_after_charges' => $data['amountAfterCharges'] ?? 0,
            'is_fulfilled' => $data['isFulfilled'] ?? null
        ];
    }
    
    /**
     * Check if transaction is paid
     * 
     * @param string $sessionId - SessionId to check
     * @return bool - True if paid, false otherwise
     */
    public function isPaid($sessionId)
    {
        $result = $this->checkBySessionId($sessionId);
        
        if (!$result['success'] || !$result['data']) {
            return false;
        }
        
        $status = $result['data']['status'] ?? '';
        return $status === 'Paid';
    }
    
    /**
     * Get transaction amount after charges
     * 
     * @param string $sessionId - SessionId to check
     * @return float|null - Amount after charges or null if not found
     */
    public function getAmountAfterCharges($sessionId)
    {
        $result = $this->checkBySessionId($sessionId);
        
        if (!$result['success'] || !$result['data']) {
            return null;
        }
        
        return $result['data']['amountAfterCharges'] ?? null;
    }
}
