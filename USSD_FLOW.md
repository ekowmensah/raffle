# USSD Flow Documentation

## New Smart USSD Flow

The USSD system now adapts based on campaign types, providing the shortest path to ticket purchase.

---

## Flow Diagram

### **Scenario 1: Station Has Station-Wide Campaigns (Fastest)**

```
*123# 
  ↓
┌─────────────────────────┐
│  Welcome to Raffle      │
│  1. Buy Ticket          │
│  2. Check My Tickets    │
│  3. Check Winners       │
│  0. Exit                │
└─────────────────────────┘
  ↓ (User selects 1)
┌─────────────────────────┐
│  Select Station:        │
│  1. Peace FM            │
│  2. Okay FM             │
│  3. Adom FM             │
│  0. Back                │
└─────────────────────────┘
  ↓ (User selects 1 - Peace FM)
┌─────────────────────────┐
│  Select Campaign:       │
│  1. Christmas Promo     │  ← Station-wide
│     (GHS 5.00)          │
│  2. New Year Raffle     │  ← Station-wide
│     (GHS 10.00)         │
│  3. Browse by Programme │  ← Optional
│  0. Back                │
└─────────────────────────┘
  ↓ (User selects 1)
┌─────────────────────────┐
│  Enter ticket quantity: │
└─────────────────────────┘
  ↓
┌─────────────────────────┐
│  Confirm:               │
│  Campaign: Christmas    │
│  Quantity: 2            │
│  Amount: GHS 10.00      │
│  1. Confirm             │
│  0. Cancel              │
└─────────────────────────┘
  ↓
Payment & Ticket Generation
```

**Steps: 5** (Welcome → Station → Campaign → Quantity → Confirm)

---

### **Scenario 2: User Wants Programme-Specific Campaigns**

```
*123# 
  ↓
┌─────────────────────────┐
│  Welcome to Raffle      │
│  1. Buy Ticket          │
└─────────────────────────┘
  ↓
┌─────────────────────────┐
│  Select Station:        │
│  1. Peace FM            │
└─────────────────────────┘
  ↓
┌─────────────────────────┐
│  Select Campaign:       │
│  1. Christmas Promo     │
│  2. New Year Raffle     │
│  3. Browse by Programme │  ← User selects this
│  0. Back                │
└─────────────────────────┘
  ↓ (User selects 3)
┌─────────────────────────┐
│  Select Programme:      │
│  1. Morning Show        │
│  2. Drive Time          │
│  3. Sports Hour         │
│  0. Back                │
└─────────────────────────┘
  ↓ (User selects 1)
┌─────────────────────────┐
│  Select Campaign:       │
│  1. Christmas Promo     │  ← Station-wide (available to all)
│     (GHS 5.00)          │
│  2. Morning Show Promo  │  ← Programme-specific
│     (GHS 3.00)          │
│  0. Back                │
└─────────────────────────┘
  ↓
Continue with quantity & payment...
```

**Steps: 7** (Welcome → Station → Browse → Programme → Campaign → Quantity → Confirm)

---

## Key Features

### 1. **Smart Menu Adaptation**
- Shows station-wide campaigns first (fastest path)
- Option to "Browse by Programme" for programme-specific campaigns
- Programme view shows BOTH station-wide AND programme-specific campaigns

### 2. **Campaign Display**
```
Campaign Name (Currency Price)
Example: Christmas Promo (GHS 5.00)
```

### 3. **Sorting Logic**
- Station-wide campaigns appear first
- Then programme-specific campaigns
- Alphabetically within each group

---

## Implementation Examples

### Example 1: Peace FM Setup

**Station-Wide Campaigns:**
- Christmas Mega Raffle (GHS 10)
- New Year Bonanza (GHS 5)

**Programme-Specific Campaigns:**
- Morning Show: "Wake Up & Win" (GHS 3)
- Drive Time: "Rush Hour Raffle" (GHS 2)

**USSD Experience:**
```
User dials → Selects Peace FM
Shows:
  1. Christmas Mega Raffle (GHS 10)    [Station-wide]
  2. New Year Bonanza (GHS 5)          [Station-wide]
  3. Browse by Programme

If user selects "Morning Show":
  1. Wake Up & Win (GHS 3)             [Programme-specific ONLY]
  
Note: Station-wide campaigns do NOT appear here - they're only at station level
```

---

## Code Implementation

### UssdMenuService Methods

#### 1. `buildStationCampaignMenu($stationId)`
- Shows station-wide campaigns only
- Adds "Browse by Programme" option
- **Use after**: User selects station

#### 2. `buildCampaignMenu($stationId, $programmeId)`
- Shows programme-specific campaigns ONLY
- Station-wide campaigns do NOT appear here
- **Use after**: User selects programme

### Query Logic

**Station-Wide Campaigns:**
```sql
SELECT * FROM raffle_campaigns 
WHERE station_id = ? 
AND status = 'active'
AND id NOT IN (
    SELECT campaign_id FROM campaign_programme_access 
    WHERE programme_id IS NOT NULL
)
```

**Programme View (Programme-Specific Only)**
```sql
SELECT DISTINCT rc.* 
FROM raffle_campaigns rc
INNER JOIN campaign_programme_access cpa ON rc.id = cpa.campaign_id
WHERE rc.station_id = ?
AND rc.status = 'active'
AND cpa.programme_id = ?
ORDER BY rc.name
```

**Note**: Station-wide campaigns do NOT appear in programme views. They only appear at the station level.

---

## Benefits

### For Users
✅ **Faster**: 5 steps instead of 7 for station-wide campaigns
✅ **Clearer**: See all available campaigns immediately
✅ **Flexible**: Can still browse programme-specific campaigns

### For Stations
✅ **Broader Reach**: Station-wide campaigns reach all listeners
✅ **Targeted Marketing**: Programme-specific campaigns for niche audiences
✅ **Better Analytics**: Track which campaigns perform better

### For USSD Performance
✅ **Fewer Menus**: Reduced navigation depth
✅ **Less Confusion**: Clear campaign availability
✅ **Better UX**: Users get to campaigns faster

---

## Migration Path

### Phase 1: Current State
- All campaigns are programme-specific
- Users must select programme first

### Phase 2: After Implementation
- New campaigns can be station-wide or programme-specific
- Existing campaigns remain programme-specific
- USSD flow adapts automatically

### Phase 3: Optimization
- Convert popular campaigns to station-wide
- Keep exclusive campaigns programme-specific
- Monitor user behavior and adjust

---

## Testing Scenarios

### Test 1: Station with only station-wide campaigns
- Should show campaigns immediately after station selection
- "Browse by Programme" should show same campaigns

### Test 2: Station with only programme-specific campaigns
- Should show "Browse by Programme" option
- Must select programme to see campaigns

### Test 3: Station with mixed campaigns
- Station-wide campaigns shown first
- Programme selection shows both types
- No duplicates in listings

### Test 4: Programme with no specific campaigns
- Should still see station-wide campaigns
- Clear indication of campaign availability
