<?php
require_once 'config.php';
session_start();

/* ================= CEK LOGIN ADMIN ================= */
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
  header("Location: login.php");
  exit;
}

/* ================= VALIDASI ID ================= */
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
  die("ID tidak valid");
}

$id = (int) $_GET['id'];

/* ================= DELETE DATA ================= */
$stmt = $conn->prepare("DELETE FROM cards WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
  header("Location: admin_list.php?msg=deleted");
  exit;
} else {
  echo "Gagal menghapus data";
}
