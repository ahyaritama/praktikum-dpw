<?php
require __DIR__ . '/../config.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_out(['error' => 'POST only'], 405);

$name = trim($_POST['name'] ?? '');
$msg = trim($_POST['message'] ?? '');
if ($name === '' || $msg === '' || mb_strlen($name) > 100 || mb_strlen($msg) > 300) {
    json_out(['error' => 'Nama dan ucapan wajib diisi (maks 300 huruf)'], 422);
}
$s = db()->prepare('SELECT id FROM guests WHERE code = ?');
$s->execute([$_POST['code'] ?? '']);
$g = $s->fetch();
db()->prepare('INSERT INTO wishes (guest_id, name, message) VALUES (?,?,?)')
    ->execute([$g['id'] ?? null, $name, $msg]);
json_out(['ok' => true]);
