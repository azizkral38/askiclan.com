-- ASKi Clan Panel - Veritabanı Kurulum
-- phpMyAdmin'de bu dosyayı import et

CREATE DATABASE IF NOT EXISTS askiclan CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE askiclan;

-- Admin tablosu
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    steamid VARCHAR(50),
    level INT DEFAULT 1 COMMENT '1=Moderatör, 2=Admin, 3=Süper Admin',
    last_login DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Ban tablosu
CREATE TABLE IF NOT EXISTS bans (
    id INT AUTO_INCREMENT PRIMARY KEY,
    steamid VARCHAR(50) NOT NULL,
    name VARCHAR(100),
    reason VARCHAR(255),
    duration INT DEFAULT 0 COMMENT '0=Kalıcı, diğerleri dakika cinsinden',
    admin VARCHAR(50),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    expires_at DATETIME
);

-- Varsayılan süper admin (şifre: admin123 - GİRİŞ SONRASI DEĞİŞTİR!)
INSERT INTO admins (username, password, level) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3);
