<?php

namespace Altum\Helpers;

defined('ALTUMCODE') || die();

class MidtransDetector {

    public static function get_active_methods() {
        // Jika Midtrans dinonaktifkan secara global, langsung return array kosong
        if (empty(settings()->midtrans->is_enabled) || empty(settings()->midtrans->server_key)) {
            return [];
        }

        // Cache 24 jam (86400 detik)
        $cache_key = 'midtrans_detected_payments';
        
        // Kita simpan cache di tabel settings database
        // Cek apakah data settings memiliki key tersebut
        $cache_data = settings()->{$cache_key} ?? null;
        
        if ($cache_data && isset($cache_data->timestamp) && (time() - $cache_data->timestamp < 86400)) {
            return (array) $cache_data->methods;
        }

        // Jalankan deteksi otomatis
        $active_methods = self::detect_active_methods();

        // Simpan hasil ke database settings
        $value = json_encode([
            'timestamp' => time(),
            'methods' => $active_methods
        ]);

        // Gunakan query upsert/update ke tabel settings
        $stmt = database()->prepare("INSERT INTO `settings` (`key`, `value`) VALUES (?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)");
        $stmt->bind_param('ss', $cache_key, $value);
        $stmt->execute();
        $stmt->close();

        // Clear settings cache agar settings() memuat data terbaru
        cache()->deleteItem('settings');

        return $active_methods;
    }

    public static function clear_cache() {
        $cache_key = 'midtrans_detected_payments';
        database()->query("DELETE FROM `settings` WHERE `key` = '{$cache_key}'");
        cache()->deleteItem('settings');
    }

    private static function detect_active_methods() {
        $midtrans_api_url = (settings()->midtrans->mode == 'sandbox')
            ? 'https://app.sandbox.midtrans.com/v1/payment-links'
            : 'https://app.midtrans.com/v1/payment-links';

        $server_key = settings()->midtrans->server_key;

        // Daftar 14 channel pembayaran yang akan dideteksi
        $channels = [
            'bca_va', 'echannel', 'bni_va', 'bri_va', 'cimb_va', 'permata_va', 'danamon_va', 'other_va',
            'qris', 'gopay', 'shopeepay', 'credit_card', 'alfamart', 'indomaret'
        ];

        $mh = curl_multi_init();
        $curl_handles = [];

        foreach ($channels as $channel) {
            $order_id = 'DET-' . strtoupper($channel) . '-' . strtoupper(substr(md5(uniqid()), 0, 8));
            
            $payload = [
                'transaction_details' => [
                    'order_id' => $order_id,
                    'gross_amount' => 10000
                ],
                'expiry' => ['duration' => 1, 'unit' => 'days'],
                'enabled_payments' => [$channel],
                'item_details' => [[
                    'price' => 10000,
                    'quantity' => 1,
                    'name' => 'Detection Dummy'
                ]]
            ];

            $ch = curl_init($midtrans_api_url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => json_encode($payload),
                CURLOPT_HTTPHEADER     => [
                    'Content-Type: application/json',
                    'Accept: application/json',
                    'Authorization: Basic ' . base64_encode($server_key . ':'),
                ],
                CURLOPT_TIMEOUT        => 8,
                CURLOPT_CONNECTTIMEOUT => 4,
            ]);

            curl_multi_add_handle($mh, $ch);
            $curl_handles[$channel] = $ch;
        }

        // Eksekusi paralel
        $running = null;
        do {
            curl_multi_exec($mh, $running);
            curl_multi_select($mh);
        } while ($running > 0);

        $active_channels = [];

        // Evaluasi respon
        foreach ($curl_handles as $channel => $ch) {
            $response = curl_multi_getcontent($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_multi_remove_handle($mh, $ch);
            curl_close($ch);

            $resp_obj = json_decode($response);

            // Jika status HTTP sukses (< 400) dan mengembalikan payment_url, tandanya channel tersebut aktif
            if ($http_code < 400 && isset($resp_obj->payment_url)) {
                $active_channels[] = $channel;
            }
        }

        curl_multi_close($mh);

        return $active_channels;
    }
}
