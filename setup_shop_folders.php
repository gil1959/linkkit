<?php
/*
 * Setup Shop Upload Folders
 * Jalankan SEKALI di browser production: https://linkkit.id/setup_shop_folders.php
 * HAPUS file ini setelah berhasil!
 */

$base = __DIR__ . '/uploads/';

$folders = [
    'shop_items',
    'shop_covers',
    'shop_logos',
    'shop_proofs',
    'logs',
];

echo '<h2>Setup Shop Upload Folders</h2><ul>';
foreach ($folders as $folder) {
    $path = $base . $folder;
    if (!is_dir($path)) {
        if (mkdir($path, 0755, true)) {
            echo "<li style='color:green'>✅ Created: uploads/{$folder}/</li>";
        } else {
            echo "<li style='color:red'>❌ FAILED to create: uploads/{$folder}/ — check parent folder permissions</li>";
        }
    } else {
        echo "<li style='color:blue'>ℹ️ Already exists: uploads/{$folder}/</li>";
    }
    // Set permissions
    chmod($path, 0755);
}
echo '</ul>';
echo '<p style="color:red"><strong>⚠️ PENTING: Hapus file ini setelah selesai! (setup_shop_folders.php)</strong></p>';
