<?php
require __DIR__ . '/layout.php';
require_admin();

$statuses = ['hadir' => 'Hadir', 'menunggu' => 'Menunggu', 'tidak_hadir' => 'Tidak Hadir'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $act = $_POST['action'] ?? '';
    if ($act === 'delete') {
        db()->prepare('DELETE FROM guests WHERE id = ?')->execute([(int)$_POST['id']]);
    } elseif ($act === 'save') {
        $name = mb_substr(trim($_POST['name'] ?? ''), 0, 100);
        $pax = max(1, min(20, (int)($_POST['pax'] ?? 1)));
        $status = array_key_exists($_POST['status'] ?? '', $statuses) ? $_POST['status'] : 'menunggu';
        $phone = mb_substr(trim($_POST['phone'] ?? ''), 0, 30);
        if ($name !== '') {
            if (!empty($_POST['id'])) {
                db()->prepare('UPDATE guests SET name=?, phone=?, pax=?, status=?,
                    confirmed_at = IF(? <> status AND ? <> "menunggu", NOW(), confirmed_at) WHERE id=?')
                    ->execute([$name, $phone, $pax, $status, $status, $status, (int)$_POST['id']]);
            } else {
                db()->prepare('INSERT INTO guests (code,name,phone,pax,status) VALUES (?,?,?,?,?)')
                    ->execute([new_code(), $name, $phone, $pax, $status]);
            }
        }
    }
    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'guests.php'));
    exit;
}

$q = trim($_GET['q'] ?? '');
$fs = array_key_exists($_GET['status'] ?? '', $statuses) ? $_GET['status'] : '';
$orders = ['new' => 'id DESC', 'name' => 'name ASC', 'pax' => 'pax DESC', 'date' => 'confirmed_at DESC'];
$sort = isset($orders[$_GET['sort'] ?? '']) ? $_GET['sort'] : 'new';

$where = 'WHERE 1'; $args = [];
if ($q !== '') { $where .= ' AND name LIKE ?'; $args[] = "%$q%"; }
if ($fs !== '') { $where .= ' AND status = ?'; $args[] = $fs; }

$per = 10;
$cnt = db()->prepare("SELECT COUNT(*) FROM guests $where");
$cnt->execute($args);
$total = (int)$cnt->fetchColumn();
$pages = max(1, (int)ceil($total / $per));
$page = max(1, min($pages, (int)($_GET['page'] ?? 1)));
$off = ($page - 1) * $per;
$rows = db()->prepare("SELECT * FROM guests $where ORDER BY {$orders[$sort]} LIMIT $per OFFSET $off");
$rows->execute($args);
$rows = $rows->fetchAll();

$tpl = db()->query("SELECT v FROM content WHERE k = 'set.wa_template'")->fetchColumn() ?: WA_DEFAULT;
$qs = fn(array $o) => '?' . http_build_query(array_merge(['q' => $q, 'status' => $fs, 'sort' => $sort], $o));

admin_head('Data Tamu', 'guests');
?>
<div class="page-head"><div><h1>Data Tamu</h1><p>Kelola daftar tamu undangan dan konfirmasi kehadiran.</p></div>
  <button class="btn" id="add-guest">+ Tambah Tamu</button></div>

<div class="card">
  <form class="filters" method="get">
    <input type="search" name="q" value="<?= e($q) ?>" placeholder="Cari nama tamu...">
    <select name="status" onchange="this.form.submit()">
      <option value="">Semua Status</option>
      <?php foreach ($statuses as $k => $l): ?><option value="<?= $k ?>" <?= $fs === $k ? 'selected' : '' ?>><?= $l ?></option><?php endforeach; ?>
    </select>
    <select name="sort" onchange="this.form.submit()">
      <?php foreach (['new' => 'Terbaru', 'name' => 'Nama A-Z', 'pax' => 'Jumlah Tamu', 'date' => 'Tgl Konfirmasi'] as $k => $l): ?>
        <option value="<?= $k ?>" <?= $sort === $k ? 'selected' : '' ?>>Urutkan: <?= $l ?></option>
      <?php endforeach; ?>
    </select>
  </form>

  <table>
    <thead><tr><th>No</th><th>Nama Tamu</th><th>Jumlah Tamu</th><th>Status</th><th>Tanggal Konfirmasi</th><th>Aksi</th></tr></thead>
    <tbody>
    <?php foreach ($rows as $i => $g): $link = guest_link($g['code']); ?>
      <tr>
        <td><?= $off + $i + 1 ?></td>
        <td><?= e($g['name']) ?></td>
        <td><?= (int)$g['pax'] ?></td>
        <td><span class="badge <?= $g['status'] ?>"><?= $statuses[$g['status']] ?></span></td>
        <td><?= $g['confirmed_at'] ? e(date('d M Y, H:i', strtotime($g['confirmed_at']))) : '-' ?></td>
        <td class="actions">
          <button class="icon share" title="Share link undangan"
            data-name="<?= e($g['name']) ?>" data-link="<?= e($link) ?>" data-phone="<?= e($g['phone']) ?>">🔗</button>
          <button class="icon edit" title="Edit"
            data-id="<?= (int)$g['id'] ?>" data-name="<?= e($g['name']) ?>" data-phone="<?= e($g['phone']) ?>"
            data-pax="<?= (int)$g['pax'] ?>" data-status="<?= $g['status'] ?>">✎</button>
          <form method="post" onsubmit="return confirm('Hapus tamu ini?')">
            <input type="hidden" name="csrf" value="<?= e(csrf()) ?>"><input type="hidden" name="action" value="delete">
            <button class="icon danger" name="id" value="<?= (int)$g['id'] ?>" title="Hapus">🗑</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
    <?php if (!$rows): ?><tr><td colspan="6" class="muted">Belum ada tamu.</td></tr><?php endif; ?>
    </tbody>
  </table>

  <div class="pager">
    <small>Menampilkan <?= $total ? $off + 1 : 0 ?>–<?= min($off + $per, $total) ?> dari <?= $total ?> data</small>
    <span><?php for ($p = 1; $p <= $pages; $p++): ?><a class="<?= $p === $page ? 'on' : '' ?>" href="<?= e($qs(['page' => $p])) ?>"><?= $p ?></a><?php endfor; ?></span>
  </div>
</div>

<dialog id="guest-dialog">
  <form method="post" class="dlg">
    <h2 id="gd-title">Tambah Tamu</h2>
    <input type="hidden" name="csrf" value="<?= e(csrf()) ?>"><input type="hidden" name="action" value="save">
    <input type="hidden" name="id" id="gd-id">
    <label class="field-block"><span>Nama Tamu</span><input name="name" id="gd-name" required maxlength="100"></label>
    <label class="field-block"><span>No. WhatsApp (opsional, mis. 628123456789)</span><input name="phone" id="gd-phone" maxlength="30"></label>
    <label class="field-block"><span>Jumlah Tamu</span><input type="number" name="pax" id="gd-pax" min="1" max="20" value="1"></label>
    <label class="field-block"><span>Status</span>
      <select name="status" id="gd-status"><?php foreach ($statuses as $k => $l): ?><option value="<?= $k ?>"><?= $l ?></option><?php endforeach; ?></select></label>
    <div class="dlg-actions"><button type="button" class="btn ghost" data-close>Batal</button><button class="btn">Simpan</button></div>
  </form>
</dialog>

<dialog id="share-dialog">
  <div class="dlg">
    <h2>Share Undangan</h2>
    <p class="muted" id="sd-name"></p>
    <input id="sd-link" readonly>
    <div class="dlg-actions">
      <button class="btn ghost" data-close>Tutup</button>
      <button class="btn" id="sd-copy">Salin Link</button>
      <a class="btn" id="sd-wa" target="_blank" rel="noopener">WhatsApp</a>
    </div>
  </div>
</dialog>
<script>window.WA_TEMPLATE = <?= json_encode($tpl) ?>; window.COUPLE = <?= json_encode(c('cover.couple')) ?>;</script>
<?php admin_foot(); ?>
