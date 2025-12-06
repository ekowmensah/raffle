-- =========================================================
-- Raffle System v1.0 Schema
-- =========================================================
-- Assumes: MySQL 8+, InnoDB, utf8mb4_unicode_ci
-- =========================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------
-- 1. User & Role Management
-- ---------------------------------------------------------

CREATE TABLE roles (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name            VARCHAR(50) NOT NULL UNIQUE,      -- super_admin, station_admin, programme_manager, finance, auditor, etc.
    description     VARCHAR(255) NULL,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE users (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    role_id         BIGINT UNSIGNED NOT NULL,
    station_id      BIGINT UNSIGNED NULL,            -- if user belongs to a station
    programme_id    BIGINT UNSIGNED NULL,            -- if user is tied to a specific programme
    name            VARCHAR(120) NOT NULL,
    email           VARCHAR(120) NULL UNIQUE,
    phone           VARCHAR(30) NULL UNIQUE,
    password_hash   VARCHAR(255) NOT NULL,
    is_active       TINYINT(1) NOT NULL DEFAULT 1,
    last_login_at   DATETIME NULL,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_users_role
        FOREIGN KEY (role_id) REFERENCES roles(id)
        ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------
-- 2. Stations & Programmes
-- ---------------------------------------------------------

CREATE TABLE stations (
    id                              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name                            VARCHAR(150) NOT NULL,
    short_code_label                VARCHAR(20) NULL,   -- label used in USSD menu, like "1", "2", "HFM"
    code                            VARCHAR(50) NOT NULL UNIQUE, -- e.g. HOPE_FM
    phone                           VARCHAR(30) NULL,
    email                           VARCHAR(120) NULL,
    location                        VARCHAR(255) NULL,
    default_station_percent         TINYINT NOT NULL DEFAULT 25,  -- default commission %
    default_programme_percent       TINYINT NOT NULL DEFAULT 10,  -- default programme %
    default_prize_pool_percent      TINYINT NOT NULL DEFAULT 40,  -- if station-specific override is ever needed
    is_active                       TINYINT(1) NOT NULL DEFAULT 1,
    created_at                      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at                      DATETIME NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE programmes (
    id                              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    station_id                      BIGINT UNSIGNED NOT NULL,
    name                            VARCHAR(150) NOT NULL,
    code                            VARCHAR(50) NOT NULL,       -- e.g. MORNING_SHOW
    ussd_option_number              INT NULL,                   -- menu index under station, if you hard-map
    station_percent                 TINYINT NULL,               -- override station share if needed
    programme_percent               TINYINT NULL,               -- override programme share if needed
    is_active                       TINYINT(1) NOT NULL DEFAULT 1,
    created_at                      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at                      DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_programmes_station_code (station_id, code),
    CONSTRAINT fk_programmes_station
        FOREIGN KEY (station_id) REFERENCES stations(id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- Link users to stations/programmes after these tables exist
ALTER TABLE users
    ADD CONSTRAINT fk_users_station
        FOREIGN KEY (station_id) REFERENCES stations(id)
        ON DELETE SET NULL,
    ADD CONSTRAINT fk_users_programme
        FOREIGN KEY (programme_id) REFERENCES programmes(id)
        ON DELETE SET NULL;

-- ---------------------------------------------------------
-- 3. Sponsors (Optional but useful)
-- ---------------------------------------------------------

CREATE TABLE sponsors (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name            VARCHAR(150) NOT NULL,
    logo_url        VARCHAR(255) NULL,
    contact_person  VARCHAR(120) NULL,
    phone           VARCHAR(30) NULL,
    email           VARCHAR(120) NULL,
    notes           TEXT NULL,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------
-- 4. Raffle Campaigns
-- ---------------------------------------------------------

CREATE TABLE raffle_campaigns (
    id                              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    sponsor_id                      BIGINT UNSIGNED NULL,
    name                            VARCHAR(150) NOT NULL,
    code                            VARCHAR(60) NOT NULL UNIQUE,      -- e.g. DEC_MEGA_2025
    description                     TEXT NULL,
    ticket_price                    DECIMAL(12,2) NOT NULL DEFAULT 1.00,
    currency                        CHAR(3) NOT NULL DEFAULT 'GHS',
    start_date                      DATE NOT NULL,
    end_date                        DATE NOT NULL,
    status                          ENUM('draft','active','closed','draw_done') NOT NULL DEFAULT 'draft',

    -- Commission / sharing percentages
    platform_percent                TINYINT NOT NULL DEFAULT 25,
    station_percent                 TINYINT NOT NULL DEFAULT 25,
    programme_percent               TINYINT NOT NULL DEFAULT 10,
    prize_pool_percent              TINYINT NOT NULL DEFAULT 40,

    -- Internal split of the prize pool
    daily_share_percent_of_pool     TINYINT NOT NULL DEFAULT 50,
    final_share_percent_of_pool     TINYINT NOT NULL DEFAULT 50,

    -- Daily draw controls
    daily_draw_enabled              TINYINT(1) NOT NULL DEFAULT 1,
    max_daily_prize_amount          DECIMAL(12,2) NULL,              -- hard cap per daily draw
    min_daily_turnover_amount       DECIMAL(12,2) NULL,              -- min total payments/day to allow draw
    min_daily_prize_amount          DECIMAL(12,2) NULL,              -- optional guaranteed minimum (if you top-up)
    
    -- Flags
    is_config_locked                TINYINT(1) NOT NULL DEFAULT 0,   -- lock config once active
    created_by_user_id              BIGINT UNSIGNED NULL,
    created_at                      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at                      DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_campaigns_sponsor
        FOREIGN KEY (sponsor_id) REFERENCES sponsors(id)
        ON DELETE SET NULL,
    CONSTRAINT fk_campaigns_created_by
        FOREIGN KEY (created_by_user_id) REFERENCES users(id)
        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- Which programmes can market which campaign (optional; if not used, assume all)
CREATE TABLE campaign_programme_access (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    campaign_id     BIGINT UNSIGNED NOT NULL,
    programme_id    BIGINT UNSIGNED NOT NULL,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_campaign_programme (campaign_id, programme_id),
    CONSTRAINT fk_cpa_campaign
        FOREIGN KEY (campaign_id) REFERENCES raffle_campaigns(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_cpa_programme
        FOREIGN KEY (programme_id) REFERENCES programmes(id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------
-- 5. Players (end users / listeners)
-- ---------------------------------------------------------

CREATE TABLE players (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    phone           VARCHAR(30) NOT NULL,
    alt_phone       VARCHAR(30) NULL,
    name            VARCHAR(150) NULL,
    email           VARCHAR(120) NULL,
    total_spent     DECIMAL(14,2) NOT NULL DEFAULT 0.00,
    total_tickets   INT UNSIGNED NOT NULL DEFAULT 0,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_players_phone (phone)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------
-- 6. Payments (raw money in)
-- ---------------------------------------------------------

CREATE TABLE payments (
    id                      BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    player_id               BIGINT UNSIGNED NOT NULL,
    station_id              BIGINT UNSIGNED NOT NULL,
    programme_id            BIGINT UNSIGNED NOT NULL,
    campaign_id             BIGINT UNSIGNED NOT NULL,
    gateway                 VARCHAR(50) NOT NULL,          -- e.g. MTN_MOMO, HUBTEL, PAYSTACK
    gateway_reference       VARCHAR(100) NOT NULL,
    internal_reference      VARCHAR(100) NOT NULL UNIQUE,  -- your own ref
    amount                  DECIMAL(12,2) NOT NULL,
    currency                CHAR(3) NOT NULL DEFAULT 'GHS',
    status                  ENUM('pending','success','failed') NOT NULL DEFAULT 'pending',
    channel                 VARCHAR(30) NULL,              -- USSD, WEB, APP
    paid_at                 DATETIME NULL,
    created_at              DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at              DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_payments_campaign_date (campaign_id, paid_at),
    INDEX idx_payments_status_date (status, paid_at),

    CONSTRAINT fk_payments_player
        FOREIGN KEY (player_id) REFERENCES players(id)
        ON DELETE RESTRICT,
    CONSTRAINT fk_payments_station
        FOREIGN KEY (station_id) REFERENCES stations(id)
        ON DELETE RESTRICT,
    CONSTRAINT fk_payments_programme
        FOREIGN KEY (programme_id) REFERENCES programmes(id)
        ON DELETE RESTRICT,
    CONSTRAINT fk_payments_campaign
        FOREIGN KEY (campaign_id) REFERENCES raffle_campaigns(id)
        ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------
-- 7. Revenue Allocations (commissions + prize pool split)
-- ---------------------------------------------------------

CREATE TABLE revenue_allocations (
    id                          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    payment_id                  BIGINT UNSIGNED NOT NULL,
    campaign_id                 BIGINT UNSIGNED NOT NULL,
    station_id                  BIGINT UNSIGNED NOT NULL,
    programme_id                BIGINT UNSIGNED NOT NULL,

    gross_amount                DECIMAL(12,2) NOT NULL,    -- same as payments.amount for success
    platform_amount             DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    station_amount              DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    programme_amount            DECIMAL(12,2) NOT NULL DEFAULT 0.00,

    winner_pool_amount_total    DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    winner_pool_amount_daily    DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    winner_pool_amount_final    DECIMAL(12,2) NOT NULL DEFAULT 0.00,

    created_at                  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_ra_campaign_date (campaign_id, created_at),
    INDEX idx_ra_station_campaign (station_id, campaign_id),

    CONSTRAINT fk_ra_payment
        FOREIGN KEY (payment_id) REFERENCES payments(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_ra_campaign
        FOREIGN KEY (campaign_id) REFERENCES raffle_campaigns(id)
        ON DELETE RESTRICT,
    CONSTRAINT fk_ra_station
        FOREIGN KEY (station_id) REFERENCES stations(id)
        ON DELETE RESTRICT,
    CONSTRAINT fk_ra_programme
        FOREIGN KEY (programme_id) REFERENCES programmes(id)
        ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------
-- 8. Tickets
-- ---------------------------------------------------------

CREATE TABLE tickets (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    player_id       BIGINT UNSIGNED NOT NULL,
    station_id      BIGINT UNSIGNED NOT NULL,
    programme_id    BIGINT UNSIGNED NOT NULL,
    campaign_id     BIGINT UNSIGNED NOT NULL,
    payment_id      BIGINT UNSIGNED NOT NULL,

    ticket_code     VARCHAR(80) NOT NULL UNIQUE,      -- e.g. HFM-DEC-98374-1
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_tickets_campaign (campaign_id),
    INDEX idx_tickets_campaign_date (campaign_id, created_at),
    INDEX idx_tickets_player_campaign (player_id, campaign_id),

    CONSTRAINT fk_tickets_player
        FOREIGN KEY (player_id) REFERENCES players(id)
        ON DELETE RESTRICT,
    CONSTRAINT fk_tickets_station
        FOREIGN KEY (station_id) REFERENCES stations(id)
        ON DELETE RESTRICT,
    CONSTRAINT fk_tickets_programme
        FOREIGN KEY (programme_id) REFERENCES programmes(id)
        ON DELETE RESTRICT,
    CONSTRAINT fk_tickets_campaign
        FOREIGN KEY (campaign_id) REFERENCES raffle_campaigns(id)
        ON DELETE RESTRICT,
    CONSTRAINT fk_tickets_payment
        FOREIGN KEY (payment_id) REFERENCES payments(id)
        ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------
-- 9. Draws (daily / final, manually triggered)
-- ---------------------------------------------------------

CREATE TABLE draws (
    id                      BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    campaign_id             BIGINT UNSIGNED NOT NULL,
    draw_type               ENUM('daily','final','bonus') NOT NULL,
    draw_date               DATE NOT NULL,
    total_prize_pool        DECIMAL(12,2) NOT NULL DEFAULT 0.00,   -- prize allocated for this draw
    started_by_user_id      BIGINT UNSIGNED NOT NULL,
    duration_seconds        INT NOT NULL DEFAULT 15,
    status                  ENUM('pending','completed','published') NOT NULL DEFAULT 'pending',
    created_at              DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at              DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE KEY uq_draw_campaign_type_date (campaign_id, draw_type, draw_date),

    CONSTRAINT fk_draws_campaign
        FOREIGN KEY (campaign_id) REFERENCES raffle_campaigns(id)
        ON DELETE RESTRICT,
    CONSTRAINT fk_draws_started_by
        FOREIGN KEY (started_by_user_id) REFERENCES users(id)
        ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE draw_winners (
    id                      BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    draw_id                 BIGINT UNSIGNED NOT NULL,
    player_id               BIGINT UNSIGNED NOT NULL,
    ticket_id               BIGINT UNSIGNED NOT NULL,
    prize_amount            DECIMAL(12,2) NOT NULL,
    prize_rank              INT NOT NULL DEFAULT 1,      -- 1 = first, 2 = second, etc.
    prize_type              ENUM('cash','airtime','data','voucher','other') NOT NULL DEFAULT 'cash',
    prize_description       VARCHAR(255) NULL,
    prize_paid_status       ENUM('pending','processing','paid','failed') NOT NULL DEFAULT 'pending',
    prize_paid_at           DATETIME NULL,
    created_at              DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_draw_winners_draw (draw_id),
    INDEX idx_draw_winners_player (player_id),

    CONSTRAINT fk_dw_draw
        FOREIGN KEY (draw_id) REFERENCES draws(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_dw_player
        FOREIGN KEY (player_id) REFERENCES players(id)
        ON DELETE RESTRICT,
    CONSTRAINT fk_dw_ticket
        FOREIGN KEY (ticket_id) REFERENCES tickets(id)
        ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------
-- 10. Station Wallets & Transactions (for commissions)
-- ---------------------------------------------------------

CREATE TABLE station_wallets (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    station_id      BIGINT UNSIGNED NOT NULL UNIQUE,
    balance         DECIMAL(14,2) NOT NULL DEFAULT 0.00,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_station_wallets_station
        FOREIGN KEY (station_id) REFERENCES stations(id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE station_wallet_transactions (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    station_wallet_id   BIGINT UNSIGNED NOT NULL,
    related_campaign_id BIGINT UNSIGNED NULL,
    related_payment_id  BIGINT UNSIGNED NULL,
    transaction_type    ENUM('credit','debit') NOT NULL,
    amount              DECIMAL(12,2) NOT NULL,
    description         VARCHAR(255) NULL,
    created_by_user_id  BIGINT UNSIGNED NULL,
    created_at          DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_swt_wallet (station_wallet_id),

    CONSTRAINT fk_swt_wallet
        FOREIGN KEY (station_wallet_id) REFERENCES station_wallets(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_swt_campaign
        FOREIGN KEY (related_campaign_id) REFERENCES raffle_campaigns(id)
        ON DELETE SET NULL,
    CONSTRAINT fk_swt_payment
        FOREIGN KEY (related_payment_id) REFERENCES payments(id)
        ON DELETE SET NULL,
    CONSTRAINT fk_swt_created_by
        FOREIGN KEY (created_by_user_id) REFERENCES users(id)
        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------
-- 11. Audit Logs (for safety & traceability)
-- ---------------------------------------------------------

CREATE TABLE audit_logs (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id         BIGINT UNSIGNED NULL,
    event_type      VARCHAR(80) NOT NULL,          -- e.g. CAMPAIGN_CONFIG_CHANGE, DRAW_TRIGGERED
    entity_type     VARCHAR(80) NULL,             -- e.g. raffle_campaign, draw, payment
    entity_id       BIGINT UNSIGNED NULL,
    description     TEXT NULL,
    ip_address      VARCHAR(45) NULL,
    user_agent      VARCHAR(255) NULL,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_audit_user (user_id),
    INDEX idx_audit_entity (entity_type, entity_id),

    CONSTRAINT fk_audit_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


SET FOREIGN_KEY_CHECKS = 1;

-FIXES- 

ALTER TABLE draws
    ADD COLUMN eligible_ticket_count BIGINT UNSIGNED NOT NULL DEFAULT 0 AFTER total_prize_pool,
    ADD COLUMN random_seed           VARCHAR(64) NULL AFTER eligible_ticket_count,
    ADD COLUMN selection_method      VARCHAR(80) NOT NULL DEFAULT 'weighted_by_ticket_count' AFTER random_seed;

ALTER TABLE raffle_campaigns
    ADD COLUMN config_snapshot_json JSON NULL AFTER is_config_locked;


ALTER TABLE players
    ADD COLUMN loyalty_points INT UNSIGNED NOT NULL DEFAULT 0 AFTER total_tickets,
    ADD COLUMN loyalty_level  ENUM('bronze','silver','gold','platinum') NOT NULL DEFAULT 'bronze' AFTER loyalty_points;


CREATE TABLE promo_codes (
    id                  BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    station_id          BIGINT UNSIGNED NOT NULL,
    programme_id        BIGINT UNSIGNED NULL,
    user_id             BIGINT UNSIGNED NULL,  -- if tied to a user/presenter
    code                VARCHAR(40) NOT NULL UNIQUE,  -- e.g. HFM_MORN_JOHN
    name                VARCHAR(120) NOT NULL,        -- Presenter / Agent name or code name
    extra_commission_percent   TINYINT NULL,          -- optional bonus commission
    is_active           TINYINT(1) NOT NULL DEFAULT 1,
    created_at          DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at          DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_promo_station
        FOREIGN KEY (station_id) REFERENCES stations(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_promo_programme
        FOREIGN KEY (programme_id) REFERENCES programmes(id)
        ON DELETE SET NULL,
    CONSTRAINT fk_promo_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


ALTER TABLE payments
    ADD COLUMN promo_code_id BIGINT UNSIGNED NULL AFTER programme_id,
    ADD CONSTRAINT fk_payments_promo_code
        FOREIGN KEY (promo_code_id) REFERENCES promo_codes(id)
        ON DELETE SET NULL;


ALTER TABLE raffle_campaigns
    ADD COLUMN default_language_code VARCHAR(10) NOT NULL DEFAULT 'en' AFTER currency;
