<?php
require_once 'config.php';

/* ================= AUTH ================= */
if (!isset($_SESSION['user'])) {
  header("Location: login.php");
  exit;
}

$user = $_SESSION['user'];

/* ================= LOAD PREMIUM STATUS ================= */
$stmt = $conn->prepare("SELECT premium FROM users WHERE id = ?");
$stmt->bind_param("i", $user['id']);
$stmt->execute();
$res = $stmt->get_result()->fetch_assoc();

$user['premium'] = $res['premium'] ?? 0;
$_SESSION['user']['premium'] = $user['premium'];
$isPremium = !empty($user['premium']);

/* ================= LOAD TEMPLATES ================= */
$templates = [];
$q = $conn->query("
  SELECT id, template_key, title, image, is_premium
  FROM templates
  ORDER BY created_at DESC
");
while ($row = $q->fetch_assoc()) {
  $templates[] = $row;
}

/* ================= LOAD USER CARDS ================= */
$stmt = $conn->prepare("
  SELECT 
    c.id,
    c.template_type,
    c.receiver_name,
    c.payment_status,
    t.is_premium
  FROM cards c
  JOIN templates t 
    ON t.template_key = c.template_type
  WHERE c.user_id = ?
  ORDER BY c.created_at DESC
");
$stmt->bind_param("i", $user['id']);
$stmt->execute();
$userCards = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>User Dashboard - LoveCrafted</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700;800&display=swap" rel="stylesheet">

<style>
body{margin:0;font-family:'Poppins',sans-serif;background:#f9fafb}
.lc-header{background:#ec4899;box-shadow:0 8px 20px rgba(0,0,0,.12)}
.header-flex{display:flex;justify-content:space-between;align-items:center;padding:18px 0}
.logo-text{color:#fff;font-weight:800;font-size:22px}
.user-nav{color:#fff;font-size:14px}
.user-nav a{color:#fff;text-decoration:none;margin-left:16px;font-weight:600}
.lc-container{width:90%;max-width:1400px;margin:0 auto}
main{padding:36px 0}

/* ===== STATS ===== */
.user-stats{display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:24px}
.stat-box{padding:28px;border-radius:22px;color:#fff;box-shadow:0 18px 45px rgba(0,0,0,.15)}
.stat-box h4{margin:0;font-size:15px;opacity:.9}
.stat-box p{font-size:30px;font-weight:800;margin-top:12px}
.pink{background:linear-gradient(135deg,#ec4899,#db2777)}
.green{background:linear-gradient(135deg,#22c55e,#16a34a)}
.purple{background:linear-gradient(135deg,#8b5cf6,#6366f1)}

/* ===== CARD ===== */
.card{background:#fff;border-radius:30px;padding:28px;box-shadow:0 25px 60px rgba(0,0,0,.08)}
.template-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:24px}
.template-card{border-radius:22px;overflow:hidden;box-shadow:0 14px 35px rgba(0,0,0,.12)}
.template-card img{width:100%;height:190px;object-fit:cover}
.template-info{padding:16px}
.template-action{text-align:center;padding:16px}

/* ===== BADGE ===== */
.badge{padding:6px 14px;border-radius:999px;font-size:11px;font-weight:700}
.badge-free{background:#22c55e;color:#fff}
.badge-premium{background:linear-gradient(135deg,#facc15,#f59e0b);color:#7c2d12}

/* ===== BUTTON ===== */
.btn-use{background:#ec4899;color:#fff;padding:12px 22px;border-radius:999px;text-decoration:none;font-weight:700}

/* ===== TABLE ===== */
.table{width:100%;border-collapse:collapse;margin-top:16px}
.table th,.table td{padding:14px;border-bottom:1px solid #eee}
.text-left{text-align:left}

/* ===== ACTION ICON ===== */
.action a{margin-right:10px;text-decoration:none;font-weight:600;color:#ec4899}
.icon-btn{
  display:inline-flex;
  align-items:center;
  justify-content:center;
  width:34px;
  height:34px;
  border-radius:10px;
  background:#f3f4f6;
  font-size:16px;
  transition:.2s ease
}
.icon-btn:hover{
  background:#ec4899;
  color:#fff;
  transform:scale(1.05)
}
</style>
</head>

<body>

<header class="lc-header">
  <div class="lc-container header-flex">
    <div class="logo-text">LoveCrafted</div>
    <div class="user-nav">
      Halo, <b><?= htmlspecialchars($user['fullname']) ?></b>
      <a href="logout.php">Logout</a>
    </div>
  </div>
</header>

<main class="lc-container">

<!-- ===== STATS ===== -->
<section class="user-stats">
  <div class="stat-box pink">
    <h4>Total Kartu</h4>
    <p><?= count($userCards) ?></p>
  </div>
  <div class="stat-box green">
    <h4>Status Akun</h4>
    <p><?= $isPremium ? 'PREMIUM' : 'FREE' ?></p>
  </div>
  <div class="stat-box purple">
    <h4>Template</h4>
    <p><?= $isPremium ? 'Semua' : 'Gratis' ?></p>
  </div>
</section>

<!-- ===== TEMPLATE ===== -->
<section class="card" style="margin-top:36px">
  <h2>Template Greeting Cards</h2>
  <div class="template-grid" style="margin-top:24px">
    <?php foreach ($templates as $t): ?>
      <div class="template-card">
        <img src="<?= htmlspecialchars($t['image']) ?>">
        <div class="template-info">
          <span class="badge <?= $t['is_premium'] ? 'badge-premium' : 'badge-free' ?>">
            <?= $t['is_premium'] ? 'PREMIUM' : 'FREE' ?>
          </span>
          <h4><?= htmlspecialchars($t['title']) ?></h4>
        </div>
        <div class="template-action">
          <?php if ($t['is_premium'] && !$isPremium): ?>
            <a href="upgrade.php" class="btn-use">Upgrade</a>
          <?php else: ?>
            <a class="btn-use"
               href="create_free_card.php?type=<?= urlencode($t['template_key']) ?>">
              Gunakan
            </a>
          <?php endif; ?>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</section>

<!-- ===== USER CARDS ===== -->
<section class="card" style="margin-top:36px">
<h2>Kartu yang Telah Dibuat</h2>

<?php if (empty($userCards)): ?>
  <p>Belum ada kartu dibuat.</p>
<?php else: ?>
<div class="table-wrapper">
<table class="table">
<thead>
<tr>
  <th class="text-left">ID</th>
  <th class="text-left">Template</th>
  <th class="text-left">To</th>
  <th class="text-left">Aksi</th>
</tr>
</thead>
<tbody>

<?php foreach ($userCards as $c): ?>
<tr>
  <td>#<?= $c['id'] ?></td>
  <td><?= strtoupper($c['template_type']) ?></td>
  <td><?= htmlspecialchars($c['receiver_name']) ?></td>
  
  <td class="action">

    <!-- PREVIEW -->
    <a href="view.php?id=<?= $c['id'] ?>" class="icon-btn" title="Preview">👁️</a>

    <!-- EDIT -->
    <a href="edit_card.php?id=<?= $c['id'] ?>" class="icon-btn" title="Edit">✏️</a>

    <!-- DELETE -->
    <a href="delete_card.php?id=<?= $c['id'] ?>"
       class="icon-btn"
       title="Hapus"
       onclick="return confirm('Yakin mau hapus kartu ini?')">
       🗑️
    </a>

  </td>
</tr>
<?php endforeach; ?>

</tbody>
</table>
</div>
<?php endif; ?>
</section>

</main>
</body>
</html>
