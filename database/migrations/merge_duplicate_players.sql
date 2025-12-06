-- Merge duplicate player records with different phone formats
-- This script finds players with the same phone number in different formats
-- (e.g., 0545644749 and 233545644749) and merges them into one

-- Step 1: Update tickets to point to the normalized player
UPDATE tickets t
INNER JOIN players p1 ON t.player_id = p1.id
INNER JOIN players p2 ON (
    CONCAT('233', SUBSTRING(p1.phone, 2)) = p2.phone 
    OR p1.phone = CONCAT('233', SUBSTRING(p2.phone, 2))
)
SET t.player_id = CASE 
    WHEN p2.phone LIKE '233%' THEN p2.id 
    ELSE p1.id 
END
WHERE p1.id != p2.id;

-- Step 2: Update payments to point to the normalized player
UPDATE payments pay
INNER JOIN players p1 ON pay.player_id = p1.id
INNER JOIN players p2 ON (
    CONCAT('233', SUBSTRING(p1.phone, 2)) = p2.phone 
    OR p1.phone = CONCAT('233', SUBSTRING(p2.phone, 2))
)
SET pay.player_id = CASE 
    WHEN p2.phone LIKE '233%' THEN p2.id 
    ELSE p1.id 
END
WHERE p1.id != p2.id;

-- Step 3: Delete duplicate players (keep the one with 233 prefix)
DELETE p1 FROM players p1
INNER JOIN players p2 ON (
    CONCAT('233', SUBSTRING(p1.phone, 2)) = p2.phone 
    OR p1.phone = CONCAT('233', SUBSTRING(p2.phone, 2))
)
WHERE p1.id != p2.id 
AND p1.phone NOT LIKE '233%';

-- Step 4: Update remaining players to have normalized phone numbers
UPDATE players 
SET phone = CONCAT('233', SUBSTRING(phone, 2))
WHERE phone LIKE '0%';

-- Done! All players now have normalized phone numbers starting with 233
