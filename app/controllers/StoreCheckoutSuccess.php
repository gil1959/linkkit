<?php
/*
 * Store Checkout Success Controller
 * Tampilkan halaman sukses setelah pembayaran berhasil, termasuk:
 * - Download link untuk produk digital
 * - Kode unik untuk produk random_code
 * - Status untuk webhook_event & manual
 * - Form upload bukti untuk offline payment
 */

namespace Altum\Controllers;

use Altum\Title;
use Altum\Alerts;

class StoreCheckoutSuccess extends Controller {

    public function index() {

        $invoice_number = isset($this->params[0]) ? input_clean($this->params[0]) : null;
        if(!$invoice_number) redirect();

        /* Ambil order berdasarkan invoice */
        $order = database()->query("SELECT so.*, sc.email, sc.full_name, sc.phone
            FROM `shop_orders` so
            JOIN `shop_customers` sc ON so.customer_id = sc.id
            WHERE so.invoice_number = '" . database()->real_escape_string($invoice_number) . "'"
        )->fetch_object() ?? null;

        if(!$order) redirect();

        $shop = database()->query("SELECT * FROM `shops` WHERE `id` = {$order->shop_id}")->fetch_object() ?? null;
        $item = database()->query("SELECT * FROM `shop_items` WHERE `id` = {$order->item_id}")->fetch_object() ?? null;

        if(!$shop || !$item) redirect();

        /* Jika offline payment + ada proof upload */
        if(!empty($_POST) && isset($_FILES['proof_image'])) {
            /* Simpan bukti transfer */
            $allowed_ext = ['jpg','jpeg','png','pdf'];
            $file = $_FILES['proof_image'];
            if($file['error'] === UPLOAD_ERR_OK) {
                $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                if(in_array($ext, $allowed_ext)) {
                    $upload_dir = UPLOADS_PATH . 'shop_proof/';
                    if(!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
                    $filename = 'proof_' . $order->id . '_' . time() . '.' . $ext;
                    if(move_uploaded_file($file['tmp_name'], $upload_dir . $filename)) {
                        database()->query("UPDATE `shop_orders` SET `proof_image` = '{$filename}', `status` = 'proof_uploaded' WHERE `id` = {$order->id}");
                        /* Update juga di tabel payments global agar admin bisa melihat buktinya */
                        database()->query("UPDATE `payments` SET `payment_proof` = '{$filename}', `status` = 'pending' WHERE `code` = 'shop_order_{$order->id}'");
                        
                        $order->proof_image = $filename;
                        $order->status = 'proof_uploaded';
                        Alerts::add_success('Bukti pembayaran berhasil diunggah. Admin akan memverifikasi dalam 1x24 jam.');
                    }
                }
            }
        }

        /* Reload order setelah potential update */
        $order = database()->query("SELECT so.*, sc.email, sc.full_name, sc.phone
            FROM `shop_orders` so
            JOIN `shop_customers` sc ON so.customer_id = sc.id
            WHERE so.invoice_number = '" . database()->real_escape_string($invoice_number) . "'"
        )->fetch_object() ?? $order;

        Title::set('Pesanan Berhasil - ' . $shop->name);

        $data = [
            'order'   => $order,
            'shop'    => $shop,
            'item'    => $item,
        ];

        $view = new \Altum\View('store_checkout_success/index', (array) $this);
        $this->add_view_content('content', $view->run($data));
    }
}
