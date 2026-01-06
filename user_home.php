<?php
require_once 'config.php';
// session_start & DB

/* ================= AUTH ================= */
if (!isset($_SESSION['user'])) {
  header("Location: login.php");
  exit;
}

$user = $_SESSION['user'];

/* ================= LOAD PREMIUM STATUS ================= */
$stmt = $conn->prepare("SELECT premium FROM users WHERE id = ?");
$stmt->execute([$user['id']]);
$res = $stmt->fetch(PDO::FETCH_ASSOC);

function getPremiumStatus($res)
{
  if (!$res)
    return 0;
  return is_array($res) ? ($res['premium'] ?? 0) : ($res->premium ?? 0);
}

$user['premium'] = $res ? $res['premium'] : 0;
$_SESSION['user']['premium'] = $user['premium'];

$isPremium = !empty($user['premium']);

/* ================= TEMPLATE LIST ================= */
/* ================= LOAD TEMPLATES FROM DB ================= */
$templates = [];

$q = $conn->query("
    SELECT 
        id,
        template_key,
        title,
        image,
        is_premium
    FROM templates
    ORDER BY created_at DESC
");

while ($row = $q->fetch(PDO::FETCH_ASSOC)) {
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
$stmt->execute([$user['id']]);
$userCards = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <title>User Dashboard - LoveCrafted</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700;800&display=swap" rel="stylesheet">
  <style>
    /* ==== STYLE ASLI TIDAK DIUBAH ==== */
    body {
      margin: 0;
      font-family: 'Poppins', sans-serif;
      background: #f9fafb
    }

    .lc-header {
      background: #ec4899;
      box-shadow: 0 8px 20px rgba(0, 0, 0, .12)
    }

    .header-flex {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 18px 0
    }

    .logo-text {
      color: #fff;
      font-weight: 800;
      font-size: 22px
    }

    .user-nav {
      color: #fff;
      font-size: 14px
    }

    .user-nav a {
      color: #fff;
      text-decoration: none;
      margin-left: 16px;
      font-weight: 600
    }

    .lc-container {
      width: 90%;
      max-width: 1400px;
      margin: 0 auto
    }

    main {
      padding: 36px 0
    }

    .user-stats {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
      gap: 24px
    }

    .stat-box {
      padding: 28px;
      border-radius: 22px;
      color: #fff;
      box-shadow: 0 18px 45px rgba(0, 0, 0, .15)
    }

    .stat-box h4 {
      margin: 0;
      font-size: 15px;
      opacity: .9
    }

    .stat-box p {
      font-size: 30px;
      font-weight: 800;
      margin-top: 12px
    }

    .pink {
      background: linear-gradient(135deg, #ec4899, #db2777)
    }

    .green {
      background: linear-gradient(135deg, #22c55e, #16a34a)
    }

    .purple {
      background: linear-gradient(135deg, #8b5cf6, #6366f1)
    }

    /* ==== UPGRADE POPUP MODERN ==== */
    .upgrade-overlay {
      position: fixed;
      inset: 0;
      background: rgba(0, 0, 0, .45);
      backdrop-filter: blur(6px);
      z-index: 9999;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .upgrade-card {
      background: #fff;
      border-radius: 28px;
      padding: 32px 28px;
      width: 90%;
      max-width: 380px;
      text-align: center;
      box-shadow: 0 30px 80px rgba(0, 0, 0, .3);
      animation: popupScale .3s ease;
    }

    @keyframes popupScale {
      from {
        transform: scale(.85);
        opacity: 0;
      }

      to {
        transform: scale(1);
        opacity: 1;
      }
    }

    .upgrade-card h2 {
      margin: 0;
      color: #ec4899;
      font-weight: 800;
    }

    .price-pill {
      margin: 18px auto;
      background: #fde2f0;
      color: #db2777;
      padding: 12px 22px;
      border-radius: 999px;
      font-weight: 800;
      font-size: 18px;
      width: fit-content;
    }

    .upgrade-list {
      list-style: none;
      padding: 0;
      margin: 20px 0;
      text-align: left;
    }

    .upgrade-list li {
      margin-bottom: 10px;
      font-size: 14px;
      color: #444;
    }

    .btn-upgrade-big {
      display: block;
      background: linear-gradient(135deg, #ec4899, #db2777);
      color: #fff;
      padding: 14px;
      border-radius: 999px;
      text-decoration: none;
      font-weight: 700;
      margin-top: 16px;
    }

    .btn-upgrade-big:hover {
      opacity: .9;
    }

    .btn-later {
      margin-top: 14px;
      background: none;
      border: none;
      color: #6b7280;
      font-weight: 600;
      cursor: pointer;
    }

    .card {
      background: #fff;
      border-radius: 30px;
      padding: 28px;
      box-shadow: 0 25px 60px rgba(0, 0, 0, .08)
    }

    .template-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
      gap: 24px
    }

    .template-card {
      border-radius: 22px;
      overflow: hidden;
      box-shadow: 0 14px 35px rgba(0, 0, 0, .12)
    }

    .template-card img {
      width: 100%;
      height: 190px;
      object-fit: cover
    }

    .template-info {
      padding: 16px
    }

    .badge {
      padding: 6px 14px;
      border-radius: 999px;
      font-size: 11px;
      font-weight: 700
    }

    .badge-free {
      background: #22c55e;
      color: #fff
    }

    .badge-premium {
      background: linear-gradient(135deg, #facc15, #f59e0b);
      color: #7c2d12
    }

    .template-action {
      text-align: center;
      padding: 16px
    }

    .btn-use {
      background: #ec4899;
      color: #fff;
      padding: 12px 22px;
      border-radius: 999px;
      text-decoration: none;
      font-weight: 700
    }

    .table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 16px;

    }

    .table th,
    .table td {
      padding: 14px;
      border-bottom: 1px solid #eee
    }

    .text-left {
      text-align: left !important;
    }


    .badge-paid,
    .badge-unpaid {
      background: #22c55e;
      color: #fff;
      padding: 6px 12px;
      border-radius: 999px;
      font-size: 12px
    }

    /* ==== PREMIUM MODAL ==== */
    .modal-overlay {
      position: fixed;
      inset: 0;
      background: rgba(0, 0, 0, .5);
      z-index: 999;
    }

    .modal-box {
      position: fixed;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      background: #fff;
      border-radius: 24px;
      padding: 28px;
      width: 90%;
      max-width: 420px;
      z-index: 1000;
      text-align: center;
      box-shadow: 0 30px 70px rgba(0, 0, 0, .25);
    }

    .modal-box h3 {
      margin-top: 0;
      color: #ec4899;
      font-weight: 800;
    }

    .modal-box p {
      font-size: 14px;
      color: #555;
    }

    .modal-actions {
      margin-top: 22px;
      display: flex;
      gap: 12px;
      justify-content: center;
    }

    .btn-upgrade {
      background: linear-gradient(135deg, #facc15, #f59e0b);
      color: #7c2d12;
      padding: 12px 20px;
      border-radius: 999px;
      text-decoration: none;
      font-weight: 700;
    }

    .btn-cancel {
      background: #e5e7eb;
      border: none;
      padding: 12px 20px;
      border-radius: 999px;
      font-weight: 600;
      cursor: pointer;
    }

    .badge-free {
      background: #22c55e;
      color: #fff;
      padding: 6px 14px;
      border-radius: 999px;
      font-size: 12px;
      font-weight: 700;
    }

    .badge-premium {
      background: linear-gradient(135deg, #facc15, #f59e0b);
      color: #7c2d12;
      padding: 6px 14px;
      border-radius: 999px;
      font-size: 12px;
      font-weight: 700;
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
    <!-- TEMPLATE -->
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

                <a href="upgrade.php" class="btn-upgrade-big">
                  Upgrade
                </a>

              <?php else: ?>

                <a class="btn-use" href="create_free_card.php?type=<?= urlencode($t['template_key']) ?>">
                  Gunakan
                </a>

              <?php endif; ?>
            </div>

          </div>
        <?php endforeach; ?>
      </div>
    </section>
    <!-- USER CARDS -->
    <section class="card" style="margin-top:36px">
      <h2>Kartu yang Telah Dibuat</h2> <?php if (empty($userCards)): ?>
        <p>Belum ada kartu dibuat.</p> <?php else: ?>
        <div class="table-wrapper">
          <table class="table">
            <thead>
              <tr>
                <th class="text-left">ID</th>
                <th class="text-left">Template</th>
                <th class="text-left">To</th>
                <th class="text-left">Jenis Kartu</th>
                <th class="text-left">Aksi</th>
              </tr>
            </thead>
            <tbody> <?php foreach ($userCards as $c): ?>
                <tr>
                  <td>#<?= $c['id'] ?></td>
                  <td><?= strtoupper($c['template_type']) ?></td>
                  <td><?= htmlspecialchars($c['receiver_name']) ?></td>
                  <td>
                    <?php if ($c['is_premium']): ?>
                      <span class="badge badge-premium">PREMIUM</span>
                    <?php else: ?>
                      <span class="badge badge-free">FREE</span>
                    <?php endif; ?>
                  </td>
                  <td class="action"> <a href="view.php?id=<?= $c['id'] ?>" title="View">Preview</a>
                    <!-- <?php if ($c['payment_status'] != 'paid'): ?> <a href="payment.php?id=<?= $c['id'] ?>" title="Pay">💳</a> <?php endif; ?> -->
                  </td>
                </tr> <?php endforeach; ?>
            </tbody>
          </table>
        </div> <?php endif; ?>
    </section>

  </main>
  <!-- PREMIUM POPUP -->
  <div id="upgradeModal" class="upgrade-overlay" style="display:none;">
    <div class="upgrade-card">
      <h2>Upgrade Premium 💎</h2>

      <div class="price-pill">Rp15.000</div>

      <ul class="upgrade-list">
        <li>✨ Semua template premium</li>
        <li>✨ Tanpa watermark</li>
        <li>✨ Musik & animasi</li>
        <li>✨ Unlimited edit</li>
      </ul>

      <a href="upgrade.php" class="btn-upgrade-big">Upgrade Sekarang</a>
      <button class="btn-later" onclick="closeUpgrade()">Nanti dulu</button>
    </div>
  </div>
  <div id="checkPaymentModal" style="display:none;">
    <div class="modal-overlay"></div>
    <div class="modal-box">
      <h3>Sudah Melakukan Pembayaran? 💳</h3>
      <p>
        Jika kamu sudah menyelesaikan pembayaran,<br>
        klik tombol di bawah untuk mengaktifkan Premium.
      </p>

      <div class="modal-actions">
        <button class="btn-upgrade" onclick="confirmPayment()">
          ✅ Sudah Bayar
        </button>
        <button class="btn-cancel" onclick="closeCheckPayment()">
          Nanti
        </button>
      </div>
    </div>
  </div>





</body>

</html>