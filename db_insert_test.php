<?php
require 'config.php';
$db = new mysqli(DATABASE_SERVER, DATABASE_USERNAME, DATABASE_PASSWORD, DATABASE_NAME);

$sql = "INSERT INTO `payments` (`user_id`, `plan_id`, `processor`, `type`, `frequency`, `code`, `discount_amount`, `base_amount`, `email`, `payment_id`, `name`, `plan`, `billing`, `business`, `taxes_ids`, `total_amount`, `currency`, `payment_proof`, `status`, `datetime`) VALUES (1, 1, 'offline_payment', 'one_time', 'monthly', NULL, 0, 0, 'test@test.com', 'abc', 'Test', '{}', '{}', '{}', '{}', 1000, 'IDR', 'test.jpg', 'pending', NOW())";

if (!$db->query($sql)) {
    echo "Error inserting: " . $db->error . "\n";
} else {
    echo "Success! ID: " . $db->insert_id . "\n";
}
