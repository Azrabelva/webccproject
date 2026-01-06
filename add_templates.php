<?php
require 'config.php';

// Add sample templates
$templates = [
    [
        'template_key' => 'birthday',
        'title' => 'Happy Birthday',
        'image' => 'assets/templates/naao-1767720390.png',
        'is_premium' => 0
    ],
    [
        'template_key' => 'anniversary',
        'title' => 'Happy Anniversary',
        'image' => 'assets/templates/naao-1767720390.png',
        'is_premium' => 1
    ],
    [
        'template_key' => 'wedding',
        'title' => 'Wedding Invitation',
        'image' => 'assets/templates/naao-1767720390.png',
        'is_premium' => 1
    ]
];

foreach ($templates as $t) {
    // Check if exists
    $stmt = $conn->prepare("SELECT id FROM templates WHERE template_key = ?");
    $stmt->execute([$t['template_key']]);

    if (!$stmt->fetch()) {
        // Insert
        $stmt = $conn->prepare("INSERT INTO templates (template_key, title, image, is_premium) VALUES (?, ?, ?, ?)");
        $stmt->execute([$t['template_key'], $t['title'], $t['image'], $t['is_premium']]);
        echo "✅ Added template: {$t['title']}\n";
    } else {
        echo "⏭️  Template '{$t['title']}' already exists\n";
    }
}

echo "\n✅ Done! Refresh user_home.php to see templates.\n";
