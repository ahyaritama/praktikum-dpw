<?php
require __DIR__ . '/../config.php';
require_admin();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_out(['error' => 'POST only'], 405);
check_csrf();

$f = $_FILES['file'] ?? null;
if (!$f || $f['error'] !== UPLOAD_ERR_OK) json_out(['error' => 'Upload gagal'], 422);
if ($f['size'] > 8 * 1024 * 1024) json_out(['error' => 'Maksimal 8 MB'], 422);

$types = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
$mime = (new finfo(FILEINFO_MIME_TYPE))->file($f['tmp_name']);
if (!isset($types[$mime])) json_out(['error' => 'Hanya JPG, PNG, atau WebP'], 422);

$name = bin2hex(random_bytes(8)) . '.' . $types[$mime];
$dir = __DIR__ . '/../uploads';
if (!is_dir($dir)) mkdir($dir, 0755, true);
if (!move_uploaded_file($f['tmp_name'], "$dir/$name")) json_out(['error' => 'Gagal menyimpan file'], 500);
json_out(['path' => "uploads/$name"]);
