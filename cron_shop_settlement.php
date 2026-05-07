<?php
/*
 * Copyright (c) 2025 AltumCode (https://altumcode.com/)
 * 
 * Shop Settlement Cron Job
 * Move pending funds to withdrawable funds after 3 days.
 * 
 * Run this via crontab daily:
 * 0 0 * * * /usr/bin/php /path/to/cron_shop_settlement.php
 */

require_once __DIR__ . '/app/init.php';

use Altum\Database\Database;

// Find all paid orders that have been settled by Tripay (i.e. > 3 days ago)
// but haven't been settled in our system yet.
$three_days_ago = (new \DateTime())->modify('-3 days')->format('Y-m-d H:i:s');

$orders_result = database()->query("
    SELECT o.id, o.shop_id, o.grand_total, o.service_fee, s.user_id 
    FROM `shop_orders` o
    JOIN `shops` s ON o.shop_id = s.id
    WHERE o.status = 'paid' 
      AND o.settle_status = 'unsettled' 
      AND o.paid_date <= '{$three_days_ago}'
");

$processed_count = 0;

while($order = $orders_result->fetch_object()) {
    $seller_revenue = $order->grand_total - $order->service_fee;

    // Move from pending to withdrawable
    database()->query("
        UPDATE `users` 
        SET `pending_funds` = `pending_funds` - {$seller_revenue},
            `withdrawable_funds` = `withdrawable_funds` + {$seller_revenue}
        WHERE `user_id` = {$order->user_id}
    ");

    // Mark order as settled
    database()->query("
        UPDATE `shop_orders` 
        SET `settle_status` = 'settled' 
        WHERE `id` = {$order->id}
    ");

    $processed_count++;
}

echo "Shop Settlement Cron finished successfully. Processed {$processed_count} orders.\n";
