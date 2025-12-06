# Hubtel Quick Setup Guide

## Step 1: Get Hubtel Credentials

1. **Go to Hubtel Dashboard**
   - Visit: https://hubtel.com
   - Login to your account

2. **Get API Credentials**
   - Navigate to: **Settings** → **API Keys**
   - Copy your:
     - **Client ID** (Username)
     - **Client Secret** (Password)

3. **Get Merchant Account**
   - Navigate to: **Receive Money** → **POS Sales**
   - Copy your **Merchant Account Number** (POS Sales ID)

---

## Step 2: Configure Environment Variables

### Option A: Using .env File (Recommended)

1. **Open the `.env` file** in the raffle root directory:
   ```
   c:\xampp\htdocs\raffle\.env
   ```

2. **Fill in your credentials:**
   ```env
   HUBTEL_CLIENT_ID=your_client_id_here
   HUBTEL_CLIENT_SECRET=your_client_secret_here
   HUBTEL_MERCHANT_ACCOUNT=your_merchant_account_number
   HUBTEL_IP_WHITELIST=
   ```

3. **Example with actual values:**
   ```env
   HUBTEL_CLIENT_ID=abcd1234
   HUBTEL_CLIENT_SECRET=xyz789secret
   HUBTEL_MERCHANT_ACCOUNT=HM12345
   HUBTEL_IP_WHITELIST=
   ```

4. **Save the file**

### Option B: Direct in config.php (Alternative)

If `.env` doesn't work, you can set values directly in `app/config/config.php`:

```php
// Hubtel Payment Gateway Configuration
define('HUBTEL_CLIENT_ID', 'your_client_id_here');
define('HUBTEL_CLIENT_SECRET', 'your_client_secret_here');
define('HUBTEL_MERCHANT_ACCOUNT', 'your_merchant_account_number');
define('HUBTEL_IP_WHITELIST', '');
```

---

## Step 3: Test Configuration

### Test 1: Check if credentials are loaded

Create a test file: `c:\xampp\htdocs\raffle\test-hubtel-config.php`

```php
<?php
require_once 'app/config/config.php';

echo "Hubtel Configuration Test\n";
echo "=========================\n\n";

echo "Client ID: " . (HUBTEL_CLIENT_ID ? '✓ Set' : '✗ Not Set') . "\n";
echo "Client Secret: " . (HUBTEL_CLIENT_SECRET ? '✓ Set' : '✗ Not Set') . "\n";
echo "Merchant Account: " . (HUBTEL_MERCHANT_ACCOUNT ? '✓ Set' : '✗ Not Set') . "\n";

if (HUBTEL_CLIENT_ID && HUBTEL_CLIENT_SECRET && HUBTEL_MERCHANT_ACCOUNT) {
    echo "\n✓ All credentials configured!\n";
} else {
    echo "\n✗ Some credentials missing. Please check .env file.\n";
}
?>
```

Run it:
```
http://localhost/raffle/test-hubtel-config.php
```

**Expected Output:**
```
Hubtel Configuration Test
=========================

Client ID: ✓ Set
Client Secret: ✓ Set
Merchant Account: ✓ Set

✓ All credentials configured!
```

### Test 2: Test USSD Payment

1. **Open USSD Simulator:**
   ```
   http://localhost/raffle/public/ussd-simulator.php
   ```

2. **Complete a purchase:**
   - Enter phone: `0241234567`
   - Select: `1` (Buy Ticket)
   - Select: `1` (Station)
   - Select: `1` (Campaign)
   - Enter: `1` (Quantity)
   - Select: `1` (Confirm)
   - Select: `1` (Mobile Money)

3. **Expected Response:**
   ```
   END Payment request sent!
   Amount: GHS 5.00
   Quantity: 1 ticket(s)
   
   Payment initiated. Please approve on your phone.
   
   Reference: USSD1733476800123
   ```

4. **If you see error:**
   ```
   END Payment failed.
   Hubtel merchant account (POS Sales ID) not configured
   ```
   → Go back to Step 2 and verify credentials

---

## Step 4: Setup Webhook (Optional for Testing)

For production, configure webhook in Hubtel dashboard:

1. **Go to Hubtel Dashboard**
   - Navigate to: **Settings** → **Webhooks**

2. **Add Webhook URL:**
   ```
   https://yourdomain.com/webhook/hubtel
   ```

3. **For Local Testing:**
   - Use ngrok or similar tool to expose localhost
   - Or test manually with curl (see below)

### Manual Webhook Test

```bash
curl -X POST http://localhost/raffle/webhook/hubtel \
  -H "Content-Type: application/json" \
  -d '{
    "ResponseCode": "0000",
    "Message": "Success",
    "Data": {
      "ClientReference": "USSD1733476800123",
      "TransactionId": "HUB123456",
      "Amount": 5.00
    }
  }'
```

---

## Troubleshooting

### Issue: "Hubtel merchant account not configured"

**Solution 1: Check .env file exists**
```
File: c:\xampp\htdocs\raffle\.env
Should contain: HUBTEL_MERCHANT_ACCOUNT=your_value
```

**Solution 2: Check file permissions**
- Ensure `.env` file is readable
- Try setting values directly in `config.php`

**Solution 3: Restart Apache**
```bash
# Restart XAMPP Apache server
# Or restart from XAMPP Control Panel
```

**Solution 4: Verify credentials format**
```env
# ✓ Correct
HUBTEL_MERCHANT_ACCOUNT=HM12345

# ✗ Wrong (no spaces around =)
HUBTEL_MERCHANT_ACCOUNT = HM12345

# ✗ Wrong (no quotes)
HUBTEL_MERCHANT_ACCOUNT="HM12345"
```

### Issue: "Missing required field: phone"

**Solution:** See previous fix - phone validation added to UssdController

### Issue: Payment initiated but no prompt

**Possible causes:**
1. Wrong Hubtel credentials
2. Sandbox vs Production mode mismatch
3. Phone number format issue

**Check logs:**
```sql
SELECT * FROM payment_gateway_logs 
WHERE gateway_provider = 'hubtel' 
ORDER BY created_at DESC 
LIMIT 5;
```

---

## Quick Reference

### File Locations
- Configuration: `app/config/config.php`
- Environment: `.env`
- Hubtel Service: `app/services/PaymentGateway/HubtelService.php`
- USSD Controller: `app/controllers/UssdController.php`
- Webhook Handler: `app/controllers/WebhookController.php`

### Important URLs
- USSD Simulator: `http://localhost/raffle/public/ussd-simulator.php`
- Webhook Endpoint: `http://localhost/raffle/webhook/hubtel`
- Hubtel Dashboard: `https://hubtel.com`

### Database Tables
- Payments: `payments`
- Gateway Logs: `payment_gateway_logs`
- USSD Sessions: `ussd_sessions`

---

## Next Steps

1. ✅ Configure credentials in `.env`
2. ✅ Test with USSD simulator
3. ✅ Verify payment gateway logs
4. ✅ Setup webhook URL in Hubtel dashboard
5. ✅ Test with real phone number (small amount)
6. ✅ Monitor logs for any issues
7. ✅ Go live!

---

## Support

If you encounter issues:

1. **Check error logs:**
   ```
   C:\xampp\apache\logs\error.log
   ```

2. **Check payment gateway logs:**
   ```sql
   SELECT * FROM payment_gateway_logs 
   WHERE error_message IS NOT NULL 
   ORDER BY created_at DESC;
   ```

3. **Contact Hubtel Support:**
   - Email: support@hubtel.com
   - Phone: +233 30 281 0100

4. **Check documentation:**
   - `HUBTEL_SETUP_GUIDE.md`
   - `USSD_HUBTEL_SUMMARY.md`
