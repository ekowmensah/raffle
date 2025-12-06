# SMS Integration - Hubtel SMS API

## Overview
The raffle system uses Hubtel SMS API to send notifications to players. The same Hubtel credentials used for payments are used for SMS.

## Features

### 1. **Ticket Purchase SMS**
Sent immediately after successful payment:
```
Payment successful! GHS 5.00
Campaign: December Raffle
Tickets (2): 1234567890, 0987654321
Good luck!
```

### 2. **Winner Notification SMS**
Sent when a player wins in a draw:
```
🎉 CONGRATULATIONS! 🎉
You WON in December Raffle!
Ticket: 1234567890
Prize: GHS 500.00 (1st Prize)
Contact us to claim your prize!
```

### 3. **Draw Results SMS**
Sent after draw completion:
```
Draw completed for December Raffle
Date: 2024-12-06
Type: DAILY
Better luck next time!
```

### 4. **Payment Confirmation SMS**
Sent when payment is received:
```
Payment received!
Amount: GHS 10.00
Ref: PAY-123456
Your tickets will be sent shortly.
```

### 5. **Balance Inquiry SMS**
Sent via USSD or API:
```
Account Summary
Name: Player 4749
Total Tickets: 25
Total Winnings: GHS 150.00
Thank you for playing!
```

### 6. **OTP Verification SMS**
Sent for API authentication:
```
Your Raffle verification code is: 123456
Valid for 10 minutes.
Do not share this code.
```

## Configuration

### Environment Variables (.env)
```env
# Hubtel SMS Configuration
SMS_SENDER_ID=RAFFLE
SMS_GATEWAY=hubtel

# Uses same credentials as payment gateway
HUBTEL_CLIENT_ID=your_client_id
HUBTEL_CLIENT_SECRET=your_client_secret
```

### Sender ID
- Default: `RAFFLE`
- Can be customized via `SMS_SENDER_ID` in .env
- Must be registered with Hubtel

## Database Schema

### SMS Logs Table
```sql
CREATE TABLE sms_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    phone_number VARCHAR(20) NOT NULL,
    message TEXT NOT NULL,
    message_type ENUM('ticket', 'payment', 'winner', 'draw', 'balance', 'otp', 'general'),
    status ENUM('pending', 'sent', 'failed', 'logged'),
    gateway VARCHAR(50) DEFAULT 'hubtel',
    gateway_response TEXT,
    message_id VARCHAR(100),
    cost DECIMAL(10, 4) DEFAULT 0.0000,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    sent_at TIMESTAMP NULL
);
```

## API Endpoints

### Hubtel SMS API
- **Endpoint**: `https://sms.hubtel.com/v1/messages/send`
- **Method**: POST
- **Authentication**: Basic Auth (ClientId:ClientSecret)
- **Content-Type**: application/json

### Request Format
```json
{
    "From": "RAFFLE",
    "To": "233241234567",
    "Content": "Your message here"
}
```

### Response Format
```json
{
    "MessageId": "abc123",
    "Status": "Sent",
    "NetworkId": "62002",
    "Rate": 0.04
}
```

## Usage Examples

### Send Ticket Confirmation
```php
$smsService = new \App\Services\SMS\HubtelSmsService();
$smsService->sendTicketConfirmation(
    '0241234567',
    $tickets,
    'December Raffle',
    10.00
);
```

### Send Winner Notification
```php
$smsService->sendWinnerNotification(
    '0241234567',
    '1234567890',
    500.00,
    '1st Prize',
    'December Raffle'
);
```

### Send Bulk SMS
```php
$phoneNumbers = ['0241234567', '0201234567', '0551234567'];
$message = "Draw happening tomorrow at 3pm!";
$results = $smsService->sendBulk($phoneNumbers, $message, 'general');
```

## Phone Number Format

All phone numbers are automatically normalized to Ghana international format:
- Input: `0241234567` → Output: `233241234567`
- Input: `241234567` → Output: `233241234567`
- Input: `233241234567` → Output: `233241234567`

## Error Handling

### Failed SMS
- Logged to database with status `failed`
- Gateway response stored for debugging
- Can be retried manually from admin panel

### Rate Limiting
- 100ms delay between bulk SMS sends
- Prevents gateway rate limiting
- Adjustable in code

## Monitoring

### SMS Statistics
```php
$smsService = new \App\Services\SMS\HubtelSmsService();
$stats = $smsService->getStats('2024-12-01', '2024-12-31');
```

Returns:
- Total SMS sent
- Success rate
- Failed count
- Breakdown by message type
- Daily statistics

### Database Queries
```sql
-- Total SMS sent today
SELECT COUNT(*) FROM sms_logs 
WHERE DATE(created_at) = CURDATE() AND status = 'sent';

-- Failed SMS
SELECT * FROM sms_logs 
WHERE status = 'failed' 
ORDER BY created_at DESC LIMIT 50;

-- SMS by type
SELECT message_type, COUNT(*) as count 
FROM sms_logs 
WHERE DATE(created_at) = CURDATE() 
GROUP BY message_type;
```

## Cost Tracking

SMS costs are logged per message:
```sql
SELECT 
    DATE(created_at) as date,
    COUNT(*) as total_sms,
    SUM(cost) as total_cost
FROM sms_logs
WHERE status = 'sent'
GROUP BY DATE(created_at);
```

## Testing

### Development Mode
Set `SMS_GATEWAY=log` to log SMS instead of sending:
```env
SMS_GATEWAY=log
```

SMS will be written to:
- `storage/logs/sms_YYYY-MM-DD.log`
- Database with status `logged`

### Test SMS
```php
$smsService = new \App\Services\SMS\HubtelSmsService();
$result = $smsService->send('0241234567', 'Test message', 'general');

if ($result['success']) {
    echo "SMS sent! Message ID: " . $result['message_id'];
} else {
    echo "Failed: " . $result['error'];
}
```

## Troubleshooting

### SMS Not Sending
1. Check Hubtel credentials in .env
2. Verify sender ID is registered
3. Check phone number format
4. Review `sms_logs` table for errors
5. Check gateway response in logs

### Common Errors
- **401 Unauthorized**: Invalid credentials
- **403 Forbidden**: Sender ID not registered
- **400 Bad Request**: Invalid phone number format
- **429 Too Many Requests**: Rate limit exceeded

## Best Practices

1. **Always log SMS** - Track all sends for audit
2. **Normalize phone numbers** - Use consistent format
3. **Handle failures gracefully** - Don't block user flow
4. **Monitor costs** - Track SMS usage and costs
5. **Test thoroughly** - Use development mode first
6. **Keep messages short** - Under 160 characters when possible
7. **Use message types** - For better analytics

## Integration Points

SMS is sent from:
1. **WebhookController** - After successful payment
2. **DrawService** - Winner notifications
3. **ApiAuthService** - OTP verification
4. **USSD** - Balance inquiries (future)
5. **Admin Panel** - Manual bulk SMS (future)

## Future Enhancements

- [ ] SMS templates management
- [ ] Scheduled SMS campaigns
- [ ] SMS delivery reports
- [ ] Cost optimization
- [ ] Multi-language support
- [ ] SMS retry mechanism
- [ ] Admin SMS dashboard
