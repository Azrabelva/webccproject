<?php
require "config.php"; // session_start sudah ada di sini
header("Content-Type: application/json");

/*
  === DUMMY MODE ===
  Kalau card_id ada → update card + user
  Kalau card_id TIDAK ada → langsung upgrade user login
*/

// AMBIL USER LOGIN
if (!isset($_SESSION['user'])) {
  echo json_encode(['success' => false]);
  exit;
}

$userId = $_SESSION['user']['id'];
$cardId = $_POST['card_id'] ?? null;

/* === JIKA ADA CARD ID (OPSIONAL) === */
if ($cardId) {

  // ambil user dari card
  $stmt = $conn->prepare("
    SELECT user_id
    FROM cards
    WHERE id = ?
  ");
  $stmt->bind_param("i", $cardId);
  $stmt->execute();
  $data = $stmt->get_result()->fetch_assoc();

  if ($data) {
    $userId = $data['user_id'];

    // set card jadi paid (dummy)
    $stmt = $conn->prepare("
      UPDATE cards 
      SET payment_status = 'paid' 
      WHERE id = ?
    ");
    $stmt->bind_param("i", $cardId);
    $stmt->execute();
  }
}

/* === DUMMY: AKTIFKAN PREMIUM USER === */
$stmt = $conn->prepare("
  UPDATE users 
  SET premium = 1 
  WHERE id = ?
");
$stmt->bind_param("i", $userId);
$stmt->execute();

/* === UPDATE SESSION BIAR LANGSUNG KEDETECT === */
$_SESSION['user']['premium'] = 1;

echo json_encode(['success' => true]);
exit;
