<?php
require __DIR__ . '/layout.php';
require_admin();

$tab = isset($FIELDS[$_GET['tab'] ?? '']) ? $_GET['tab'] : 'cover';
admin_head('Edit Landing Page', 'landing');
?>
<div class="page-head"><div><h1>Edit Landing Page</h1><p>Kelola konten undangan pernikahan untuk satu acara.</p></div>
  <a class="btn" href="../index.php" target="_blank">Lihat Preview ↗</a></div>

<div class="tabs">
  <?php foreach ($FIELDS as $sec => $s): ?>
    <button type="button" class="tab <?= $sec === $tab ? 'on' : '' ?>" data-tab="<?= $sec ?>" data-anchor="<?= $s['anchor'] ?>"><?= e($s['label']) ?></button>
  <?php endforeach; ?>
</div>

<div class="two-col">
  <div class="card">
    <?php foreach ($FIELDS as $sec => $s): ?>
      <form class="panel save-form" data-tab="<?= $sec ?>" <?= $sec === $tab ? '' : 'hidden' ?>>
        <h2>Konten <?= e($s['label']) ?></h2>
        <input type="hidden" name="csrf" value="<?= e(csrf()) ?>">
        <input type="hidden" name="section" value="<?= $sec ?>">
        <?php foreach ($s['fields'] as $k => [$label, $type]): $val = c("$sec.$k"); ?>
          <label class="field-block"><span><?= e($label) ?></span>
            <?php if ($type === 'textarea'): ?>
              <textarea name="<?= $k ?>" rows="3" maxlength="1500"><?= e($val) ?></textarea>
            <?php elseif ($type === 'image'): ?>
              <div class="img-row">
                <img class="thumb" src="../<?= e($val) ?>" alt="" onerror="this.style.visibility='hidden'">
                <input type="hidden" name="<?= $k ?>" value="<?= e($val) ?>">
                <input type="file" accept="image/jpeg,image/png,image/webp" class="upload">
              </div>
            <?php elseif ($type === 'audio'): ?>
              <div class="img-row">
                <audio class="audio-preview" src="../<?= e($val) ?>" controls preload="metadata" style="max-width:220px"></audio>
                <input type="hidden" name="<?= $k ?>" value="<?= e($val) ?>">
                <input type="file" accept="audio/mpeg,.mp3" class="upload">
              </div>
            <?php elseif ($type === 'datetime'): ?>
              <input type="datetime-local" name="<?= $k ?>" value="<?= e($val) ?>">
            <?php else: ?>
              <input type="text" name="<?= $k ?>" value="<?= e($val) ?>" maxlength="500">
            <?php endif; ?>
          </label>
        <?php endforeach; ?>
        <button class="btn" type="submit">Simpan</button> <span class="saved"></span>
      </form>
    <?php endforeach; ?>
  </div>
  <div class="card">
    <h2>Preview</h2>
    <iframe id="preview" class="preview" src="../index.php?preview=1#<?= $FIELDS[$tab]['anchor'] ?>"></iframe>
  </div>
</div>
<?php admin_foot(); ?>
