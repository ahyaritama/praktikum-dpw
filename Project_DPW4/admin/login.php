<?php
require __DIR__ . '/../config.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $s = db()->prepare('SELECT * FROM admins WHERE email = ?');
    $s->execute([trim($_POST['email'] ?? '')]);
    $a = $s->fetch();
    if ($a && password_verify($_POST['password'] ?? '', $a['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['admin'] = $a['id'];
        $_SESSION['admin_email'] = $a['email'];
        header('Location: index.php');
        exit;
    }
    $error = 'Email atau password salah.';
    usleep(500000);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Masuk - Dashboard Undangan</title>
<link rel="stylesheet" href="css/admin.css">
</head>
<body class="login-page">
  <form class="login-card" method="post">
    <h1>Selamat Datang</h1>
    <p class="sub">Masuk ke dashboard untuk mengelola undangan pernikahan Anda</p>
    <input type="hidden" name="csrf" value="<?= e(csrf()) ?>">
    <?php if ($error): ?><p class="err"><?= e($error) ?></p><?php endif; ?>
    <label class="field"><span>Email</span>
      <input type="email" name="email" placeholder="admin@gmail.com" required autofocus></label>
    <label class="field"><span>Password</span>
      <input type="password" name="password" id="pw" placeholder="••••••••" required>
      <button type="button" class="eye" onclick="pw.type = pw.type === 'password' ? 'text' : 'password'" aria-label="Tampilkan password">👁</button></label>
    <button class="btn wide" type="submit">Masuk</button>
    <p class="foot">© 2026 Wedding Invitation.<br>All rights reserved</p>
  </form>
</body>
</html>
