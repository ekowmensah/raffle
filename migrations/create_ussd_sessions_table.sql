-- Create USSD sessions table for managing USSD user sessions

CREATE TABLE IF NOT EXISTS ussd_sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    session_id VARCHAR(255) NOT NULL UNIQUE,
    phone_number VARCHAR(20) NOT NULL,
    current_step VARCHAR(50) NOT NULL DEFAULT 'main_menu',
    session_data TEXT,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_session_id (session_id),
    INDEX idx_phone_number (phone_number),
    INDEX idx_is_active (is_active),
    INDEX idx_updated_at (updated_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add comments
ALTER TABLE ussd_sessions 
COMMENT = 'Stores USSD session data for user interactions';
