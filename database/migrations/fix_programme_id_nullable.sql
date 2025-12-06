-- Make programme_id nullable in tickets and payments tables to support station-wide campaigns
-- Station-wide campaigns don't have a programme, only programme-specific campaigns do

-- First, let's find and drop any foreign key constraints on tickets.programme_id
-- Run this query first to see what constraints exist:
-- SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE 
-- WHERE TABLE_NAME = 'tickets' AND COLUMN_NAME = 'programme_id' AND TABLE_SCHEMA = DATABASE();

-- TICKETS TABLE - Drop all foreign keys on programme_id
SET @constraint_name = (
    SELECT CONSTRAINT_NAME 
    FROM information_schema.KEY_COLUMN_USAGE 
    WHERE TABLE_NAME = 'tickets' 
    AND COLUMN_NAME = 'programme_id' 
    AND TABLE_SCHEMA = DATABASE()
    AND CONSTRAINT_NAME != 'PRIMARY'
    LIMIT 1
);

SET @sql = IF(@constraint_name IS NOT NULL, 
              CONCAT('ALTER TABLE `tickets` DROP FOREIGN KEY `', @constraint_name, '`'), 
              'SELECT "No foreign key constraint found on tickets.programme_id"');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Modify tickets.programme_id to allow NULL
ALTER TABLE `tickets` 
MODIFY COLUMN `programme_id` INT(11) NULL DEFAULT NULL;

-- PAYMENTS TABLE - Drop all foreign keys on programme_id
SET @constraint_name = (
    SELECT CONSTRAINT_NAME 
    FROM information_schema.KEY_COLUMN_USAGE 
    WHERE TABLE_NAME = 'payments' 
    AND COLUMN_NAME = 'programme_id' 
    AND TABLE_SCHEMA = DATABASE()
    AND CONSTRAINT_NAME != 'PRIMARY'
    LIMIT 1
);

SET @sql = IF(@constraint_name IS NOT NULL, 
              CONCAT('ALTER TABLE `payments` DROP FOREIGN KEY `', @constraint_name, '`'), 
              'SELECT "No foreign key constraint found on payments.programme_id"');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Modify payments.programme_id to allow NULL
ALTER TABLE `payments` 
MODIFY COLUMN `programme_id` INT(11) NULL DEFAULT NULL;

-- REVENUE_ALLOCATIONS TABLE - Drop all foreign keys on programme_id
SET @constraint_name = (
    SELECT CONSTRAINT_NAME 
    FROM information_schema.KEY_COLUMN_USAGE 
    WHERE TABLE_NAME = 'revenue_allocations' 
    AND COLUMN_NAME = 'programme_id' 
    AND TABLE_SCHEMA = DATABASE()
    AND CONSTRAINT_NAME != 'PRIMARY'
    LIMIT 1
);

SET @sql = IF(@constraint_name IS NOT NULL, 
              CONCAT('ALTER TABLE `revenue_allocations` DROP FOREIGN KEY `', @constraint_name, '`'), 
              'SELECT "No foreign key constraint found on revenue_allocations.programme_id"');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Modify revenue_allocations.programme_id to allow NULL
ALTER TABLE `revenue_allocations` 
MODIFY COLUMN `programme_id` INT(11) NULL DEFAULT NULL;

-- Done! Foreign key constraints removed to allow NULL values for station-wide campaigns
