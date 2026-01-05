<?php
session_start();
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Daftar Akun - LoveCrafted</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700;800&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">

<style>

/* ================= PAGE BACKGROUND ================= */
body{
    margin:0;
    min-height:100vh;
    display:flex;
    align-items:center;
    justify-content:center;
    font-family:'Poppins',sans-serif;
    background:
        radial-gradient(circle at 10% 20%, rgba(236,72,153,.35), transparent 45%),
        radial-gradient(circle at 90% 80%, rgba(168,85,247,.35), transparent 45%),
        linear-gradient(135deg,#fde2f3,#fbcfe8,#e0e7ff);
    position:relative;
    overflow:hidden;
}

/* Watermark */
body::before{
    content:"LoveCrafted";
    position:absolute;
    top:50%;
    left:50%;
    transform:translate(-50%,-50%) rotate(-10deg);
    font-size:160px;
    font-weight:800;
    color:rgba(236,72,153,.08);
    letter-spacing:10px;
    pointer-events:none;
    white-space:nowrap;
}

/* Floating decorations */
body::after{
    content:"💌 🎀 💖 🎂 ✨ 💐";
    position:absolute;
    bottom:40px;
    right:60px;
    font-size:44px;
    opacity:.35;
    animation:floatDecor 6s ease-in-out infinite;
    pointer-events:none;
}

@keyframes floatDecor{
    0%{ transform:translateY(0); }
    50%{ transform:translateY(-16px); }
    100%{ transform:translateY(0); }
}

/* ================= REGISTER CARD ================= */
.register-card{
    width:100%;
    max-width:540px;
    background:rgba(255,255,255,.96);
    border-radius:34px;
    padding:48px 46px 50px;
    box-shadow:0 40px 90px rgba(236,72,153,.35);
    position:relative;
    z-index:1;
    animation:fadeUp .8s ease;
    overflow:hidden; /* 🔒 pengaman input */
}

/* Gradient border */
.register-card::before{
    content:"";
    position:absolute;
    inset:-3px;
    border-radius:36px;
    background:linear-gradient(135deg,#ec4899,#a855f7,#60a5fa);
    z-index:-1;
}

/* ================= TITLE ================= */
.register-title{
    text-align:center;
    font-family:'Playfair Display',serif;
    font-size:34px;
    font-weight:700;
    color:hsl(335, 100%, 81%);
}

.register-sub{
    text-align:center;
    font-size:15px;
    color:#ffffff;
    margin-bottom:32px;
}

/* ================= INPUT ================= */
.form-group{
    margin-bottom:20px;
    position:relative;
}

.form-group label{
    font-size:13px;
    font-weight:600;
    color:#7a003f;
    display:block;
    margin-bottom:6px;
}

.form-group span{
    position:absolute;
    left:18px;
    top:52%;
    transform:translateY(-50%);
    font-size:20px;
    opacity:.6;
}

.form-group input{
    width:100%;
    box-sizing:border-box;               /* 🔒 WAJIB */
    padding:16px 18px 16px 54px;
    border-radius:18px;
    border:1.6px solid #fbcfe8;
    font-size:15px;
    background:#fff;
    transition:.25s;
}

.form-group input:focus{
    outline:none;
    border-color:#ec4899;
    box-shadow:0 0 0 4px rgba(236,72,153,.18);
}

/* ================= BUTTON ================= */
.btn-register{
    width:100%;
    padding:16px;
    border-radius:999px;
    background:linear-gradient(135deg,#ec4899,#db2777);
    color:#fff;
    border:none;
    font-size:17px;
    font-weight:800;
    cursor:pointer;
    margin-top:10px;
    transition:.25s;
}

.btn-register:hover{
    transform:translateY(-3px);
    box-shadow:0 18px 40px rgba(236,72,153,.55);
}

.btn-register:active{
    transform:scale(.96);
}

/* ================= FOOTER ================= */
.register-footer{
    text-align:center;
    margin-top:22px;
    font-size:14px;
}

.register-footer a{
    color:#ec4899;
    font-weight:700;
    text-decoration:none;
}

.register-footer a:hover{
    text-decoration:underline;
}

/* ================= ANIMATION ================= */
@keyframes fadeUp{
    from{opacity:0; transform:translateY(40px);}
    to{opacity:1; transform:translateY(0);}
}

</style>
</head>

<body>

<div class="register-card">

    <div class="register-title">Daftar Akun</div>
    <div class="register-sub">Buat akun untuk membuat greeting card spesial✨</div>

    <form method="POST" action="user_register_process.php">

        <div class="form-group">
            <label>Nama Lengkap</label>
            <span>👤</span>
            <input type="text" name="fullname" required>
        </div>

        <div class="form-group">
            <label>No WhatsApp</label>
            <span>📱</span>
            <input type="text" name="whatsapp" required>
        </div>

        <div class="form-group">
            <label>Username</label>
            <span>🧸</span>
            <input type="text" name="username" required>
        </div>

        <div class="form-group">
            <label>Password</label>
            <span>🔒</span>
            <input type="password" name="password" required>
        </div>

        <button class="btn-register">Daftar Sekarang</button>

    </form>

    <div class="register-footer">
        Sudah punya akun? <a href="login.php">Login</a>
    </div>

</div>

</body>
</html>
