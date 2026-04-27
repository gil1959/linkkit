<?php
require 'config.php';
$db = new mysqli(DATABASE_SERVER, DATABASE_USERNAME, DATABASE_PASSWORD, DATABASE_NAME);
$res = $db->query("SELECT value FROM settings WHERE `key` = 'license'");
if($row = $res->fetch_assoc()) {
    print_r(json_decode($row['value'], true));
}
