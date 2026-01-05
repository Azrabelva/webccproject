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
$amount = 100000; // Harga Premium LoveCrafted
$order_id = 'LC-' . time() . '-' . $user['id'];

/* ================= SAVE ORDER (PENDING) ================= */
$stmt = $conn->prepare("
    INSERT INTO orders (order_id, user_id, amount, status)
    VALUES (?, ?, ?, 'pending')
");

if (!$stmt) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed prepare order'
    ]);
    exit;
}

$stmt->bind_param("sii", $order_id, $user['id'], $amount);
$stmt->execute();

/* ================= MIDTRANS PARAM ================= */
$params = [
    'transaction_details' => [
        'order_id'     => $order_id,
        'gross_amount' => $amount
    ],
    'enabled_payments' => [
    'credit_card'
],
    'item_details' => [
        [
            'id'       => 'LC-PREMIUM',
            'price'    => $amount,
            'quantity' => 1,
            'name'     => 'LoveCrafted Premium Access'
        ]
    ],
    'customer_details' => [
        'first_name' => $user['fullname'] ?? 'LoveCrafted User',
        'email'      => $user['email'] ?? 'user@lovecrafted.local'
    ],
    'callbacks' => [
        'finish' => $BASE_URL . '/payment_success.php'
    ]
];

/* ================= CREATE SNAP TOKEN ================= */
try {

    // Snap otomatis mengikuti MIDTRANS_ENV dari config.php
    $snapToken = \Midtrans\Snap::getSnapToken($params);

    header('Content-Type: application/json');
    echo json_encode([
        'status'   => 'success',
        'token'    => $snapToken,
        'order_id' => $order_id,
        'env'      => MIDTRANS_ENV // optional (debug)
    ]);

} catch (Exception $e) {

    // Jika gagal, tandai order failed
    $stmt = $conn->prepare("
        UPDATE orders SET status = 'failed'
        WHERE order_id = ?
    ");
    $stmt->bind_param("s", $order_id);
    $stmt->execute();

    http_response_code(500);
    echo json_encode([
        'status'  => 'error',
        'message' => $e->getMessage()
    ]);
}
