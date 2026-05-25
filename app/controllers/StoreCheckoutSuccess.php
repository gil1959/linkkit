<?php
/*
 * Store Checkout Success Controller
 * Tampilkan halaman sukses setelah pembayaran berhasil, termasuk:
 * - Download link untuk produk digital
 * - Kode unik untuk produk random_code
 * - Status untuk webhook_event & manual
 * - Form upload bukti untuk offline payment
 * - Support multi-order (cart checkout)
 */

namespace Altum\Controllers;

use Altum\Title;
use Altum\Alerts;

class StoreCheckoutSuccess extends Controller {

    public function index() {

        $invoice_number = isset($this->params[0]) ? input_clean($this->params[0]) : null;
        if(!$invoice_number) redirect();

        /* Ambil SEMUA orders berdasarkan invoice (cart = multi, single = 1) */
        $orders_raw = [];
        $res = database()->query("SELECT so.*, sc.email, sc.full_name, sc.phone
            FROM `shop_orders` so
            JOIN `shop_customers` sc ON so.customer_id = sc.id
            WHERE so.invoice_number = '" . database()->real_escape_string($invoice_number) . "'
            ORDER BY so.id ASC"
        );
        while($row = $res->fetch_object()) {
            $orders_raw[] = $row;
        }

        if(empty($orders_raw)) redirect();

        /* Gunakan order pertama untuk info umum (shop, customer, status) */
        $first_order = $orders_raw[0];

        $shop = database()->query("SELECT * FROM `shops` WHERE `id` = {$first_order->shop_id}")->fetch_object() ?? null;
        if(!$shop) redirect();

        /* Load item per order */
        $orders = [];
        foreach($orders_raw as $o) {
            $item = database()->query("SELECT * FROM `shop_items` WHERE `id` = {$o->item_id}")->fetch_object() ?? null;
            $orders[] = ['order' => $o, 'item' => $item];
        }

        /* Jika offline payment + ada proof upload (support single order saja) */
        if(!empty($_POST) && isset($_FILES['proof_image'])) {
            $allowed_ext = ['jpg','jpeg','png','pdf'];
            $file = $_FILES['proof_image'];
            if($file['error'] === UPLOAD_ERR_OK) {
                $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                if(in_array($ext, $allowed_ext)) {
                    $upload_dir = UPLOADS_PATH . 'shop_proof/';
                    if(!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
                    $filename = 'proof_' . $first_order->id . '_' . time() . '.' . $ext;
                    if(move_uploaded_file($file['tmp_name'], $upload_dir . $filename)) {
                        /* Update semua orders dalam invoice dengan proof */
                        database()->query("UPDATE `shop_orders` SET
                            `proof_image` = '" . database()->real_escape_string($filename) . "',
                            `status` = 'proof_uploaded'
                            WHERE `invoice_number` = '" . database()->real_escape_string($invoice_number) . "'");
                        /* Update payments table agar admin bisa lihat */
                        foreach($orders_raw as $o) {
                            database()->query("UPDATE `payments` SET
                                `payment_proof` = '" . database()->real_escape_string($filename) . "',
                                `status` = 'pending'
                                WHERE `code` = 'shop_order_{$o->id}'");
                        }
                        Alerts::add_success('Bukti pembayaran berhasil diunggah. Admin akan memverifikasi dalam 1x24 jam.');
                    }
                }
            }
        }

        /* Reload orders setelah potential update */
        $orders_raw = [];
        $res2 = database()->query("SELECT so.*, sc.email, sc.full_name, sc.phone
            FROM `shop_orders` so
            JOIN `shop_customers` sc ON so.customer_id = sc.id
            WHERE so.invoice_number = '" . database()->real_escape_string($invoice_number) . "'
            ORDER BY so.id ASC"
        );
        while($row = $res2->fetch_object()) {
            $orders_raw[] = $row;
        }

        $first_order = $orders_raw[0];
        $orders = [];
        foreach($orders_raw as $o) {
            $item = database()->query("SELECT * FROM `shop_items` WHERE `id` = {$o->item_id}")->fetch_object() ?? null;
            $orders[] = ['order' => $o, 'item' => $item];
        }

        /* Hitung total keseluruhan */
        $grand_total = array_sum(array_map(fn($o) => $o['order']->grand_total, $orders));

        Title::set('Pesanan Berhasil - ' . $shop->name);

        $data = [
            'first_order' => $first_order,   /* untuk info customer, status, checkout_url */
            'orders'      => $orders,         /* array: [['order'=>..., 'item'=>...], ...] */
            'shop'        => $shop,
            'grand_total' => $grand_total,
            /* backward compat untuk view yang masih pakai $data->order / $data->item */
            'order'       => $first_order,
            'item'        => $orders[0]['item'] ?? null,
        ];

        $view = new \Altum\View('store_checkout_success/index', (array) $this);
        $this->add_view_content('content', $view->run($data));
    }
}
