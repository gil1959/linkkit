<?php
$url  = 'https://rajaongkir.komerce.id/api/v1/destination/province';
$curl = curl_init();
$options = [
    CURLOPT_URL            => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT        => 15,
    CURLOPT_HTTPHEADER     => [
        'key: iq3OUPw9c5b6643626a71dfcZyr8hWv6',
    ],
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_SSL_VERIFYHOST => 0,
];
curl_setopt_array($curl, $options);
$response = curl_exec($curl);
$errno    = curl_errno($curl);
$error    = curl_error($curl);
curl_close($curl);
echo "ERRNO: $errno\n";
echo "ERROR: $error\n";
echo "RESPONSE: $response\n";
