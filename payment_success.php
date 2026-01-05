<?php
session_start();
require 'config.php';

$id = $_GET['id'] ?? null;
$orderId = $_GET['order_id'] ?? null;

// Update status pembayaran & premium
if ($id && $orderId) {
    $card = load_card($id);
    if ($card) {
        $card['payment_status'] = 'paid';
        $card['order_id'] = $orderId;
        $card['premium_until'] = date('Y-m-d', strtotime('+1 month'));
        save_card($card);
    }
}

$userName = $_SESSION['user']['fullname'] ?? 'Acha';
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Pembayaran Berhasil! - LoveCrafted</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;800&display=swap" rel="stylesheet">

<style>
body{
    margin:0;
    font-family:'Poppins', sans-serif;
    background:
        radial-gradient(circle at top left, rgba(236,72,153,.4), transparent 45%),
        radial-gradient(circle at bottom right, rgba(168,85,247,.4), transparent 45%),
        linear-gradient(135deg,#fde2f3,#ffccdd,#f8bbd0);
    height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    overflow:hidden;
    text-align:center;
}

/* CARD */
.card{
    background:rgba(255,255,255,.96);
    width:440px;
    padding:38px 34px;
    border-radius:30px;
    box-shadow:0 35px 80px rgba(236,72,153,.45);
    position:relative;
    animation: pop .6s ease;
}

/* GLOW BORDER */
.card::before{
    content:"";
    position:absolute;
    inset:-3px;
    border-radius:32px;
    background:linear-gradient(135deg,#ec4899,#a855f7,#6366f1);
    z-index:-1;
    filter:blur(12px);
    opacity:.55;
}

.logo{
    width:110px;
    margin-bottom:10px;
    animation:bounce 2.5s infinite;
}

h1{
    font-size:30px;
    color:#ec4899;
    font-weight:800;
}

p{
    font-size:14px;
    color:#6b7280;
    margin-top:6px;
}

.badge{
    display:inline-block;
    margin:14px 0;
    padding:8px 18px;
    border-radius:999px;
    background:linear-gradient(135deg,#facc15,#f59e0b);
    color:#7c2d12;
    font-weight:800;
    font-size:13px;
}

.btn{
    margin-top:18px;
    display:block;
    background:linear-gradient(135deg,#ec4899,#db2777);
    padding:14px;
    border-radius:999px;
    color:#fff;
    font-weight:800;
    text-decoration:none;
    transition:.25s;
}

.btn.secondary{
    background:linear-gradient(135deg,#a855f7,#6366f1);
}

.btn:hover{
    transform:translateY(-4px);
    box-shadow:0 18px 40px rgba(236,72,153,.55);
}

.small{
    margin-top:10px;
    font-size:12px;
    opacity:.6;
}

/* CONFETTI */
.confetti{
    position:absolute;
    width:12px;
    height:12px;
    top:-20px;
    border-radius:50%;
    animation: fall 3.5s linear infinite;
}

@keyframes fall{
    to{ transform: translateY(110vh) rotate(360deg); }
}

@keyframes pop{
    from{opacity:0; transform:scale(.85);}
    to{opacity:1; transform:scale(1);}
}

@keyframes bounce{
    0%,100%{transform:translateY(0);}
    50%{transform:translateY(-6px);}
}
</style>
</head>
<body>

<div class="card">
    <img src="assets/lovecrafted.png" class="logo">

    <h1>🎉 Pembayaran Berhasil!</h1>
    <div class="badge">PREMIUM ACTIVE</div>

    <p>Yeay <b><?= htmlspecialchars($userName) ?></b>!  
    Kamu sekarang resmi jadi <b>Premium Member LoveCrafted</b> 💖</p>

    <p>Semua fitur premium sudah terbuka ✨</p>

    <a href="receipt_premium.php?id=<?= urlencode($id) ?>&order_id=<?= urlencode($orderId) ?>" class="btn secondary">
        Lihat Invoice 🧾
    </a>

    <a href="user_home.php" class="btn">Masuk Dashboard</a>

    <div class="small">Dialihkan otomatis dalam 6 detik…</div>
</div>

<script>
/* CONFETTI */
for(let i=0;i<45;i++){
    let c=document.createElement("div");
    c.className="confetti";
    c.style.left=Math.random()*100+"vw";
    c.style.background=["#ec4899","#f472b6","#a855f7","#facc15"][Math.floor(Math.random()*4)];
    c.style.animationDelay=Math.random()*2+"s";
    document.body.appendChild(c);
}

/* AUTO REDIRECT */
setTimeout(()=>{
    window.location.href="user_home.php";
},6000);
</script>

</body>
</html>
