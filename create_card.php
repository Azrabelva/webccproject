<?php
require 'config.php';
require_admin();

// Debug (boleh dimatiin nanti)
ini_set('display_errors', 1);
error_reporting(E_ALL);

$DEFAULT_PRICE = 12000;
$err = null;
$success = null;

// =============================
//  PROCESS CREATE CARD
// =============================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $type = $_POST['card_type'] ?? 'birthday';
    $to   = trim($_POST['to_name'] ?? '');
    $from = trim($_POST['from_name'] ?? '');
    $msg  = trim($_POST['message'] ?? '');
    $sp   = trim($_POST['spotify_url'] ?? '');
    $yt   = trim($_POST['youtube_url'] ?? '');

    if ($to === '' || $from === '' || $msg === '') {
        $err = 'To, From, dan Pesan wajib diisi.';
    } else {

        // Generate ID unik
        do {
            $id = generate_public_id();
            $ex = load_card($id);
        } while ($ex);

        $data = [
            'id'        => $id,
            'type'      => $type,
            'to'        => $to,
            'from'      => $from,
            'message'   => $msg,
            'spotify'   => $sp ?: null,
            'youtube'   => $yt ?: null,
            'created'   => date('Y-m-d H:i:s'),
            'price'     => $DEFAULT_PRICE,
            'payment_status' => 'unpaid',
            'midtrans_order_id' => null,
            'midtrans_transaction_id' => null,
            'paid_at' => null
        ];

        if (save_card($data)) {
            $success = $BASE_URL . '/view.php?id=' . urlencode($id);
        } else {
            $err = 'Gagal menyimpan kartu.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Buat Kartu Baru - LoveCrafted</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="admin.css">
</head>

<body class="page-bg">

<!-- ================= HEADER ================= -->
<header class="lc-header">
    <div class="lc-container header-flex">
        <div class="logo-text">LoveCrafted <span>Admin</span></div>
        <nav>
            <a href="admin_list.php">Dashboard</a>
            <a href="create_card.php">+ Kartu Baru</a>
            <a href="logout.php">Logout</a>
        </nav>
    </div>
</header>

<!-- ================= CONTENT ================= -->
<main class="lc-container">

    <div class="card" style="max-width:720px;margin:auto;">

        <h2>✨ Buat Kartu Baru</h2>
        <p class="hint" style="margin-bottom:20px;">
            Isi detail greeting card yang akan dikirim ke customer.
        </p>

        <!-- ERROR -->
        <?php if ($err): ?>
            <div class="alert alert-error">
                <?= htmlspecialchars($err) ?>
            </div>
        <?php endif; ?>

        <!-- SUCCESS -->
        <?php if ($success): ?>
            <div class="alert alert-success">
                <b>🎉 Kartu berhasil dibuat!</b><br><br>
                Link untuk customer:<br>
                <a href="<?= $success ?>" target="_blank"><?= $success ?></a>
            </div>
        <?php endif; ?>

        <!-- ================= FORM ================= -->
        <form method="POST" class="form-vertical">

            <label>Jenis Kartu</label>
            <select name="card_type">
                <option value="birthday">🎂 Happy Birthday</option>
                <option value="anniversary">💍 Happy Anniversary</option>
                <option value="mother">💐 Mother’s Day</option>
                <option value="father">👔 Father’s Day</option>
                <option value="eid">🌙 Happy Eid</option>
                <option value="christmas">🎄 Merry Christmas</option>
            </select>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                <div>
                    <label>Untuk (To)</label>
                    <input type="text" name="to_name" placeholder="Nama penerima" required>
                </div>

                <div>
                    <label>Dari (From)</label>
                    <input type="text" name="from_name" placeholder="Nama pengirim" required>
                </div>
            </div>

            <label>Pesan Greeting Card</label>
            <textarea name="message" rows="5"
                placeholder="Tulis pesan manis untuk kartu ini 💖" required></textarea>

            <label>Spotify Embed URL <span class="hint">(opsional)</span></label>
            <input type="text" name="spotify_url" placeholder="https://open.spotify.com/embed/...">

            <label>YouTube Embed URL <span class="hint">(opsional)</span></label>
            <input type="text" name="youtube_url" placeholder="https://www.youtube.com/embed/...">

            <div style="margin-top:10px;">
                <span class="badge badge-premium">Harga Default</span>
                <b style="margin-left:8px;">Rp 12.000</b>
            </div>

            <button class="btn-primary" style="margin-top:20px;">
                💾 Simpan Kartu
            </button>

        </form>

    </div>

</main>

<!-- ================= FOOTER ================= -->
<footer class="lc-footer">
    <div class="lc-container">
        <small>LoveCrafted &copy; 2025</small>
    </div>
</footer>

</body>
</html>
