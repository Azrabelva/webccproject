<?php
require 'config.php';
require_admin();
require_once __DIR__ . '/templates_db.php';

$templates = templates_load();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Kelola Template - LoveCrafted</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="admin.css">
</head>
<body class="page-bg">

<header class="lc-header">
  <div class="lc-container header-flex">
    <div class="logo-text">Kelola Template</div>
    <nav>
      <a href="admin_list.php">Dashboard</a>
      <a href="admin_template_edit.php">+ Tambah Template</a>
      <a href="logout.php">Logout</a>
    </nav>
  </div>
</header>

<main class="lc-container">
  <div class="card">
    <h2>Template Greeting Cards</h2>

    <div class="admin-template-grid">
      <?php foreach ($templates as $t): 
        $id = htmlspecialchars($t['id']);
        $title = htmlspecialchars($t['title']);
        $img = htmlspecialchars($t['image']);
        $isPrem = !empty($t['premium']);
      ?>
        <div class="admin-template-card">
          <img src="<?= $img ?>" alt="<?= $title ?>">
          <div class="admin-template-info">
            <span class="badge <?= $isPrem ? 'badge-premium' : 'badge-free' ?>">
              <?= $isPrem ? 'PREMIUM' : 'FREE' ?>
            </span>
            <h4 style="margin-top:10px;"><?= $title ?></h4>
            <div style="font-size:12px;opacity:.75;">ID: <b><?= $id ?></b></div>
          </div>

          <div class="admin-template-action">
            <a class="btn-edit" href="admin_template_edit.php?id=<?= $id ?>">✏️ Edit</a>
            &nbsp;
            <a class="btn-edit" style="color:#b91c1c;background:#fee2e2;"
               href="admin_template_delete.php?id=<?= $id ?>"
               onclick="return confirm('Hapus template ini?')">🗑 Delete</a>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

  </div>
</main>

<footer class="lc-footer">
  <div class="lc-container"><small>LoveCrafted &copy; 2025</small></div>
</footer>

</body>
</html>
