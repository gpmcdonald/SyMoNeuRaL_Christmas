<?php
// htdocs/api/heartbeat.php
// Receives heartbeat from symonstat.py
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

$SECRET_TOKEN = '2671bfae89d86d8489401f888358e4bfa845ba85f26444e3a3e55e4702547041';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  echo json_encode(['ok' => false, 'error' => 'POST only']);
  exit;
}

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);

if (!is_array($data)) {
  http_response_code(400);
  echo json_encode(['ok' => false, 'error' => 'Invalid JSON']);
  exit;
}

$token = $data['token'] ?? '';
if (!hash_equals($SECRET_TOKEN, $token)) {
  http_response_code(403);
  echo json_encode(['ok' => false, 'error' => 'Bad token']);
  exit;
}

$payload = [
  'ts' => time(),
  'cod' => !empty($data['cod']),
  'vscode' => !empty($data['vscode']),
  'note' => (string)($data['note'] ?? ''),
];

$dir = __DIR__ . '/data';
if (!is_dir($dir)) {
  @mkdir($dir, 0777, true);
}

$file = $dir . '/heartbeat.json';
file_put_contents($file, json_encode($payload, JSON_PRETTY_PRINT));

echo json_encode(['ok' => true]);
