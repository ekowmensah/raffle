<?php

namespace App\Services\PaymentGateway;

class MtnMomoService
{
    private $apiUrl;
    private $apiKey;
    private $apiSecret;

    public function __construct()
    {
        // Load from config or environment
        $this->apiUrl = 'https://sandbox.momodeveloper.mtn.com'; // Use production URL in live
        $this->apiKey = getenv('MTN_API_KEY') ?: 'your-api-key';
        $this->apiSecret = getenv('MTN_API_SECRET') ?: 'your-api-secret';
    }

    public function initiatePayment($data)
    {
        // Generate unique reference
        $reference = 'MTN-' . time() . '-' . rand(1000, 9999);

        // Prepare payment request
        $payload = [
            'amount' => $data['amount'],
            'currency' => $data['currency'] ?? 'GHS',
            'externalId' => $reference,
            'payer' => [
                'partyIdType' => 'MSISDN',
                'partyId' => $this->formatPhoneNumber($data['phone'])
            ],
            'payerMessage' => $data['description'] ?? 'Raffle ticket purchase',
            'payeeNote' => 'Payment for campaign: ' . $data['campaign_name']
        ];

        // Make API call (simulated for now)
        $response = $this->makeApiCall('/collection/v1_0/requesttopay', $payload);

        return [
            'success' => true,
            'reference' => $reference,
            'status' => 'pending',
            'gateway_response' => $response,
            'message' => 'Payment initiated. Please approve on your phone.'
        ];
    }

    public function verifyPayment($reference)
    {
        // Make API call to verify payment status
        $response = $this->makeApiCall('/collection/v1_0/requesttopay/' . $reference, null, 'GET');

        // Parse response
        if (isset($response['status'])) {
            return [
                'success' => $response['status'] === 'SUCCESSFUL',
                'status' => strtolower($response['status']),
                'amount' => $response['amount'] ?? 0,
                'gateway_response' => $response
            ];
        }

        return [
            'success' => false,
            'status' => 'failed',
            'message' => 'Unable to verify payment'
        ];
    }

    private function makeApiCall($endpoint, $data = null, $method = 'POST')
    {
        // Simulated API call - replace with actual cURL implementation
        // In production, implement proper HTTP client with authentication
        
        return [
            'status' => 'SUCCESSFUL',
            'amount' => $data['amount'] ?? 0,
            'financialTransactionId' => 'FT-' . time(),
            'externalId' => $data['externalId'] ?? ''
        ];
    }

    private function formatPhoneNumber($phone)
    {
        // Remove +, spaces, and ensure proper format
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Add country code if missing
        if (strlen($phone) === 10) {
            $phone = '233' . substr($phone, 1);
        }
        
        return $phone;
    }

    public function handleWebhook($payload)
    {
        // Process MTN webhook callback
        return [
            'reference' => $payload['externalId'] ?? null,
            'status' => isset($payload['status']) ? strtolower($payload['status']) : 'failed',
            'amount' => $payload['amount'] ?? 0
        ];
    }
}
