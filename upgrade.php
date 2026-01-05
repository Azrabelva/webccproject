<?php
require 'config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Upgrade Premium - LoveCrafted</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

<!-- MIDTRANS SNAP (AUTO: SANDBOX / PRODUCTION) -->
<script src="<?= $MIDTRANS_SNAP_URL ?>"
        data-client-key="<?= $MIDTRANS_CLIENT_KEY ?>"></script>

<style>
body{
    margin:0;
    min-height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    font-family:'Poppins',sans-serif;
    background:
        radial-gradient(circle at top left, rgba(236,72,153,.35), transparent 45%),
        radial-gradient(circle at bottom right, rgba(168,85,247,.35), transparent 45%),
        linear-gradient(135deg,#fde2f3,#fbcfe8,#e0e7ff);
}
.upgrade-card{
    max-width:460px;
    background:#fff;
    border-radius:32px;
    padding:42px 36px;
    box-shadow:0 40px 80px rgba(236,72,153,.35);
    text-align:center;
}
.upgrade-title{
    font-size:28px;
    font-weight:800;
    color:#be185d;
}
.price-badge{
    background:#ffe4f1;
    padding:12px 20px;
    border-radius:999px;
    font-size:20px;
    font-weight:800;
    color:#ec4899;
    margin:20px 0;
}
.feature-box{
    text-align:left;
    background:#fff5fb;
    padding:20px;
    border-radius:20px;
    margin-bottom:26px;
}
.feature-box li{
    font-size:14px;
    margin-bottom:10px;
}
.btn-upgrade{
    display:block;
    width:100%;
    padding:16px;
    border-radius:999px;
    background:linear-gradient(135deg,#ec4899,#d92672);
    color:#fff;
    font-weight:800;
    border:none;
    cursor:pointer;
}
.btn-back{
    display:block;
    margin-top:14px;
    font-size:13px;
    color:#6b7280;
    text-decoration:none;
}
.loading{
    opacity:.6;
    pointer-events:none;
}
</style>
</head>

<body>

<div class="upgrade-card">
    <div class="upgrade-title">Upgrade Premium 💎</div>
    <div class="price-badge">Rp 25.000</div>

    <ul class="feature-box">
        <li>✨ Semua template premium</li>
        <li>✨ Tanpa watermark</li>
        <li>✨ Musik & animasi</li>
        <li>✨ Unlimited edit</li>
    </ul>

    <button id="btnPay" class="btn-upgrade">
        Upgrade Sekarang
    </button>

    <a href="user_home.php" class="btn-back">Nanti dulu</a>
</div>

<script>
document.getElementById('btnPay').addEventListener('click', function () {

    const btn = this;
    btn.classList.add('loading');
    btn.innerText = 'Memproses...';

    fetch('payment.php')
        .then(res => res.json())
        .then(data => {

            if (data.status !== 'success') {
                alert(data.message || 'Gagal memulai pembayaran');
                btn.classList.remove('loading');
                btn.innerText = 'Upgrade Sekarang';
                return;
            }

            snap.pay(data.token, {
                onSuccess: function () {
                    alert("🎉 Pembayaran berhasil!");
                    window.location.href = "user_home.php";
                },
                onPending: function () {
                    alert("⏳ Menunggu pembayaran diselesaikan");
                    btn.classList.remove('loading');
                    btn.innerText = 'Upgrade Sekarang';
                },
                onError: function () {
                    alert("❌ Pembayaran gagal");
                    btn.classList.remove('loading');
                    btn.innerText = 'Upgrade Sekarang';
                },
                onClose: function () {
                    btn.classList.remove('loading');
                    btn.innerText = 'Upgrade Sekarang';
                }
            });

        })
        .catch(() => {
            alert("⚠️ Gagal menghubungi server");
            btn.classList.remove('loading');
            btn.innerText = 'Upgrade Sekarang';
        });
});
</script>

</body>
</html>
