<?php
// Manual script to upgrade user to premium for testing
require 'config.php';

if (!isset($argv[1])) {
    die("Usage: php manual_upgrade.php <user_id>\n");
}

$user_id = $argv[1];

try {
    $stmt = $conn->prepare("UPDATE users SET premium = 1 WHERE id = ?");
    $stmt->execute([$user_id]);

    echo "✅ User ID $user_id upgraded to PREMIUM!\n";

    // Show user status
    $stmt = $conn->prepare("SELECT id, fullname, username, premium FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        echo "\nUser Info:\n";
        echo "  ID: {$user['id']}\n";
        echo "  Name: {$user['fullname']}\n";
        echo "  Username: {$user['username']}\n";
        echo "  Premium: " . ($user['premium'] ? 'YES' : 'NO') . "\n";
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
