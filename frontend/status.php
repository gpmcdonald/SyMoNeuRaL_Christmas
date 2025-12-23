<?php
// htdocs/api/status.php
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

$file = __DIR__ . '/data/heartbeat.json';
$now = time();

if (!file_exists($file)) {
  echo json_encode([
    'live' => false,
    'cod' => false,
    'vscode' => false,
    'age_s' => null,
    'note' => ''
  ]);
  exit;
}

$raw = file_get_contents($file);
$j = json_decode($raw, true);

$ts = (int)($j['ts'] ?? 0);
$age = $now - $ts;

// Consider "live" if heartbeat within last 30 seconds
$live = ($ts > 0) && ($age <= 30);

echo json_encode([
  'live' => $live,
  'cod' => !empty($j['cod']),
  'vscode' => !empty($j['vscode']),
  'age_s' => ($ts > 0 ? max(0, $age) : null),
  'note' => (string)($j['note'] ?? ''),
]);
