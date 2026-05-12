<?php
require_once 'config.php';

$mysqli = new mysqli(DATABASE_SERVER, DATABASE_USERNAME, DATABASE_PASSWORD, DATABASE_NAME);
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$tables = ['shops', 'shop_items', 'shop_orders'];
foreach ($tables as $table) {
    $result = $mysqli->query("SHOW CREATE TABLE $table");
    if ($row = $result->fetch_assoc()) {
        echo "=== $table ===\n";
        echo $row['Create Table'] . "\n\n";
    }
}
$mysqli->close();
