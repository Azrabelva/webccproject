<?php
session_start();

/* DATA SIMULASI (NANTI BISA DIAMBIL DARI DB) */
$purchaseDate = date('d M Y');
$expiryDate   = date('d M Y', strtotime('+1 month'));
$price        = "Rp 15.000";
$orderId      = "LC-" . rand(100000,999999);
$userName     = $_SESSION['user']['fullname'] ?? 'LoveCrafted User';
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Invoice Premium - LoveCrafted</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;800&display=swap" rel="stylesheet">

<style>
body{
    margin:0;
    font-family:'Poppins',sans-serif;
    background:linear-gradient(135deg,#fde2f3,#fbcfe8,#e0e7ff);
    min-height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
}

/* RECEIPT */
.receipt{
    width:420px;
    background:#fff;
    border-radius:32px;
    padding:34px 30px;
    box-shadow:0 40px 90px rgba(236,72,153,.35);
    position:relative;
    animation:pop .6s ease;
}

/* GRADIENT BORDER */
.receipt::before{
    content:"";
    position:absolute;
    inset:-3px;
    border-radius:34px;
    background:linear-gradient(135deg,#ec4899,#a855f7,#6366f1);
    z-index:-1;
}

/* LOGO */
.logo{
    display:flex;
    justify-content:center;
    margin-bottom:16px;
}
.logo img{
    height:70px;
}

/* TITLE */
h2{
    text-align:center;
    color:#db2777;
    font-weight:800;
    margin-bottom:6px;
}
.sub{
    text-align:center;
    font-size:13px;
    color:#6b7280;
    margin-bottom:22px;
}

/* INFO */
.info{
    font-size:14px;
    margin-bottom:18px;
}
.row{
    display:flex;
    justify-content:space-between;
    margin-bottom:10px;
}
.label{color:#6b7280;}
.value{font-weight:600;}

/* LINE */
.hr{
    border-top:2px dashed #fbcfe8;
    margin:18px 0;
}

/* PRODUCT */
.product{
    background:#fff5fb;
    padding:16px;
    border-radius:20px;
    margin-bottom:16px;
}
.product h4{
    margin:0 0 6px;
    color:#be185d;
}
.product p{
    font-size:13px;
    color:#6b7280;
    margin:0;
}

/* TOTAL */
.total{
    display:flex;
    justify-content:space-between;
    font-size:18px;
    font-weight:800;
    color:#ec4899;
    margin-top:10px;
}

/* SWEET TALK */
.thanks{
    margin-top:18px;
    font-size:13px;
    text-align:center;
    color:#6b7280;
}

/* BUTTON */
.btn{
    margin-top:20px;
    display:block;
    text-align:center;
    padding:14px;
    border-radius:999px;
    background:linear-gradient(135deg,#ec4899,#db2777);
    color:#fff;
    font-weight:800;
    text-decoration:none;
    transition:.25s;
}
.btn:hover{
    transform:translateY(-3px);
    box-shadow:0 18px 40px rgba(236,72,153,.55);
}

/* ANIM */
@keyframes pop{
    from{opacity:0;transform:scale(.85);}
    to{opacity:1;transform:scale(1);}
}
</style>
</head>

<body>

<div class="receipt">

    <div class="logo">
        <img src="assets/lovecrafted.png" alt="LoveCrafted">
    </div>

    <h2>Premium Invoice 💎</h2>
    <div class="sub">Terima kasih sudah berlangganan, <?= htmlspecialchars($userName) ?> 💖</div>

    <div class="info">
        <div class="row">
            <div class="label">Invoice ID</div>
            <div class="value"><?= $orderId ?></div>
        </div>
        <div class="row">
            <div class="label">Tanggal Pembelian</div>
            <div class="value"><?= $purchaseDate ?></div>
        </div>
        <div class="row">
            <div class="label">Aktif Sampai</div>
            <div class="value"><?= $expiryDate ?></div>
        </div>
    </div>

    <div class="hr"></div>

    <div class="product">
        <h4>✨ Premium Membership (1 Bulan)</h4>
        <p>Akses semua template premium, tanpa watermark, musik Spotify & animasi spesial.</p>
    </div>

    <div class="total">
        <div>Total</div>
        <div><?= $price ?></div>
    </div>

    <div class="thanks">
        Makasih ya udah percaya sama <b>LoveCrafted</b> 🥺💗  
        Semoga kartu yang kamu buat bisa bikin orang tersayang senyum ✨
    </div>

    <a href="user_home.php" class="btn">Kembali ke Dashboard</a>

</div>

</body>
</html>
