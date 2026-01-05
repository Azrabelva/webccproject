<?php session_start(); ?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Pembayaran Gagal - LoveCrafted</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;800&display=swap" rel="stylesheet">

<style>
body{
    margin:0;
    font-family:'Poppins',sans-serif;
    background:
        radial-gradient(circle at top left, rgba(236,72,153,.35), transparent 45%),
        radial-gradient(circle at bottom right, rgba(168,85,247,.35), transparent 45%),
        linear-gradient(135deg,#ffe2ea,#ffd6e5,#fbcfe8);
    height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    overflow:hidden;
}

/* CARD */
.box{
    background:rgba(255,255,255,.96);
    width:420px;
    padding:36px 34px;
    border-radius:30px;
    text-align:center;
    box-shadow:0 30px 70px rgba(236,72,153,.45);
    position:relative;
    animation:pop .6s ease;
}

/* GLOW BORDER */
.box::before{
    content:"";
    position:absolute;
    inset:-3px;
    border-radius:32px;
    background:linear-gradient(135deg,#ec4899,#a855f7,#6366f1);
    z-index:-1;
    filter:blur(14px);
    opacity:.45;
}

/* EMOJI */
.emoji{
    font-size:68px;
    animation:shake 1.2s infinite;
    margin-bottom:6px;
}

/* TEXT */
h2{
    color:#d92672;
    font-size:26px;
    font-weight:800;
}

p{
    font-size:14px;
    color:#6b7280;
    margin-top:10px;
}

/* STATUS BOX */
.alert{
    margin:18px 0;
    background:#fff0f7;
    padding:14px;
    border-radius:18px;
    font-size:13px;
    box-shadow:inset 0 0 10px rgba(236,72,153,.15);
}

/* BUTTON */
.btn{
    margin-top:16px;
    display:block;
    padding:14px;
    background:linear-gradient(135deg,#ec4899,#db2777);
    color:#fff;
    border-radius:999px;
    text-decoration:none;
    font-weight:800;
    transition:.25s;
}

.btn:hover{
    transform:translateY(-4px);
    box-shadow:0 18px 40px rgba(236,72,153,.55);
}

/* BACK BTN */
.btn-secondary{
    display:block;
    margin-top:10px;
    font-size:13px;
    color:#6b7280;
    text-decoration:none;
}

/* ANIMATION */
@keyframes shake{
    0%,100%{ transform:translateX(0); }
    25%{ transform:translateX(-6px); }
    75%{ transform:translateX(6px); }
}

@keyframes pop{
    from{opacity:0; transform:scale(.85);}
    to{opacity:1; transform:scale(1);}
}
</style>
</head>

<body>

<div class="box">
    <div class="emoji">😭</div>

    <h2>Pembayaran Gagal</h2>

    <p>Aduh <b>Acha</b>…  
    pembayaran kamu belum berhasil diproses 💔</p>

    <div class="alert">
        💡 Bisa jadi karena koneksi, saldo, atau QRIS-nya ke-skip.<br>
        Tenang, kamu bisa coba lagi kok ✨
    </div>

    <a href="payment.php?id=<?= htmlspecialchars($_GET['id'] ?? '') ?>" class="btn">
        Coba Bayar Lagi
    </a>

    <a href="user_home.php" class="btn-secondary">
        Balik ke Dashboard
    </a>
</div>

</body>
</html>
