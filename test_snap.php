<?php
require 'config.php';

$params = [
    'transaction_details' => [
        'order_id' => 'TEST-' . time(),
        'gross_amount' => 10000
    ]
];

try {
    $token = \Midtrans\Snap::getSnapToken($params);
    echo "SNAP TOKEN OK: " . $token;
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
