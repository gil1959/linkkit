<?php
/*
 * WebhookMidtransShop Controller
 *
 * Handles Midtrans payment notifications for shop orders (single & cart).
 * Route: /webhook-midtrans-shop
 *
 * Lookup orders via order_id (= invoice_number, prefix INV-SHOP-)
 * sehingga handle single-item dan cart checkout sekaligus.
 */

namespace Altum\Controllers;

defined('ALTUMCODE') || die();

class WebhookMidtransShop extends Controller {

    public function index() {

        header('Cache-Control: no-store');

        if(strtoupper($_SERVER['REQUEST_METHOD']) !== 'POST') {
            throw_404();
        }

        /* Get raw payload */
        $payload = trim(@file_get_contents('php://input'));

        /* Log for debugging */
        debug_log('[' . \Altum\Router::$controller . '] ' . print_r(['payload' => $payload], true));

        $data = json_decode($payload, true);

        if(!$data) {
            http_response_code(400);
            die('INVALID_PAYLOAD');
        }

        /* Only process capture or settlement */
        if(!in_array($data['transaction_status'] ?? '', ['capture', 'settlement'])) {
            http_response_code(200);
            die('IGNORED');
        }

        /* Reject if fraud */
        if(isset($data['fraud_status']) && $data['fraud_status'] !== 'accept') {
            http_response_code(200);
            die('FRAUD');
        }

        /* Verify Midtrans signature */
        $expected_sig = hash('sha512',
            $data['order_id'] .
            $data['status_code'] .
            $data['gross_amount'] .
            settings()->midtrans->server_key
        );
        if($data['signature_key'] !== $expected_sig) {
            http_response_code(400);
            die('INVALID_SIGNATURE');
        }

        /* order_id dari Midtrans = invoice_number yang kita set saat create payment link */
        $invoice_number = $data['order_id'] ?? '';

        if(strpos($invoice_number, 'INV-SHOP-') !== 0) {
            http_response_code(400);
            die('NOT_SHOP_ORDER');
        }

        $datetime = \Altum\Date::$date;
        $midtrans_transaction_id = $data['transaction_id'] ?? '';

        /* Ambil SEMUA orders dengan invoice ini (cart = multi-order, single = 1 order) */
        $orders = [];
        $result = database()->query("SELECT * FROM `shop_orders`
            WHERE `invoice_number` = '" . database()->real_escape_string($invoice_number) . "'"
        );
        while($row = $result->fetch_object()) {
            $orders[] = $row;
        }

        if(empty($orders)) {
            http_response_code(404);
            die('ORDER_NOT_FOUND');
        }

        /* Idempotency — cek semua sudah paid */
        $all_paid = true;
        foreach($orders as $o) {
            if($o->status !== 'paid') { $all_paid = false; break; }
        }
        if($all_paid) {
            http_response_code(200);
            die('ALREADY_PAID');
        }

        /* Customer dari order pertama */
        $first_order = $orders[0];
        $customer = database()->query("SELECT * FROM `shop_customers` WHERE `id` = {$first_order->customer_id}")->fetch_object();
        if(!$customer) {
            http_response_code(500);
            die('MISSING_DATA');
        }

        /* Akumulasi per shop */
        $shop_revenue_map = [];
        $total_paid = 0;
        $fulfilled_items = [];

        foreach($orders as $order) {
            if($order->status === 'paid') continue;

            $shop = database()->query("SELECT * FROM `shops`      WHERE `id` = {$order->shop_id}")->fetch_object();
            $item = database()->query("SELECT * FROM `shop_items` WHERE `id` = {$order->item_id}")->fetch_object();

            if(!$shop || !$item) continue;

            /* Update order → paid */
            database()->query("UPDATE `shop_orders` SET
                `status`        = 'paid',
                `settle_status` = 'unsettled',
                `payment_id`    = '" . database()->real_escape_string($midtrans_transaction_id) . "',
                `paid_date`     = '{$datetime}'
                WHERE `id` = {$order->id}");

            $seller_revenue = $order->grand_total - $order->service_fee;
            if(!isset($shop_revenue_map[$shop->user_id])) $shop_revenue_map[$shop->user_id] = 0;
            $shop_revenue_map[$shop->user_id] += $seller_revenue;
            $total_paid += $order->grand_total;

            /* ── Fulfillment ── */
            $fulfilled_content = null;

            if($item->type === 'download_link') {
                $links = json_decode($item->download_links ?? '[]', true) ?: [];
                $fulfilled_content = json_encode($links);

            } elseif($item->type === 'random_code') {
                $codes = json_decode($item->download_links ?? '[]', true) ?: [];
                if(!empty($codes)) {
                    $code = array_shift($codes);
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
                $webhook_url = $item->webhook_url ?? $shop->global_webhook_url ?? null;
                if($webhook_url) {
                    $wh_payload = json_encode([
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
                        CURLOPT_POSTFIELDS     => $wh_payload,
                        CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
                        CURLOPT_TIMEOUT        => 8,
                        CURLOPT_IPRESOLVE      => CURL_IPRESOLVE_V4,
                    ]);
                    $wh_response  = curl_exec($ch);
                    $wh_http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    curl_close($ch);

                    $payload_esc = database()->real_escape_string($wh_payload);
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

            } elseif($item->type === 'physical') {
                $fulfilled_content = 'physical_pending';
            }

            if($fulfilled_content !== null) {
                $fc_esc = database()->real_escape_string($fulfilled_content);
                database()->query("UPDATE `shop_orders` SET `fulfilled_content` = '{$fc_esc}' WHERE `id` = {$order->id}");
            }

            database()->query("UPDATE `shop_items` SET `sales` = `sales` + 1 WHERE `id` = {$item->id}");

            $fulfilled_items[] = [
                'item'      => $item,
                'order'     => $order,
                'shop'      => $shop,
                'fulfilled' => $fulfilled_content,
            ];
        }

        /* Update customer stats */
        if($total_paid > 0) {
            database()->query("UPDATE `shop_customers` SET
                `total_orders` = `total_orders` + " . count($fulfilled_items) . ",
                `total_spent`  = `total_spent`  + {$total_paid}
                WHERE `id` = {$customer->id}");
        }

        /* Kredit saldo penjual ke pending_funds per shop */
        foreach($shop_revenue_map as $seller_user_id => $seller_rev) {
            database()->query("UPDATE `users` SET
                `pending_funds` = `pending_funds` + {$seller_rev}
                WHERE `user_id` = {$seller_user_id}");
        }

        /* Email ke pembeli */
        if(!empty($fulfilled_items)) {
            try {
                $email_html = $this->build_delivery_email($invoice_number, $customer, $fulfilled_items, $total_paid);
                send_mail($customer->email, 'Pembayaran Berhasil - ' . $invoice_number, $email_html);
            } catch(\Exception $e) { /* silent */ }

            /* Email notif ke seller per shop */
            $shops_notified = [];
            foreach($fulfilled_items as $fi) {
                $shop_uid = $fi['shop']->user_id;
                if(in_array($shop_uid, $shops_notified)) continue;
                $notif = json_decode($fi['shop']->notification_settings ?? '{}', true);
                if(!empty($notif['purchase_success'])) {
                    $seller = database()->query("SELECT `email`, `name` FROM `users` WHERE `user_id` = {$shop_uid}")->fetch_object();
                    if($seller) {
                        $s_rev = $shop_revenue_map[$shop_uid] ?? 0;
                        $seller_html = '<!DOCTYPE html><html><body style="font-family:Inter,sans-serif;background:#f8fafc;padding:20px">
<div style="max-width:520px;margin:0 auto;background:#fff;border-radius:16px;overflow:hidden">
    <div style="background:linear-gradient(135deg,#059669,#10b981);padding:24px;text-align:center">
        <h1 style="color:#fff;font-size:1.2rem;margin:0">Penjualan Baru via Midtrans!</h1>
    </div>
    <div style="padding:28px">
        <p>Halo <strong>' . htmlspecialchars($seller->name) . '</strong>,</p>
        <table style="width:100%;border-collapse:collapse;font-size:.88rem;margin:16px 0">
            <tr style="background:#f8fafc"><td style="padding:8px 12px;font-weight:600">Invoice</td><td style="font-family:monospace">' . htmlspecialchars($invoice_number) . '</td></tr>
            <tr><td style="padding:8px 12px;font-weight:600">Pembeli</td><td>' . htmlspecialchars($customer->full_name) . ' (' . htmlspecialchars($customer->email) . ')</td></tr>
            <tr style="background:#f8fafc"><td style="padding:8px 12px;font-weight:600">Kamu Dapat</td><td style="color:#4f46e5;font-weight:700">Rp ' . number_format($s_rev, 0, ',', '.') . '</td></tr>
        </table>
    </div>
</div></body></html>';
                        try {
                            send_mail($seller->email, '[' . $fi['shop']->name . '] Penjualan Baru (Midtrans) — Rp ' . number_format($s_rev, 0, ',', '.'), $seller_html);
                        } catch(\Exception $e) { /* silent */ }
                    }
                }
                $shops_notified[] = $shop_uid;
            }
        }

        http_response_code(200);
        echo json_encode(['success' => true, 'type' => 'shop_midtrans', 'orders_processed' => count($fulfilled_items)]);
        die();
    }

    private function build_delivery_email($invoice_number, $customer, $fulfilled_items, $total_paid) {
        $items_html = '';
        foreach($fulfilled_items as $fi) {
            $item      = $fi['item'];
            $order     = $fi['order'];
            $fulfilled = $fi['fulfilled'];
            $dl_snippet = '';

            if($item->type === 'download_link' && $fulfilled) {
                $links = json_decode($fulfilled, true) ?: [];
                $dl_snippet = '<ul style="margin:4px 0 0;padding-left:18px">';
                foreach($links as $link) {
                    $dl_snippet .= '<li><a href="' . htmlspecialchars($link) . '" style="color:#4f46e5">' . htmlspecialchars($link) . '</a></li>';
                }
                $dl_snippet .= '</ul>';
            } elseif($item->type === 'random_code' && $fulfilled && $fulfilled !== 'OUT_OF_STOCK') {
                $dl_snippet = '<div style="background:#1e1b4b;color:#a5b4fc;padding:8px 14px;border-radius:6px;font-family:monospace;font-size:1.1rem;margin-top:4px;display:inline-block">' . htmlspecialchars($fulfilled) . '</div>';
            } elseif($item->type === 'physical') {
                $dl_snippet = '<span style="color:#64748b;font-size:.82rem">Pengiriman fisik — resi akan dikirim via email.</span>';
            } else {
                $dl_snippet = '<span style="color:#64748b;font-size:.82rem">Penjual akan segera memproses pesanan ini.</span>';
            }

            $items_html .= '
<tr style="border-bottom:1px solid #f1f5f9">
    <td style="padding:12px 0;vertical-align:top">
        <div style="font-weight:600;font-size:.88rem">' . htmlspecialchars($item->name) . '</div>
        <div style="font-size:.78rem;color:#64748b">Qty: ' . $order->qty . ' · Rp ' . number_format($order->grand_total, 0, ',', '.') . '</div>
        ' . $dl_snippet . '
    </td>
</tr>';
        }

        return '<!DOCTYPE html><html><body style="font-family:Inter,sans-serif;background:#f8fafc;padding:20px;margin:0">
<div style="max-width:580px;margin:0 auto;background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,.07)">
    <div style="background:linear-gradient(135deg,#4f46e5,#6366f1);padding:24px;text-align:center">
        <h1 style="color:#fff;font-size:1.3rem;margin:0">Pembayaran Berhasil!</h1>
    </div>
    <div style="padding:28px">
        <p>Halo <strong>' . htmlspecialchars($customer->full_name) . '</strong>,</p>
        <p>Terima kasih atas pembelianmu. Berikut detail pesananmu:</p>
        <table style="width:100%;border-collapse:collapse">
            ' . $items_html . '
        </table>
        <hr style="border:none;border-top:1px solid #e2e8f0;margin:20px 0">
        <div style="display:flex;justify-content:space-between;font-weight:800;font-size:1rem">
            <span>Total Bayar</span>
            <span style="color:#4f46e5">Rp ' . number_format($total_paid, 0, ',', '.') . '</span>
        </div>
        <p style="font-size:.78rem;color:#94a3b8;margin:16px 0 0">Invoice: ' . htmlspecialchars($invoice_number) . '</p>
        <a href="' . SITE_URL . 'store-checkout-success/' . htmlspecialchars($invoice_number) . '" style="display:inline-block;background:#4f46e5;color:#fff;padding:12px 24px;border-radius:10px;text-decoration:none;font-weight:700;margin-top:8px">Lihat Detail Pesanan</a>
    </div>
</div></body></html>';
    }
}
