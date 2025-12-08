# USSD Payment Debugging Guide

## Issue
Payment prompt appears when using web but not when using USSD.

## Possible Causes & Solutions

### 1. **Phone Number Format**
**Problem:** USSD might be sending phone numbers in a different format than web.

**Check:**
```
USSD logs should show:
User Phone: 233XXXXXXXXX
Payment Phone: 233XXXXXXXXX
```

**Solution:** Ensure both are in format `233XXXXXXXXX` (not `0XXXXXXXXX` or `+233XXXXXXXXX.`)

---

### 2. **Hubtel Configuration**
**Problem:** Missing or incorrect Hubtel credentials in `.env` file.

**Check `.env` file:**
```env
HUBTEL_MODE=sandbox  # or production
HUBTEL_CLIENT_ID=your_client_id
HUBTEL_CLIENT_SECRET=your_client_secret
HUBTEL_MERCHANT_ACCOUNT=your_pos_sales_id
```

**Test:** Try making a payment via web - if it works, config is correct.

---

### 3. **Network Detection**
**Problem:** Hubtel can't detect which mobile money provider (MTN, Vodafone, AirtelTigo) to use.

**Check logs for:**
```
Hubtel Payment Init - Merchant: XXX, Phone: 233XXXXXXXXX, Channel: mtn-gh
```

**Channels should be:**
- MTN: `mtn-gh`
- Vodafone: `vodafone-gh`
- AirtelTigo: `tigo-gh`

---

### 4. **Callback URL**
**Problem:** Hubtel can't reach your callback URL to confirm payment.

**Check:**
```php
// In UssdController.php
private function getCallbackUrl()
{
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    return "{$protocol}://{$host}/webhook/hubtel";
}
```

**For USSD:** Must be a publicly accessible URL (not localhost)

---

### 5. **Hubtel API Response**
**Problem:** Hubtel is returning an error but we're not seeing it.

**Check error logs for:**
```
Hubtel Response: {"success":false,"message":"...","error_code":"..."}
```

**Common error codes:**
- `2001` - Invalid phone number
- `2002` - Insufficient balance
- `2003` - Invalid merchant account
- `4001` - Network error

---

## Debugging Steps

### Step 1: Check Error Logs
After attempting USSD payment, check error logs:

```bash
tail -f /path/to/php_error.log
```

Look for:
```
=== USSD Payment Initiation ===
User Phone: ...
Payment Phone: ...
Hubtel Response: ...
```

### Step 2: Compare Web vs USSD
Make a payment via web, then via USSD. Compare the logs:

**Web logs:**
```
Payment Phone: 233241234567
Channel: mtn-gh
Hubtel Response: {"success":true,...}
```

**USSD logs:**
```
Payment Phone: 233241234567  <- Should match web
Channel: mtn-gh              <- Should match web
Hubtel Response: ???         <- Check this
```

### Step 3: Test Phone Number Format
In `UssdController.php`, the `cleanPhoneNumber()` method should format correctly:

```php
private function cleanPhoneNumber($phone)
{
    // Remove spaces, dashes, etc.
    $phone = preg_replace('/[^0-9+]/', '', $phone);
    
    // Convert to Ghana format (233XXXXXXXXX)
    if (substr($phone, 0, 1) == '0') {
        $phone = '233' . substr($phone, 1);
    } elseif (substr($phone, 0, 1) != '+' && strlen($phone) == 9) {
        $phone = '233' . $phone;
    }
    
    return $phone;
}
```

### Step 4: Verify Hubtel Credentials
Run this test script:

```php
<?php
require_once 'app/services/PaymentGateway/HubtelService.php';

$hubtel = new \App\Services\PaymentGateway\HubtelService();

$testData = [
    'phone' => '233241234567',  // Replace with test number
    'amount' => 1.00,
    'reference' => 'TEST' . time(),
    'player_name' => 'Test User',
    'description' => 'Test Payment'
];

$response = $hubtel->initiatePayment($testData);
print_r($response);
```

---

## Expected Flow

### Successful Payment:
1. User enters quantity → Confirms
2. User selects payment method
3. **Hubtel API called** → Returns `{"success":true,"status":"pending"}`
4. **Mobile money prompt sent** to user's phone
5. User approves on phone
6. **Webhook receives callback** → Generates tickets
7. **SMS sent** with ticket code

### Current Issue:
Step 4 (Mobile money prompt) is not happening for USSD but works for web.

---

## Quick Fixes to Try

### Fix 1: Ensure Callback URL is Public
If testing locally, use ngrok or similar:

```bash
ngrok http 80
```

Then update callback URL to use ngrok URL.

### Fix 2: Check Hubtel Mode
Ensure you're in the correct mode (sandbox vs production):

```env
HUBTEL_MODE=sandbox  # For testing
# or
HUBTEL_MODE=production  # For live
```

### Fix 3: Verify Phone Number
Add this before payment initiation:

```php
error_log('Original Phone: ' . $phoneNumber);
error_log('Cleaned Phone: ' . $paymentNumber);
error_log('Phone Length: ' . strlen($paymentNumber));
```

Should output:
```
Original Phone: 0241234567
Cleaned Phone: 233241234567
Phone Length: 12
```

---

## Next Steps

1. **Make a test payment via USSD**
2. **Check error logs** immediately after
3. **Share the logs** showing:
   - User Phone
   - Payment Phone
   - Hubtel Response
4. **Compare with web payment logs**

This will help identify exactly where the issue is!
