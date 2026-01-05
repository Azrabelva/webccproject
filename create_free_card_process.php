<?php
session_start();
require_once 'config.php';

/* ================= CEK LOGIN ================= */
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user']['id'];

/* ================= AMBIL DATA FORM ================= */
$type    = $_POST['type'] ?? null;
$to      = trim($_POST['to'] ?? '');
$from    = trim($_POST['from'] ?? '');
$message = trim($_POST['message'] ?? '');

/* === MUSIC / SPOTIFY LINK === */
$spotify_link = !empty($_POST['spotify_link'])
    ? trim($_POST['spotify_link'])
    : null;

$extra_text = isset($_POST['extra_text'])
    ? json_encode($_POST['extra_text'])
    : null;

$extra_title = isset($_POST['extra_title'])
    ? json_encode($_POST['extra_title'])
    : null;

/* ================= VALIDASI ================= */
if (!$type || !$to || !$from || !$message) {
    die("Data tidak lengkap");
}

/* ================= UPLOAD FOTO (images[] → photo1-3) ================= */
$photo1 = $photo2 = $photo3 = null;

if (!empty($_FILES['images']['name'][0])) {

    $allowed = ['jpg','jpeg','png','webp'];
    $dir = 'uploads/cards/';

    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }

    for ($i = 0; $i < min(3, count($_FILES['images']['name'])); $i++) {

        if ($_FILES['images']['error'][$i] !== 0) continue;

        $ext = strtolower(pathinfo($_FILES['images']['name'][$i], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed)) continue;

        if ($_FILES['images']['size'][$i] > 2 * 1024 * 1024) continue;

        $filename = 'photo'.($i+1).'_'.time().'_'.rand(1000,9999).'.'.$ext;
        $path = $dir.$filename;

        if (move_uploaded_file($_FILES['images']['tmp_name'][$i], $path)) {
            if ($i === 0) $photo1 = $path;
            if ($i === 1) $photo2 = $path;
            if ($i === 2) $photo3 = $path;
        }
    }
}

/* ================= INSERT KE DATABASE ================= */
$stmt = $conn->prepare("
    INSERT INTO cards 
    (user_id, template_type, receiver_name, sender_name, main_message, 
     extra_title, extra_text, photo1, photo2, photo3, spotify_link)
    VALUES (?,?,?,?,?,?,?,?,?,?,?)
");

$stmt->bind_param(
    "issssssssss",
    $user_id,
    $type,
    $to,
    $from,
    $message,
    $extra_title,
    $extra_text,
    $photo1,
    $photo2,
    $photo3,
    $spotify_link
);

$stmt->execute();

/* ================= AMBIL ID ================= */
$card_id = $stmt->insert_id;

/* ================= REDIRECT ================= */
header("Location: view.php?id=".$card_id);
exit;
