<?php
require 'config.php';

// Get first user (Raya Nata)
$stmt = $conn->query("SELECT id, username FROM users LIMIT 1");
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    // Upgrade to premium
    $stmt = $conn->prepare("UPDATE users SET premium = 1 WHERE id = ?");
    $stmt->execute([$user['id']]);

    echo "✅ User '{$user['username']}' (ID: {$user['id']}) upgraded to PREMIUM!\n";
    echo "\nRefresh halaman user_home.php untuk lihat perubahan.\n";
} else {
    echo "❌ No users found\n";
}
