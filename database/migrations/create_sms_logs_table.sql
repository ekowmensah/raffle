-- Create SMS logs table
CREATE TABLE IF NOT EXISTS sms_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    phone_number VARCHAR(20) NOT NULL,
    message TEXT NOT NULL,
    message_type ENUM('ticket', 'payment', 'winner', 'draw', 'balance', 'otp', 'general') DEFAULT 'general',
    status ENUM('pending', 'sent', 'failed', 'logged') DEFAULT 'pending',
    gateway VARCHAR(50) DEFAULT 'hubtel',
    gateway_response TEXT,
    message_id VARCHAR(100),
    cost DECIMAL(10, 4) DEFAULT 0.0000,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    sent_at TIMESTAMP NULL,
    INDEX idx_phone (phone_number),
    INDEX idx_status (status),
    INDEX idx_type (message_type),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
