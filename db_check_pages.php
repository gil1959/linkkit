<?php
require 'config.php';
$db = new PDO('mysql:host='.DATABASE_SERVER.';dbname='.DATABASE_NAME, DATABASE_USERNAME, DATABASE_PASSWORD);
$stmt = $db->query('DESCRIBE pages');
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
