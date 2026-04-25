<?php
session_start();
if (!isset($_SESSION['admin'])) { http_response_code(401); exit; }
require_once 'config.php';

$data = json_decode(file_get_contents('php://input'), true);
$action = $data['action'] ?? '';

function sendRcon($command) {
    $sock = @fsockopen('udp://' . RCON_HOST, RCON_PORT, $errno, $errstr, 3);
    if (!$sock) return false;
    stream_set_timeout($sock, 3);

    // RCON challenge
    $challenge = "\xFF\xFF\xFF\xFF" . "challenge rcon\n";
    fwrite($sock, $challenge);
    $response = fread($sock, 1400);

    preg_match('/challenge rcon (\d+)/', $response, $m);
    $challengeNum = $m[1] ?? '0';

    $packet = "\xFF\xFF\xFF\xFF" . "rcon $challengeNum \"" . RCON_PASS . "\" $command\n";
    fwrite($sock, $packet);
    $result = fread($sock, 4096);
    fclose($sock);

    return trim(substr($result, 5));
}

if ($action === 'restart') {
    $out = sendRcon('restart');
    jsonResponse(['success' => true, 'message' => 'Sunucu yeniden başlatıldı!', 'output' => $out]);

} elseif ($action === 'stop') {
    $out = sendRcon('quit');
    jsonResponse(['success' => true, 'message' => 'Sunucu durduruldu!', 'output' => $out]);

} elseif ($action === 'start') {
    jsonResponse(['success' => false, 'message' => 'Sunucuyu başlatmak için VDS\'e bağlanın.']);

} elseif ($action === 'changelevel') {
    $map = preg_replace('/[^a-z0-9_]/', '', strtolower($data['map'] ?? ''));
    $out = sendRcon("changelevel $map");
    jsonResponse(['success' => true, 'message' => "$map haritasına geçildi!", 'output' => $out]);

} elseif ($action === 'rcon') {
    $cmd = $data['command'] ?? '';
    $out = sendRcon($cmd);
    if ($out !== false) {
        jsonResponse(['success' => true, 'output' => $out ?: 'Komut gönderildi.']);
    } else {
        jsonResponse(['success' => false, 'message' => 'RCON bağlantısı kurulamadı!']);
    }
}
