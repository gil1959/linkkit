<?php
require 'config.php';
$db = new mysqli(DATABASE_SERVER, DATABASE_USERNAME, DATABASE_PASSWORD, DATABASE_NAME);

// 1. Find a physical product
$res = $db->query("SELECT * FROM shop_items WHERE type = 'physical' LIMIT 1");
$item = $res->fetch_object();

if (!$item) {
    echo "Tidak ditemukan produk fisik di database.";
    exit;
}

// 2. Get shop info
$res = $db->query("SELECT * FROM shops WHERE id = " . $item->shop_id);
$shop = $res->fetch_object();

// 3. Find or create a customer
$res = $db->query("SELECT * FROM shop_customers WHERE shop_id = " . $shop->id . " LIMIT 1");
$customer = $res->fetch_object();

if (!$customer) {
    echo "Tidak ada customer untuk toko ini, silakan buat order manual dulu atau saya bisa buatkan query INSERT customer juga.\n";
    $customer_id = 1; // fallback
} else {
    $customer_id = $customer->id;
}

$invoice = 'INV-SHOP-TEST' . rand(100, 999);
$qty = 1;
$total_amount = $item->price;
$service_fee = $total_amount * 0.05;
$shipping_cost = 15000;
$grand_total = $total_amount + $shipping_cost;
$datetime = date('Y-m-d H:i:s');

echo "Berikut adalah SQL untuk produk fisik: " . $item->name . " (ID: " . $item->id . ") dari Toko ID: " . $shop->id . "\n\n";

$sql = "INSERT INTO `shop_orders` 
(`shop_id`, `item_id`, `customer_id`, `invoice_number`, `qty`, `total_amount`, `service_fee`, `grand_total`, `discount_amount`, `voucher_id`, `shipping_address`, `shipping_courier`, `shipping_service`, `shipping_cost`, `payment_processor`, `status`, `settle_status`, `paid_date`, `datetime`) 
VALUES 
({$shop->id}, {$item->id}, {$customer_id}, '{$invoice}', {$qty}, {$total_amount}, {$service_fee}, {$grand_total}, 0, NULL, 'Jl. Sudirman No 1, Jakarta Pusat', 'JNE', 'REG', {$shipping_cost}, 'tripay', 'paid', 'unsettled', '{$datetime}', '{$datetime}');\n";

echo $sql;
