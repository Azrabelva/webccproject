<?php
require_once 'config.php';

if (!isset($_SESSION['user'])) {
  die("Harus login");
}

$id = $_GET['id'] ?? null;
if (!$id)
  die("Card tidak ditemukan");

$stmt = $conn->prepare("
  SELECT 
    c.*,
    u.id AS user_id,
    t.is_premium
  FROM cards c
  JOIN users u ON u.id = c.user_id
  JOIN templates t ON t.template_key = c.template_type
  WHERE c.id = ?
");
$stmt->execute([$id]);
$card = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$card)
  die("Card tidak ditemukan");

/* 🚨 SECURITY */
if ($_SESSION['user']['id'] !== $card['user_id']) {
  die("Akses ditolak");
}

/* ================= ROUTING VIEW ================= */
if ((int) $card['is_premium'] === 1) {
  include 'view_premium.php';
} else {
  include 'view_free.php';
}
