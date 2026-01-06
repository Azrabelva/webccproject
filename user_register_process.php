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
$stmt->execute([$username]);

if ($stmt->fetch()) {
    header("Location: user_register.php?error=exists");
    exit;
}

/* ================= SIMPAN KE DATABASE ================= */
$hash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare("
    INSERT INTO users (fullname, username, password, premium)
    VALUES (?, ?, ?, 0)
");

if (!$stmt->execute([$fullname, $username, $hash])) {
    die("Gagal menyimpan user ke database");
}

/* ================= REDIRECT SUKSES ================= */
header("Location: login.php");
exit;
