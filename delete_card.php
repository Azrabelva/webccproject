<?php
require_once 'config.php';

/* ================= AUTH ================= */
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user']['id'];
$id = $_GET['id'] ?? null;

if (!$id) {
    die("ID kartu tidak valid");
}

/* ================= CEK KEPEMILIKAN ================= */
$stmt = $conn->prepare("SELECT * FROM cards WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$card = $stmt->get_result()->fetch_assoc();

if (!$card) {
    die("Kartu tidak ditemukan");
}

if ($card['user_id'] != $userId) {
    die("Akses ditolak");
}

/* ================= HAPUS FILE FOTO ================= */
$photos = ['photo1','photo2','photo3'];
foreach ($photos as $p) {
    if (!empty($card[$p])) {
        $file = __DIR__ . '/' . $card[$p];
        if (file_exists($file)) {
            unlink($file);
        }
    }
}

/* ================= DELETE DB ================= */
$stmt = $conn->prepare("DELETE FROM cards WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();

/* ================= REDIRECT ================= */
header("Location: user_home.php?msg=deleted");
exit;
