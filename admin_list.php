<?php
require 'config.php';
require_admin();

/* ================= LOAD CARD ================= */
$files = glob($CARD_DIR . '/*.json');
$cards = [];

foreach ($files as $f) {
    $d = json_decode(file_get_contents($f), true);
    if (!$d || empty($d['id'])) continue;
    $cards[] = $d;
}

usort($cards, function ($a, $b) {
    return strcmp($b['created'] ?? '', $a['created'] ?? '');
});

/* ================= ADMIN STAT ================= */
$totalCards   = count($cards);
$totalPaid    = 0;
$totalUnpaid  = 0;
$totalRevenue = 0;

foreach ($cards as $c) {
    if (($c['payment_status'] ?? '') === 'paid') {
        $totalPaid++;
        $totalRevenue += (int)($c['price'] ?? 0);
    } else {
        $totalUnpaid++;
    }
}

/* ================= TEMPLATE LIST ================= */
$templates = [];
$q = $conn->query("SELECT * FROM templates ORDER BY created_at DESC");
while ($row = $q->fetch_assoc()) {
    $templates[] = $row;
}

?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin - LoveCrafted</title>

    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="admin.css">

    <style>
        /* ===== CONTAINER 90% CENTER ===== */
        .lc-container {
            width: 90%;
            max-width: 1400px;
            margin: 0 auto;
        }

        /* HEADER */
        .lc-header {
            background: #ec4899;
            /* pink LoveCrafted */
            box-shadow: 0 8px 20px rgba(0, 0, 0, .12);
        }

        .header-flex {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 18px 0;
        }

        /* Text & link */
        .logo-text {
            color: #fff;
            font-weight: 800;
        }

        .logo-text span {
            opacity: .9;
        }

        .admin-nav a {
            color: #fff;
            text-decoration: none;
            margin-left: 16px;
            font-weight: 600;
        }

        .admin-nav a:hover {
            text-decoration: underline;
        }

        /* MAIN */
        main.lc-container {
            padding: 32px 0;
        }

        /* TEMPLATE GRID */
        .admin-template-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            gap: 20px;
        }

        /* TEMPLATE CARD */
        .admin-template-card {
            background: #fff;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0, 0, 0, .08);
            display: flex;
            flex-direction: column;
        }

        .admin-template-card img {
            width: 100%;
            height: 180px;
            object-fit: cover;
        }

        .admin-template-info {
            padding: 14px;
        }

        .admin-template-action {
            padding: 14px;
            border-top: 1px solid #eee;
            text-align: center;
        }

        .btn-edit {
            display: inline-block;
            padding: 8px 14px;
            border-radius: 10px;
            background: #ec4899;
            color: #fff;
            font-weight: 600;
            text-decoration: none;
        }

        .btn-edit:hover {
            background: #be185d;
        }

        .btn-add {
            background: #fff;
            color: #ec4899 !important;
            padding: 6px 12px;
            border-radius: 10px;
        }

        .btn-logout {
            background: #be185d;
            color: #fff !important;
            padding: 6px 12px;
            border-radius: 10px;
        }

        .icon-action {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .icon-action a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 34px;
            height: 34px;
            border-radius: 10px;
            background: #f3f4f6;
            text-decoration: none;
            font-size: 16px;
            transition: .2s ease;
        }

        .icon-action a:hover {
            background: #ec4899;
            color: #fff;
            transform: scale(1.05);
        }
    </style>
</head>

<body>

    <header class="lc-header">
        <div class="lc-container header-flex">
            <div class="logo-text">
                LoveCrafted <span>Admin</span>
            </div>
            <nav class="admin-nav">
                <a href="admin_list.php">📋 List Kartu</a>
                <a href="admin_template_add.php" class="btn-add">➕ Tambah Template</a>

                <a href="logout.php" class="btn-logout">Logout</a>
            </nav>
        </div>
    </header>

    <main class="lc-container">

        <!-- ================= ADMIN STATS ================= -->
        <section class="admin-stats">
            <div class="stat-box pink">
                <h4>Total Kartu</h4>
                <p><?= $totalCards ?></p>
            </div>

            <div class="stat-box green">
                <h4>Kartu PAID</h4>
                <p><?= $totalPaid ?></p>
            </div>

            <div class="stat-box red">
                <h4>Kartu UNPAID</h4>
                <p><?= $totalUnpaid ?></p>
            </div>

            <div class="stat-box purple">
                <h4>Total Pendapatan</h4>
                <p>Rp <?= number_format($totalRevenue, 0, ',', '.') ?></p>
            </div>
        </section>

        <!-- ================= TEMPLATE GREETING ================= -->
        <section class="card" style="margin-top:32px;">
            <div style="display:flex;justify-content:space-between;align-items:center;">
                <h2>Template Greeting Cards</h2>
                <small style="opacity:.6;">(Kelola desain template)</small>
            </div>

            <div class="admin-template-grid" style="margin-top:20px;">
    <?php foreach ($templates as $t): ?>
        <div class="admin-template-card">

            <img src="<?= htmlspecialchars($t['image']) ?>" alt="<?= htmlspecialchars($t['title']) ?>">

            <div class="admin-template-info">
                <h4><?= htmlspecialchars($t['title']) ?></h4>

                <span class="badge <?= $t['is_premium'] ? 'badge-premium' : 'badge-free' ?>">
                    <?= $t['is_premium'] ? 'PREMIUM' : 'FREE' ?>
                </span>

                <div class="template-id">
                    ID: <b><?= htmlspecialchars($t['template_key']) ?></b>
                </div>
            </div>

            <div class="admin-template-action">
                <a href="admin_template_edit.php?id=<?= htmlspecialchars($t['id']) ?>" class="btn-edit">
                    ✏️ Edit Template
                </a>
            </div>

        </div>
    <?php endforeach; ?>
</div>
        </section>

        <!-- ================= DAFTAR KARTU ================= -->
        <section class="card" style="margin-top:32px;">
            <h2>Daftar Kartu User</h2>

            <?php if (empty($cards)): ?>
                <p class="empty">Belum ada kartu.</p>
            <?php else: ?>
                <div class="table-wrapper">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Jenis</th>
                                <th>To</th>
                                <th>From</th>
                                <th>Tanggal</th>
                        
                            
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cards as $c):
                                $id      = htmlspecialchars($c['id']);
                                $type    = htmlspecialchars($c['type']);
                                $to      = htmlspecialchars($c['to']);
                                $from    = htmlspecialchars($c['from']);
                                $created = htmlspecialchars($c['created'] ?? '');
                                $price   = (int)($c['price'] ?? 0);
                                $status  = $c['payment_status'] ?? 'unpaid';
                                $label   = $status === 'paid' ? 'PAID' : 'UNPAID';
                                $cls     = $status === 'paid' ? 'badge-paid' : 'badge-unpaid';
                                $cardUrl = $BASE_URL . '/view.php?id=' . urlencode($id);
                                $payUrl  = $BASE_URL . '/payment.php?id=' . urlencode($id);
                            ?>
                                <tr>
                                    <td><?= $id ?></td>
                                    <td><?= $type ?></td>
                                    <td><?= $to ?></td>
                                    <td><?= $from ?></td>
                                    <td><?= $created ?></td>
                                    
                                    <td class="aksi icon-action">
                                        <a href="<?= $cardUrl ?>" target="_blank" title="View">
                                            👁️
                                        </a>


                                        <a href="admin_delete.php?id=<?= $id ?>"
                                            title="Delete"
                                            onclick="return confirm('Hapus kartu ini?')">
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

    <footer class="lc-footer">
        <div class="lc-container">
            <small>LoveCrafted &copy; 2025</small>
        </div>
    </footer>

</body>

</html>
