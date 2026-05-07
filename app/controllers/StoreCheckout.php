<?php
/*
 * Store Checkout Controller
 */

namespace Altum\Controllers;

use Altum\Title;
use Altum\Alerts;

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
        }

        if($fulfilled_content !== null) {
            $fc = database()->real_escape_string($fulfilled_content);
            database()->query("UPDATE `shop_orders` SET `fulfilled_content` = '{$fc}' WHERE `id` = {$order_id}");
        }

        database()->query("UPDATE `shop_items` SET `sales` = `sales` + 1 WHERE `id` = {$item->id}");

        return $fulfilled_content;
    }

    public function index() {

        $item_id = isset($this->params[0]) ? (int) $this->params[0] : null;
        if(!$item_id) redirect();

        $item = database()->query("SELECT * FROM `shop_items` WHERE `id` = {$item_id} AND `status` = 1")->fetch_object() ?? null;
        if(!$item) redirect();

        $shop = database()->query("SELECT * FROM `shops` WHERE `id` = {$item->shop_id} AND `is_active` = 1")->fetch_object() ?? null;
        if(!$shop) redirect();

        /* ── Collect payment channels dari gateway yang aktif ── */
        $payment_channels = [];
        $primary_gateway  = null;

        /* 1. Tripay */
        $tripay_enabled = !empty(settings()->tripay->is_enabled);
        $tripay_key     = settings()->tripay->api_key     ?? null;
        $tripay_pkey    = settings()->tripay->private_key ?? null;
        $tripay_mc      = settings()->tripay->merchant_code ?? null;
        $tripay_mode    = settings()->tripay->mode ?? 'production';

        if($tripay_enabled && !empty($tripay_key)) {
            $api_url = ($tripay_mode === 'sandbox')
                ? 'https://tripay.co.id/api-sandbox/payment/channel'
                : 'https://tripay.co.id/api/payment/channel';
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_FRESH_CONNECT  => true,
                CURLOPT_URL            => $api_url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HEADER         => false,
                CURLOPT_HTTPHEADER     => ['Authorization: Bearer ' . $tripay_key],
                CURLOPT_IPRESOLVE      => CURL_IPRESOLVE_V4,
                CURLOPT_TIMEOUT        => 8,
            ]);
            $res     = curl_exec($ch);
            curl_close($ch);
            $res_obj = json_decode($res);
            if(isset($res_obj->success) && $res_obj->success && !empty($res_obj->data)) {
                foreach($res_obj->data as $c) {
                    $c->_gateway = 'tripay';
                    $payment_channels[] = $c;
                }
                $primary_gateway = 'tripay';
            }
        }

        /* 2. Midtrans */
        if(!empty(settings()->midtrans->is_enabled) && $primary_gateway !== 'tripay') {
            $payment_channels[] = (object)[
                'code'      => 'MIDTRANS',
                'name'      => 'Midtrans (Semua Metode)',
                'group'     => 'Online Payment',
                'icon_url'  => 'https://api.midtrans.com/v2/assets/svg/brand/midtrans.svg',
                'total_fee' => (object)['flat' => 0, 'percent' => 0],
                '_gateway'  => 'midtrans',
            ];
            if(!$primary_gateway) $primary_gateway = 'midtrans';
        }

        /* 3. PayPal */
        if(!empty(settings()->paypal->is_enabled)) {
            $payment_channels[] = (object)[
                'code'      => 'PAYPAL',
                'name'      => 'PayPal',
                'group'     => 'Online Payment',
                'icon_url'  => 'https://www.paypalobjects.com/webstatic/mktg/logo/pp_cc_mark_111x69.jpg',
                'total_fee' => (object)['flat' => 0, 'percent' => 0],
                '_gateway'  => 'paypal',
            ];
            if(!$primary_gateway) $primary_gateway = 'paypal';
        }

        /* Tidak ada manual/offline — hanya gunakan gateway yang diaktifkan admin */
        /* Demo fallback jika tidak ada gateway aktif */
        $is_demo = empty($primary_gateway);
        if($is_demo) {
            $base = 'https://tripay.co.id/images/payment/icon/';
            $mk   = function($code, $name, $group, $flat, $pct) use ($base) {
                return (object)['code' => $code, 'name' => $name, 'group' => $group,
                    'icon_url' => $base . $code . '.png',
                    'total_fee' => (object)['flat' => $flat, 'percent' => $pct],
                    '_gateway' => 'demo'];
            };
            $payment_channels = [
                $mk('QRIS',      'QRIS (Semua E-Wallet)',   'QRIS',            0,    0.7),
                $mk('BRIVA',     'BRI Virtual Account',     'Virtual Account', 4000, 0),
                $mk('BNIVA',     'BNI Virtual Account',     'Virtual Account', 4000, 0),
                $mk('MANDIRIVA', 'Mandiri Virtual Account', 'Virtual Account', 4000, 0),
                $mk('BCAVA',     'BCA Virtual Account',     'Virtual Account', 4000, 0),
                $mk('DANA',      'DANA',                    'E-Wallet',        1000, 0),
                $mk('OVO',       'OVO',                     'E-Wallet',        1000, 0),
                $mk('SHOPEEPAY', 'ShopeePay',               'E-Wallet',        1000, 0),
                $mk('INDOMARET', 'Indomaret',               'Gerai',           5000, 0),
                $mk('ALFAMART',  'Alfamart',                'Gerai',           5000, 0),
            ];
            $primary_gateway = 'demo';
        }

        $offline_instructions = '';

        /* ── Handle POST ── */
        if(!empty($_POST)) {
            $email        = input_clean($_POST['email']);
            $full_name    = input_clean($_POST['full_name']);
            $phone        = input_clean($_POST['phone']);
            $method       = input_clean($_POST['payment_method'] ?? ($payment_channels[0]->code ?? 'QRIS'));
            $voucher_code = strtoupper(input_clean($_POST['voucher_code'] ?? ''));
            $qty          = 1;

            $service_fee     = $item->price * 0.05;
            $grand_total     = (float)$item->price;
            $discount_amount = 0;
            $voucher_id      = null;

            /* Validasi dan terapkan voucher */
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
                    $discount_amount = round($item->price * $voucher->discount_percentage / 100);
                    $grand_total     = max(0, $item->price - $discount_amount);
                    $voucher_id      = $voucher->id;
                } else {
                    Alerts::add_error('Voucher tidak valid, sudah kadaluarsa, atau kuota habis.');
                }
            }

            $invoice_number = 'INV-SHOP-' . strtoupper(substr(md5(uniqid()), 0, 10));

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

                /* Order — include voucher_id */
                $datetime  = \Altum\Date::$date;
                $processor = $primary_gateway;
                /* Check if voucher_id column exists (graceful fallback) */
                $stmt = database()->prepare("INSERT INTO `shop_orders`
                    (`shop_id`, `item_id`, `customer_id`, `invoice_number`, `qty`,
                     `total_amount`, `service_fee`, `grand_total`, `payment_processor`, `status`, `datetime`)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', ?)");
                $stmt->bind_param('iiisidddss', $shop->id, $item->id, $customer_id,
                    $invoice_number, $qty, $item->price, $service_fee, $grand_total, $processor, $datetime);
                $stmt->execute();
                $order_id = $stmt->insert_id;
                $stmt->close();

                /* Store voucher ref & discount on order */
                if($voucher_id) {
                    database()->query("UPDATE `shop_vouchers` SET `used` = `used` + 1 WHERE `id` = {$voucher_id}");
                }

                /* ── Gateway routing ── */
                if($is_demo || $primary_gateway === 'demo') {
                    database()->query("UPDATE `shop_orders` SET `status` = 'paid', `settle_status` = 'unsettled', `paid_date` = '{$datetime}' WHERE `id` = {$order_id}");
                    database()->query("UPDATE `shop_customers` SET `total_orders` = `total_orders` + 1, `total_spent` = `total_spent` + {$grand_total} WHERE `id` = {$customer_id}");
                    $seller_revenue = $grand_total - $service_fee;
                    database()->query("UPDATE `users` SET `pending_funds` = `pending_funds` + {$seller_revenue} WHERE `user_id` = {$shop->user_id}");

                    $this->fulfill_order($order_id, $item, $customer_id);
                    redirect('store-checkout-success/' . $invoice_number);

                } elseif($primary_gateway === 'tripay') {
                    $payload = [
                        'method'         => $method,
                        'merchant_ref'   => $invoice_number,
                        'amount'         => (int) $grand_total,
                        'customer_name'  => $full_name,
                        'customer_email' => $email,
                        'customer_phone' => $phone,
                        'order_items'    => [['sku' => 'ITEM-'.$item->id, 'name' => $item->name, 'price' => (int)$item->price, 'quantity' => $qty]],
                        'return_url'     => SITE_URL . 'store-checkout-success/' . $invoice_number,
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
                        database()->query("UPDATE `shop_orders` SET `payment_id` = '" . database()->real_escape_string($response_obj->data->reference) . "' WHERE `id` = {$order_id}");
                        header('Location: ' . $response_obj->data->checkout_url);
                        exit;
                    } else {
                        Alerts::add_error('Tripay Error: ' . ($response_obj->message ?? 'Unknown error'));
                    }

                } else {
                    Alerts::add_error('Metode pembayaran belum didukung untuk toko ini.');
                }

            } // end if !Alerts::has_errors()
        }

        Title::set('Checkout - ' . $item->name);

        $data = [
            'shop'                 => $shop,
            'item'                 => $item,
            'payment_channels'     => $payment_channels,
            'primary_gateway'      => $primary_gateway,
            'is_demo'              => $is_demo,
            'offline_instructions' => $offline_instructions,
        ];

        $view = new \Altum\View('store_checkout/index', (array) $this);
        $this->add_view_content('content', $view->run($data));
    }
}
