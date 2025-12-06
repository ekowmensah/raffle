-- Cleanup players table
-- Fix NULL phone numbers and update stats based on actual data

-- Step 1: Delete players with NULL phone numbers that have no tickets or payments
DELETE FROM players 
WHERE phone IS NULL 
AND id NOT IN (SELECT DISTINCT player_id FROM tickets WHERE player_id IS NOT NULL)
AND id NOT IN (SELECT DISTINCT player_id FROM payments WHERE player_id IS NOT NULL);

-- Step 2: Update total_spent for all players based on actual successful payments
UPDATE players p
SET total_spent = (
    SELECT COALESCE(SUM(amount), 0)
    FROM payments
    WHERE player_id = p.id AND status = 'success'
);

-- Step 3: Update total_tickets for all players based on actual tickets
UPDATE players p
SET total_tickets = (
    SELECT COALESCE(SUM(quantity), 0)
    FROM tickets
    WHERE player_id = p.id
);

-- Step 4: Update loyalty_points based on total_spent (1 point per GHS)
UPDATE players 
SET loyalty_points = FLOOR(total_spent);

-- Step 5: Update loyalty_level based on total_spent
UPDATE players 
SET loyalty_level = CASE 
    WHEN total_spent >= 1000 THEN 'platinum'
    WHEN total_spent >= 500 THEN 'gold'
    WHEN total_spent >= 100 THEN 'silver'
    ELSE 'bronze'
END;

-- Done! All player stats are now accurate
