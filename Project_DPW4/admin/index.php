<?php
require __DIR__ . '/layout.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    db()->prepare('DELETE FROM wishes WHERE id = ?')->execute([(int)($_POST['delete_wish'] ?? 0)]);
    header('Location: index.php');
    exit;
}

$st = db()->query("SELECT COUNT(*) total,
    COALESCE(SUM(status='hadir'),0) hadir,
    COALESCE(SUM(status='tidak_hadir'),0) tidak,
    COALESCE(SUM(status='menunggu'),0) tunggu,
    COALESCE(SUM(CASE WHEN status='hadir' THEN pax END),0) pax FROM guests")->fetch();
$wishes = db()->query('SELECT w.*, g.code FROM wishes w LEFT JOIN guests g ON g.id = w.guest_id ORDER BY w.id DESC')->fetchAll();

admin_head('Dashboard', 'index');
?>
<div class="page-head"><div><h1>Dashboard</h1><p>Ringkasan konfirmasi kehadiran dan ucapan tamu.</p></div>
  <a class="btn" href="../index.php" target="_blank">Lihat Undangan ↗</a></div>

<div class="stats">
  <div class="card stat"><small>Total Tamu</small><b><?= (int)$st['total'] ?></b></div>
  <div class="card stat"><small>Hadir</small><b><?= (int)$st['hadir'] ?></b><em><?= (int)$st['pax'] ?> orang</em></div>
  <div class="card stat"><small>Tidak Hadir</small><b><?= (int)$st['tidak'] ?></b></div>
  <div class="card stat"><small>Menunggu</small><b><?= (int)$st['tunggu'] ?></b></div>
  <div class="card stat"><small>Ucapan</small><b><?= count($wishes) ?></b></div>
</div>

<div class="card">
  <h2>Riwayat Ucapan</h2>
  <?php if (!$wishes): ?><p class="muted">Belum ada ucapan.</p><?php endif; ?>
  <?php foreach ($wishes as $w): ?>
    <div class="wish">
      <span class="avatar"><?= e(mb_strtoupper(mb_substr($w['name'], 0, 1))) ?></span>
      <div class="grow"><b><?= e($w['name']) ?></b> <small><?= e(date('d M Y, H:i', strtotime($w['created_at']))) ?></small>
        <p><?= e($w['message']) ?></p></div>
      <form method="post" onsubmit="return confirm('Hapus ucapan ini?')">
        <input type="hidden" name="csrf" value="<?= e(csrf()) ?>">
        <button class="icon danger" name="delete_wish" value="<?= (int)$w['id'] ?>" title="Hapus">🗑</button>
      </form>
    </div>
  <?php endforeach; ?>
</div>
<?php admin_foot(); ?>
