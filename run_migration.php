<?php
/**
 * Run migration: shop_physical_verification.sql
 * Access this file once via browser, then delete it.
 */

define('ALTUMCODE', true);
require_once __DIR__ . '/bootstrap.php';

$sql = file_get_contents(__DIR__ . '/shop_physical_verification.sql');

// Split by semicolon and run each statement
$statements = array_filter(array_map('trim', explode(';', $sql)));

$errors = [];
$success = 0;

foreach ($statements as $stmt) {
    if (empty($stmt) || strpos($stmt, '--') === 0) continue;
    if (database()->query($stmt)) {
        $success++;
    } else {
        $errors[] = database()->error . ' → ' . substr($stmt, 0, 80);
    }
}

echo '<pre>';
echo "✅ Berhasil: {$success} statement\n";
if ($errors) {
    echo "⚠️  Error (" . count($errors) . "):\n";
    foreach ($errors as $e) echo "  - $e\n";
} else {
    echo "✅ Semua migration berhasil! Hapus file ini setelah dijalankan.\n";
}
echo '</pre>';
