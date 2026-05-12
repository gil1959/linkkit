<?php
// Test pakai file_get_contents stream context (bukan cURL)
echo "=== TEST dengan stream context ===\n";

$opts = [
    'http' => [
        'method' => 'GET',
        'header' => "key: iq3OUPw9c5b6643626a71dfcZyr8hWv6\r\n",
        'timeout' => 10,
    ],
    'ssl' => [
        'verify_peer' => false,
        'verify_peer_name' => false,
    ]
];
$context = stream_context_create($opts);

$resp = @file_get_contents('https://api.rajaongkir.com/starter/province', false, $context);
if ($resp === false) {
    echo "file_get_contents GAGAL\n";
    $e = error_get_last();
    echo "Error: " . ($e['message'] ?? 'unknown') . "\n";
} else {
    echo "Sukses! Length: " . strlen($resp) . "\n";
    echo substr($resp, 0, 400) . "\n";
}

// Juga test dengan cURL tapi pakai CURLOPT_INTERFACE
echo "\n=== TEST cURL dengan explicit interface ===\n";
$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => 'https://api.rajaongkir.com/starter/province',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 10,
    CURLOPT_CONNECTTIMEOUT => 5,
    CURLOPT_HTTPHEADER => ['key: iq3OUPw9c5b6643626a71dfcZyr8hWv6'],
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_SSL_VERIFYHOST => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_USERAGENT => 'Mozilla/5.0',
    CURLOPT_DNS_CACHE_TIMEOUT => 0,
]);
$resp2 = curl_exec($ch);
$errno = curl_errno($ch);
$err = curl_error($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$resolve = curl_getinfo($ch, CURLINFO_NAMELOOKUP_TIME);
curl_close($ch);
echo "HTTP: $code | DNS time: {$resolve}s | cURL Error: $errno - $err\n";
echo "Response: " . substr($resp2 ?: '', 0, 300) . "\n";
