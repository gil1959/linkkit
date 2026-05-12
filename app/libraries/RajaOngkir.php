<?php
/**
 * RajaOngkir API V2
 * Docs  : https://rajaongkir.com/docs
 * Auth  : Header key: {API_KEY}
 */

namespace Altum\Libraries;

class RajaOngkir {

    const BASE_URL = 'https://rajaongkir.komerce.id/api/v1/';
    const API_KEY  = 'iq3OUPw9c5b6643626a71dfcZyr8hWv6';

    const COURIERS = [
        'jne'  => 'JNE (Jalur Nugraha Ekakurir)',
        'tiki' => 'TIKI (Citra Van Titipan Kilat)',
        'pos'  => 'POS Indonesia',
    ];

    /* ─────────────────────────────────────────────
     *  GET /destination/province
     * ───────────────────────────────────────────── */
    public static function get_provinces(): array {
        $result = self::curl('GET', 'destination/province');
        if (!$result || !isset($result['data'])) return [];
        
        $mapped = [];
        foreach($result['data'] as $item) {
            $mapped[] = [
                'province_id' => $item['id'],
                'province' => $item['name']
            ];
        }
        return $mapped;
    }

    /* ─────────────────────────────────────────────
     *  GET /destination/city/{id}
     * ───────────────────────────────────────────── */
    public static function get_cities(?int $province_id = null): array {
        if (!$province_id) return [];
        
        $result = self::curl('GET', "destination/city/{$province_id}");
        if (!$result || !isset($result['data'])) return [];
        
        $mapped = [];
        foreach($result['data'] as $item) {
            $mapped[] = [
                'city_id' => $item['id'],
                'province_id' => $province_id,
                'province' => '',
                'type' => '',
                'city_name' => $item['name'],
                'postal_code' => ''
            ];
        }
        return $mapped;
    }

    /* ─────────────────────────────────────────────
     *  Not implemented in new API properly, fallback to getting all cities and filtering
     * ───────────────────────────────────────────── */
    public static function get_city(int $city_id): ?array {
        return null; 
    }

    /* ─────────────────────────────────────────────
     *  POST /calculate/domestic-cost
     * ───────────────────────────────────────────── */
    public static function get_cost(int $origin, int $destination, int $weight_gram, string $courier = 'jne'): array {
        if (!array_key_exists($courier, self::COURIERS)) {
            return [];
        }

        $result = self::curl('POST', 'calculate/domestic-cost', [
            'origin'      => $origin,
            'destination' => $destination,
            'weight'      => max(1, $weight_gram),
            'courier'     => $courier,
        ]);

        if (!$result || !isset($result['data'])) return [];
        
        $mapped_costs = [];
        foreach($result['data'] as $item) {
            $mapped_costs[] = [
                'service' => $item['service'] ?? '',
                'description' => $item['description'] ?? '',
                'cost' => [
                    [
                        'value' => $item['cost'] ?? 0,
                        'etd' => $item['etd'] ?? '',
                        'note' => ''
                    ]
                ]
            ];
        }

        return [
            [
                'code' => $courier,
                'costs' => $mapped_costs
            ]
        ];
    }

    /* ─────────────────────────────────────────────
     *  Ambil ongkir semua kurir sekaligus
     * ───────────────────────────────────────────── */
    public static function get_all_costs(int $origin, int $destination, int $weight_gram): array {
        $all = [];
        foreach (array_keys(self::COURIERS) as $courier) {
            $results = self::get_cost($origin, $destination, $weight_gram, $courier);
            if (!empty($results)) {
                $all[$courier] = $results[0]['costs'] ?? [];
            }
        }
        return $all;
    }

    /* ─────────────────────────────────────────────
     *  Internal cURL helper
     * ───────────────────────────────────────────── */
    private static function curl(string $method, string $endpoint, array $data = []): ?array {
        $url  = self::BASE_URL . $endpoint;
        $curl = curl_init();

        $options = [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 15,
            CURLOPT_HTTPHEADER     => [
                'key: ' . self::API_KEY,
            ],
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => 0,
        ];

        if (strtoupper($method) === 'POST') {
            $options[CURLOPT_POST]       = true;
            $options[CURLOPT_POSTFIELDS] = http_build_query($data);
            $options[CURLOPT_HTTPHEADER][] = 'Content-Type: application/x-www-form-urlencoded';
        }

        curl_setopt_array($curl, $options);
        $response = curl_exec($curl);
        $errno    = curl_errno($curl);
        curl_close($curl);

        if ($errno || !$response) return null;

        $decoded = json_decode($response, true);
        if (!$decoded) return null;

        return $decoded;
    }
}
