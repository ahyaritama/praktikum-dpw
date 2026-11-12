<?php
require __DIR__ . '/../config.php';
require_admin();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_out(['error' => 'POST only'], 405);
check_csrf();

$sec = $_POST['section'] ?? '';
if (!isset($FIELDS[$sec])) json_out(['error' => 'Section tidak dikenal'], 422);

$stmt = db()->prepare('REPLACE INTO content (k, v) VALUES (?, ?)');
foreach ($FIELDS[$sec]['fields'] as $k => [$label, $type]) {
    if (!isset($_POST[$k])) continue;
    $v = trim((string)$_POST[$k]);
    if (mb_strlen($v) > 1500) json_out(['error' => "$label terlalu panjang"], 422);
    if ($type === 'image' && $v !== '' && !preg_match('#^(assets|uploads)/[A-Za-z0-9._-]+$#', $v)) {
        json_out(['error' => "$label: path gambar tidak valid"], 422);
    }
    if ($type === 'datetime' && $v !== '' && !preg_match('/^\d{4}-\d\d-\d\dT\d\d:\d\d$/', $v)) {
        json_out(['error' => "$label: format tanggal tidak valid"], 422);
    }
    $stmt->execute(["$sec.$k", $v]);
}
json_out(['ok' => true]);
