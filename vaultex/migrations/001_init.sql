-- Vaultex Database Schema

CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    last_login DATETIME DEFAULT NULL,
    is_active TINYINT(1) DEFAULT 1,
    is_admin TINYINT(1) DEFAULT 0,
    INDEX idx_email (email),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS wallets (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    currency VARCHAR(20) NOT NULL,
    address VARCHAR(255) NOT NULL,
    balance DECIMAL(24, 8) DEFAULT 0,
    created_at DATETIME NOT NULL,
    updated_at DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_currency (currency),
    UNIQUE KEY unique_user_currency (user_id, currency)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS transactions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    wallet_id INT UNSIGNED NOT NULL,
    type ENUM('deposit', 'withdraw', 'transfer', 'exchange') NOT NULL,
    amount DECIMAL(24, 8) NOT NULL,
    status ENUM('pending', 'processing', 'completed', 'failed', 'cancelled') DEFAULT 'pending',
    tx_hash VARCHAR(255) DEFAULT NULL,
    description TEXT DEFAULT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    completed_at DATETIME DEFAULT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (wallet_id) REFERENCES wallets(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_wallet_id (wallet_id),
    INDEX idx_type (type),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS banners (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    subtitle VARCHAR(255) DEFAULT NULL,
    image VARCHAR(255) NOT NULL,
    link VARCHAR(255) DEFAULT '#',
    alt_text VARCHAR(255) DEFAULT NULL,
    is_active TINYINT(1) DEFAULT 1,
    sort_order INT DEFAULT 0,
    created_at DATETIME NOT NULL,
    updated_at DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_is_active (is_active),
    INDEX idx_sort_order (sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS settings (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT DEFAULT NULL,
    setting_type VARCHAR(20) DEFAULT 'string',
    updated_at DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS sessions (
    id VARCHAR(128) PRIMARY KEY,
    user_id INT UNSIGNED DEFAULT NULL,
    ip_address VARCHAR(45) DEFAULT NULL,
    user_agent VARCHAR(255) DEFAULT NULL,
    payload TEXT NOT NULL,
    last_activity INT UNSIGNED NOT NULL,
    INDEX idx_user_id (user_id),
    INDEX idx_last_activity (last_activity)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default admin user (password: admin123 - CHANGE IMMEDIATELY!)
INSERT INTO users (email, password_hash, created_at, is_admin) VALUES 
('admin@vaultex.io', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NOW(), 1);

-- Insert default banners
INSERT INTO banners (title, subtitle, image, link, alt_text, is_active, sort_order, created_at) VALUES
('Храните криптовалюту безопасно', 'Store crypto securely', 'banners/banner-01.jpg', '/register', 'Secure storage', 1, 1, NOW()),
('Вывод за несколько минут', 'Withdraw in minutes', 'banners/banner-02.jpg', '/withdraw', 'Fast withdrawal', 1, 2, NOW()),
('P2P торговля — скоро', 'P2P Trading — Coming Soon', 'banners/banner-03.jpg', '#', 'P2P soon', 1, 3, NOW()),
('Ваш аккаунт под защитой', 'Your account is protected', 'banners/banner-04.jpg', '/settings', '2FA protection', 1, 4, NOW()),
('Добро пожаловать в Vaultex', 'Welcome to Vaultex', 'banners/banner-05.jpg', '/register', 'Welcome', 1, 5, NOW());

-- Insert default settings
INSERT INTO settings (setting_key, setting_value, setting_type, updated_at) VALUES
('site_name', 'Vaultex', 'string', NOW()),
('maintenance_mode', '0', 'boolean', NOW()),
('min_deposit', '0.0001', 'decimal', NOW()),
('min_withdraw', '0.0002', 'decimal', NOW()),
('withdraw_fee_percent', '0.5', 'decimal', NOW());
