-- Migration: Create payment_gateway_logs table for tracking all gateway requests/responses
-- This table logs all interactions with payment gateways (Hubtel, MTN, Paystack, etc.)

CREATE TABLE IF NOT EXISTS `payment_gateway_logs` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `gateway_provider` VARCHAR(50) NOT NULL COMMENT 'hubtel, mtn, paystack, etc.',
  `transaction_reference` VARCHAR(100) NOT NULL COMMENT 'Client reference or transaction ID',
  `request_type` VARCHAR(50) NOT NULL COMMENT 'initialize, verify, webhook, etc.',
  `request_data` TEXT NULL COMMENT 'JSON encoded request payload',
  `response_data` TEXT NULL COMMENT 'JSON encoded response data',
  `response_code` VARCHAR(20) NULL COMMENT 'Gateway response code',
  `http_status` INT NULL COMMENT 'HTTP status code',
  `error_message` TEXT NULL COMMENT 'Error message if any',
  `ip_address` VARCHAR(45) NULL COMMENT 'Client IP address',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_gateway_reference` (`gateway_provider`, `transaction_reference`),
  INDEX `idx_request_type` (`request_type`),
  INDEX `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add index for quick lookup of recent logs
CREATE INDEX `idx_gateway_recent` ON `payment_gateway_logs` (`gateway_provider`, `created_at` DESC);
