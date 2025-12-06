# Hubtel Payment Gateway Setup Guide

## Overview
This guide explains how to integrate Hubtel's Direct Receive Money API into your raffle system for mobile money payments in Ghana.

---

## Features

### ✅ What's Included
- **Mobile Money Payments**: MTN, Telecel (Vodafone), AirtelTigo
- **Automatic Network Detection**: Detects network from phone number
- **Payment Verification**: Check payment status
- **Webhook Support**: Real-time payment notifications
- **Comprehensive Logging**: All requests/responses logged to database
- **IP Whitelisting**: Security for webhooks
- **Error Handling**: User-friendly error messages
- **Retry Logic**: Handles "not found" as pending status

---

## Prerequisites

### 1. Hubtel Account Setup
1. Create a Hubtel account at https://hubtel.com
2. Get your API credentials:
   - **Client ID** (Username)
   - **Client Secret** (Password)
   - **Merchant Account Number** (POS Sales ID)

### 2. API Endpoints
- **Payment API**: `https://rmp.hubtel.com`
- **Status Check API**: `https://api-txnstatus.hubtel.com`

### 3. IP Whitelisting
Whitelist your server IP on Hubtel dashboard for webhook callbacks.

---

## Installation

### Step 1: Run Database Migration

```bash
mysql -u root raffle < migration_payment_gateway_logs.sql
```

This creates the `payment_gateway_logs` table for tracking all gateway interactions.

### Step 2: Configure Environment Variables

Add to your `.env` file or set as environment variables:

```env
# Hubtel Configuration
HUBTEL_CLIENT_ID=your_client_id_here
HUBTEL_CLIENT_SECRET=your_client_secret_here
HUBTEL_MERCHANT_ACCOUNT=your_merchant_account_number
HUBTEL_IP_WHITELIST=154.160.16.223,154.160.17.223
```

**Important**: 
- Replace with your actual Hubtel credentials
- Add Hubtel's webhook IPs to the whitelist (check Hubtel docs for current IPs)
- For development, leave `HUBTEL_IP_WHITELIST` empty to allow all IPs

---

## Usage

### 1. Initialize Payment (USSD/Web)

```php
use App\Services\PaymentGateway\HubtelService;

$hubtel = new HubtelService();

$paymentData = [
    'phone' => '0241234567',           // Customer phone number
    'amount' => 10.00,                 // Amount to charge
    'reference' => 'USSD1234567890',   // Your unique reference
    'player_name' => 'John Doe',       // Optional: Customer name
    'email' => 'john@example.com',     // Optional: Customer email
    'description' => 'Raffle Ticket',  // Optional: Payment description
    'callback_url' => 'https://yoursite.com/webhook/hubtel' // Optional
];

$response = $hubtel->initiatePayment($paymentData);

if ($response['success']) {
    // Payment initiated successfully
    echo "Status: " . $response['status']; // 'pending' or 'success'
    echo "Message: " . $response['message'];
    echo "Gateway Ref: " . $response['gateway_reference'];
    
    // User will receive a prompt on their phone to approve
} else {
    // Payment failed
    echo "Error: " . $response['message'];
    echo "Code: " . $response['error_code'];
}
```

### 2. Verify Payment Status

```php
$reference = 'USSD1234567890';
$result = $hubtel->verifyPayment($reference);

if ($result['success']) {
    if ($result['status'] === 'success') {
        // Payment completed
        echo "Amount: " . $result['amount'];
        echo "Paid at: " . $result['paid_at'];
        
        // Generate tickets, update database, etc.
    } elseif ($result['status'] === 'pending') {
        // Still waiting for user approval
        echo "Payment pending approval";
    } elseif ($result['status'] === 'failed') {
        // Payment failed
        echo "Payment failed";
    }
}
```

### 3. Handle Webhook (Automatic Notifications)

Create a webhook endpoint at `app/controllers/WebhookController.php`:

```php
<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Services\PaymentGateway\HubtelService;

class WebhookController extends Controller
{
    public function hubtel()
    {
        // Get raw POST data
        $payload = json_decode(file_get_contents('php://input'), true);
        
        // Get signature if provided
        $signature = $_SERVER['HTTP_X_HUBTEL_SIGNATURE'] ?? null;
        
        $hubtel = new HubtelService();
        $result = $hubtel->handleWebhook($payload, $signature);
        
        if ($result['success']) {
            $reference = $result['reference'];
            $status = $result['status'];
            
            // Update payment in database
            $paymentModel = $this->model('Payment');
            $payment = $paymentModel->findByReference($reference);
            
            if ($payment) {
                if ($status === 'success') {
                    // Payment successful - generate tickets
                    $paymentModel->update($payment->id, [
                        'status' => 'success',
                        'gateway_reference' => $result['transaction_id'],
                        'paid_at' => date('Y-m-d H:i:s')
                    ]);
                    
                    // Generate tickets
                    $ticketService = new \App\Services\TicketGeneratorService();
                    $ticketService->generateTickets([
                        'payment_id' => $payment->id,
                        'player_id' => $payment->player_id,
                        'campaign_id' => $payment->campaign_id,
                        'station_id' => $payment->station_id,
                        'programme_id' => $payment->programme_id,
                        'amount' => $payment->amount
                    ]);
                    
                    // Allocate revenue
                    $revenueService = new \App\Services\RevenueAllocationService();
                    $revenueService->allocate([
                        'payment_id' => $payment->id,
                        'amount' => $payment->amount
                    ]);
                    
                } elseif ($status === 'failed') {
                    // Payment failed
                    $paymentModel->update($payment->id, [
                        'status' => 'failed'
                    ]);
                }
            }
            
            // Return 200 OK to Hubtel
            http_response_code(200);
            echo json_encode(['status' => 'received']);
        } else {
            // Error processing webhook
            http_response_code(400);
            echo json_encode(['error' => $result['message']]);
        }
    }
}
```

---

## Network Detection

The service automatically detects the mobile money network from the phone number:

| Network | Prefixes |
|---------|----------|
| **MTN** | 024, 025, 053, 054, 055, 059 |
| **Telecel (Vodafone)** | 020, 050 |
| **AirtelTigo** | 026, 027, 056, 057 |

**Phone Number Formats Supported:**
- `0241234567` → Converted to `233241234567`
- `241234567` → Converted to `233241234567`
- `233241234567` → Used as is

---

## Response Codes

### Hubtel Response Codes
| Code | Meaning | Action |
|------|---------|--------|
| `0000` | Success | Payment completed |
| `0001` | Pending | Waiting for user approval |
| `2001` | Failed | Payment failed (insufficient balance, etc.) |
| `4000` | Invalid | Invalid payment details |
| `4070` | Unavailable | Service temporarily unavailable |
| `4101` | Config Error | Gateway configuration issue |
| `4103` | Not Allowed | Payment not allowed on this channel |

### Internal Status Mapping
- `success` → Payment completed successfully
- `pending` → Waiting for user approval
- `failed` → Payment failed
- `refunded` → Payment was refunded

---

## Testing

### Test Phone Numbers (Sandbox)
Hubtel provides test numbers for sandbox testing. Check their documentation for current test numbers.

### Test Flow
1. **Initialize Payment**
   ```php
   $response = $hubtel->initiatePayment([
       'phone' => '0241234567',
       'amount' => 5.00,
       'reference' => 'TEST' . time()
   ]);
   ```

2. **Check Status** (after 5 seconds)
   ```php
   $result = $hubtel->verifyPayment('TEST1234567890');
   ```

3. **Simulate Webhook** (manually trigger)
   ```bash
   curl -X POST http://localhost/raffle/webhook/hubtel \
     -H "Content-Type: application/json" \
     -d '{
       "ResponseCode": "0000",
       "Message": "Success",
       "Data": {
         "ClientReference": "TEST1234567890",
         "TransactionId": "HUB123456",
         "Amount": 5.00
       }
     }'
   ```

---

## Logging & Debugging

All gateway interactions are logged to the `payment_gateway_logs` table:

```sql
-- View recent Hubtel logs
SELECT * FROM payment_gateway_logs 
WHERE gateway_provider = 'hubtel' 
ORDER BY created_at DESC 
LIMIT 20;

-- View logs for specific transaction
SELECT * FROM payment_gateway_logs 
WHERE transaction_reference = 'USSD1234567890';

-- View failed requests
SELECT * FROM payment_gateway_logs 
WHERE error_message IS NOT NULL 
ORDER BY created_at DESC;
```

---

## Security Best Practices

### 1. IP Whitelisting
Always configure `HUBTEL_IP_WHITELIST` in production:
```env
HUBTEL_IP_WHITELIST=154.160.16.223,154.160.17.223
```

### 2. HTTPS Only
Ensure your webhook URL uses HTTPS in production.

### 3. Validate Webhooks
The service automatically validates:
- IP address (if whitelist configured)
- Required fields in payload
- Response codes

### 4. Secure Credentials
- Never commit `.env` file to version control
- Use environment variables for credentials
- Rotate credentials periodically

---

## Common Issues & Solutions

### Issue 1: "Merchant account not configured"
**Solution**: Set `HUBTEL_MERCHANT_ACCOUNT` in `.env`

### Issue 2: "IP not whitelisted"
**Solution**: 
- Add your server IP to Hubtel dashboard
- Add Hubtel IPs to `HUBTEL_IP_WHITELIST`
- For development, leave whitelist empty

### Issue 3: "Payment record not found"
**Solution**: This is normal for new payments. The service treats this as "pending".

### Issue 4: "cURL error"
**Solution**: 
- Check internet connectivity
- Verify SSL certificates are up to date
- Check firewall settings

---

## Support

### Hubtel Support
- **Email**: support@hubtel.com
- **Phone**: +233 30 281 0100
- **Docs**: https://developers.hubtel.com

### Integration Support
Check the `payment_gateway_logs` table for detailed error messages and request/response data.

---

## Changelog

### Version 1.0 (Current)
- ✅ Direct Receive Money API integration
- ✅ Automatic network detection
- ✅ Payment verification
- ✅ Webhook support
- ✅ Comprehensive logging
- ✅ IP whitelisting
- ✅ Error handling

### Future Enhancements
- [ ] Card payment support (Checkout API)
- [ ] Recurring payments
- [ ] Refund support
- [ ] Payment links
