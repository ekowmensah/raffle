-- Add quantity column to tickets table
-- This allows one ticket record to represent multiple entries

ALTER TABLE tickets 
ADD COLUMN quantity INT UNSIGNED NOT NULL DEFAULT 1 AFTER ticket_code;

-- Add index for better performance when querying by quantity
CREATE INDEX idx_tickets_quantity ON tickets(quantity);
