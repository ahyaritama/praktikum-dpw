<?php
session_start();
require __DIR__ . '/fields.php';

const DB_HOST = 'localhost';
const DB_NAME = 'undangan_db';
const DB_USER = 'root';
const DB_PASS = '';
const WA_DEFAULT = "Kepada Yth. {nama},\n\nDengan hormat kami mengundang Anda ke pernikahan {pasangan}. Buka undangan di sini:\n{link}";

function db(): PDO {
    static $pdo;
    return $pdo ??= new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4', DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
}

function e($s): string { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

// Semua nilai konten: default dari fields.php ditimpa isi tabel content
function content_all(): array {
    global $FIELDS;
    static $all;
    if ($all === null) {
        $all = [];
        foreach ($FIELDS as $sec => $s) foreach ($s['fields'] as $k => $f) $all["$sec.$k"] = $f[2];
        foreach (db()->query('SELECT k, v FROM content') as $r) $all[$r['k']] = $r['v'];
    }
    return $all;
}
function c(string $key): string { return content_all()[$key] ?? ''; }

// Path gambar relatif; kosong jika file tidak ada
function img(string $key): string {
    $p = c($key);
    return ($p !== '' && is_file(__DIR__ . '/' . $p)) ? $p : '';
}

function new_code(): string {
    $chars = 'abcdefghjkmnpqrstuvwxyz23456789';
    do {
        $code = '';
        for ($i = 0; $i < 8; $i++) $code .= $chars[random_int(0, strlen($chars) - 1)];
        $s = db()->prepare('SELECT 1 FROM guests WHERE code = ?');
        $s->execute([$code]);
    } while ($s->fetch());
    return $code;
}

function app_url(): string {
    $dir = preg_replace('#/(admin|api)$#', '', dirname($_SERVER['SCRIPT_NAME']));
    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    $path = implode('/', array_map('rawurlencode', explode('/', rtrim($dir, '/'))));
    return ($https ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $path;
}
function guest_link(string $code): string { return app_url() . '/index.php?id=' . $code; }

function csrf(): string { return $_SESSION['csrf'] ??= bin2hex(random_bytes(16)); }
function check_csrf(): void {
    $t = $_POST['csrf'] ?? '';
    if (!hash_equals($_SESSION['csrf'] ?? '', $t)) { http_response_code(403); exit('Token tidak valid'); }
}
function require_admin(): void {
    if (empty($_SESSION['admin'])) { header('Location: login.php'); exit; }
}
function json_out(array $d, int $code = 200): void {
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode($d);
    exit;
}
