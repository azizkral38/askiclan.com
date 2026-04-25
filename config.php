<?php
// api/config.php - Veritabanı bağlantısı
define('DB_HOST', 'localhost');
define('DB_USER', 'root');        // XAMPP varsayılan
define('DB_PASS', '');            // XAMPP varsayılan (şifre koyduysan yaz)
define('DB_NAME', 'askiclan');

// CS Sunucu RCON Ayarları
define('RCON_HOST', '127.0.0.1');
define('RCON_PORT', 27015);
define('RCON_PASS', 'rcon_sifren'); // server.cfg'deki rcon_password

function getDB() {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER, DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    return $pdo;
}

function jsonResponse($data) {
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}
