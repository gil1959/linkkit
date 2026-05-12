<?php
$m = new mysqli('127.0.0.1','root','','linkkit_BiolinkPr0');
if($m->connect_error) die("Connect error: ".$m->connect_error);

// Settle semua order yang paid + unsettled
$orders = $m->query("
    SELECT o.*, s.user_id AS seller_user_id
    FROM shop_orders o
    JOIN shops s ON o.shop_id = s.id
    WHERE o.status = 'paid' AND o.settle_status = 'unsettled'
");

$count = 0;
while($order = $orders->fetch_object()) {
    $revenue = (float)$order->grand_total - (float)$order->service_fee;
    $m->query("UPDATE users SET
        withdrawable_funds = withdrawable_funds + {$revenue},
        pending_funds      = GREATEST(0, pending_funds - {$revenue})
        WHERE user_id = {$order->seller_user_id}");
    $m->query("UPDATE shop_orders SET settle_status = 'settled' WHERE id = {$order->id}");
    echo "Settled order #{$order->id} - Revenue: Rp " . number_format($revenue,0,',','.') . "\n";
    $count++;
}

echo "\nTotal settled: {$count} orders\n\n";

// Cek saldo terbaru
$r = $m->query("SELECT user_id, name, withdrawable_funds, pending_funds FROM users WHERE user_id > 1 LIMIT 10");
echo "=== UPDATED BALANCES ===\n";
while($row = $r->fetch_assoc()) {
    echo "User #{$row['user_id']} {$row['name']}: WD={$row['withdrawable_funds']}, Pending={$row['pending_funds']}\n";
}
