<?php
// htdocs/api/wishlist.php
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

$dbDir = __DIR__ . '/data';
if (!is_dir($dbDir)) {
  @mkdir($dbDir, 0777, true);
}
$dbPath = $dbDir . '/wishlist.sqlite';

try {
  $db = new PDO('sqlite:' . $dbPath);
  $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  $db->exec("
    CREATE TABLE IF NOT EXISTS wishlist (
      id INTEGER PRIMARY KEY AUTOINCREMENT,
      name TEXT NOT NULL,
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );
  ");

  if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $db->query("SELECT id, name, created_at FROM wishlist ORDER BY id DESC LIMIT 100");
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['ok' => true, 'items' => $items]);
    exit;
  }

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);
    if (!is_array($data)) {
      http_response_code(400);
      echo json_encode(['ok' => false, 'error' => 'Invalid JSON']);
      exit;
    }

    $name = trim((string)($data['name'] ?? ''));
    if ($name === '') {
      http_response_code(400);
      echo json_encode(['ok' => false, 'error' => 'Empty']);
      exit;
    }
    if (strlen($name) > 120) {
      $name = substr($name, 0, 120);
    }

    $stmt = $db->prepare("INSERT INTO wishlist (name) VALUES (:name)");
    $stmt->execute([':name' => $name]);

    $id = (int)$db->lastInsertId();
    $row = $db->query("SELECT id, name, created_at FROM wishlist WHERE id = " . $id)->fetch(PDO::FETCH_ASSOC);

    echo json_encode(['ok' => true, 'item' => $row]);
    exit;
  }

  http_response_code(405);
  echo json_encode(['ok' => false, 'error' => 'GET/POST only']);
} catch (Exception $e) {
  http_response_code(500);
  echo json_encode(['ok' => false, 'error' => 'Server error']);
}
