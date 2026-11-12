<?php
require __DIR__ . '/../config.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_out(['error' => 'POST only'], 405);

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$status = $_POST['status'] ?? '';
$pax = (int)($_POST['pax'] ?? 1);
if ($name === '' || mb_strlen($name) > 100 || !in_array($status, ['hadir', 'tidak_hadir'], true)) {
    json_out(['error' => 'Data tidak lengkap'], 422);
}
$pax = max(1, min(20, $pax));
$email = mb_substr($email, 0, 100);
$phone = mb_substr($phone, 0, 30);

$s = db()->prepare('SELECT id FROM guests WHERE code = ?');
$s->execute([$_POST['code'] ?? '']);
$g = $s->fetch();
if ($g) {
    db()->prepare('UPDATE guests SET name=?, email=?, phone=?, pax=?, status=?, confirmed_at=NOW() WHERE id=?')
        ->execute([$name, $email, $phone, $pax, $status, $g['id']]);
} else {
    db()->prepare('INSERT INTO guests (code,name,email,phone,pax,status,confirmed_at) VALUES (?,?,?,?,?,?,NOW())')
        ->execute([new_code(), $name, $email, $phone, $pax, $status]);
}
json_out(['ok' => true]);
