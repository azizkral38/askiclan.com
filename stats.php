<?php
session_start();
if (!isset($_SESSION['admin'])) { http_response_code(401); exit; }
require_once 'config.php';

$data = json_decode(file_get_contents('php://input'), true);

try {
    $db = getDB();
    $bans = $db->query('SELECT COUNT(*) FROM bans')->fetchColumn();
    $admins = $db->query('SELECT COUNT(*) FROM admins')->fetchColumn();
    $recent = $db->query('SELECT * FROM bans ORDER BY created_at DESC LIMIT 5')->fetchAll(PDO::FETCH_ASSOC);
    jsonResponse(['success' => true, 'players' => 0, 'bans' => $bans, 'admins' => $admins, 'recent_bans' => $recent]);
} catch (Exception $e) {
    jsonResponse(['success' => false]);
}
