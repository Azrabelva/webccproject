<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/config.php';

/* ================= VALIDASI INPUT ================= */
if (
    empty($_POST['fullname']) ||
    empty($_POST['whatsapp']) ||
    empty($_POST['username']) ||
    empty($_POST['password'])
) {
    header("Location: user_register.php?error=1");
    exit;
}

$fullname = trim($_POST['fullname']);
$whatsapp = trim($_POST['whatsapp']);
$username = strtolower(trim($_POST['username']));
$password = $_POST['password'];

/* ================= CEK DUPLIKASI USERNAME ================= */
$stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    header("Location: user_register.php?error=exists");
    exit;
}
$stmt->close();

/* ================= SIMPAN KE DATABASE ================= */
$hash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare("
    INSERT INTO users (fullname, username, password, premium)
    VALUES (?, ?, ?, 0)
");
$stmt->bind_param("sss", $fullname, $username, $hash);

if (!$stmt->execute()) {
    die("Gagal menyimpan user ke database");
}

$stmt->close(); 

/* ================= REDIRECT SUKSES ================= */
header("Location: login.php");
exit;
