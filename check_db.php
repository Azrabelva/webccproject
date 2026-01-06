<?php
require 'config.php';

echo "=== User List ===\n";
$stmt = $conn->query("SELECT id, fullname, username, premium FROM users");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $status = $row['premium'] ? 'PREMIUM' : 'FREE';
    echo "ID: {$row['id']} | {$row['username']} | {$row['fullname']} | Status: $status\n";
}

echo "\n=== Orders List ===\n";
$stmt = $conn->query("SELECT order_id, user_id, amount, status FROM orders ORDER BY created_at DESC LIMIT 5");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "Order: {$row['order_id']} | User ID: {$row['user_id']} | Amount: {$row['amount']} | Status: {$row['status']}\n";
}
