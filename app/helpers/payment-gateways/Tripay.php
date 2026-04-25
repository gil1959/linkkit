<?php
/*
 * Tripay Payment Gateway Helper
 * Docs: https://tripay.co.id/developer
 */

defined('ALTUMCODE') || die();

namespace Altum\PaymentGateways;

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
     * Generate HMAC-SHA256 signature for closed payment transaction.
     * Formula: HMAC-SHA256(merchant_code + merchant_ref + amount, private_key)
     */
    public static function generate_signature(string $merchant_ref, int $amount): string {
        $merchant_code = settings()->tripay->merchant_code ?? '';
        $private_key   = settings()->tripay->private_key ?? '';
        return hash_hmac('sha256', $merchant_code . $merchant_ref . $amount, $private_key);
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

        if (!empty($error) || !$response) {
            return null;
        }

        return json_decode($response);
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
