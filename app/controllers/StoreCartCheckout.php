<?php
/*
 * StoreCartCheckout Controller
 * Handles multi-item cart checkout via POST (secure, prices from DB only)
 */

namespace Altum\Controllers;

use Altum\Title;
use Altum\Alerts;

class StoreCartCheckout extends Controller {

    public function index() {

        /* Must be POST */
        if (empty($_POST)) {
            redirect();
        }

        /* CSRF check */
        if (!\Altum\Csrf::check()) {
            Alerts::add_error('Invalid token.');
            redirect();
        }

        /* Get shop */
        $shop_url = input_clean($_POST['shop_url'] ?? '');
        if (empty($shop_url)) redirect();

        $shop = database()->query("SELECT * FROM `shops` WHERE `url` = '" . database()->real_escape_string($shop_url) . "' AND `is_active` = 1")->fetch_object() ?? null;
        if (!$shop) redirect();

        /* Parse items from POST - only id and qty, price ALWAYS from DB */
        $raw_items = $_POST['items'] ?? [];
        if (empty($raw_items) || !is_array($raw_items)) {
            redirect('store/' . $shop_url);
        }

        $cart_items = [];
        $grand_total = 0;

        foreach ($raw_items as $raw) {
            $item_id = (int)($raw['id'] ?? 0);
            $qty     = max(1, (int)($raw['qty'] ?? 1));
            if ($item_id <= 0) continue;

            /* Ambil item dari DB — harga SELALU dari DB */
            $item = database()->query("SELECT * FROM `shop_items` WHERE `id` = {$item_id} AND `shop_id` = {$shop->id} AND `status` = 1")->fetch_object() ?? null;
            if (!$item) continue;

            /* Gunakan harga diskon jika ada */
            $unit_price = (!empty($item->has_discount) && !empty($item->discount_price))
                ? (float)$item->discount_price
                : (float)$item->price;

            $subtotal     = $unit_price * $qty;
            $grand_total += $subtotal;

            $cart_items[] = [
                'item'       => $item,
                'qty'        => $qty,
                'unit_price' => $unit_price,
                'subtotal'   => $subtotal,
            ];
        }

        if (empty($cart_items)) {
            redirect('store/' . $shop_url);
        }

        /* ── Collect payment channels (same as StoreCheckout) ── */
        $payment_channels = [];
        $primary_gateway  = null;

        $tripay_enabled = !empty(settings()->tripay->is_enabled);
        $tripay_key     = settings()->tripay->api_key     ?? null;
        $tripay_pkey    = settings()->tripay->private_key ?? null;
        $tripay_mc      = settings()->tripay->merchant_code ?? null;
        $tripay_mode    = settings()->tripay->mode ?? 'production';

        if ($tripay_enabled && !empty($tripay_key)) {
            $raw_channels = \Altum\PaymentGateways\Tripay::get_channels();
            if (!empty($raw_channels)) {
                foreach ($raw_channels as $c) { $c->_gateway = 'tripay'; $payment_channels[] = $c; }
                $primary_gateway = 'tripay';
            }
        }

        if (!empty(settings()->midtrans->is_enabled)) {
            $midtrans_active = \Altum\Helpers\MidtransDetector::get_active_methods();
            if (!empty($midtrans_active)) {
                $midtrans_mapping = [
                    'bca_va' => ['name' => 'BCA Virtual Account', 'group' => 'Virtual Account', 'icon' => 'https://api.midtrans.com/v2/assets/svg/brand/bca.svg'],
                    'echannel' => ['name' => 'Mandiri Virtual Account', 'group' => 'Virtual Account', 'icon' => 'https://api.midtrans.com/v2/assets/svg/brand/mandiri.svg'],
                    'bni_va' => ['name' => 'BNI Virtual Account', 'group' => 'Virtual Account', 'icon' => 'https://api.midtrans.com/v2/assets/svg/brand/bni.svg'],
                    'bri_va' => ['name' => 'BRI Virtual Account', 'group' => 'Virtual Account', 'icon' => 'https://api.midtrans.com/v2/assets/svg/brand/bri.svg'],
                    'cimb_va' => ['name' => 'CIMB Niaga Virtual Account', 'group' => 'Virtual Account', 'icon' => 'https://api.midtrans.com/v2/assets/svg/brand/cimb.svg'],
                    'permata_va' => ['name' => 'Permata Virtual Account', 'group' => 'Virtual Account', 'icon' => 'https://api.midtrans.com/v2/assets/svg/brand/permata.svg'],
                    'danamon_va' => ['name' => 'Danamon Virtual Account', 'group' => 'Virtual Account', 'icon' => 'https://api.midtrans.com/v2/assets/svg/brand/danamon.svg'],
                    'other_va' => ['name' => 'Other Bank Virtual Account', 'group' => 'Virtual Account', 'icon' => ''],
                    'qris' => ['name' => 'QRIS (Semua E-Wallet)', 'group' => 'QRIS', 'icon' => 'https://api.midtrans.com/v2/assets/svg/brand/qris.svg'],
                    'gopay' => ['name' => 'GoPay', 'group' => 'E-Wallet', 'icon' => 'https://api.midtrans.com/v2/assets/svg/brand/gopay.svg'],
                    'shopeepay' => ['name' => 'ShopeePay', 'group' => 'E-Wallet', 'icon' => 'https://api.midtrans.com/v2/assets/svg/brand/shopeepay.svg'],
                    'credit_card' => ['name' => 'Credit Card', 'group' => 'Online Payment', 'icon' => 'https://api.midtrans.com/v2/assets/svg/brand/visa.svg'],
                    'alfamart' => ['name' => 'Alfamart', 'group' => 'Gerai', 'icon' => 'https://api.midtrans.com/v2/assets/svg/brand/alfamart.svg'],
                    'indomaret' => ['name' => 'Indomaret', 'group' => 'Gerai', 'icon' => 'https://api.midtrans.com/v2/assets/svg/brand/indomaret.svg']
                ];

                foreach ($midtrans_active as $code) {
                    $map = $midtrans_mapping[$code] ?? ['name' => strtoupper($code), 'group' => 'Online Payment', 'icon' => ''];
                    $payment_channels[] = (object)[
                        'code'      => $code,
                        'name'      => $map['name'],
                        'group'     => $map['group'],
                        'icon_url'  => $map['icon'],
                        'total_fee' => (object)['flat' => 0, 'percent' => 0],
                        '_gateway'  => 'midtrans',
                    ];
                }
                if (!$primary_gateway) $primary_gateway = 'midtrans';
            }
        }

        if (!empty(settings()->offline_payment->is_enabled)) {
            $payment_channels[] = (object)[
                'code'      => 'offline_payment',
                'name'      => l('pay.custom_plan.offline_payment'),
                'group'     => 'Manual Payment',
                'icon_url'  => '',
                'total_fee' => (object)['flat' => 0, 'percent' => 0],
                '_gateway'  => 'offline_payment',
            ];
            if (!$primary_gateway) $primary_gateway = 'offline_payment';
        }

        $is_demo = empty($primary_gateway);
        if ($is_demo) $primary_gateway = 'demo';

        /* ── Handle payment POST ── */
        if (!empty($_POST['do_payment'])) {
            $email     = input_clean($_POST['email'] ?? '');
            $full_name = input_clean($_POST['full_name'] ?? '');
            $phone     = input_clean($_POST['phone'] ?? '');
            $method    = input_clean($_POST['payment_method'] ?? ($payment_channels[0]->code ?? 'QRIS'));

            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                Alerts::add_error('Email tidak valid.');
            }
            if (empty($full_name)) {
                Alerts::add_error('Nama lengkap wajib diisi.');
            }

            if (!Alerts::has_errors()) {
                $datetime       = \Altum\Date::$date;
                $invoice_number = 'INV-CART-' . strtoupper(substr(md5(uniqid()), 0, 10));
                $order_ids      = [];

                /* ── Insert order per item ── */
                foreach ($cart_items as $ci) {
                    $item = $ci['item'];

                    /* Upsert customer */
                    $customer = database()->query("SELECT `id` FROM `shop_customers` WHERE `shop_id` = {$shop->id} AND `email` = '" . database()->real_escape_string($email) . "'")->fetch_object() ?? null;
                    if (!$customer) {
                        $stmt = database()->prepare("INSERT INTO `shop_customers` (`shop_id`, `email`, `full_name`, `phone`) VALUES (?, ?, ?, ?)");
                        $stmt->bind_param('isss', $shop->id, $email, $full_name, $phone);
                        $stmt->execute();
                        $customer_id = $stmt->insert_id;
                        $stmt->close();
                    } else {
                        $customer_id = $customer->id;
                    }

                    $qty         = $ci['qty'];
                    $unit_price  = $ci['unit_price'];
                    $subtotal    = $ci['subtotal'];
                    $service_fee = $subtotal * 0.05;

                    $stmt = database()->prepare("INSERT INTO `shop_orders`
                        (`shop_id`, `item_id`, `customer_id`, `invoice_number`, `qty`,
                         `total_amount`, `service_fee`, `grand_total`, `discount_amount`,
                         `payment_processor`, `status`, `datetime`)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0, ?, 'pending', ?)");
                    $stmt->bind_param(
                        'iiisidddss',
                        $shop->id, $item->id, $customer_id,
                        $invoice_number, $qty,
                        $subtotal, $service_fee, $subtotal,
                        $method, $datetime
                    );
                    $stmt->execute();
                    $order_ids[] = $stmt->insert_id;
                    $stmt->close();
                }

                /* ── Gateway routing ── */
                $items_label = implode(', ', array_map(fn($ci) => $ci['item']->name . ' x' . $ci['qty'], $cart_items));

                if (in_array($method, ['bca_va', 'echannel', 'bni_va', 'bri_va', 'cimb_va', 'permata_va', 'danamon_va', 'other_va', 'qris', 'gopay', 'shopeepay', 'credit_card', 'alfamart', 'indomaret'])) {
                    $midtrans_api_url = (settings()->midtrans->mode == 'sandbox')
                        ? 'https://app.sandbox.midtrans.com/v1/payment-links'
                        : 'https://app.midtrans.com/v1/payment-links';

                    $midtrans_item_details = array_map(fn($ci) => [
                        'price'    => (int) ceil($ci['unit_price']),
                        'quantity' => $ci['qty'],
                        'name'     => substr($ci['item']->name, 0, 50),
                    ], $cart_items);

                    $midtrans_payload = [
                        'transaction_details' => [
                            'order_id'     => $invoice_number,
                            'gross_amount' => (int) ceil($grand_total),
                        ],
                        'expiry' => ['duration' => 1, 'unit' => 'days'],
                        'enabled_payments' => [$method],
                        'item_details'     => $midtrans_item_details,
                        'customer_details' => [
                            'first_name' => $full_name,
                            'email'      => $email,
                            'phone'      => $phone ?: '-',
                        ],
                        'callbacks' => [
                            'finish' => SITE_URL . 'store-checkout-success/' . $invoice_number,
                        ],
                        'custom_field1' => 'cart_order_' . implode('_', $order_ids),
                    ];

                    $ch = curl_init($midtrans_api_url);
                    curl_setopt_array($ch, [
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_POST           => true,
                        CURLOPT_POSTFIELDS     => json_encode($midtrans_payload),
                        CURLOPT_HTTPHEADER     => [
                            'Content-Type: application/json',
                            'Accept: application/json',
                            'Authorization: Basic ' . base64_encode(settings()->midtrans->server_key . ':'),
                            'X-Override-Notification: ' . SITE_URL . 'webhook-midtrans-shop',
                        ],
                        CURLOPT_TIMEOUT => 30,
                    ]);
                    $raw = curl_exec($ch);
                    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    curl_close($ch);
                    $resp = json_decode($raw);

                    if ($http_code < 400 && isset($resp->payment_url)) {
                        header('Location: ' . $resp->payment_url);
                        exit;
                    } else {
                        $err = $resp->error_messages[0] ?? ($resp->message ?? 'Midtrans error.');
                        Alerts::add_error('Midtrans: ' . $err);
                    }

                } elseif ($primary_gateway === 'tripay' && !in_array($method, ['bca_va', 'echannel', 'bni_va', 'bri_va', 'cimb_va', 'permata_va', 'danamon_va', 'other_va', 'qris', 'gopay', 'shopeepay', 'credit_card', 'alfamart', 'indomaret'])) {
                    $tripay_items = array_map(fn($ci) => [
                        'sku'      => 'ITEM-' . $ci['item']->id,
                        'name'     => $ci['item']->name . ' (x' . $ci['qty'] . ')',
                        'price'    => (int) ceil($ci['subtotal']),
                        'quantity' => 1,
                    ], $cart_items);

                    $payload = [
                        'method'         => $method,
                        'merchant_ref'   => $invoice_number,
                        'amount'         => (int) ceil($grand_total),
                        'customer_name'  => $full_name,
                        'customer_email' => $email,
                        'customer_phone' => $phone,
                        'order_items'    => $tripay_items,
                        'return_url'     => SITE_URL . 'store-checkout-success/' . $invoice_number,
                        'callback_url'   => SITE_URL . 'webhook-tripay',
                        'expired_time'   => (time() + (24 * 60 * 60)),
                        'signature'      => hash_hmac('sha256', $tripay_mc . $invoice_number . (int) ceil($grand_total), $tripay_pkey),
                    ];
                    $api_url = ($tripay_mode === 'sandbox')
                        ? 'https://tripay.co.id/api-sandbox/transaction/create'
                        : 'https://tripay.co.id/api/transaction/create';

                    $curl = curl_init();
                    curl_setopt_array($curl, [
                        CURLOPT_FRESH_CONNECT  => true,
                        CURLOPT_URL            => $api_url,
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

                    if (isset($response_obj->success) && $response_obj->success) {
                        $checkout_url = $response_obj->data->checkout_url ?? '';
                        header('Location: ' . $checkout_url);
                        exit;
                    } else {
                        Alerts::add_error('Tripay: ' . ($response_obj->message ?? 'Unknown error'));
                    }

                } elseif ($is_demo || $method === 'demo') {
                    redirect('store-checkout-success/' . $invoice_number);

                } else {
                    Alerts::add_error('Metode pembayaran tidak didukung.');
                }
            }
        }

        Title::set('Checkout Keranjang — ' . htmlspecialchars($shop->name));

        $data = [
            'shop'             => $shop,
            'cart_items'       => $cart_items,
            'grand_total'      => $grand_total,
            'payment_channels' => $payment_channels,
            'primary_gateway'  => $primary_gateway,
            'is_demo'          => $is_demo,
            'post_items'       => $_POST['items'] ?? [],
        ];

        $view = new \Altum\View('store_cart_checkout/index', (array)$this);
        $this->add_view_content('content', $view->run($data));
    }
}
