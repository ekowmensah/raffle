# USSD Flow Fix - Station-Wide Campaigns

## Issue
When selecting a station on USSD, it was showing the programme list directly instead of showing station-wide campaigns first.

## Root Cause
The `UssdController` was routing directly from station selection to programme selection, bypassing the station-wide campaigns menu.

## Solution

### 1. Updated Flow
**Old Flow:**
```
Select Station → Select Programme → Select Campaign
```

**New Flow:**
```
Select Station → Select Campaign (Station-wide) → Buy
                      ↓
                 Browse by Programme → Select Programme → Select Campaign → Buy
```

### 2. Code Changes

#### UssdController.php
- **Added new step**: `select_station_campaign`
- **Added new handler**: `handleStationCampaignSelection()`
- **Updated**: `handleStationSelection()` to show station campaigns
- **Updated**: `handleProgrammeSelection()` back button to return to station campaigns

#### UssdMenuService.php
- **Added**: `getStationCampaignsArray()` - Gets station-wide campaigns only
- **Updated**: `getCampaignsArray()` - Now includes both station-wide and programme-specific campaigns

### 3. New User Experience

#### Scenario 1: Station-Wide Campaign Purchase (Fast)
```
*123#
↓
1. Buy Ticket
↓
1. Peace FM
↓
CON Select Campaign:
1. Christmas Promo (GHS 5.00)      ← Station-wide
2. New Year Raffle (GHS 10.00)     ← Station-wide
3. Browse by Programme              ← Optional
0. Back
↓
1. (Select Christmas Promo)
↓
Enter quantity...
```
**Total Steps: 5**

#### Scenario 2: Programme-Specific Campaign
```
*123#
↓
1. Buy Ticket
↓
1. Peace FM
↓
CON Select Campaign:
1. Christmas Promo (GHS 5.00)
2. New Year Raffle (GHS 10.00)
3. Browse by Programme              ← User selects this
0. Back
↓
3. (Browse by Programme)
↓
CON Select Programme:
1. Morning Show
2. Drive Time
0. Back
↓
1. (Morning Show)
↓
CON Select Campaign:
1. Christmas Promo (GHS 5.00)      ← Station-wide (still available!)
2. New Year Raffle (GHS 10.00)     ← Station-wide (still available!)
3. Morning Show Exclusive (GHS 3)   ← Programme-specific
0. Back
```
**Total Steps: 7**

## Benefits

✅ **Faster**: Users can buy station-wide campaigns in 5 steps instead of 7
✅ **Flexible**: Programme-specific campaigns still accessible
✅ **Clear**: Users see what's available immediately
✅ **Smart**: Station-wide campaigns appear in both views

## Testing

### Test Case 1: Station-Wide Campaign
1. Dial USSD code
2. Select "Buy Ticket"
3. Select a station
4. **Expected**: See station-wide campaigns with "Browse by Programme" option
5. Select a campaign
6. Complete purchase

### Test Case 2: Programme-Specific Campaign
1. Dial USSD code
2. Select "Buy Ticket"
3. Select a station
4. Select "Browse by Programme"
5. Select a programme
6. **Expected**: See both station-wide AND programme-specific campaigns
7. Complete purchase

### Test Case 3: Back Navigation
1. Go to programme selection
2. Press "0" (Back)
3. **Expected**: Return to station campaigns menu (not station selection)

## Database Queries

### Station-Wide Campaigns
```sql
SELECT rc.* 
FROM raffle_campaigns rc
WHERE rc.station_id = ?
AND rc.status = 'active'
AND rc.id NOT IN (
    SELECT campaign_id 
    FROM campaign_programme_access 
    WHERE programme_id IS NOT NULL
)
```

### Programme View (Both Types)
```sql
SELECT DISTINCT rc.* 
FROM raffle_campaigns rc
LEFT JOIN campaign_programme_access cpa ON rc.id = cpa.campaign_id
WHERE rc.station_id = ?
AND rc.status = 'active'
AND (cpa.programme_id = ? OR cpa.programme_id IS NULL)
ORDER BY (CASE WHEN cpa.programme_id IS NULL THEN 0 ELSE 1 END)
```

## Files Modified
1. `app/controllers/UssdController.php`
2. `app/services/UssdMenuService.php`

## Backward Compatibility
✅ Existing programme-specific campaigns work as before
✅ No database schema changes required
✅ Old USSD sessions will gracefully handle the new flow
