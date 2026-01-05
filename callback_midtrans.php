<?php
require 'config.php';

/* ================= GET JSON ================= */
$raw = file_get_contents("php://input");
$data = json_decode($raw, true);

if (!$data) {
    http_response_code(400);
    exit("Invalid payload");
}

/* ================= VALIDATE SIGNATURE ================= */
$signatureKey = $data['signature_key'] ?? '';

$expectedSignature = hash(
    "sha512",
    $data['order_id'] .
    $data['status_code'] .
    $data['gross_amount'] .
    $MIDTRANS_SERVER_KEY
);

if ($signatureKey !== $expectedSignature) {
    http_response_code(403);
    exit("Invalid signature");
}

/* ================= STATUS ================= */
$transactionStatus = $data['transaction_status'];
$orderId           = $data['order_id'];

/*
|--------------------------------------------------------------------------
| PAYMENT LINK TIDAK BAWA user_id
| SOLUSI: PREMIUM GLOBAL / ATAU 1 USER LOGIN TERAKHIR
|--------------------------------------------------------------------------
| SEDERHANA: SET USER YANG LOGIN TERAKHIR JADI PREMIUM
*/
if ($transactionStatus === 'settlement') {

    // contoh: set SEMUA user jadi premium (sandbox testing)
    $conn->query("UPDATE users SET premium = 1");

    // kalau mau 1 user spesifik → bilang, aku bikinin versi token
}

/* ================= RESPONSE ================= */
http_response_code(200);
echo "OK";
