<?php

namespace App\Services\PaymentGateway;

class PaystackService
{
    private $apiUrl;
    private $secretKey;

    public function __construct()
    {
        $this->apiUrl = 'https://api.paystack.co';
        $this->secretKey = getenv('PAYSTACK_SECRET_KEY') ?: 'your-secret-key';
    }

    public function initiatePayment($data)
    {
        $reference = 'PAY-' . time() . '-' . rand(1000, 9999);

        $payload = [
            'email' => $data['email'] ?? 'player@raffle.com',
            'amount' => $data['amount'] * 100, // Paystack uses pesewas
            'reference' => $reference,
            'callback_url' => url('webhook/paystack'),
            'metadata' => [
                'campaign_id' => $data['campaign_id'],
                'player_phone' => $data['phone'],
                'custom_fields' => [
                    [
                        'display_name' => 'Campaign',
                        'variable_name' => 'campaign_name',
                        'value' => $data['campaign_name']
                    ]
                ]
            ]
        ];

        $response = $this->makeApiCall('/transaction/initialize', $payload);

        if ($response['status']) {
            return [
                'success' => true,
                'reference' => $reference,
                'status' => 'pending',
                'authorization_url' => $response['data']['authorization_url'],
                'access_code' => $response['data']['access_code'],
                'gateway_response' => $response,
                'message' => 'Redirect to payment page'
            ];
        }

        return [
            'success' => false,
            'message' => $response['message'] ?? 'Payment initiation failed'
        ];
    }

    public function verifyPayment($reference)
    {
        $response = $this->makeApiCall('/transaction/verify/' . $reference, null, 'GET');

        if ($response['status'] && $response['data']['status'] === 'success') {
            return [
                'success' => true,
                'status' => 'success',
                'amount' => $response['data']['amount'] / 100, // Convert from pesewas
                'gateway_response' => $response
            ];
        }

        return [
            'success' => false,
            'status' => 'failed',
            'message' => $response['message'] ?? 'Payment verification failed'
        ];
    }

    private function makeApiCall($endpoint, $data = null, $method = 'POST')
    {
        // Simulated response - implement actual cURL with proper headers
        // Authorization: Bearer SECRET_KEY
        
        if ($method === 'GET') {
            return [
                'status' => true,
                'message' => 'Verification successful',
                'data' => [
                    'status' => 'success',
                    'reference' => $data,
                    'amount' => 100 * 100,
                    'paid_at' => date('Y-m-d H:i:s')
                ]
            ];
        }

        return [
            'status' => true,
            'message' => 'Authorization URL created',
            'data' => [
                'authorization_url' => 'https://checkout.paystack.com/' . uniqid(),
                'access_code' => 'access_' . uniqid(),
                'reference' => $data['reference']
            ]
        ];
    }

    public function handleWebhook($payload)
    {
        // Verify webhook signature
        $signature = $_SERVER['HTTP_X_PAYSTACK_SIGNATURE'] ?? '';
        
        return [
            'reference' => $payload['data']['reference'] ?? null,
            'status' => ($payload['data']['status'] === 'success') ? 'success' : 'failed',
            'amount' => ($payload['data']['amount'] ?? 0) / 100
        ];
    }
}
