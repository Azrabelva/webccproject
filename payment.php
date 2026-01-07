<?php
/* =====================================================
   LOVECRAFTED - PAYMENT (MIDTRANS SNAP)
   SUPPORT SANDBOX ↔ PRODUCTION SWITCH
   ===================================================== */

require_once 'config.php';

/* ================= SESSION ================= */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* ================= AUTH CHECK ================= */
if (!isset($_SESSION['user'])) {
    http_response_code(401);
    echo json_encode([
        'status' => 'error',
        'message' => 'User not logged in'
    ]);
    exit;
}

$user = $_SESSION['user'];

/* ================= VALIDATION ================= */
if (empty($user['id'])) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid user data'
    ]);
    exit;
}

/* ================= PAYMENT CONFIG ================= */
$amount = 25000; // Harga Premium LoveCrafted (Rp 25.000)
$order_id = 'LC-' . time() . '-' . $user['id'];

/* ================= SAVE ORDER (PENDING) ================= */
try {
    $stmt = $conn->prepare("
        INSERT INTO orders (order_id, user_id, amount, status)
        VALUES (?, ?, ?, 'pending')
    ");

    if (!$stmt->execute([$order_id, $user['id'], $amount])) {
        throw new Exception("Failed to save order");
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed prepare order: ' . $e->getMessage()
    ]);
    exit;
}

/* ================= MIDTRANS PARAM ================= */
$params = [
    'transaction_details' => [
        'order_id' => $order_id,
        'gross_amount' => $amount
    ],
    'enabled_payments' => [
        'credit_card',
        'bca_va',
        'bni_va',
        'bri_va',
        'gopay',
        'qris'
    ],
    'item_details' => [
        [
            'id' => 'LC-PREMIUM',
            'price' => $amount,
            'quantity' => 1,
            'name' => 'LoveCrafted Premium Access'
        ]
    ],
    'customer_details' => [
        'first_name' => $user['fullname'] ?? 'LoveCrafted User',
        'email' => $user['email'] ?? 'user@lovecrafted.local'
    ],
    'callbacks' => [
        'finish' => $BASE_URL . '/payment_success.php'
    ]
];

/* ================= CREATE SNAP TOKEN ================= */
try {

    // Check if Midtrans is available
    if (!class_exists('\Midtrans\Snap')) {
        throw new Exception('Midtrans library not loaded');
    }

    // Snap otomatis mengikuti MIDTRANS_ENV dari config.php
    $snapToken = \Midtrans\Snap::getSnapToken($params);

    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'success',
        'token' => $snapToken,
        'order_id' => $order_id,
        'env' => $MIDTRANS_ENV // optional (debug)
    ]);

} catch (Exception $e) {

    // Log the error
    error_log('Payment Error: ' . $e->getMessage());
    error_log('Stack trace: ' . $e->getTraceAsString());

    // Jika gagal, tandai order failed
    $stmt = $conn->prepare("
        UPDATE orders SET status = 'failed'
        WHERE order_id = ?
    ");
    $stmt->execute([$order_id]);

    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage(),
        'debug' => [
            'midtrans_available' => class_exists('\Midtrans\Snap'),
            'server_key' => isset($MIDTRANS_SERVER_KEY) ? 'set' : 'not set'
        ]
    ]);
}
