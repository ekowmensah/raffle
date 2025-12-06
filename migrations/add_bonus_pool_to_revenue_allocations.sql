-- Add bonus pool column to revenue_allocations table
-- Bonus pool is the remainder/overflow after daily and final allocation
-- Example: If daily=40% and final=40%, then bonus=20%

ALTER TABLE revenue_allocations 
ADD COLUMN winner_pool_amount_bonus DECIMAL(12,2) NOT NULL DEFAULT 0.00 AFTER winner_pool_amount_final;

-- Add index for better query performance
CREATE INDEX idx_ra_bonus_pool ON revenue_allocations(winner_pool_amount_bonus);
