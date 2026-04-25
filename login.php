<?php
session_start();
require_once 'config.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || empty($data['username']) || empty($data['password'])) {
    jsonResponse(['success' => false, 'message' => 'Eksik bilgi']);
}

try {
    $db = getDB();
    $stmt = $db->prepare('SELECT * FROM admins WHERE username = ?');
    $stmt->execute([$data['username']]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin && password_verify($data['password'], $admin['password'])) {
        $_SESSION['admin'] = $admin['username'];
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_level'] = $admin['level'];

        // Son giriş güncelle
        $db->prepare('UPDATE admins SET last_login = NOW() WHERE id = ?')->execute([$admin['id']]);

        jsonResponse(['success' => true]);
    } else {
        jsonResponse(['success' => false, 'message' => 'Hatalı giriş']);
    }
} catch (Exception $e) {
    jsonResponse(['success' => false, 'message' => 'Veritabanı hatası']);
}
