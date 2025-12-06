-- Migration: Add station_id to campaigns and make programme optional
-- This allows campaigns to be attached directly to stations OR to programmes

-- Step 1: Add station_id column to raffle_campaigns table (without foreign key first)
ALTER TABLE raffle_campaigns 
ADD COLUMN station_id BIGINT UNSIGNED NULL AFTER sponsor_id;

-- Step 2: Update existing campaigns to have station_id based on their programme
-- This ensures existing data remains consistent
UPDATE raffle_campaigns rc
INNER JOIN campaign_programme_access cpa ON rc.id = cpa.campaign_id
INNER JOIN programmes p ON cpa.programme_id = p.id
SET rc.station_id = p.station_id
WHERE rc.station_id IS NULL;

-- Step 3: Add foreign key constraint for station_id
ALTER TABLE raffle_campaigns
ADD CONSTRAINT fk_campaigns_station
    FOREIGN KEY (station_id) REFERENCES stations(id)
    ON DELETE RESTRICT;

-- Step 4: Add index for better query performance
CREATE INDEX idx_campaigns_station ON raffle_campaigns(station_id);

-- Step 5: Make programme_id optional in campaign_programme_access
-- (This table will now only be used for programme-specific campaigns)
ALTER TABLE campaign_programme_access
MODIFY programme_id BIGINT UNSIGNED NULL;

-- Note: After this migration:
-- - Station-level campaigns: station_id is set, no entry in campaign_programme_access
-- - Programme-level campaigns: station_id is set, AND has entry in campaign_programme_access with programme_id
