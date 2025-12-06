-- =========================================================
-- Raffle System - Seed Data
-- =========================================================
-- Run this after importing db.sql to populate initial data
-- =========================================================

-- Insert default roles
INSERT INTO roles (name, description) VALUES
('super_admin', 'Super Administrator with full system access'),
('station_admin', 'Station Administrator managing station operations'),
('programme_manager', 'Programme Manager handling programme activities'),
('finance', 'Finance Officer managing payments and reports'),
('auditor', 'Auditor with read-only access to all data');

-- Insert default super admin user
-- Password: admin123
INSERT INTO users (role_id, name, email, password_hash, is_active) VALUES
(1, 'System Administrator', 'admin@raffle.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1);

-- Insert sample stations
INSERT INTO stations (name, short_code_label, code, phone, email, location, default_station_percent, default_programme_percent, default_prize_pool_percent, is_active) VALUES
('Hope FM', 'HFM', 'HOPE_FM', '+233201234567', 'info@hopefm.com', 'Accra, Ghana', 25, 10, 40, 1),
('Joy FM', 'JFM', 'JOY_FM', '+233201234568', 'info@joyfm.com', 'Kumasi, Ghana', 25, 10, 40, 1),
('Peace FM', 'PFM', 'PEACE_FM', '+233201234569', 'info@peacefm.com', 'Takoradi, Ghana', 25, 10, 40, 1);

-- Insert sample programmes for Hope FM (station_id = 1)
INSERT INTO programmes (station_id, name, code, ussd_option_number, is_active) VALUES
(1, 'Morning Show', 'MORNING_SHOW', 1, 1),
(1, 'Drive Time', 'DRIVE_TIME', 2, 1),
(1, 'Evening Vibes', 'EVENING_VIBES', 3, 1);

-- Insert sample programmes for Joy FM (station_id = 2)
INSERT INTO programmes (station_id, name, code, ussd_option_number, is_active) VALUES
(2, 'Breakfast Club', 'BREAKFAST_CLUB', 1, 1),
(2, 'Afternoon Delight', 'AFTERNOON_DELIGHT', 2, 1);

-- Insert sample programmes for Peace FM (station_id = 3)
INSERT INTO programmes (station_id, name, code, ussd_option_number, is_active) VALUES
(3, 'Wake Up Call', 'WAKE_UP_CALL', 1, 1),
(3, 'Night Shift', 'NIGHT_SHIFT', 2, 1);

-- Insert sample sponsor
INSERT INTO sponsors (name, contact_person, phone, email, notes) VALUES
('MTN Ghana', 'John Mensah', '+233201234570', 'john.mensah@mtn.com', 'Major telecommunications sponsor'),
('Coca-Cola Ghana', 'Sarah Osei', '+233201234571', 'sarah.osei@coca-cola.com', 'Beverage industry sponsor');

-- Create wallets for all stations
INSERT INTO station_wallets (station_id, balance) 
SELECT id, 0.00 FROM stations;

-- Insert sample station admin users
-- Password: station123
INSERT INTO users (role_id, station_id, name, email, password_hash, is_active) VALUES
(2, 1, 'Hope FM Admin', 'admin@hopefm.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1),
(2, 2, 'Joy FM Admin', 'admin@joyfm.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1),
(2, 3, 'Peace FM Admin', 'admin@peacefm.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1);

-- Insert sample programme managers
-- Password: programme123
INSERT INTO users (role_id, station_id, programme_id, name, email, password_hash, is_active) VALUES
(3, 1, 1, 'Morning Show Manager', 'morning@hopefm.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1),
(3, 1, 2, 'Drive Time Manager', 'drive@hopefm.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1);

-- Insert finance officer
-- Password: finance123
INSERT INTO users (role_id, name, email, password_hash, is_active) VALUES
(4, 'Finance Officer', 'finance@raffle.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1);

-- Insert auditor
-- Password: auditor123
INSERT INTO users (role_id, name, email, password_hash, is_active) VALUES
(5, 'System Auditor', 'auditor@raffle.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1);

-- =========================================================
-- Default Passwords Summary:
-- =========================================================
-- admin@raffle.com       -> admin123
-- admin@hopefm.com       -> station123
-- admin@joyfm.com        -> station123
-- admin@peacefm.com      -> station123
-- morning@hopefm.com     -> programme123
-- drive@hopefm.com       -> programme123
-- finance@raffle.com     -> finance123
-- auditor@raffle.com     -> auditor123
-- =========================================================
-- IMPORTANT: Change all default passwords after first login!
-- =========================================================
