<?php
/*
 * Tripay Payment Gateway Helper
 * Docs: https://tripay.co.id/developer
 */

namespace Altum\PaymentGateways;

defined('ALTUMCODE') || die();

class Tripay {

    public static string $production_api_url = 'https://tripay.co.id/api/';
    public static string $sandbox_api_url    = 'https://tripay.co.id/api-sandbox/';

    /**
     * Get the API base URL based on mode setting.
     */
    public static function get_api_url(): string {
        $mode = settings()->tripay->mode ?? 'production';
        return $mode === 'sandbox' ? self::$sandbox_api_url : self::$production_api_url;
    }

    /**
     * Channels that use Open Payment API (no fixed amount, different endpoint & signature).
     * Source: https://tripay.co.id/developer#open-payment-create
     * Note: QRIS uses Closed Payment (fixed QR amount). Only OVO/DANA/SHOPEEPAY may use Open Payment.
     * If a channel returns "not supported for open payment", move it out of this list.
     */
    public static function get_open_payment_channels(): array {
        return ['OVO', 'DANA', 'SHOPEEPAY', 'SHOPEEPAYV2', 'LINKAJA'];
    }

    /**
     * Check if a given channel code requires the Open Payment API.
     */
    public static function is_open_payment_channel(string $channel_code): bool {
        return in_array(strtoupper($channel_code), self::get_open_payment_channels());
    }

    /**
     * Generate HMAC-SHA256 signature for CLOSED payment transaction.
     * Formula: HMAC-SHA256(merchant_code + merchant_ref + amount, private_key)
     */
    public static function generate_signature(string $merchant_ref, int $amount): string {
        $merchant_code = settings()->tripay->merchant_code ?? '';
        $private_key   = settings()->tripay->private_key ?? '';
        return hash_hmac('sha256', $merchant_code . $merchant_ref . $amount, $private_key);
    }

    /**
     * Generate HMAC-SHA256 signature for OPEN payment transaction.
     * Formula: HMAC-SHA256(merchant_code + channel + merchant_ref, private_key)
     */
    public static function generate_open_signature(string $channel, string $merchant_ref): string {
        $merchant_code = settings()->tripay->merchant_code ?? '';
        $private_key   = settings()->tripay->private_key ?? '';
        return hash_hmac('sha256', $merchant_code . $channel . $merchant_ref, $private_key);
    }

    /**
     * Create an OPEN payment transaction on Tripay (for QRIS, OVO, Dana, ShopeePay etc).
     * Open Payment = no fixed amount, uses different endpoint & signature.
     * Returns the API response as decoded object.
     */
    public static function create_open_payment(array $payload): ?object {
        $api_key = settings()->tripay->api_key ?? '';
        $api_url = self::get_api_url() . 'open-payment/create';

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_FRESH_CONNECT  => true,
            CURLOPT_URL            => $api_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER         => false,
            CURLOPT_HTTPHEADER     => ['Authorization: Bearer ' . $api_key],
            CURLOPT_FAILONERROR    => false,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => http_build_query($payload),
            CURLOPT_IPRESOLVE      => CURL_IPRESOLVE_V4,
            CURLOPT_TIMEOUT        => 30,
        ]);

        $response = curl_exec($curl);
        $error    = curl_error($curl);
        curl_close($curl);

        $decoded = $response ? json_decode($response) : null;

        if (!empty($error) || !$response || !$decoded || !$decoded->success) {
            $log_message = date('Y-m-d H:i:s') . " - Tripay create_open_payment ERROR:\n";
            $log_message .= "Payload: " . json_encode($payload) . "\n";
            $log_message .= "CURL Error: " . ($error ?: 'None') . "\n";
            $log_message .= "Response: " . ($response ?: 'Empty') . "\n";
            $log_message .= "----------------------------------------\n";

            $log_dir = UPLOADS_PATH . 'logs/';
            if(!is_dir($log_dir)) {
                mkdir($log_dir, 0777, true);
            }
            file_put_contents($log_dir . 'tripay_api_errors.log', $log_message, FILE_APPEND);
        }

        return $decoded;
    }

    /**
     * Get available (active) payment channels from Tripay API.
     * Returns array of channel objects or empty array on failure.
     * Cached for 1 hour to avoid repeated API calls.
     */
    public static function get_channels(): array {
        $cache_key = 'tripay_channels_' . (settings()->tripay->mode ?? 'production');

        /* Try to get from cache */
        $cached = cache()->getItem($cache_key);
        if ($cached->isHit()) {
            return $cached->get() ?: [];
        }

        $api_key = settings()->tripay->api_key ?? '';
        if (empty($api_key)) {
            return [];
        }

        $api_url = self::get_api_url() . 'merchant/payment-channel';

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_FRESH_CONNECT  => true,
            CURLOPT_URL            => $api_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER         => false,
            CURLOPT_HTTPHEADER     => ['Authorization: Bearer ' . $api_key],
            CURLOPT_IPRESOLVE      => CURL_IPRESOLVE_V4,
            CURLOPT_TIMEOUT        => 10,
        ]);

        $response = curl_exec($curl);
        $error    = curl_error($curl);
        $decoded = $response ? json_decode($response) : null;

        if (!empty($error) || !$response || !$decoded || !$decoded->success || empty($decoded->data)) {
            /* Log the exact error to a file so the user can debug it */
            $log_message = date('Y-m-d H:i:s') . " - Tripay get_channels ERROR:\n";
            $log_message .= "URL: " . $api_url . "\n";
            $log_message .= "API Key Prefix: " . substr($api_key, 0, 5) . "...\n";
            $log_message .= "CURL Error: " . ($error ?: 'None') . "\n";
            $log_message .= "Response: " . ($response ?: 'Empty') . "\n";
            $log_message .= "----------------------------------------\n";
            
            $log_dir = UPLOADS_PATH . 'logs/';
            if(!is_dir($log_dir)) {
                mkdir($log_dir, 0777, true);
            }
            file_put_contents($log_dir . 'tripay_api_errors.log', $log_message, FILE_APPEND);

            return [];
        }

        /* Filter only active channels */
        $channels = array_filter((array) $decoded->data, fn($ch) => ($ch->active ?? false) === true);
        $channels = array_values($channels);

        /* Cache for 1 hour */
        $cached->set($channels)->expiresAfter(3600);
        cache()->save($cached);

        return $channels;
    }

    /**
     * Create a closed payment transaction on Tripay.
     * Returns the API response as decoded object, or null on failure.
     */
    public static function create_transaction(array $payload): ?object {
        $api_key = settings()->tripay->api_key ?? '';
        $api_url = self::get_api_url() . 'transaction/create';

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_FRESH_CONNECT  => true,
            CURLOPT_URL            => $api_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER         => false,
            CURLOPT_HTTPHEADER     => ['Authorization: Bearer ' . $api_key],
            CURLOPT_FAILONERROR    => false,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => http_build_query($payload),
            CURLOPT_IPRESOLVE      => CURL_IPRESOLVE_V4,
            CURLOPT_TIMEOUT        => 30,
        ]);

        $response = curl_exec($curl);
        $error    = curl_error($curl);
        curl_close($curl);

        $decoded = $response ? json_decode($response) : null;

        if (!empty($error) || !$response || !$decoded || !$decoded->success) {
            /* Log the exact error */
            $log_message = date('Y-m-d H:i:s') . " - Tripay create_transaction ERROR:\n";
            $log_message .= "Payload: " . json_encode($payload) . "\n";
            $log_message .= "CURL Error: " . ($error ?: 'None') . "\n";
            $log_message .= "Response: " . ($response ?: 'Empty') . "\n";
            $log_message .= "----------------------------------------\n";
            
            $log_dir = UPLOADS_PATH . 'logs/';
            if(!is_dir($log_dir)) {
                mkdir($log_dir, 0777, true);
            }
            file_put_contents($log_dir . 'tripay_api_errors.log', $log_message, FILE_APPEND);
        }

        return $decoded;
    }

    /**
     * Get transaction detail from Tripay.
     */
    public static function get_transaction(string $reference): ?object {
        $api_key = settings()->tripay->api_key ?? '';
        $api_url = self::get_api_url() . 'transaction/detail?reference=' . $reference;

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_FRESH_CONNECT  => true,
            CURLOPT_URL            => $api_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER         => false,
            CURLOPT_HTTPHEADER     => ['Authorization: Bearer ' . $api_key],
            CURLOPT_FAILONERROR    => false,
            CURLOPT_IPRESOLVE      => CURL_IPRESOLVE_V4,
            CURLOPT_TIMEOUT        => 30,
        ]);

        $response = curl_exec($curl);
        $error    = curl_error($curl);
        curl_close($curl);

        if (!empty($error) || !$response) {
            return null;
        }

        return json_decode($response);
    }

    /**
     * Verify callback signature from Tripay.
     * Tripay sends X-Callback-Signature header = HMAC-SHA256(raw_body, private_key)
     */
    public static function verify_callback_signature(string $raw_body, string $received_signature): bool {
        $private_key        = settings()->tripay->private_key ?? '';
        $expected_signature = hash_hmac('sha256', $raw_body, $private_key);
        return hash_equals($expected_signature, $received_signature);
    }
}
