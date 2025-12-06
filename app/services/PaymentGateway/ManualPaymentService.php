<?php

namespace App\Services\PaymentGateway;

/**
 * Manual Payment Service for Testing
 * Simulates successful payment without external gateway
 */
class ManualPaymentService
{
    private $config;

    public function __construct()
    {
        $this->config = [
            'name' => 'Manual Payment',
            'enabled' => true
        ];
    }

    /**
     * Initialize manual payment (always succeeds for testing)
     */
    public function initiatePayment($amount, $phone, $reference, $metadata = [])
    {
        // Simulate successful payment initialization
        return [
            'success' => true,
            'reference' => $reference,
            'transaction_id' => 'MANUAL-' . time() . '-' . rand(1000, 9999),
            'message' => 'Manual payment initiated successfully',
            'payment_url' => null, // No external URL needed
            'status' => 'success' // Immediately successful for testing
        ];
    }

    /**
     * Verify manual payment (always returns success)
     */
    public function verifyPayment($reference)
    {
        // Simulate successful payment verification
        return [
            'success' => true,
            'status' => 'success',
            'reference' => $reference,
            'transaction_id' => 'MANUAL-' . time(),
            'amount' => 0, // Amount should be passed from payment record
            'message' => 'Manual payment verified successfully',
            'paid_at' => date('Y-m-d H:i:s')
        ];
    }

    /**
     * Handle webhook callback (not needed for manual payments)
     */
    public function handleWebhook($payload)
    {
        return [
            'success' => true,
            'message' => 'Manual payment does not use webhooks'
        ];
    }

    /**
     * Get payment status
     */
    public function getPaymentStatus($reference)
    {
        return [
            'success' => true,
            'status' => 'success',
            'reference' => $reference,
            'message' => 'Payment successful'
        ];
    }

    /**
     * Check if service is available
     */
    public function isAvailable()
    {
        return true;
    }

    /**
     * Get service configuration
     */
    public function getConfig()
    {
        return $this->config;
    }
}
