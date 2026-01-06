<?php
require 'config.php';

try {
    echo "Testing SQLite Connection...\n";
    if ($conn) {
        echo "✅ Connection Successful!\n";
    }

    echo "\nTesting Tables:\n";
    $tables = ['users', 'templates', 'cards', 'orders'];
    foreach ($tables as $t) {
        $stmt = $conn->query("SELECT count(*) FROM $t");
        $count = $stmt->fetchColumn();
        echo "✅ Table '$t' exists (Rows: $count)\n";
    }

    echo "\nTesting Insert/Select (User)...\n";
    $stmt = $conn->prepare("INSERT INTO users (fullname, username, email, password) VALUES (?, ?, ?, ?)");
    $testUser = 'Test User ' . time();
    $stmt->execute([$testUser, 'testuser' . time(), 'test' . time() . '@example.com', 'password']);
    $uid = $conn->lastInsertId();
    echo "✅ Inserted User ID: $uid\n";

    echo "\nALL TESTS PASSED! 🚀\n";

} catch (PDOException $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}
