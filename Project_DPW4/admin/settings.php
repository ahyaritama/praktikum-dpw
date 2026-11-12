<?php
require __DIR__ . '/layout.php';
require_admin();

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    if (($_POST['action'] ?? '') === 'wa') {
        db()->prepare('REPLACE INTO content (k, v) VALUES (?, ?)')
            ->execute(['set.wa_template', mb_substr(trim($_POST['wa'] ?? ''), 0, 1000)]);
        $msg = 'Template pesan disimpan.';
    } else {
        $s = db()->prepare('SELECT * FROM admins WHERE id = ?');
        $s->execute([$_SESSION['admin']]);
        $a = $s->fetch();
        $email = trim($_POST['email'] ?? '');
        $new = $_POST['new_password'] ?? '';
        if (!password_verify($_POST['current'] ?? '', $a['password_hash'])) {
            $msg = 'Password saat ini salah.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $msg = 'Email tidak valid.';
        } elseif ($new !== '' && strlen($new) < 8) {
            $msg = 'Password baru minimal 8 karakter.';
        } else {
            $hash = $new !== '' ? password_hash($new, PASSWORD_DEFAULT) : $a['password_hash'];
            db()->prepare('UPDATE admins SET email=?, password_hash=? WHERE id=?')->execute([$email, $hash, $a['id']]);
            $_SESSION['admin_email'] = $email;
            $msg = 'Akun diperbarui.';
        }
    }
}
$tpl = db()->query("SELECT v FROM content WHERE k = 'set.wa_template'")->fetchColumn() ?: WA_DEFAULT;

admin_head('Pengaturan', 'settings');
?>
<div class="page-head"><div><h1>Pengaturan</h1><p>Akun admin dan pesan share undangan.</p></div></div>
<?php if ($msg): ?><p class="notice"><?= e($msg) ?></p><?php endif; ?>
<div class="two-col">
  <form class="card" method="post">
    <h2>Akun Admin</h2>
    <input type="hidden" name="csrf" value="<?= e(csrf()) ?>">
    <label class="field-block"><span>Email</span><input type="email" name="email" value="<?= e($_SESSION['admin_email']) ?>" required></label>
    <label class="field-block"><span>Password Saat Ini</span><input type="password" name="current" required></label>
    <label class="field-block"><span>Password Baru (kosongkan jika tidak diganti)</span><input type="password" name="new_password" minlength="8"></label>
    <button class="btn">Simpan</button>
  </form>
  <form class="card" method="post">
    <h2>Pesan Share WhatsApp</h2>
    <input type="hidden" name="csrf" value="<?= e(csrf()) ?>"><input type="hidden" name="action" value="wa">
    <label class="field-block"><span>Gunakan {nama}, {pasangan}, {link}</span><textarea name="wa" rows="7"><?= e($tpl) ?></textarea></label>
    <button class="btn">Simpan</button>
  </form>
</div>
<?php admin_foot(); ?>
