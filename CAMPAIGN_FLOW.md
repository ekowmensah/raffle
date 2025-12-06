# Campaign Flow Documentation

## New Flexible Campaign Structure

The system now supports two types of campaigns for more flexible ticket purchasing, especially via USSD:

### 1. **Station-Wide Campaigns** (Shorter Flow)
- **Structure**: Station → Campaign
- **Access**: Available to ALL programmes under the station
- **USSD Flow**: 
  1. Select Station
  2. Select Campaign
  3. Buy Ticket
- **Use Case**: General promotions, station-level raffles, cross-programme campaigns

### 2. **Programme-Specific Campaigns** (Targeted Flow)
- **Structure**: Station → Programme → Campaign
- **Access**: Only available to the specific programme
- **USSD Flow**:
  1. Select Station
  2. Select Programme
  3. Select Campaign
  4. Buy Ticket
- **Use Case**: Programme-exclusive promotions, targeted marketing

## Database Changes

### `raffle_campaigns` Table
- Added `station_id` column (BIGINT UNSIGNED NULL)
- All campaigns must have a `station_id`
- Foreign key to `stations(id)`

### `campaign_programme_access` Table
- `programme_id` is now OPTIONAL (NULL allowed)
- If `programme_id` IS NULL → Station-wide campaign
- If `programme_id` IS SET → Programme-specific campaign

## Creating Campaigns

### Station-Wide Campaign
```php
// In campaign creation form:
- Select Station: Required
- Select Programme: Leave as "Station-wide (No specific programme)"

// Database:
- raffle_campaigns.station_id = [selected station]
- No entry in campaign_programme_access table
```

### Programme-Specific Campaign
```php
// In campaign creation form:
- Select Station: Required
- Select Programme: Select specific programme

// Database:
- raffle_campaigns.station_id = [selected station]
- campaign_programme_access entry with programme_id = [selected programme]
```

## Retrieving Campaigns

### For USSD/API - By Station
```php
$campaigns = $campaignModel->getActiveByStation($stationId);
// Returns all active station-wide campaigns
```

### For USSD/API - By Programme
```php
$campaigns = $campaignModel->getActiveByProgramme($programmeId);
// Returns:
// 1. Station-wide campaigns for the programme's station
// 2. Programme-specific campaigns for this programme
```

## USSD Implementation

### Recommended Flow
```
1. User dials USSD code
2. System identifies user's station (from phone number or selection)
3. Show campaigns:
   - If user has programme: Show station-wide + programme-specific
   - If user has no programme: Show only station-wide
4. User selects campaign
5. User enters ticket quantity
6. Payment & ticket generation
```

### Benefits
- **Faster**: Station-wide campaigns skip programme selection
- **Flexible**: Programmes can still have exclusive campaigns
- **Simpler**: Easier for users to understand and navigate
- **Scalable**: Works for both small and large station networks

## UI Indicators

### Campaign List
- **Station-wide**: Green badge with broadcast tower icon
- **Programme-specific**: Blue badge with users icon
- Station name shown below campaign name

### Campaign Creation
- Programme dropdown has helpful text: "(Optional - leave blank for station-wide campaign)"
- Success message indicates campaign type: "Campaign created successfully as station-wide campaign"

## Migration Notes

- Existing campaigns automatically get `station_id` from their programme's station
- All existing campaigns remain programme-specific (have entries in campaign_programme_access)
- No data loss or breaking changes
