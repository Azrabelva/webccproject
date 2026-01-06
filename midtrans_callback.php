<?php
/* =====================================================
   MIDTRANS CALLBACK - LOVECRAFTED
   ===================================================== */

require_once 'config.php';

/* ================= SECURITY HEADER ================= */
http_response_code(200);

/* ================= MIDTRANS NOTIFICATION ================= */
try {
    $notif = new \Midtrans\Notification();
} catch (Exception $e) {
    error_log('Midtrans Notification Error: ' . $e->getMessage());
    exit;
}

/* ================= DATA FROM MIDTRANS ================= */
$order_id = $notif->order_id ?? null;
$transaction_status = $notif->transaction_status ?? null;
$fraud_status = $notif->fraud_status ?? null;

/* ================= BASIC VALIDATION ================= */
if (!$order_id || !$transaction_status) {
    error_log('Invalid Midtrans callback payload');
    exit;
}

/* ================= HANDLE STATUS ================= */
if (
    $transaction_status === 'settlement' ||
    ($transaction_status === 'capture' && $fraud_status === 'accept')
) {

    /* ---------- Update order ---------- */
    $stmt = $conn->prepare("
        UPDATE orders 
        SET status = 'paid'
        WHERE order_id = ?
    ");
    $stmt->execute([$order_id]);

    /* ---------- Aktifkan premium user ---------- */
    $stmt = $conn->prepare("
        UPDATE users 
        SET premium = 1
        WHERE id = (
            SELECT user_id FROM orders WHERE order_id = ?
        )
    ");
    $stmt->execute([$order_id]);

} elseif (
    $transaction_status === 'cancel' ||
    $transaction_status === 'expire' ||
    $transaction_status === 'deny'
) {

    /* ---------- Update order gagal ---------- */
    $stmt = $conn->prepare("
        UPDATE orders 
        SET status = 'failed'
        WHERE order_id = ?
    ");
    $stmt->execute([$order_id]);
}

/* ================= DONE ================= */
echo 'OK';
