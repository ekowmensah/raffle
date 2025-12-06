-- Migration: Make programme_id nullable in payments table
-- This allows payments for station-wide campaigns that don't have a specific programme

-- Make programme_id nullable
ALTER TABLE payments 
MODIFY programme_id BIGINT UNSIGNED NULL;

-- Note: This change is backward compatible
-- Existing payments with programme_id will remain unchanged
-- New payments for station-wide campaigns can have NULL programme_id
