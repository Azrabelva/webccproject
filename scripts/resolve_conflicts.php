<?php
// Simple conflict resolver: keeps the incoming branch (the block after =======)
// Use with: php scripts\resolve_conflicts.php

function resolve_file($path){
    $s = file_get_contents($path);
    if (strpos($s, '<<<<<<< HEAD') === false) return false;

    // Normalize line endings
    $s = str_replace("\r\n", "\n", $s);

    // Loop to replace all conflicts
    while (strpos($s, "<<<<<<< HEAD") !== false) {
        $start = strpos($s, "<<<<<<< HEAD");
        $mid = strpos($s, "=======", $start);
        $end = strpos($s, ">>>>>>>", $mid);
        if ($start === false || $mid === false || $end === false) break;

        $incoming = substr($s, $mid + strlen("======="), $end - ($mid + strlen("=======")));

        // Trim leading/trailing newlines
        $incoming = preg_replace("/^\n+|\n+$/", "", $incoming);

        // Replace the whole conflict block with incoming content
        $s = substr($s, 0, $start) . $incoming . substr($s, $end + strlen(">>>>>>>"));
    }

    // Restore CRLF if running on Windows
    if (DIRECTORY_SEPARATOR === "\\") {
        $s = str_replace("\n", "\r\n", $s);
    }

    file_put_contents($path, $s);
    return true;
}

// Walk repo
$root = __DIR__ . '/../';
$rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root));
$changed = [];
foreach ($rii as $file) {
    if ($file->isDir()) continue;
    $p = $file->getPathname();
    // only php files and config
    if (!preg_match('/\.(php|inc|tpl)$/i', $p)) continue;
    // skip vendor
    if (strpos($p, DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR) !== false) continue;

    try {
        if (resolve_file($p)) $changed[] = substr($p, strlen($root));
    } catch (Exception $e) {
        // ignore
    }
}

if (empty($changed)) {
    echo "No conflicts found.\n";
} else {
    echo "Resolved conflicts in:\n" . implode("\n", $changed) . "\n";
}

