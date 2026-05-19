<?php
/*
 * StoreCheckout Controller
 * Supports: digital, physical (with shipping via RajaOngkir)
 */

namespace Altum\Controllers;

use Altum\Title;
use Altum\Alerts;
use Altum\PaymentGateways\Tripay;

class StoreCheckout extends Controller {

    /* ── Shared fulfillment logic ── */
    private function fulfill_order($order_id, $item, $customer_id) {
        $fulfilled_content = null;
        $datetime = \Altum\Date::$date;

        if($item->type === 'download_link') {
            $links = json_decode($item->download_links ?? '[]', true) ?: [];
            $fulfilled_content = json_encode($links);

        } elseif($item->type === 'random_code') {
            $codes = json_decode($item->download_links ?? '[]', true) ?: [];
            if(!empty($codes)) {
                $code = array_shift($codes);
                $remaining = database()->real_escape_string(json_encode(array_values($codes)));
                database()->query("UPDATE `shop_items` SET `download_links` = '{$remaining}', `stock` = GREATEST(0, COALESCE(`stock`, 0) - 1) WHERE `id` = {$item->id}");
                $fulfilled_content = $code;
            } else {
                $fulfilled_content = 'OUT_OF_STOCK';
            }

        } elseif($item->type === 'webhook_event') {
            $fulfilled_content = 'webhook_pending';

        } elseif($item->type === 'manual') {
            $fulfilled_content = 'manual_pending';

        } elseif($item->type === 'physical') {
            $fulfilled_content = 'physical_pending'; // seller will update resi later
        }

        if($fulfilled_content !== null) {
            $fc = database()->real_escape_string($fulfilled_content);
            database()->query("UPDATE `shop_orders` SET `fulfilled_content` = '{$fc}' WHERE `id` = {$order_id}");
        }

        database()->query("UPDATE `shop_items` SET `sales` = `sales` + 1 WHERE `id` = {$item->id}");

        return $fulfilled_content;
    }

    /* ── Email pending payment ── */
    private function build_pending_email($invoice, $customer_name, $item_name, $shop_name, $grand_total, $checkout_url) {
        $success_url = SITE_URL . 'store-checkout-success/' . $invoice;
        $amount_fmt  = 'Rp ' . number_format($grand_total, 0, ',', '.');
        return '<!DOCTYPE html><html><body style="font-family:Inter,sans-serif;background:#f8fafc;padding:20px;margin:0">
<div style="max-width:520px;margin:0 auto;background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,.08)">
    <div style="background:linear-gradient(135deg,#f59e0b,#d97706);padding:28px;text-align:center">
        <div style="width:52px;height:52px;background:rgba(255,255,255,.2);border-radius:50%;display:inline-flex;align-items:center;justify-content:center;margin-bottom:12px">
            <svg style="width:24px;height:24px;fill:none;stroke:#fff;stroke-width:2;stroke-linecap:round;stroke-linejoin:round" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
        </div>
        <h1 style="color:#fff;font-size:1.15rem;margin:0">Menunggu Pembayaran</h1>
        <p style="color:rgba(255,255,255,.85);font-size:.82rem;margin:6px 0 0">Pesanan kamu belum dibayar</p>
    </div>
    <div style="padding:28px">
        <p style="color:#374151">Halo <strong>' . htmlspecialchars($customer_name) . '</strong>,</p>
        <p style="color:#374151">Kamu memiliki pesanan yang menunggu pembayaran di toko <strong>' . htmlspecialchars($shop_name) . '</strong>:</p>
        <div style="background:#f8fafc;border-radius:12px;padding:16px;margin:16px 0">
            <table style="width:100%;font-size:.85rem;color:#374151;border-collapse:collapse">
                <tr><td style="padding:6px 0;color:#6b7280">Produk</td><td style="text-align:right;font-weight:600">' . htmlspecialchars($item_name) . '</td></tr>
                <tr><td style="padding:6px 0;color:#6b7280">Invoice</td><td style="text-align:right;font-family:monospace;color:#4f46e5">' . htmlspecialchars($invoice) . '</td></tr>
                <tr style="border-top:1px solid #e5e7eb"><td style="padding:10px 0 0;font-weight:700">Total</td><td style="text-align:right;font-weight:800;color:#059669;font-size:1.05rem;padding-top:10px">' . $amount_fmt . '</td></tr>
            </table>
        </div>
        <a href="' . $checkout_url . '" style="display:block;background:#4f46e5;color:#fff;text-align:center;padding:14px;border-radius:12px;text-decoration:none;font-weight:700;font-size:.95rem;margin-bottom:12px">
            Bayar Sekarang
        </a>
        <a href="' . $success_url . '" style="display:block;background:#f1f5f9;color:#475569;text-align:center;padding:12px;border-radius:12px;text-decoration:none;font-size:.85rem">
            Lihat Detail Pesanan
        </a>
        <p style="font-size:.75rem;color:#94a3b8;margin:16px 0 0;text-align:center">Link pembayaran berlaku 24 jam sejak pesanan dibuat.</p>
    </div>
</div></body></html>';
    }

    public function index() {

        $item_id = isset($this->params[0]) ? (int) $this->params[0] : null;
        if(!$item_id) redirect();

        $item = database()->query("SELECT * FROM `shop_items` WHERE `id` = {$item_id} AND `status` = 1")->fetch_object() ?? null;
        if(!$item) redirect();

        $shop = database()->query("SELECT * FROM `shops` WHERE `id` = {$item->shop_id} AND `is_active` = 1")->fetch_object() ?? null;
        if(!$shop) redirect();

        /* ── Ensure payments.plan_id allows NULL for shop orders ── */
        try {
            database()->query("ALTER TABLE `payments` MODIFY `plan_id` INT NULL DEFAULT NULL");
        } catch(\Exception $e) {}

        /* ── Collect payment channels ── */
        $payment_channels = [];
        $primary_gateway  = null;

        $tripay_enabled = !empty(settings()->tripay->is_enabled);
        $tripay_key     = settings()->tripay->api_key     ?? null;
        $tripay_pkey    = settings()->tripay->private_key ?? null;
        $tripay_mc      = settings()->tripay->merchant_code ?? null;
        $tripay_mode    = settings()->tripay->mode ?? 'production';

        if($tripay_enabled && !empty($tripay_key)) {
            $raw_channels = \Altum\PaymentGateways\Tripay::get_channels();
            if(!empty($raw_channels)) {
                foreach($raw_channels as $c) { $c->_gateway = 'tripay'; $payment_channels[] = $c; }
                $primary_gateway = 'tripay';
            } else {
                $base = 'https://tripay.co.id/images/payment/icon/';
                $mk = function($code, $name, $group, $flat, $pct) use ($base) {
                    return (object)['code'=>$code,'name'=>$name,'group'=>$group,'icon_url'=>$base.$code.'.png','total_fee'=>(object)['flat'=>$flat,'percent'=>$pct],'_gateway'=>'tripay'];
                };
                $payment_channels = [
                    $mk('QRIS','QRIS (Semua E-Wallet)','QRIS',0,0.7), $mk('BRIVA','BRI Virtual Account','Virtual Account',4000,0),
                    $mk('BNIVA','BNI Virtual Account','Virtual Account',4000,0), $mk('MANDIRIVA','Mandiri Virtual Account','Virtual Account',4000,0),
                    $mk('BCAVA','BCA Virtual Account','Virtual Account',4000,0), $mk('DANA','DANA','E-Wallet',1000,0),
                    $mk('OVO','OVO','E-Wallet',1000,0), $mk('SHOPEEPAY','ShopeePay','E-Wallet',1000,0),
                    $mk('INDOMARET','Indomaret','Gerai',5000,0), $mk('ALFAMART','Alfamart','Gerai',5000,0),
                ];
                $primary_gateway = 'tripay';
            }
        }

        if(!empty(settings()->midtrans->is_enabled)) {
            $payment_channels[] = (object)['code'=>'MIDTRANS','name'=>'Midtrans (Semua Metode)','group'=>'Online Payment','icon_url'=>'https://api.midtrans.com/v2/assets/svg/brand/midtrans.svg','total_fee'=>(object)['flat'=>0,'percent'=>0],'_gateway'=>'midtrans'];
            if(!$primary_gateway) $primary_gateway = 'midtrans';
        }

        if(!empty(settings()->paypal->is_enabled)) {
            $payment_channels[] = (object)['code'=>'PAYPAL','name'=>'PayPal','group'=>'Online Payment','icon_url'=>'https://www.paypalobjects.com/webstatic/mktg/logo/pp_cc_mark_111x69.jpg','total_fee'=>(object)['flat'=>0,'percent'=>0],'_gateway'=>'paypal'];
            if(!$primary_gateway) $primary_gateway = 'paypal';
        }

        if(!empty(settings()->offline_payment->is_enabled)) {
            $payment_channels[] = (object)['code'=>'offline_payment','name'=>l('pay.custom_plan.offline_payment'),'group'=>'Manual Payment','icon_url'=>'','total_fee'=>(object)['flat'=>0,'percent'=>0],'_gateway'=>'offline_payment'];
            if(!$primary_gateway) $primary_gateway = 'offline_payment';
        }

        $is_demo = empty($primary_gateway);
        if($is_demo) {
            $base = 'https://tripay.co.id/images/payment/icon/';
            $mk = function($code,$name,$group,$flat,$pct) use ($base) {
                return (object)['code'=>$code,'name'=>$name,'group'=>$group,'icon_url'=>$base.$code.'.png','total_fee'=>(object)['flat'=>$flat,'percent'=>$pct],'_gateway'=>'demo'];
            };
            $payment_channels = [
                $mk('QRIS','QRIS (Semua E-Wallet)','QRIS',0,0.7),$mk('BRIVA','BRI Virtual Account','Virtual Account',4000,0),
                $mk('BNIVA','BNI Virtual Account','Virtual Account',4000,0),$mk('MANDIRIVA','Mandiri Virtual Account','Virtual Account',4000,0),
                $mk('BCAVA','BCA Virtual Account','Virtual Account',4000,0),$mk('DANA','DANA','E-Wallet',1000,0),
                $mk('OVO','OVO','E-Wallet',1000,0),$mk('SHOPEEPAY','ShopeePay','E-Wallet',1000,0),
                $mk('INDOMARET','Indomaret','Gerai',5000,0),$mk('ALFAMART','Alfamart','Gerai',5000,0),
            ];
            $primary_gateway = 'demo';
        }

        $get_qty = isset($_GET['qty']) ? (int) $_GET['qty'] : 1;
        if($get_qty < 1) $get_qty = 1;

        $base_item_price = (!empty($item->has_discount) && !empty($item->discount_price)) ? (float)$item->discount_price : (float)$item->price;
        $get_price = isset($_GET['price']) ? (float) $_GET['price'] : $base_item_price;
        if($get_price < $base_item_price) $get_price = $base_item_price;

        /* ── Handle POST ── */
        if(!empty($_POST)) {
            $email          = input_clean($_POST['email'] ?? '');
            $full_name      = input_clean($_POST['full_name'] ?? '');
            $phone          = input_clean($_POST['phone'] ?? '');
            $method         = input_clean($_POST['payment_method'] ?? ($payment_channels[0]->code ?? 'QRIS'));
            $voucher_code   = strtoupper(input_clean($_POST['voucher_code'] ?? ''));
            $qty            = $get_qty;

            /* Physical shipping inputs */
            $shipping_address = null;
            $shipping_courier = null;
            $shipping_service = null;
            $shipping_cost    = 0;
            $dest_city_id     = null;

            if($item->type === 'physical') {
                $shipping_address = input_clean($_POST['shipping_address'] ?? '');
                $shipping_courier = input_clean($_POST['shipping_courier'] ?? '');
                $shipping_service = input_clean($_POST['shipping_service'] ?? '');
                $shipping_cost    = (float)($_POST['shipping_cost'] ?? 0);
                $dest_city_id     = (int)($_POST['dest_city_id'] ?? 0);
                
                $shipping_province = input_clean($_POST['shipping_province'] ?? '');
                $shipping_city     = input_clean($_POST['shipping_city'] ?? '');
                if($shipping_province && $shipping_city) {
                    $shipping_address .= "\n" . $shipping_city . ", " . $shipping_province;
                }

                if(empty($shipping_address)) Alerts::add_error('Alamat pengiriman wajib diisi.');
                if(empty($shipping_courier)) Alerts::add_error('Pilih ekspedisi pengiriman.');
                if($shipping_cost <= 0)       Alerts::add_error('Pilih layanan ongkir terlebih dahulu.');
            }

            $base_total      = $get_price * $qty;
            $service_fee     = $base_total * 0.05;
            $discount_amount = 0;
            $voucher_id      = null;

            /* Voucher */
            if(!empty($voucher_code)) {
                $now = date('Y-m-d H:i:s');
                $voucher = database()->query("
                    SELECT * FROM `shop_vouchers`
                    WHERE `shop_id`={$shop->id}
                      AND `code`='" . database()->real_escape_string($voucher_code) . "'
                      AND `is_active`=1
                      AND (`valid_from` IS NULL OR `valid_from` <= '{$now}')
                      AND (`valid_to`   IS NULL OR `valid_to`   >= '{$now}')
                      AND (`item_id` IS NULL OR `item_id`={$item->id})
                ")->fetch_object() ?? null;

                if($voucher && ($voucher->is_unlimited || $voucher->quota === null || $voucher->used < $voucher->quota)) {
                    $discount_amount = round($base_total * $voucher->discount_percentage / 100);
                    $voucher_id      = $voucher->id;
                } else {
                    Alerts::add_error('Voucher tidak valid, sudah kadaluarsa, atau kuota habis.');
                }
            }

            $grand_total    = max(0, $base_total - $discount_amount) + $shipping_cost;
            $invoice_number = 'INV-SHOP-' . strtoupper(substr(md5(uniqid()), 0, 10));

            $offline_payment_proof_file = null;
            if($method === 'offline_payment') {
                $offline_payment_proof_provided = !empty($_FILES['offline_payment_proof']['name']);
                if(!$offline_payment_proof_provided) {
                    Alerts::add_error(l('pay.error_message.offline_payment_proof_missing'));
                } else {
                    $offline_payment_proof_file = \Altum\Uploads::process_upload(null, 'offline_payment_proofs', 'offline_payment_proof', 'offline_payment_proof_remove', settings()->offline_payment->proof_size_limit);
                    if(Alerts::has_field_errors('offline_payment_proof')) Alerts::add_error(Alerts::output_field_error('offline_payment_proof'));
                }
            }

            if(!Alerts::has_errors()) {
                /* Customer */
                $customer = database()->query("SELECT `id` FROM `shop_customers`
                    WHERE `shop_id` = {$shop->id} AND `email` = '" . database()->real_escape_string($email) . "'"
                )->fetch_object() ?? null;

                if(!$customer) {
                    $stmt = database()->prepare("INSERT INTO `shop_customers` (`shop_id`, `email`, `full_name`, `phone`) VALUES (?, ?, ?, ?)");
                    $stmt->bind_param('isss', $shop->id, $email, $full_name, $phone);
                    $stmt->execute();
                    $customer_id = $stmt->insert_id;
                    $stmt->close();
                } else {
                    $customer_id = $customer->id;
                }

                /* Order */
                $datetime  = \Altum\Date::$date;
                $processor = $primary_gateway;
                $sa_esc    = database()->real_escape_string($shipping_address ?? '');
                $sc_esc    = database()->real_escape_string($shipping_courier ?? '');
                $ss_esc    = database()->real_escape_string($shipping_service ?? '');

                $stmt = database()->prepare("INSERT INTO `shop_orders`
                    (`shop_id`, `item_id`, `customer_id`, `invoice_number`, `qty`,
                     `total_amount`, `service_fee`, `grand_total`, `discount_amount`, `voucher_id`,
                     `shipping_address`, `shipping_courier`, `shipping_service`, `shipping_cost`,
                     `payment_processor`, `payment_proof`, `status`, `datetime`)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', ?)");
                $stmt->bind_param(
                    'iiisiddddisssdsss',
                    $shop->id, $item->id, $customer_id,
                    $invoice_number, $qty,
                    $base_total, $service_fee, $grand_total, $discount_amount, $voucher_id,
                    $sa_esc, $sc_esc, $ss_esc, $shipping_cost,
                    $method, $offline_payment_proof_file, $datetime
                );
                $stmt->execute();
                $order_id = $stmt->insert_id;
                $stmt->close();

                if($voucher_id) {
                    database()->query("UPDATE `shop_vouchers` SET `used` = `used` + 1 WHERE `id` = {$voucher_id}");
                }

                /* ── Gateway routing ── */
                if($method === 'offline_payment') {
                    try {
                        $pending_email = $this->build_pending_email($invoice_number, $full_name, $item->name, $shop->name, $grand_total, SITE_URL . 'store-checkout-success/' . $invoice_number);
                        send_mail($email, 'Selesaikan pembayaran manual - ' . $invoice_number, $pending_email);
                    } catch(\Exception $e) {}
                    
                    // Insert to global payments for admin to approve
                    $payment_code = 'shop_order_' . $order_id;
                    $ins_result = database()->query("INSERT INTO `payments` (`user_id`, `plan_id`, `processor`, `type`, `frequency`, `email`, `name`, `total_amount`, `currency`, `payment_proof`, `code`, `status`, `datetime`) VALUES (" . (int)$shop->user_id . ", NULL, 'offline_payment', 'one_time', 'lifetime', '" . database()->real_escape_string($email) . "', '" . database()->real_escape_string($full_name) . "', " . (float)$grand_total . ", '" . settings()->payment->default_currency . "', '" . database()->real_escape_string($offline_payment_proof_file) . "', '" . database()->real_escape_string($payment_code) . "', 'pending', '{$datetime}')");
                    if(!$ins_result) {
                        error_log('[SHOP_OFFLINE_PAYMENT] INSERT payments FAILED. Error: ' . database()->error . ' | code=' . $payment_code . ' | user_id=' . $shop->user_id);
                    }
                    
                    redirect('store-checkout-success/' . $invoice_number);
                    
                } elseif($is_demo || $method === 'demo') {
                    database()->query("UPDATE `shop_orders` SET `status` = 'paid', `settle_status` = 'unsettled', `paid_date` = '{$datetime}' WHERE `id` = {$order_id}");
                    database()->query("UPDATE `shop_customers` SET `total_orders` = `total_orders` + 1, `total_spent` = `total_spent` + {$grand_total} WHERE `id` = {$customer_id}");
                    $seller_revenue = $grand_total - $service_fee;
                    database()->query("UPDATE `users` SET `pending_funds` = `pending_funds` + {$seller_revenue} WHERE `user_id` = {$shop->user_id}");
                    $this->fulfill_order($order_id, $item, $customer_id);
                    redirect('store-checkout-success/' . $invoice_number);

                } elseif($primary_gateway === 'tripay' && $method !== 'MIDTRANS') {
                    $tripay_order_items = [
                        [
                            'sku'      => 'ITEM-' . $item->id,
                            'name'     => $item->name . ($qty > 1 ? ' (x' . $qty . ')' : ''),
                            'price'    => (int) max(0, $base_total - $discount_amount),
                            'quantity' => 1
                        ]
                    ];

                    if($shipping_cost > 0) {
                        $tripay_order_items[] = [
                            'sku'      => 'SHIP',
                            'name'     => 'Ongkos Kirim (' . strtoupper($shipping_courier) . ')',
                            'price'    => (int) $shipping_cost,
                            'quantity' => 1
                        ];
                    }

                    $payload = [
                        'method'         => $method,
                        'merchant_ref'   => $invoice_number,
                        'amount'         => (int) $grand_total,
                        'customer_name'  => $full_name,
                        'customer_email' => $email,
                        'customer_phone' => $phone,
                        'order_items'    => $tripay_order_items,
                        'return_url'     => SITE_URL . 'store-checkout-success/' . $invoice_number,
                        'callback_url'   => SITE_URL . 'webhook-tripay',
                        'expired_time'   => (time() + (24 * 60 * 60)),
                        'signature'      => hash_hmac('sha256', $tripay_mc . $invoice_number . (int)$grand_total, $tripay_pkey),
                    ];
                    $api_create = ($tripay_mode === 'sandbox')
                        ? 'https://tripay.co.id/api-sandbox/transaction/create'
                        : 'https://tripay.co.id/api/transaction/create';
                    $curl = curl_init();
                    curl_setopt_array($curl, [
                        CURLOPT_FRESH_CONNECT  => true,
                        CURLOPT_URL            => $api_create,
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_HEADER         => false,
                        CURLOPT_HTTPHEADER     => ['Authorization: Bearer ' . $tripay_key],
                        CURLOPT_POST           => true,
                        CURLOPT_POSTFIELDS     => http_build_query($payload),
                        CURLOPT_IPRESOLVE      => CURL_IPRESOLVE_V4,
                    ]);
                    $response     = curl_exec($curl);
                    curl_close($curl);
                    $response_obj = json_decode($response);

                    if(isset($response_obj->success) && $response_obj->success == true) {
                        $checkout_url = $response_obj->data->checkout_url ?? '';
                        $tripay_ref   = $response_obj->data->reference ?? '';
                        $co_url_esc   = database()->real_escape_string($checkout_url);
                        database()->query("UPDATE `shop_orders` SET
                            `payment_id`   = '" . database()->real_escape_string($tripay_ref) . "',
                            `checkout_url` = '{$co_url_esc}'
                            WHERE `id` = {$order_id}");

                        try {
                            $pending_email = $this->build_pending_email($invoice_number, $full_name, $item->name, $shop->name, $grand_total, $checkout_url);
                            send_mail($email, 'Selesaikan pembayaran - ' . $invoice_number, $pending_email);
                        } catch(\Exception $e) {}

                        header('Location: ' . $checkout_url);
                        exit;
                    } else {
                        Alerts::add_error('Tripay Error: ' . ($response_obj->message ?? 'Unknown error'));
                    }

                } elseif($method === 'MIDTRANS') {
                    /* Midtrans Payment Link for shop orders */
                    $midtrans_api_url = (settings()->midtrans->mode == 'sandbox')
                        ? 'https://app.sandbox.midtrans.com/v1/payment-links'
                        : 'https://app.midtrans.com/v1/payment-links';

                    $midtrans_custom_field = 'shop_order_' . $order_id;
                    $midtrans_payload = [
                        'transaction_details' => [
                            'order_id'    => $invoice_number,
                            'gross_amount' => (int) ceil($grand_total),
                        ],
                        'expiry' => [
                            'duration' => 1,
                            'unit'     => 'days',
                        ],
                        'enabled_payments' => [
                            'credit_card',
                            'bca_va',
                            'bni_va',
                            'bri_va',
                            'permata_va',
                            'echannel',
                            'gopay',
                            'shopeepay',
                            'qris',
                            'akulaku',
                        ],
                        'item_details' => [[
                            'price'    => (int) ceil($grand_total),
                            'quantity' => 1,
                            'name'     => substr($item->name, 0, 50) . ($qty > 1 ? ' x' . $qty : ''),
                        ]],
                        'customer_details' => [
                            'first_name' => $full_name,
                            'email'      => $email,
                            'phone'      => $phone ?: '-',
                        ],
                        'callbacks' => [
                            'finish' => SITE_URL . 'store-checkout-success/' . $invoice_number,
                        ],
                        'custom_field1' => $midtrans_custom_field,
                    ];

                    $midtrans_ch = curl_init($midtrans_api_url);
                    curl_setopt_array($midtrans_ch, [
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_POST           => true,
                        CURLOPT_POSTFIELDS     => json_encode($midtrans_payload),
                        CURLOPT_HTTPHEADER     => [
                            'Content-Type: application/json',
                            'Accept: application/json',
                            'Authorization: Basic ' . base64_encode(settings()->midtrans->server_key . ':'),
                            'X-Override-Notification: ' . SITE_URL . 'webhook-midtrans-shop',
                        ],
                        CURLOPT_TIMEOUT        => 30,
                    ]);
                    $midtrans_raw_response = curl_exec($midtrans_ch);
                    $midtrans_http_code    = curl_getinfo($midtrans_ch, CURLINFO_HTTP_CODE);
                    curl_close($midtrans_ch);

                    $midtrans_response_obj = json_decode($midtrans_raw_response);

                    if($midtrans_http_code < 400 && isset($midtrans_response_obj->payment_url)) {
                        $midtrans_payment_url = $midtrans_response_obj->payment_url;
                        $co_url_esc = database()->real_escape_string($midtrans_payment_url);
                        database()->query("UPDATE `shop_orders` SET
                            `payment_id`   = '" . database()->real_escape_string($invoice_number) . "',
                            `checkout_url` = '{$co_url_esc}'
                            WHERE `id` = {$order_id}");

                        try {
                            $pending_email = $this->build_pending_email($invoice_number, $full_name, $item->name, $shop->name, $grand_total, $midtrans_payment_url);
                            send_mail($email, 'Selesaikan pembayaran - ' . $invoice_number, $pending_email);
                        } catch(\Exception $e) {}

                        header('Location: ' . $midtrans_payment_url);
                        exit;
                    } else {
                        $midtrans_error = $midtrans_response_obj->error_messages[0] ?? ($midtrans_response_obj->message ?? 'Midtrans error. Coba lagi.');
                        Alerts::add_error('Midtrans: ' . $midtrans_error);
                    }

                } else {
                    Alerts::add_error('Metode pembayaran belum didukung untuk toko ini.');
                }
            }
        }

        Title::set('Checkout - ' . $item->name);

        $data = [
            'shop'             => $shop,
            'item'             => $item,
            'qty'              => $get_qty,
            'price'            => $get_price,
            'base_total'       => $get_price * $get_qty,
            'payment_channels' => $payment_channels,
            'primary_gateway'  => $primary_gateway,
            'is_demo'          => $is_demo,
            'payment_method'   => $method ?? null,
        ];

        $view = new \Altum\View('store_checkout/index', (array) $this);
        $this->add_view_content('content', $view->run($data));
    }
}
