<?php
// Test RajaOngkir API directly
$api_key = 'iq3OUPw9c5b6643626a71dfcZyr8hWv6';
$url = 'https://api.rajaongkir.com/starter/province';

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 15,
    CURLOPT_HTTPHEADER => ['key: ' . $api_key],
]);
$response = curl_exec($ch);
$errno = curl_errno($ch);
$error = curl_error($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $http_code\n";
echo "cURL Error: $errno - $error\n";
echo "Response length: " . strlen($response) . " bytes\n";
echo "Response (first 500 chars):\n";
echo substr($response, 0, 500) . "\n";

$decoded = json_decode($response, true);
if ($decoded) {
    $results = $decoded['rajaongkir']['results'] ?? [];
    echo "\nTotal provinces returned: " . count($results) . "\n";
    if (count($results) > 0) {
        echo "First 3: ";
        echo json_encode(array_slice($results, 0, 3), JSON_UNESCAPED_UNICODE) . "\n";
    }
} else {
    echo "JSON decode failed!\n";
    echo "Raw: " . $response . "\n";
}
