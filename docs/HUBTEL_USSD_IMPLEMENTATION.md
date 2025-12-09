# Hubtel Programmable Services API - USSD Implementation

## Overview

This document describes the comprehensive refactored implementation of the Hubtel Programmable Services API for the Raffle System. The implementation follows the official Hubtel API specification and supports USSD, Hubtel App, and Webstore platforms.

## Architecture

### Core Components

1. **UssdController** - Main controller handling all USSD interactions
2. **UssdSessionService** - Session management and state tracking
3. **UssdMenuService** - Menu building and navigation
4. **HubtelTransactionStatusService** - Transaction status verification

### Request Flow

```
User Dials USSD Code
    ↓
Hubtel → Service Interaction URL (index method)
    ↓
Process Request (Initiation/Response/Timeout)
    ↓
Send Response (response/release/AddToCart)
    ↓
[If AddToCart] User Completes Payment
    ↓
Hubtel → Service Fulfillment URL
    ↓
Process Payment & Generate Tickets
    ↓
Send Service Fulfillment Callback to Hubtel
```

## API Implementation Details

### 1. Service Interaction URL

**Endpoint**: `/ussd` (or your configured endpoint)

**Handles Three Request Types**:

#### A. Initiation Request
- **Type**: `"Initiation"`
- **Description**: First interaction when user dials USSD code
- **Response**: Shows main menu with `Type: "response"`

```php
// Request from Hubtel
{
    "Type": "Initiation",
    "Mobile": "233200585542",
    "SessionId": "unique-session-id",
    "ServiceCode": "713",
    "Message": "*713#",
    "Operator": "vodafone",
    "Sequence": 1,
    "ClientState": "",
    "Platform": "USSD"
}

// Response to Hubtel
{
    "SessionId": "unique-session-id",
    "Type": "response",
    "Message": "Be A Winner Today\n1. Buy Ticket\n2. Check My Tickets\n3. Check Winnings\n4. My Balance\n0. Exit",
    "Label": "Main Menu",
    "DataType": "input",
    "FieldType": "text",
    "ClientState": "main_menu"
}
```

#### B. Response Request
- **Type**: `"Response"`
- **Description**: User provided input in ongoing session
- **Uses**: `ClientState` to track current step

```php
// Request from Hubtel
{
    "Type": "Response",
    "Mobile": "233200585542",
    "SessionId": "unique-session-id",
    "ServiceCode": "713",
    "Message": "1",  // User's input
    "Operator": "vodafone",
    "Sequence": 2,
    "ClientState": "main_menu",
    "Platform": "USSD"
}
```

#### C. Timeout Request
- **Type**: `"Timeout"`
- **Description**: User took too long to respond
- **Response**: Release session with `Type: "release"`

### 2. AddToCart Implementation

**Purpose**: Triggers Hubtel's payment collection flow

**When to Use**: When user confirms purchase and is ready to pay

**Item Structure** (per API spec):
```php
{
    "ItemName": "Campaign Name",
    "Qty": 5,           // Integer quantity
    "Price": 10.50      // Float unit price
}
```

**Complete AddToCart Response**:
```php
{
    "SessionId": "unique-session-id",
    "Type": "AddToCart",
    "Message": "Please complete payment:\n\nItem: National Raffle\nQuantity: 5\nUnit Price: GHS 10.50\nTotal: GHS 52.50\n\nYou will receive a payment prompt.",
    "Label": "Payment Checkout",
    "DataType": "display",
    "FieldType": "text",
    "Item": {
        "ItemName": "National Raffle",
        "Qty": 5,
        "Price": 10.50
    }
}
```

**Important Notes**:
- `Qty` must be integer
- `Price` is unit price (not total)
- Total is calculated as `Qty * Price`
- No `ClientState` needed - session ends after AddToCart
- Hubtel handles payment collection automatically

### 3. Service Fulfillment URL

**Endpoint**: Same as Service Interaction URL (detects `OrderId` in payload)

**Triggered**: After user successfully completes payment

**Request from Hubtel**:
```php
{
    "SessionId": "unique-session-id",
    "OrderId": "hubtel-order-id",
    "ExtraData": {},
    "OrderInfo": {
        "CustomerMobileNumber": "233200585542",
        "CustomerEmail": null,
        "CustomerName": "John Doe",
        "Status": "Paid",
        "OrderDate": "2023-11-06T15:16:50.3581338+00:00",
        "Currency": "GHS",
        "BranchName": "Haatso",
        "IsRecurring": false,
        "RecurringInvoiceId": null,
        "Subtotal": 52.50,
        "Items": [
            {
                "ItemId": "item-id",
                "Name": "National Raffle",
                "Quantity": 5,
                "UnitPrice": 10.50
            }
        ],
        "Payment": {
            "PaymentType": "mobilemoney",
            "AmountPaid": 52.50,
            "AmountAfterCharges": 52.00,
            "PaymentDate": "2023-11-06T15:16:50.3581338+00:00",
            "PaymentDescription": "Payment successful",
            "IsSuccessful": true
        }
    }
}
```

**Processing Steps**:
1. Validate `Status === "Paid"` and `Payment.IsSuccessful === true`
2. Retrieve session data using `SessionId`
3. Update payment record with `OrderId`
4. Generate tickets for user
5. Allocate revenue
6. Send Service Fulfillment Callback to Hubtel
7. Return 200 OK response

### 4. Service Fulfillment Callback

**Purpose**: Notify Hubtel whether service was successfully delivered

**Endpoint**: `https://gs-callback.hubtel.com:9055/callback`

**Method**: POST

**Requirements**:
- Must be sent within 1 hour of receiving fulfillment
- Requires IP whitelisting (share your public IP with Hubtel)

**Success Callback**:
```php
{
    "SessionId": "unique-session-id",
    "OrderId": "hubtel-order-id",
    "ServiceStatus": "success",
    "MetaData": {
        "payment_id": 12345,
        "tickets_generated": 5
    }
}
```

**Failure Callback**:
```php
{
    "SessionId": "unique-session-id",
    "OrderId": "hubtel-order-id",
    "ServiceStatus": "failed",
    "MetaData": {
        "reason": "Ticket generation failed"
    }
}
```

### 5. Transaction Status Check

**Purpose**: Verify payment status when Service Fulfillment callback is not received within 5 minutes

**Endpoint**: `https://api-txnstatus.hubtel.com/transactions/{POS_Sales_ID}/status`

**Method**: GET

**Requirements**:
- IP whitelisting required
- Basic Authentication (optional but recommended)
- POS Sales ID from Hubtel Merchant Dashboard

**Usage**:
```php
$statusService = new HubtelTransactionStatusService();

// Check by SessionId (recommended)
$result = $statusService->checkBySessionId($sessionId);

if ($result['success'] && $result['data']['status'] === 'Paid') {
    // Payment confirmed - process manually
    $amountAfterCharges = $result['data']['amountAfterCharges'];
    // Generate tickets...
}
```

**Response**:
```php
{
    "message": "Successful",
    "responseCode": "0000",
    "data": {
        "date": "2024-04-25T21:45:48.4740964Z",
        "status": "Paid",
        "transactionId": "hubtel-txn-id",
        "externalTransactionId": "telco-txn-id",
        "paymentMethod": "mobilemoney",
        "clientReference": "session-id",
        "currencyCode": null,
        "amount": 52.50,
        "charges": 0.50,
        "amountAfterCharges": 52.00,
        "isFulfilled": null
    }
}
```

## ClientState Management

**Purpose**: Track user's current position in the flow across requests

**How It Works**:
1. Send `ClientState` in response
2. Hubtel returns it in next request
3. Use it to route to correct handler

**Example Flow**:
```
Response 1: ClientState = "main_menu"
Request 2: ClientState = "main_menu" → handleMainMenu()

Response 2: ClientState = "select_station"
Request 3: ClientState = "select_station" → handleStationSelection()

Response 3: ClientState = "confirm_payment"
Request 4: ClientState = "confirm_payment" → handlePaymentConfirmation()
```

**Benefits**:
- Session continuity even if database session is lost
- Stateless operation possible
- Better error recovery

## Response Types

### 1. Response Type (Continue Session)
```php
{
    "Type": "response",
    "DataType": "input",
    "FieldType": "text"  // or "number", "decimal", "phone", "email", "textarea"
}
```

### 2. Release Type (End Session)
```php
{
    "Type": "release",
    "DataType": "display",
    "FieldType": "text"
}
```

### 3. AddToCart Type (Checkout)
```php
{
    "Type": "AddToCart",
    "DataType": "display",
    "FieldType": "text",
    "Item": { /* item details */ }
}
```

## Field Types for Rich UX

**For Web/Mobile Channels** (Hubtel App, Webstore):

- `text` - Simple text input, alphanumeric keyboard
- `number` - Whole numbers only, numeric keyboard
- `decimal` - Decimal numbers, numeric keyboard with decimal point
- `phone` - Phone number with validation
- `email` - Email address with email keyboard
- `textarea` - Large text field for longer input

**For USSD**: All field types work as text input

## Error Handling

### 1. Invalid JSON
```php
if (!$request) {
    $this->sendErrorResponse('', 'Service temporarily unavailable.');
    return;
}
```

### 2. Missing Required Fields
```php
if (empty($sessionId) || empty($phoneNumber)) {
    $this->sendErrorResponse($sessionId, 'Invalid request. Please try again.');
    return;
}
```

### 3. Session Not Found
```php
if (!$session) {
    $this->sendErrorResponse($sessionId, 'Session expired. Please dial again.');
    return;
}
```

### 4. Payment Validation
```php
if ($paymentStatus !== 'Paid' || !$paymentInfo['IsSuccessful']) {
    $this->sendServiceFulfillmentCallback($sessionId, $orderId, 'failed', [
        'reason' => 'Payment not successful'
    ]);
    return;
}
```

## Security Considerations

### 1. IP Whitelisting
- **Service Fulfillment**: Whitelist Hubtel IPs
  - 52.50.116.54
  - 18.202.122.131
  - 52.31.15.68
- **Transaction Status Check**: Share your public IP with Hubtel

### 2. Request Validation
- Always validate `SessionId`, `Mobile`, `Type`
- Verify `OrderInfo.Status === "Paid"`
- Check `Payment.IsSuccessful === true`

### 3. Data Sanitization
- Clean phone numbers to standard format (233XXXXXXXXX)
- Validate quantity ranges (1-1000)
- Sanitize user input before database operations

## Configuration Requirements

### Environment Variables
```env
# Hubtel Configuration
HUBTEL_POS_SALES_ID=your_pos_sales_id
HUBTEL_API_USERNAME=your_api_username
HUBTEL_API_PASSWORD=your_api_password

# Service URLs (configure in Hubtel Dashboard)
SERVICE_INTERACTION_URL=https://yourdomain.com/ussd
SERVICE_FULFILLMENT_URL=https://yourdomain.com/ussd
```

### Hubtel Dashboard Setup
1. **Add Service**: Provide Service Interaction and Fulfillment URLs
2. **Request USSD Code**: Get shortcode (e.g., *713#)
3. **Link Service to Code**: Attach your service to the USSD code
4. **IP Whitelisting**: Submit your public IP for Transaction Status Check

## Testing Checklist

### Service Interaction
- [ ] Initiation request shows main menu
- [ ] Response requests navigate correctly
- [ ] Timeout requests close session gracefully
- [ ] ClientState maintains flow continuity
- [ ] All field types work on different platforms

### AddToCart Flow
- [ ] Item structure is correct (ItemName, Qty, Price)
- [ ] Payment prompt is received
- [ ] Session data is stored for fulfillment
- [ ] Payment record is created with pending status

### Service Fulfillment
- [ ] Fulfillment request is received after payment
- [ ] Payment status is validated correctly
- [ ] Tickets are generated successfully
- [ ] Revenue is allocated properly
- [ ] Success callback is sent to Hubtel
- [ ] Session is closed after fulfillment

### Transaction Status Check
- [ ] Status can be checked by SessionId
- [ ] Paid status is detected correctly
- [ ] Amount after charges is retrieved
- [ ] Manual fulfillment works for missed callbacks

### Error Scenarios
- [ ] Invalid JSON is handled
- [ ] Missing fields are caught
- [ ] Session not found is handled
- [ ] Payment failures send failed callback
- [ ] Exceptions are logged and reported

## Logging Best Practices

### Request/Response Logging
```php
error_log('=== USSD REQUEST ===' . PHP_EOL . $rawInput);
error_log('=== USSD RESPONSE ===' . PHP_EOL . json_encode($response, JSON_PRETTY_PRINT));
```

### Service Fulfillment Logging
```php
error_log("=== SERVICE FULFILLMENT REQUEST ===" . PHP_EOL . $rawInput);
error_log("Service Fulfillment - SessionId: $sessionId, OrderId: $orderId, Status: $paymentStatus");
error_log("Payment updated - ID: $paymentId, OrderId: $orderId, Amount: $amountAfterCharges");
```

### Error Logging
```php
error_log('USSD Exception: ' . $e->getMessage() . PHP_EOL . $e->getTraceAsString());
```

## Common Issues & Solutions

### Issue 1: "UUE" Error
**Cause**: Response body format is invalid
**Solution**: Ensure all required fields are present and properly formatted

### Issue 2: Payment Prompt Not Received
**Cause**: AddToCart response format incorrect
**Solution**: Verify Item structure (ItemName, Qty as int, Price as float)

### Issue 3: Service Fulfillment Not Received
**Cause**: IP not whitelisted or endpoint not accessible
**Solution**: 
- Verify IP whitelisting with Hubtel
- Use Transaction Status Check as fallback
- Check server logs for incoming requests

### Issue 4: Callback to Hubtel Fails
**Cause**: IP not whitelisted for callback endpoint
**Solution**: Share your public IP with Hubtel for whitelisting

## Performance Optimization

### 1. Database Queries
- Use indexed columns for session lookups
- Implement connection pooling
- Cache frequently accessed data

### 2. Session Management
- Clean up old sessions regularly (24+ hours)
- Use efficient session data structure
- Minimize session data size

### 3. Response Time
- Keep processing under 10 seconds
- Use async processing for heavy operations
- Implement timeout handling

## Monitoring & Maintenance

### Key Metrics to Track
- Session initiation rate
- Conversion rate (initiation → payment)
- Payment success rate
- Average session duration
- Error rate by type

### Regular Maintenance
- Clean up old sessions (daily)
- Review error logs (daily)
- Monitor payment reconciliation (daily)
- Update IP whitelist as needed
- Test transaction status check (weekly)

## Support & Resources

### Hubtel Resources
- API Documentation: [Hubtel Developer Portal]
- Merchant Dashboard: [Your Hubtel account]
- Support: Contact your Retail Systems Engineer

### Internal Resources
- Controller: `app/controllers/UssdController.php`
- Services: `app/services/Ussd*.php`
- Transaction Status: `app/services/HubtelTransactionStatusService.php`
- Documentation: `docs/HUBTEL_USSD_IMPLEMENTATION.md`

## Version History

- **v2.0** (Current) - Complete refactoring with proper AddToCart, Service Fulfillment, and Transaction Status Check
- **v1.0** - Initial implementation with basic USSD flow
