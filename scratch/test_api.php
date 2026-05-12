<?php
// Test 1: RajaOngkir langsung
$api_key = 'iq3OUPw9c5b6643626a71dfcZyr8hWv6';

echo "=== TEST 1: api.rajaongkir.com ===\n";
$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => 'https://api.rajaongkir.com/starter/province',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 10,
    CURLOPT_HTTPHEADER => ['key: ' . $api_key],
    CURLOPT_SSL_VERIFYPEER => false,
]);
$resp = curl_exec($ch);
$errno = curl_errno($ch);
$err = curl_error($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
echo "HTTP Code: $code\n";
echo "cURL Error: $errno - $err\n";
echo "Response: " . substr($resp ?: '', 0, 300) . "\n";

// Test 2: Cek API key dari tab API Key di Komerce
echo "\n=== TEST 2: Komerce API ===\n";
$ch2 = curl_init();
curl_setopt_array($ch2, [
    CURLOPT_URL => 'https://api.komerce.id/api/rajaongkir/province',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 10,
    CURLOPT_HTTPHEADER => ['key: ' . $api_key],
    CURLOPT_SSL_VERIFYPEER => false,
]);
$resp2 = curl_exec($ch2);
$errno2 = curl_errno($ch2);
$err2 = curl_error($ch2);
$code2 = curl_getinfo($ch2, CURLINFO_HTTP_CODE);
curl_close($ch2);
echo "HTTP Code: $code2\n";
echo "cURL Error: $errno2 - $err2\n";
echo "Response: " . substr($resp2 ?: '', 0, 300) . "\n";
