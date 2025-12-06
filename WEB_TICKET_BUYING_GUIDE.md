# Web Ticket Buying Flow - Station-Wide & Programme Campaigns

## Overview
The web ticket buying flow has been updated to support the new flexible campaign structure, matching the USSD flow for consistency.

---

## New Flow Structure

### **User Journey**

```
Home Page
    ↓
Buy Tickets Button
    ↓
Step 1: Select Station
    ↓
Step 2: Choose Campaign
    ├─→ Station-Wide Campaigns (Direct)
    └─→ Browse by Programme → Programme-Specific Campaigns
    ↓
Step 3: Enter Details (Name, Phone, Quantity)
    ↓
Step 4: Select Payment Method
    ↓
Payment Processing
    ↓
Success Page with Tickets
```

---

## Features

### ✅ Step-by-Step Wizard
- **Visual Progress Indicator**: Shows current step and completed steps
- **Back Navigation**: Users can go back to previous steps
- **Validation**: Each step validates before proceeding

### ✅ Campaign Types
- **Station-Wide Campaigns**: 
  - Displayed immediately after station selection
  - Marked with blue "Station-Wide" badge
  - Available to all listeners of the station
  
- **Programme-Specific Campaigns**:
  - Accessed via "Browse by Programme" button
  - Marked with pink "Programme" badge
  - Exclusive to specific programme listeners

### ✅ Payment Methods
- **Manual Payment**: For cash/mobile money payments
- **MTN Mobile Money**: Direct MTN MoMo integration
- **Hubtel**: All networks (MTN, Telecel, AirtelTigo)

---

## Implementation Details

### **Files Created/Modified**

#### 1. New View: `app/views/public/buy-ticket.php`
Complete ticket buying wizard with 4 steps:
- Step 1: Station selection
- Step 2: Campaign selection (station-wide or programme-specific)
- Step 3: User details and quantity
- Step 4: Payment method selection

#### 2. Updated Controller: `app/controllers/PublicController.php`
**New Methods:**
- `buyTicket()` - Display ticket buying page
- `getCampaignsByStation($stationId)` - Get station-wide campaigns only
- `getCampaignsByProgramme($programmeId)` - Get programme-specific campaigns only

**Updated Methods:**
- `index()` - Now uses `getAllWithDetails()` for campaigns
- `processPayment()` - Supports null `programme_id` for station-wide campaigns

#### 3. Updated View: `app/views/public/index.php`
- Added "Buy Tickets Now" button in hero section
- Added "View Campaigns" button for browsing

---

## API Endpoints

### Get Station-Wide Campaigns
```
GET /public/getCampaignsByStation/{stationId}

Response:
{
    "success": true,
    "campaigns": [
        {
            "id": 17,
            "name": "Christmas Promo",
            "ticket_price": "5.00",
            "currency": "GHS",
            ...
        }
    ]
}
```

### Get Programme-Specific Campaigns
```
GET /public/getCampaignsByProgramme/{programmeId}

Response:
{
    "success": true,
    "campaigns": [
        {
            "id": 15,
            "name": "Morning Show Exclusive",
            "ticket_price": "3.00",
            "currency": "GHS",
            ...
        }
    ]
}
```

### Get Programmes by Station
```
GET /public/getProgrammesByStation/{stationId}

Response:
{
    "success": true,
    "programmes": [
        {
            "id": 1,
            "name": "Morning Show",
            ...
        }
    ]
}
```

---

## User Experience

### **Scenario 1: Buying Station-Wide Campaign (Fast)**

1. **Home Page** → Click "Buy Tickets Now"
2. **Step 1** → Select "Peace FM"
3. **Step 2** → See station-wide campaigns immediately
   ```
   ┌─────────────────────────────────┐
   │ 🌟 Station-Wide                 │
   │ Christmas Promo                 │
   │ GHS 5.00 per ticket             │
   └─────────────────────────────────┘
   ```
4. **Step 3** → Enter name, phone, quantity
5. **Step 4** → Select payment method
6. **Complete** → Payment & tickets generated

**Total Clicks: 6-7**

---

### **Scenario 2: Buying Programme-Specific Campaign**

1. **Home Page** → Click "Buy Tickets Now"
2. **Step 1** → Select "Peace FM"
3. **Step 2** → Click "Browse by Programme"
4. **Step 2b** → Select "Morning Show"
5. **Step 2c** → See programme campaigns
   ```
   ┌─────────────────────────────────┐
   │ 🎤 Programme                    │
   │ Morning Show Exclusive          │
   │ GHS 3.00 per ticket             │
   └─────────────────────────────────┘
   ```
6. **Step 3** → Enter details
7. **Step 4** → Select payment
8. **Complete** → Payment & tickets

**Total Clicks: 8-9**

---

## Visual Design

### **Step Indicator**
```
┌──────────┐  ┌──────────┐  ┌──────────┐  ┌──────────┐
│    1     │  │    2     │  │    3     │  │    4     │
│ Station  │→ │ Campaign │→ │ Details  │→ │ Payment  │
└──────────┘  └──────────┘  └──────────┘  └──────────┘
  ✓ Active      Pending       Pending       Pending
```

### **Campaign Cards**
- **Hover Effect**: Lift and shadow
- **Type Badges**: Color-coded (Blue for station-wide, Pink for programme)
- **Clear Pricing**: Large, prominent price display
- **Icons**: Visual indicators for each type

### **Responsive Design**
- Mobile-friendly
- Touch-optimized buttons
- Collapsible sections
- Smooth transitions

---

## Database Considerations

### **Payments Table**
The `programme_id` column is now **nullable** to support station-wide campaigns:

```sql
ALTER TABLE payments 
MODIFY programme_id BIGINT UNSIGNED NULL;
```

**Payment Record Example (Station-Wide):**
```php
[
    'campaign_id' => 17,
    'player_id' => 5,
    'station_id' => 1,
    'programme_id' => null,  // NULL for station-wide
    'amount' => 10.00,
    ...
]
```

**Payment Record Example (Programme-Specific):**
```php
[
    'campaign_id' => 15,
    'player_id' => 5,
    'station_id' => 1,
    'programme_id' => 2,  // Set for programme-specific
    'amount' => 6.00,
    ...
]
```

---

## JavaScript Functions

### Key Functions in `buy-ticket.php`

#### `selectStation(stationId, stationName)`
- Loads station-wide campaigns
- Transitions to Step 2

#### `showProgrammes()`
- Loads programmes for selected station
- Shows programme selection UI

#### `selectProgramme(programmeId, programmeName)`
- Loads programme-specific campaigns
- Displays campaign list

#### `selectCampaign(campaignId, name, price, currency, programmeId)`
- Sets selected campaign
- Updates form fields
- Transitions to Step 3

#### `updateTotalAmount()`
- Calculates total based on quantity
- Updates display in real-time

#### `selectPaymentMethod(method)`
- Sets payment method
- Visual feedback on selection

---

## Testing Checklist

### ✅ Station-Wide Campaign Purchase
- [ ] Select station
- [ ] See station-wide campaigns immediately
- [ ] Select a station-wide campaign
- [ ] Enter details
- [ ] Complete payment
- [ ] Verify `programme_id` is NULL in database

### ✅ Programme-Specific Campaign Purchase
- [ ] Select station
- [ ] Click "Browse by Programme"
- [ ] Select a programme
- [ ] See programme campaigns
- [ ] Select a campaign
- [ ] Enter details
- [ ] Complete payment
- [ ] Verify `programme_id` is set in database

### ✅ Navigation
- [ ] Back button works at each step
- [ ] Step indicator updates correctly
- [ ] Form validation works
- [ ] Error messages display properly

### ✅ Responsive Design
- [ ] Mobile view works
- [ ] Tablet view works
- [ ] Desktop view works
- [ ] Touch interactions work

---

## Comparison: USSD vs Web

| Feature | USSD | Web |
|---------|------|-----|
| **Station Selection** | Text menu | Visual cards |
| **Campaign Display** | Numbered list | Rich cards with badges |
| **Navigation** | 0 for back | Back buttons |
| **Payment** | Prompt on phone | Form selection |
| **Steps** | 5-7 | 4 steps (wizard) |
| **Visual Feedback** | Text only | Colors, icons, animations |

**Consistency**: Both flows follow the same logic:
1. Select Station
2. Choose Campaign (station-wide or programme)
3. Enter Details/Quantity
4. Payment

---

## Benefits

### For Users
✅ **Clear Path**: Visual wizard guides through process
✅ **Flexibility**: Can choose station-wide or programme campaigns
✅ **Transparency**: See all costs upfront
✅ **Convenience**: Multiple payment options

### For Stations
✅ **Broader Reach**: Station-wide campaigns reach all listeners
✅ **Targeted Marketing**: Programme campaigns for specific audiences
✅ **Better Analytics**: Track which campaign types perform better
✅ **Consistent Experience**: Matches USSD flow

### For Developers
✅ **Maintainable**: Clean separation of concerns
✅ **Extensible**: Easy to add new payment methods
✅ **Consistent**: Same API structure as USSD
✅ **Well-Documented**: Clear code comments

---

## Future Enhancements

### Planned Features
- [ ] **Promo Code Support**: Apply discount codes
- [ ] **Saved Payment Methods**: Remember user preferences
- [ ] **Quick Buy**: One-click purchase for returning users
- [ ] **Gift Tickets**: Buy tickets for others
- [ ] **Bulk Purchase**: Special pricing for large quantities
- [ ] **Campaign Filters**: Filter by price, end date, etc.
- [ ] **Favorites**: Save favorite campaigns

### Payment Gateway Integration
- [ ] **Hubtel Integration**: Real-time mobile money
- [ ] **MTN MoMo API**: Direct MTN integration
- [ ] **Paystack**: Card payments
- [ ] **Webhook Handling**: Automatic payment confirmation

---

## Support & Troubleshooting

### Common Issues

**Issue 1: "No campaigns available"**
- **Cause**: No active station-wide campaigns
- **Solution**: Use "Browse by Programme" or create station-wide campaigns

**Issue 2: "Programme_id cannot be null" error**
- **Cause**: Database migration not run
- **Solution**: Run `migration_payments_nullable_programme.sql`

**Issue 3: AJAX requests failing**
- **Cause**: Incorrect URL routing
- **Solution**: Check `.htaccess` and route configuration

**Issue 4: Payment not processing**
- **Cause**: Missing required fields
- **Solution**: Check browser console for validation errors

---

## Conclusion

The web ticket buying flow now fully supports the flexible campaign structure, providing a seamless experience that matches the USSD flow while taking advantage of web capabilities for richer visual feedback and easier navigation.

**Key Takeaway**: Users can now buy tickets for station-wide campaigns in just 4 steps, or browse programme-specific campaigns for more targeted options.
