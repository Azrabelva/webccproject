<?php session_start(); ?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Pembayaran Pending - LoveCrafted</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;800&display=swap" rel="stylesheet">

<style>
body{
    margin:0;
    font-family:'Poppins',sans-serif;
    background:
        radial-gradient(circle at top left, rgba(236,72,153,.35), transparent 45%),
        radial-gradient(circle at bottom right, rgba(168,85,247,.35), transparent 45%),
        linear-gradient(135deg,#fde2f3,#ffdeec,#f8c9d8);
    height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
}

/* CARD */
.box{
    background:rgba(255,255,255,.96);
    width:400px;
    padding:34px 30px;
    border-radius:30px;
    text-align:center;
    box-shadow:0 30px 70px rgba(236,72,153,.4);
    position:relative;
    animation:fadeUp .6s ease;
}

.box::before{
    content:"";
    position:absolute;
    inset:-3px;
    border-radius:32px;
    background:linear-gradient(135deg,#ec4899,#a855f7,#6366f1);
    z-index:-1;
    opacity:.45;
}

/* LOADER */
.loading{
    width:68px;
    height:68px;
    border:6px solid #fbcfe8;
    border-top-color:#ec4899;
    border-radius:50%;
    margin:0 auto 18px;
    animation:spin 1s linear infinite;
}

h2{
    color:#d92672;
    font-weight:800;
}

p{
    font-size:14px;
    color:#6b7280;
}

.status{
    margin-top:12px;
    font-size:13px;
    background:#fff0f7;
    padding:10px;
    border-radius:14px;
}

.btn{
    margin-top:20px;
    display:block;
    padding:12px;
    background:linear-gradient(135deg,#ec4899,#db2777);
    color:#fff;
    border-radius:999px;
    text-decoration:none;
    font-weight:800;
}

@keyframes spin{
    to{transform:rotate(360deg);}
}

@keyframes fadeUp{
    from{opacity:0; transform:translateY(24px);}
    to{opacity:1; transform:translateY(0);}
}
</style>
</head>
<body>

<div class="box">
    <div class="loading"></div>

    <h2>Menunggu Pembayaran 💗</h2>
    <p>Transaksimu masih diproses oleh Midtrans.</p>

    <div class="status">
        📱 Silakan selesaikan pembayaran QRIS / e-wallet kamu ya ✨
    </div>

    <a href="user_home.php" class="btn">Kembali ke Dashboard</a>
</div>

</body>
</html>
