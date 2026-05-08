<?php
/*
 * Webhook Tripay Shop Controller
 * Handles Tripay payment callback for shop orders
 * Docs: https://tripay.co.id/developer#callback
 */

namespace Altum\Controllers;

defined('ALTUMCODE') || die();

class WebhookTripayShop extends Controller {

    public function index() {

        $json = @file_get_contents('php://input');
        $callbackSignature = $_SERVER['HTTP_X_CALLBACK_SIGNATURE'] ?? '';
        $callbackEvent     = $_SERVER['HTTP_X_CALLBACK_EVENT']    ?? '';

        $privateKey = settings()->tripay->private_key ?? '';
        $signature  = hash_hmac('sha256', $json, $privateKey);

        if(!hash_equals($signature, $callbackSignature)) {
            http_response_code(403);
            die('INVALID_SIGNATURE');
        }

        if($callbackEvent !== 'payment_status') {
            http_response_code(400);
            die('INVALID_EVENT');
        }

        $data = json_decode($json);
        if(!$data || json_last_error() !== JSON_ERROR_NONE) {
            http_response_code(400);
            die('INVALID_JSON');
        }

        /* Hanya proses jika PAID */
        if(strtoupper((string)($data->status ?? '')) !== 'PAID') {
            http_response_code(200);
            die('IGNORED');
        }

        $merchantRef     = $data->merchant_ref ?? '';
        $tripayReference = $data->reference    ?? '';

        /* Ambil order */
        $order = database()->query("SELECT * FROM `shop_orders`
            WHERE `invoice_number` = '" . database()->real_escape_string($merchantRef) . "'"
        )->fetch_object() ?? null;

        if(!$order) { http_response_code(404); die('ORDER_NOT_FOUND'); }
        if($order->status === 'paid') { http_response_code(200); die('ALREADY_PAID'); }

        $shop     = database()->query("SELECT * FROM `shops`         WHERE `id` = {$order->shop_id}")->fetch_object();
        $item     = database()->query("SELECT * FROM `shop_items`    WHERE `id` = {$order->item_id}")->fetch_object();
        $customer = database()->query("SELECT * FROM `shop_customers` WHERE `id` = {$order->customer_id}")->fetch_object();

        if(!$shop || !$item || !$customer) { http_response_code(500); die('MISSING_DATA'); }

        $datetime = \Altum\Date::$date;

        /* Update order */
        database()->query("UPDATE `shop_orders` SET
            `status` = 'paid',
            `settle_status` = 'unsettled',
            `payment_id` = '" . database()->real_escape_string($tripayReference) . "',
            `paid_date` = '{$datetime}'
            WHERE `id` = {$order->id}");

        /* Update customer stats */
        database()->query("UPDATE `shop_customers` SET
            `total_orders` = `total_orders` + 1,
            `total_spent`  = `total_spent`  + {$order->grand_total}
            WHERE `id` = {$customer->id}");

        /* Kredit saldo penjual */
        $seller_revenue = $order->grand_total - $order->service_fee;
        database()->query("UPDATE `users` SET
            `withdrawable_funds` = `withdrawable_funds` + {$seller_revenue}
            WHERE `user_id` = {$shop->user_id}");

        /* ── Fulfillment per tipe produk ── */
        $fulfilled_content = null;

        if($item->type === 'download_link') {
            /* Ambil semua link download */
            $links = json_decode($item->download_links ?? '[]', true) ?: [];
            $fulfilled_content = json_encode($links);

        } elseif($item->type === 'random_code') {
            /* Pop satu kode dari pool */
            $codes = json_decode($item->download_links ?? '[]', true) ?: [];
            if(!empty($codes)) {
                $code = array_shift($codes);
                /* Simpan sisa kode ke produk */
                $remaining = database()->real_escape_string(json_encode(array_values($codes)));
                database()->query("UPDATE `shop_items` SET
                    `download_links` = '{$remaining}',
                    `stock` = GREATEST(0, COALESCE(`stock`, 0) - 1)
                    WHERE `id` = {$item->id}");
                $fulfilled_content = $code;
            } else {
                $fulfilled_content = 'OUT_OF_STOCK';
            }

        } elseif($item->type === 'webhook_event') {
            /* Fire external webhook */
            $webhook_url = $item->webhook_url ?? $shop->global_webhook_url ?? null;
            if($webhook_url) {
                $payload = json_encode([
                    'event'          => 'purchase_success',
                    'invoice'        => $order->invoice_number,
                    'customer_name'  => $customer->full_name,
                    'customer_email' => $customer->email,
                    'product_id'     => $item->id,
                    'product_name'   => $item->name,
                    'amount'         => $order->grand_total,
                    'datetime'       => $datetime,
                ]);
                $ch = curl_init($webhook_url);
                curl_setopt_array($ch, [
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_POST           => true,
                    CURLOPT_POSTFIELDS     => $payload,
                    CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
                    CURLOPT_TIMEOUT        => 8,
                    CURLOPT_IPRESOLVE      => CURL_IPRESOLVE_V4,
                ]);
                $wh_response  = curl_exec($ch);
                $wh_http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                /* Log webhook */
                $payload_esc = database()->real_escape_string($payload);
                $url_esc     = database()->real_escape_string($webhook_url);
                database()->query("INSERT INTO `shop_webhook_events`
                    (`shop_id`, `item_id`, `webhook_url`, `payload`, `status_code`, `datetime`)
                    VALUES ({$shop->id}, {$item->id}, '{$url_esc}', '{$payload_esc}', {$wh_http_code}, '{$datetime}')");

                $fulfilled_content = 'webhook_fired:' . $wh_http_code;
            } else {
                $fulfilled_content = 'webhook_fired:no_url';
            }

        } elseif($item->type === 'manual') {
            $fulfilled_content = 'manual_pending';
        }

        /* Simpan fulfilled_content ke order */
        if($fulfilled_content !== null) {
            $fc_esc = database()->real_escape_string($fulfilled_content);
            database()->query("UPDATE `shop_orders` SET `fulfilled_content` = '{$fc_esc}' WHERE `id` = {$order->id}");
        }

        /* Update sales count produk */
        database()->query("UPDATE `shop_items` SET `sales` = `sales` + 1 WHERE `id` = {$item->id}");

        /* Kirim email ke pembeli */
        $email_html = $this->build_delivery_email($order, $item, $customer, $shop, $fulfilled_content);
        try {
            send_mail($customer->email, 'Pesanan kamu di ' . $shop->name . ' sudah berhasil!', $email_html);
        } catch(\Exception $e) { /* silent */ }

        /* Kirim email notifikasi ke pemilik toko (jika notification_settings aktif) */
        $notif = json_decode($shop->notification_settings ?? '{}', true);
        if(!empty($notif['purchase_success'])) {
            $seller = database()->query("SELECT `email`, `name` FROM `users` WHERE `user_id` = {$shop->user_id}")->fetch_object();
            if($seller) {
                $seller_html = '
<!DOCTYPE html><html><body style="font-family:Inter,sans-serif;background:#f8fafc;padding:20px">
<div style="max-width:520px;margin:0 auto;background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,.07)">
    <div style="background:linear-gradient(135deg,#059669,#10b981);padding:24px;text-align:center">
        <h1 style="color:#fff;font-size:1.2rem;margin:0"><i style="margin-right:8px"></i>Penjualan Baru di Toko Kamu!</h1>
    </div>
    <div style="padding:28px">
        <p>Halo <strong>' . htmlspecialchars($seller->name) . '</strong>,</p>
        <p>Ada pembelian baru di toko <strong>' . htmlspecialchars($shop->name) . '</strong>!</p>
        <table style="width:100%;border-collapse:collapse;font-size:.88rem;margin:16px 0">
            <tr style="background:#f8fafc"><td style="padding:8px 12px;font-weight:600">Produk</td><td style="padding:8px 12px">' . htmlspecialchars($item->name) . '</td></tr>
            <tr><td style="padding:8px 12px;font-weight:600">Pembeli</td><td style="padding:8px 12px">' . htmlspecialchars($customer->full_name) . ' (' . htmlspecialchars($customer->email) . ')</td></tr>
            <tr style="background:#f8fafc"><td style="padding:8px 12px;font-weight:600">Invoice</td><td style="padding:8px 12px;font-family:monospace">' . htmlspecialchars($order->invoice_number) . '</td></tr>
            <tr><td style="padding:8px 12px;font-weight:600">Total</td><td style="padding:8px 12px;color:#059669;font-weight:700">Rp ' . number_format($order->grand_total, 0, ',', '.') . '</td></tr>
            <tr style="background:#f8fafc"><td style="padding:8px 12px;font-weight:600">Kamu Dapat</td><td style="padding:8px 12px;color:#4f46e5;font-weight:700">Rp ' . number_format($seller_revenue, 0, ',', '.') . '</td></tr>
        </table>
        <p style="font-size:.8rem;color:#94a3b8">Dana akan masuk ke saldo setelah settlement (3-5 hari kerja).</p>
    </div>
</div></body></html>';
                try {
                    send_mail($seller->email, '[' . $shop->name . '] Penjualan Baru — Rp ' . number_format($order->grand_total, 0, ',', '.'), $seller_html);
                } catch(\Exception $e) { /* silent */ }
            }
        }

        http_response_code(200);
        echo json_encode(['success' => true, 'fulfilled' => $fulfilled_content]);
        die();
    }

    private function build_delivery_email($order, $item, $customer, $shop, $fulfilled_content) {
        $delivery_html = '';

        if($item->type === 'download_link') {
            $links = json_decode($fulfilled_content, true) ?: [];
            $delivery_html = '<p><strong>Link Download:</strong></p><ul>';
            foreach($links as $link) {
                $delivery_html .= '<li><a href="' . htmlspecialchars($link) . '">' . htmlspecialchars($link) . '</a></li>';
            }
            $delivery_html .= '</ul>';
        } elseif($item->type === 'random_code') {
            $delivery_html = '<p><strong>Kode produk kamu:</strong></p><div style="background:#1e1b4b;color:#a5b4fc;padding:16px;border-radius:8px;font-size:1.4rem;font-family:monospace;letter-spacing:.1em;text-align:center">' . htmlspecialchars($fulfilled_content) . '</div><p style="font-size:.8rem;color:#64748b">Simpan kode ini — hanya dikirim sekali.</p>';
        } elseif($item->type === 'webhook_event') {
            $delivery_html = '<p>Pesanan kamu sedang diproses otomatis. Kamu akan dihubungi segera.</p>';
        } else {
            $delivery_html = '<p>Penjual akan segera memproses pesanan kamu secara manual.</p>';
        }

        return '<!DOCTYPE html><html><body style="font-family:Inter,sans-serif;background:#f8fafc;padding:20px">
<div style="max-width:520px;margin:0 auto;background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,.07)">
    <div style="background:linear-gradient(135deg,#4f46e5,#6366f1);padding:24px;text-align:center">
        <h1 style="color:#fff;font-size:1.3rem;margin:0">🎉 Pembayaran Berhasil!</h1>
    </div>
    <div style="padding:28px">
        <p>Halo <strong>' . htmlspecialchars($customer->full_name) . '</strong>,</p>
        <p>Terima kasih sudah membeli <strong>' . htmlspecialchars($item->name) . '</strong> di toko <strong>' . htmlspecialchars($shop->name) . '</strong>.</p>
        ' . $delivery_html . '
        <hr style="border:none;border-top:1px solid #e2e8f0;margin:20px 0">
        <p style="font-size:.8rem;color:#94a3b8">Invoice: ' . htmlspecialchars($order->invoice_number) . '<br>Total: Rp ' . number_format($order->grand_total, 0, ',', '.') . '</p>
        <a href="' . SITE_URL . 'store-checkout-success/' . htmlspecialchars($order->invoice_number) . '" style="display:inline-block;background:#4f46e5;color:#fff;padding:12px 24px;border-radius:10px;text-decoration:none;font-weight:700;margin-top:8px">Lihat Detail Pesanan</a>
    </div>
</div></body></html>';
    }
}
