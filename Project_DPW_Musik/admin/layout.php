<?php
require __DIR__ . '/../config.php';

function admin_head(string $title, string $active): void {
    require_admin();
    $menu = [
        'index'    => ['Dashboard', 'index.php', '⌂'],
        'landing'  => ['Edit Landing Page', 'landing.php', '▣'],
        'guests'   => ['Data Tamu', 'guests.php', '☺'],
        'settings' => ['Pengaturan', 'settings.php', '⚙'],
    ];
    ?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($title) ?> - Dashboard</title>
<link href="https://fonts.googleapis.com/css2?family=Great+Vibes&display=swap" rel="stylesheet">
<link rel="stylesheet" href="css/admin.css">
</head>
<body>
<header class="topbar">
  <a class="brand" href="index.php"><?= e(c('cover.couple')) ?></a>
  <div class="user">
    <span class="avatar">A</span>
    <span><b>Admin</b><small><?= e($_SESSION['admin_email'] ?? '') ?></small></span>
  </div>
</header>
<div class="shell">
  <nav class="side">
    <?php foreach ($menu as $key => [$label, $href, $icon]): ?>
      <a href="<?= $href ?>" class="<?= $key === $active ? 'on' : '' ?>"><i><?= $icon ?></i><?= e($label) ?></a>
    <?php endforeach; ?>
    <a href="logout.php" class="logout"><i>↪</i>Keluar</a>
  </nav>
  <main class="main">
<?php
}

function admin_foot(string $script = ''): void {
    echo "  </main>\n</div>\n<script>window.CSRF = " . json_encode(csrf()) . ";</script>\n";
    echo '<script src="js/admin.js"></script>' . "\n";
    echo "</body>\n</html>\n";
}
