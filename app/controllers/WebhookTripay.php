<?php
/*
 * Tripay Webhook Controller
 * Handles payment callback from Tripay
 */

namespace Altum\Controllers;

use Altum\Models\Payments;

defined('ALTUMCODE') || die();

class WebhookTripay extends Controller {

    public function index() {

        /* No cache on webhook endpoint */
        header('Cache-Control: no-store');

        if(!in_array(settings()->license->type, ['Extended License', 'extended'])) {
            throw_404();
        }

        if(strtoupper($_SERVER['REQUEST_METHOD']) !== 'POST') {
            throw_404();
        }

        /* Get raw payload */
        $payload = trim(@file_get_contents('php://input'));

        /* Log for debugging */
        debug_log('[' . \Altum\Router::$controller . '] ' . print_r(['payload' => $payload], true));

        /* Get the callback signature from header */
        $headers = getallheaders();
        $received_signature = $headers['X-Callback-Signature'] ?? '';

        /* Verify signature: HMAC-SHA256(raw_body, private_key) */
        $private_key = settings()->tripay->private_key ?? '';
        $expected_signature = hash_hmac('sha256', $payload, $private_key);

        if(!hash_equals($expected_signature, $received_signature)) {
            die('INVALID_SIGNATURE');
        }

        $data = json_decode($payload, true);

        if(!$data) {
            die('0');
        }

        /* Only process PAID status */
        if(!isset($data['status']) || $data['status'] !== 'PAID') {
            die('1');
        }

        /* Extract merchant_ref which contains our order metadata */
        /* Format: userId-planId-frequency-baseAmount-code-discountAmount-taxesIds */
        $merchant_ref = $data['merchant_ref'] ?? '';
        $metadata_parts = explode('-', $merchant_ref, 7);

        if(count($metadata_parts) < 6) {
            die('2');
        }

        $user_id           = (int) $metadata_parts[0];
        $plan_id           = (int) $metadata_parts[1];
        $payment_frequency = $metadata_parts[2];
        $base_amount       = $metadata_parts[3];
        $code              = $metadata_parts[4] ?? null;
        $discount_amount   = $metadata_parts[5] ?? 0;
        $taxes_ids         = $metadata_parts[6] ?? null;

        /* Payment data */
        $external_payment_id      = $data['reference'] ?? '';
        $payment_total            = $data['total_amount'] ?? 0;
        $payment_currency         = 'IDR'; /* Tripay always uses IDR */
        $payment_type             = 'one_time';
        $payment_subscription_id  = null;
        $payer_email              = $data['customer_email'] ?? '';
        $payer_name               = $data['customer_name'] ?? '';

        (new Payments())->webhook_process_payment(
            'tripay',
            $external_payment_id,
            $payment_total,
            $payment_currency,
            $user_id,
            $plan_id,
            $payment_frequency,
            $code,
            $discount_amount,
            $base_amount,
            $taxes_ids,
            $payment_type,
            $payment_subscription_id,
            $payer_email,
            $payer_name
        );

        echo 'successful';

    }

}
