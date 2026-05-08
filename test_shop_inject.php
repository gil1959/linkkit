<?php
/**
 * TEST INJECT SHOP TRANSACTION
 * Buka di browser: https://linkkit.id/test_shop_inject.php?item_id=1
 * HAPUS setelah selesai test!
 */

define('ALTUMCODE', 1);
require_once __DIR__ . '/app/init.php';

$item_id = (int)($_GET['item_id'] ?? 1);
$simulate_paid = isset($_GET['paid']); // ?paid=1 untuk simulate webhook paid

$item = database()->query("SELECT * FROM `shop_items` WHERE `id` = {$item_id} AND `status` = 1")->fetch_object();
if(!$item) die('Item not found');

$shop = database()->query("SELECT * FROM `shops` WHERE `id` = {$item->shop_id}")->fetch_object();
if(!$shop) die('Shop not found');

echo '<h2>Shop Transaction Test</h2>';
echo '<p><strong>Item:</strong> ' . htmlspecialchars($item->name) . ' (ID: ' . $item->id . ')</p>';
echo '<p><strong>Type:</strong> ' . $item->type . '</p>';
echo '<p><strong>Price:</strong> Rp ' . number_format($item->price, 0, ',', '.') . '</p>';
echo '<p><strong>Shop:</strong> ' . htmlspecialchars($shop->name) . '</p>';
echo '<hr>';

// Cek notification_settings
$notif = json_decode($shop->notification_settings ?? '{}', true);
echo '<h3>Notification Settings:</h3>';
echo '<pre>' . json_encode($notif, JSON_PRETTY_PRINT) . '</pre>';

// Cek orders terbaru
$orders = database()->query("SELECT so.*, sc.email, sc.full_name FROM `shop_orders` so
    JOIN `shop_customers` sc ON so.customer_id = sc.id
    WHERE so.shop_id = {$shop->id}
    ORDER BY so.id DESC LIMIT 5");

echo '<h3>5 Pesanan Terakhir:</h3>';
echo '<table border="1" cellpadding="8" style="border-collapse:collapse;font-size:.85rem;">';
echo '<tr><th>ID</th><th>Invoice</th><th>Pembeli</th><th>Status</th><th>Fulfilled</th><th>Paid Date</th><th>Settlement</th></tr>';
while($o = $orders->fetch_object()) {
    $color = $o->status === 'paid' ? '#d1fae5' : '#fef3c7';
    echo '<tr style="background:'.$color.'">';
    echo '<td>' . $o->id . '</td>';
    echo '<td style="font-family:monospace">' . $o->invoice_number . '</td>';
    echo '<td>' . htmlspecialchars($o->full_name) . ' (' . htmlspecialchars($o->email) . ')</td>';
    echo '<td><strong>' . $o->status . '</strong></td>';
    echo '<td>' . htmlspecialchars(substr($o->fulfilled_content ?? '-', 0, 60)) . '</td>';
    echo '<td>' . ($o->paid_date ?? '-') . '</td>';
    echo '<td>' . ($o->settle_status ?? '-') . '</td>';
    echo '</tr>';
}
echo '</table>';

// Simulate webhook callback jika ?paid=1
if($simulate_paid) {
    $latest = database()->query("SELECT * FROM `shop_orders` WHERE `shop_id` = {$shop->id} ORDER BY `id` DESC LIMIT 1")->fetch_object();
    if($latest && $latest->status === 'pending') {
        $datetime = date('Y-m-d H:i:s');
        database()->query("UPDATE `shop_orders` SET `status`='paid', `settle_status`='unsettled', `paid_date`='{$datetime}' WHERE `id`={$latest->id}");
        echo '<hr><p style="color:green;font-weight:bold">✅ Order #' . $latest->id . ' (' . $latest->invoice_number . ') di-mark PAID!</p>';
        echo '<p><a href="' . SITE_URL . 'store-checkout-success/' . $latest->invoice_number . '" target="_blank">→ Buka halaman success</a></p>';
    } else {
        echo '<hr><p style="color:orange">⚠️ Order terbaru sudah paid atau tidak ditemukan.</p>';
    }
}

echo '<hr>';
echo '<p><strong>Webhook URL yang harus diset di Tripay:</strong></p>';
echo '<code>' . SITE_URL . 'webhook-tripay-shop</code>';
echo '<br><br>';
echo '<p style="color:red"><strong>⚠️ HAPUS FILE INI SETELAH TEST!</strong></p>';
