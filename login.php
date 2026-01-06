<?php
session_start();
require 'config.php';

$error = null;

// ================= REDIRECT JIKA SUDAH LOGIN =================
if (isset($_SESSION['is_admin'])) {
    header("Location: admin_list.php");
    exit;
}

if (isset($_SESSION['user'])) {
    header("Location: user_home.php");
    exit;
}

// ================= PROSES LOGIN =================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // ===== ADMIN LOGIN =====
    if ($username === $ADMIN_USER && $password === $ADMIN_PASS) {
        $_SESSION['is_admin'] = true;
        header("Location: admin_list.php");
        exit;
    }

    // ===== USER LOGIN =====
    $stmt = $conn->prepare("SELECT id, fullname, password, premium FROM users WHERE username=? LIMIT 1");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = [
            'id' => $user['id'],
            'fullname' => $user['fullname'],
            'premium' => $user['premium']
        ];
        header("Location: user_home.php");
        exit;
    }

    $error = "Username atau password salah 😢";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Login - LoveCrafted</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700;800&display=swap" rel="stylesheet">

<style>
*{box-sizing:border-box}
body{
    margin:0;
    min-height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    font-family:'Poppins',sans-serif;
    background:linear-gradient(135deg,#fde2f3,#fbcfe8,#e0e7ff);
}

/* CARD */
.login-card{
    width:380px;
    background:#fff;
    border-radius:26px;
    padding:34px 30px;
    box-shadow:0 25px 60px rgba(236,72,153,.35);
    animation:fadeUp .7s ease;
}

/* TITLE */
.login-title{
    font-size:26px;
    font-weight:800;
    text-align:center;
    color:#be185d;
}
.login-sub{
    font-size:13px;
    text-align:center;
    color:#6b7280;
    margin-bottom:22px;
}

/* INPUT */
.input-group{
    display:flex;
    align-items:center;
    gap:10px;
    border:1px solid #f3bfd8;
    padding:12px 14px;
    border-radius:14px;
    margin-bottom:14px;
}
.input-group span{font-size:18px}
.input-group input{
    border:none;
    outline:none;
    width:100%;
    font-size:14px;
}

/* BUTTON */
.btn-login{
    width:100%;
    padding:14px;
    margin-top:8px;
    border:none;
    border-radius:16px;
    background:linear-gradient(135deg,#ec4899,#db2777);
    color:#fff;
    font-weight:700;
    font-size:15px;
    cursor:pointer;
    transition:.3s;
}
.btn-login:hover{
    transform:translateY(-2px);
    box-shadow:0 12px 25px rgba(236,72,153,.45);
}

/* ERROR */
.login-error{
    background:#fee2e2;
    color:#991b1b;
    padding:10px;
    border-radius:12px;
    font-size:13px;
    text-align:center;
    margin-bottom:12px;
}

/* FOOTER */
.login-footer{
    text-align:center;
    margin-top:18px;
    font-size:13px;
    color:#6b7280;
}
.login-footer a{
    color:#ec4899;
    font-weight:600;
    text-decoration:none;
}
.google-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 12px;

  width: 100%;
  max-width: 360px;
  margin: 14px auto 0;

  padding: 12px 18px;
  border-radius: 999px;

  background: #ffffff;
  color: #374151;
  text-decoration: none;
  font-weight: 600;
  font-size: 14px;

  border: 1px solid #e5e7eb;
  box-shadow: 0 8px 20px rgba(0,0,0,.08);

  transition: all .25s ease;
}

.google-btn img {
  width: 18px;
  height: 18px;
}

.google-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 12px 28px rgba(0,0,0,.12);
}

.google-btn:active {
  transform: scale(.97);
}


/* ANIM */
@keyframes fadeUp{
    from{opacity:0;transform:translateY(20px)}
    to{opacity:1;transform:translateY(0)}
}
</style>
</head>

<body>

<div class="login-card">

    <div class="login-title">LoveCrafted 💖</div>
    <div class="login-sub">Login User & Admin</div>

    <?php if($error): ?>
        <div class="login-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST">

        <div class="input-group">
            <span>👤</span>
            <input type="text" name="username" placeholder="Username" required>
        </div>

        <div class="input-group">
            <span>🔒</span>
            <input type="password" name="password" placeholder="Password" required>
        </div>

        <button class="btn-login">Masuk</button>
    </form>
    <a href="login_google.php" class="google-btn">
  <img src="https://developers.google.com/identity/images/g-logo.png" alt="Google">
  <span>Login with Google</span>
</a>


    <div class="login-footer">
        Belum punya akun?
        <a href="user_register.php">Daftar di sini</a><br><br>
        <small>Admin default: <b>admin</b></small>
    </div>

</div>

</body>
</html>
