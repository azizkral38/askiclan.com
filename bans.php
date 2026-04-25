<?php
session_start();
if (!isset($_SESSION['admin'])) { http_response_code(401); exit; }
require_once 'config.php';

$data = json_decode(file_get_contents('php://input'), true);
$action = $data['action'] ?? '';

try {
    $db = getDB();

    if ($action === 'list') {
        $bans = $db->query('SELECT * FROM bans ORDER BY created_at DESC')->fetchAll(PDO::FETCH_ASSOC);
        jsonResponse(['success' => true, 'bans' => $bans]);

    } elseif ($action === 'add') {
        $steamid = $data['steamid'] ?? '';
        $name = $data['name'] ?? '';
        $reason = $data['reason'] ?? '';
        $duration = intval($data['duration'] ?? 0);
        $expires = $duration > 0 ? date('Y-m-d H:i:s', time() + $duration * 60) : null;

        $stmt = $db->prepare('INSERT INTO bans (steamid, name, reason, duration, admin, expires_at) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->execute([$steamid, $name, $reason, $duration, $_SESSION['admin'], $expires]);
        jsonResponse(['success' => true]);

    } elseif ($action === 'remove') {
        $db->prepare('DELETE FROM bans WHERE id = ?')->execute([$data['id']]);
        jsonResponse(['success' => true]);
    }

} catch (Exception $e) {
    jsonResponse(['success' => false, 'message' => 'Hata: ' . $e->getMessage()]);
}
