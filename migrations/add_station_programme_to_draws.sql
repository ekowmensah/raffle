-- Add station_id and programme_id to draws table
-- This allows draws to be specific to a station and programme

ALTER TABLE draws 
ADD COLUMN station_id BIGINT UNSIGNED NULL AFTER campaign_id,
ADD COLUMN programme_id BIGINT UNSIGNED NULL AFTER station_id;

-- Add foreign key constraints
ALTER TABLE draws
ADD CONSTRAINT fk_draws_station
    FOREIGN KEY (station_id) REFERENCES stations(id)
    ON DELETE RESTRICT,
ADD CONSTRAINT fk_draws_programme
    FOREIGN KEY (programme_id) REFERENCES programmes(id)
    ON DELETE RESTRICT;

-- Add index for better query performance
CREATE INDEX idx_draws_station_programme ON draws(station_id, programme_id);

-- Update unique constraint to include station and programme
ALTER TABLE draws DROP INDEX uq_draw_campaign_type_date;
ALTER TABLE draws ADD UNIQUE KEY uq_draw_station_programme_campaign_type_date (station_id, programme_id, campaign_id, draw_type, draw_date);
