<?php
session_start();
if (!isset($_SESSION['admin'])) { http_response_code(401); exit; }
require_once 'config.php';

$data = json_decode(file_get_contents('php://input'), true);
$action = $data['action'] ?? '';

try {
    $db = getDB();

    if ($action === 'list') {
        $admins = $db->query('SELECT id, username, steamid, level, last_login FROM admins ORDER BY level DESC')->fetchAll(PDO::FETCH_ASSOC);
        jsonResponse(['success' => true, 'admins' => $admins]);

    } elseif ($action === 'add') {
        if ($_SESSION['admin_level'] < 3) jsonResponse(['success' => false, 'message' => 'Yetki yok!']);
        $hash = password_hash($data['password'], PASSWORD_BCRYPT);
        $stmt = $db->prepare('INSERT INTO admins (username, password, steamid, level) VALUES (?, ?, ?, ?)');
        $stmt->execute([$data['username'], $hash, $data['steamid'], $data['level']]);
        jsonResponse(['success' => true]);

    } elseif ($action === 'delete') {
        if ($_SESSION['admin_level'] < 3) jsonResponse(['success' => false, 'message' => 'Yetki yok!']);
        if ($data['id'] == $_SESSION['admin_id']) jsonResponse(['success' => false, 'message' => 'Kendinizi silemezsiniz!']);
        $db->prepare('DELETE FROM admins WHERE id = ?')->execute([$data['id']]);
        jsonResponse(['success' => true]);
    }

} catch (Exception $e) {
    jsonResponse(['success' => false, 'message' => 'Hata: ' . $e->getMessage()]);
}
