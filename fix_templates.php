<?php
require 'config.php';

echo "=== Checking Templates ===\n";
$stmt = $conn->query("SELECT * FROM templates");
$count = 0;
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $count++;
    echo "$count. {$row['title']} ({$row['template_key']}) - " .
        ($row['is_premium'] ? 'PREMIUM' : 'FREE') . "\n";
}

if ($count == 0) {
    echo "No templates found! Adding sample templates...\n\n";

    // Add templates
    $templates = [
        ['birthday', 'Happy Birthday', 'assets/templates/naao-1767720390.png', 0],
        ['anniversary', 'Happy Anniversary', 'assets/templates/naao-1767720390.png', 1],
        ['wedding', 'Wedding Invitation', 'assets/templates/naao-1767720390.png', 1]
    ];

    foreach ($templates as $t) {
        $stmt = $conn->prepare("INSERT INTO templates (template_key, title, image, is_premium) VALUES (?, ?, ?, ?)");
        $stmt->execute($t);
        echo "✅ Added: {$t[1]}\n";
    }

    echo "\nDone! Refresh user_home.php now.\n";
}
