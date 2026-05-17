<?php
namespace Altum\Controllers;
defined('ALTUMCODE') || die();

class ShopAjax extends Controller {

    public function index() {
        header('Content-Type: application/json');

        $action = input_clean($_POST['action'] ?? $_GET['action'] ?? '');

        /* ── Public endpoints (no auth needed, called from checkout) ── */
        if(in_array($action, ['ongkir_provinces', 'ongkir_cities', 'ongkir_cost', 'buyer_check_order', 'item_reviews', 'report_review', 'buyer_edit_review', 'track_product_view'])) {
            switch($action) {
                case 'ongkir_provinces':  $this->ongkir_provinces();  break;
                case 'ongkir_cities':     $this->ongkir_cities();     break;
                case 'ongkir_cost':       $this->ongkir_cost();       break;
                case 'buyer_check_order': $this->buyer_check_order(); break;
                case 'item_reviews':      $this->item_reviews();      break;
                case 'report_review':     $this->report_review();     break;
                case 'buyer_edit_review': $this->buyer_edit_review(); break;
                case 'track_product_view':$this->track_product_view();break;
            }
            return;
        }

        /* ── Authenticated endpoints ── */
        \Altum\Authentication::guard();

        if((empty($_POST) && !in_array($action, ['ongkir_provinces','ongkir_cities'])) || !\Altum\Csrf::check()) {
            die(json_encode(['success' => false, 'message' => 'Invalid request']));
        }

        $shop = database()->query("SELECT * FROM `shops` WHERE `user_id` = {$this->user->user_id}")->fetch_object() ?? null;
        if(!$shop) die(json_encode(['success' => false, 'message' => 'Shop not found']));

        switch($action) {
            case 'voucher_create':    $this->voucher_create($shop);    break;
            case 'voucher_update':    $this->voucher_update($shop);    break;
            case 'voucher_delete':    $this->voucher_delete($shop);    break;
            case 'listing_create':    $this->listing_create($shop);    break;
            case 'listing_update':    $this->listing_update($shop);    break;
            case 'listing_delete':    $this->listing_delete($shop);    break;
            case 'review_reply':      $this->review_reply($shop);      break;
            case 'seller_report_review': $this->seller_report_review($shop); break;
            case 'update_tracking':   $this->update_tracking($shop);   break;
            default: die(json_encode(['success' => false, 'message' => 'Unknown action']));
        }
    }

    /* ─────────────────────────────────────────────
     *  RajaOngkir — Provinces (public)
     * ───────────────────────────────────────────── */
    private function ongkir_provinces() {
        $provinces = \Altum\Libraries\RajaOngkir::get_provinces();
        die(json_encode(['success' => true, 'data' => $provinces]));
    }

    /* ─────────────────────────────────────────────
     *  RajaOngkir — Cities by province (public)
     * ───────────────────────────────────────────── */
    private function ongkir_cities() {
        $province_id = isset($_GET['province_id']) ? (int)$_GET['province_id'] : null;
        $cities      = \Altum\Libraries\RajaOngkir::get_cities($province_id);
        die(json_encode(['success' => true, 'data' => $cities]));
    }

    /* ─────────────────────────────────────────────
     *  RajaOngkir — Shipping cost (public, GET)
     *  ?action=ongkir_cost&origin=X&dest=Y&weight=Z
     * ───────────────────────────────────────────── */
    private function ongkir_cost() {
        $origin      = (int)($_GET['origin']  ?? 0);
        $destination = (int)($_GET['dest']    ?? 0);
        $weight      = (int)($_GET['weight']  ?? 1000);

        if(!$origin || !$destination || $weight < 1) {
            die(json_encode(['success' => false, 'message' => 'Invalid parameters']));
        }

        $all_costs = \Altum\Libraries\RajaOngkir::get_all_costs($origin, $destination, $weight);
        die(json_encode(['success' => true, 'data' => $all_costs, 'couriers' => \Altum\Libraries\RajaOngkir::get_couriers()]));
    }

    /* ─────────────────────────────────────────────
     *  Check Order (Public)
     * ───────────────────────────────────────────── */
    private function buyer_check_order() {
        $shop_id = (int)($_POST['shop_id'] ?? 0);
        $invoice = input_clean($_POST['invoice'] ?? '');
        $email   = input_clean($_POST['email'] ?? '');

        if(!$shop_id || empty($invoice) || empty($email)) {
            die(json_encode(['success' => false, 'message' => 'Lengkapi form untuk mengecek pesanan.']));
        }

        $invoice_esc = database()->real_escape_string($invoice);
        $email_esc   = database()->real_escape_string($email);

        $order = database()->query("
            SELECT o.*, i.name as item_name, i.type as item_type, i.image as item_image 
            FROM `shop_orders` o
            JOIN `shop_customers` c ON o.customer_id = c.id
            JOIN `shop_items` i ON o.item_id = i.id
            WHERE o.shop_id = {$shop_id} AND o.invoice_number = '{$invoice_esc}' AND c.email = '{$email_esc}'
        ")->fetch_object() ?? null;

        if(!$order) {
            die(json_encode(['success' => false, 'message' => 'Pesanan tidak ditemukan. Pastikan Invoice dan Email benar.']));
        }

        $data = [
            'invoice_number'  => $order->invoice_number,
            'datetime'        => date('d M Y H:i', strtotime($order->datetime)),
            'status'          => $order->status,
            'grand_total'     => $order->grand_total,
            'item_name'       => $order->item_name,
            'item_type'       => $order->item_type,
            'item_image'      => $order->item_image ? \Altum\Uploads::get_full_url('shop_items') . $order->item_image : null,
            'shipping_courier'=> $order->shipping_courier,
            'shipping_service'=> $order->shipping_service,
            'tracking_number' => $order->tracking_number,
            'shipping_status' => $order->shipping_status,
            'checkout_url'    => $order->checkout_url
        ];

        $review = database()->query("SELECT `id`, `rating`, `review`, `reply` FROM `shop_reviews` WHERE `order_id` = {$order->id}")->fetch_object();
        if($review) {
            $data['review'] = $review;
        }

        die(json_encode(['success' => true, 'data' => $data]));
    }

    /* ─────────────────────────────────────────────
     *  Track Product View (Public)
     * ───────────────────────────────────────────── */
    private function track_product_view() {
        $shop_id = (int)($_POST['shop_id'] ?? 0);
        $item_id = (int)($_POST['item_id'] ?? 0);
        if($shop_id && $item_id) {
            database()->query("INSERT INTO `shop_statistics` (`shop_id`, `item_id`, `type`, `datetime`) VALUES ({$shop_id}, {$item_id}, 'click', '" . \Altum\Date::$date . "')");
        }
        die(json_encode(['success' => true]));
    }

    private function item_reviews() {
        $item_id = (int)($_GET['item_id'] ?? 0);
        $reviews = [];
        if($item_id > 0) {
            $res = database()->query("
                SELECT r.*, c.full_name as reviewer_name, c.email as reviewer_email 
                FROM `shop_reviews` r
                JOIN `shop_orders` o ON r.order_id = o.id
                JOIN `shop_customers` c ON o.customer_id = c.id
                WHERE r.item_id = {$item_id} AND r.status != 'hidden'
                ORDER BY r.datetime DESC
            ");
            while($row = $res->fetch_object()) {
                $is_verified = false;
                $u = database()->query("SELECT `verification_status` FROM `users` WHERE `email` = '" . database()->real_escape_string($row->reviewer_email) . "'")->fetch_object();
                if($u && $u->verification_status == 'verified') {
                    $is_verified = true;
                }
                $reviews[] = [
                    'id' => $row->id,
                    'name' => $row->reviewer_name,
                    'rating' => (int)$row->rating,
                    'review' => $row->review,
                    'reply' => $row->reply,
                    'datetime' => date('d M Y', strtotime($row->datetime)),
                    'is_verified' => $is_verified
                ];
            }
        }
        die(json_encode(['success' => true, 'data' => $reviews]));
    }

    private function report_review() {
        $review_id = (int)($_POST['review_id'] ?? 0);
        $reason = input_clean($_POST['reason'] ?? '');
        if(!$review_id || !$reason) die(json_encode(['success' => false, 'message' => 'Review ID or reason missing']));

        database()->query("UPDATE `shop_reviews` SET `is_reported` = 1, `report_reason` = '" . database()->real_escape_string($reason) . "' WHERE `id` = {$review_id}");
        die(json_encode(['success' => true]));
    }

    private function buyer_edit_review() {
        $shop_id = (int)($_POST['shop_id'] ?? 0);
        $invoice = input_clean($_POST['invoice'] ?? '');
        $email   = input_clean($_POST['email'] ?? '');
        $rating  = (int)($_POST['rating'] ?? 5);
        $review_text = input_clean($_POST['review'] ?? '');

        $order = database()->query("
            SELECT o.id FROM `shop_orders` o
            JOIN `shop_customers` c ON o.customer_id = c.id
            WHERE o.shop_id = {$shop_id} AND o.invoice_number = '" . database()->real_escape_string($invoice) . "' AND c.email = '" . database()->real_escape_string($email) . "'
        ")->fetch_object();

        if(!$order) die(json_encode(['success' => false, 'message' => 'Invalid order']));

        $review = database()->query("SELECT `id`, `datetime` FROM `shop_reviews` WHERE `order_id` = {$order->id}")->fetch_object();
        if(!$review) die(json_encode(['success' => false, 'message' => 'Review not found']));

        if(strtotime($review->datetime) < strtotime('-30 days')) {
            die(json_encode(['success' => false, 'message' => 'Review can no longer be edited (30 days limit)']));
        }

        database()->query("UPDATE `shop_reviews` SET `rating` = {$rating}, `review` = '" . database()->real_escape_string($review_text) . "', `updated_at` = '" . \Altum\Date::$date . "' WHERE `id` = {$review->id}");
        die(json_encode(['success' => true]));
    }

    /* ─────────────────────────────────────────────
     *  Update tracking number (seller action)
     *  → kirim email notifikasi ke pembeli
     * ───────────────────────────────────────────── */
    private function update_tracking($shop) {
        $order_id        = (int)($_POST['order_id'] ?? 0);
        $tracking_number = input_clean($_POST['tracking_number'] ?? '');
        $shipping_status = input_clean($_POST['shipping_status'] ?? 'shipped');

        if(!$order_id || empty($tracking_number)) {
            die(json_encode(['success' => false, 'message' => 'Data tidak lengkap']));
        }

        /* Pastikan order milik shop ini */
        $order = database()->query("
            SELECT o.*, i.name AS item_name, i.weight, c.email AS buyer_email, c.full_name AS buyer_name, c.phone AS buyer_phone
            FROM `shop_orders` o
            JOIN `shop_items` i ON o.item_id = i.id
            JOIN `shop_customers` c ON o.customer_id = c.id
            WHERE o.id = {$order_id} AND o.shop_id = {$shop->id}
        ")->fetch_object() ?? null;

        if(!$order) die(json_encode(['success' => false, 'message' => 'Order tidak ditemukan']));

        $tn_esc = database()->real_escape_string($tracking_number);
        $ss_esc = database()->real_escape_string($shipping_status);
        database()->query("UPDATE `shop_orders` SET
            `tracking_number` = '{$tn_esc}',
            `shipping_status` = '{$ss_esc}'
            WHERE `id` = {$order_id}");

        $courier_names = ['jne' => 'JNE', 'tiki' => 'TIKI', 'pos' => 'POS Indonesia'];
        $courier_label = $courier_names[$order->shipping_courier ?? ''] ?? strtoupper($order->shipping_courier ?? '');

        $wa_url = '';
        if(!empty($order->buyer_phone)) {
            $phone = preg_replace('/[^0-9]/', '', $order->buyer_phone);
            if(strpos($phone, '0') === 0) $phone = '62' . substr($phone, 1);
            $wa_msg = "Halo *{$order->buyer_name}*, pesanan Anda dari toko *{$shop->name}* dengan invoice *{$order->invoice_number}* telah diperbarui!\n\nKurir: {$courier_label} - {$order->shipping_service}\nResi: *{$tracking_number}*\nStatus: " . ucfirst($shipping_status) . "\n\nTerima kasih telah berbelanja.";
            $wa_url = "https://wa.me/{$phone}?text=" . rawurlencode($wa_msg);
        }

        $msg = $wa_url ? 'Resi disimpan & diteruskan ke WA' : 'Resi disimpan (tidak ada nomor WA)';
        die(json_encode(['success' => true, 'message' => $msg, 'wa_url' => $wa_url]));
    }

    private function build_shipping_email($buyer_name, $item_name, $shop_name, $tracking_number, $courier, $service, $amount, $invoice, $tracking_url) {
        return '<!DOCTYPE html><html><body style="font-family:Inter,sans-serif;background:#f8fafc;padding:20px;margin:0">
<div style="max-width:520px;margin:0 auto;background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,.08)">
  <div style="background:linear-gradient(135deg,#059669,#10b981);padding:28px;text-align:center">
    <div style="width:52px;height:52px;background:rgba(255,255,255,.2);border-radius:50%;display:inline-flex;align-items:center;justify-content:center;margin-bottom:12px">
      <svg style="width:24px;height:24px;fill:none;stroke:#fff;stroke-width:2;stroke-linecap:round;stroke-linejoin:round" viewBox="0 0 24 24"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
    </div>
    <h1 style="color:#fff;font-size:1.15rem;margin:0">Pesanan Sedang Dikirim!</h1>
    <p style="color:rgba(255,255,255,.85);font-size:.82rem;margin:6px 0 0">Nomor resi sudah tersedia</p>
  </div>
  <div style="padding:28px">
    <p style="color:#374151">Halo <strong>' . htmlspecialchars($buyer_name) . '</strong>,</p>
    <p style="color:#374151">Pesanan kamu dari <strong>' . htmlspecialchars($shop_name) . '</strong> sudah dikirim! Berikut detail pengirimannya:</p>
    <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:12px;padding:20px;margin:16px 0">
      <div style="font-size:1.3rem;font-weight:800;color:#059669;text-align:center;letter-spacing:2px;margin-bottom:12px">' . htmlspecialchars($tracking_number) . '</div>
      <table style="width:100%;font-size:.85rem;color:#374151;border-collapse:collapse">
        <tr><td style="padding:5px 0;color:#6b7280">Produk</td><td style="text-align:right;font-weight:600">' . htmlspecialchars($item_name) . '</td></tr>
        <tr><td style="padding:5px 0;color:#6b7280">Invoice</td><td style="text-align:right;font-family:monospace;color:#4f46e5">' . htmlspecialchars($invoice) . '</td></tr>
        <tr><td style="padding:5px 0;color:#6b7280">Ekspedisi</td><td style="text-align:right;font-weight:600">' . htmlspecialchars($courier) . ' ' . htmlspecialchars($service) . '</td></tr>
        <tr><td style="padding:5px 0;color:#6b7280">Total Bayar</td><td style="text-align:right;font-weight:700;color:#059669">' . $amount . '</td></tr>
      </table>
    </div>
    <a href="' . $tracking_url . '" style="display:block;background:#059669;color:#fff;text-align:center;padding:14px;border-radius:12px;text-decoration:none;font-weight:700;font-size:.95rem;margin-bottom:12px">🔍 Lacak Paket</a>
    <p style="font-size:.75rem;color:#94a3b8;text-align:center;margin:16px 0 0">Terima kasih sudah berbelanja di ' . htmlspecialchars($shop_name) . '!</p>
  </div>
</div></body></html>';
    }

    private function voucher_create($shop) {
        $code        = strtoupper(input_clean($_POST['code'] ?? ''));
        $discount    = min(100, max(1, (int)($_POST['discount_percentage'] ?? 0)));
        $is_unlimited= isset($_POST['is_unlimited']) && $_POST['is_unlimited'] ? 1 : 0;
        $quota       = $is_unlimited ? 'NULL' : ((int)($_POST['quota'] ?? 0) > 0 ? (int)$_POST['quota'] : 'NULL');
        $is_active   = isset($_POST['is_active']) && $_POST['is_active'] ? 1 : 0;
        $item_id     = !empty($_POST['item_id']) ? (int)$_POST['item_id'] : 'NULL';
        $valid_from  = !empty($_POST['valid_from']) ? "'" . database()->real_escape_string($_POST['valid_from']) . "'" : 'NULL';
        $valid_to    = !empty($_POST['valid_to'])   ? "'" . database()->real_escape_string($_POST['valid_to'])   . "'" : 'NULL';
        $datetime    = \Altum\Date::$date;

        if(empty($code)) die(json_encode(['success' => false, 'message' => 'Voucher code required']));

        $exists = database()->query("SELECT `id` FROM `shop_vouchers` WHERE `shop_id` = {$shop->id} AND `code` = '" . database()->real_escape_string($code) . "'")->fetch_object();
        if($exists) die(json_encode(['success' => false, 'message' => 'Code already exists']));

        database()->query("INSERT INTO `shop_vouchers` (`shop_id`,`item_id`,`code`,`discount_percentage`,`is_unlimited`,`quota`,`is_active`,`valid_from`,`valid_to`,`datetime`)
            VALUES ({$shop->id},{$item_id},'" . database()->real_escape_string($code) . "',{$discount},{$is_unlimited},{$quota},{$is_active},{$valid_from},{$valid_to},'{$datetime}')");

        die(json_encode(['success' => true]));
    }

    private function voucher_update($shop) {
        $id          = (int)($_POST['id'] ?? 0);
        $code        = strtoupper(input_clean($_POST['code'] ?? ''));
        $discount    = min(100, max(1, (int)($_POST['discount_percentage'] ?? 0)));
        $is_unlimited= isset($_POST['is_unlimited']) && $_POST['is_unlimited'] ? 1 : 0;
        $quota       = $is_unlimited ? 'NULL' : ((int)($_POST['quota'] ?? 0) > 0 ? (int)$_POST['quota'] : 'NULL');
        $is_active   = isset($_POST['is_active']) && $_POST['is_active'] ? 1 : 0;
        $item_id     = !empty($_POST['item_id']) ? (int)$_POST['item_id'] : 'NULL';
        $valid_from  = !empty($_POST['valid_from']) ? "'" . database()->real_escape_string($_POST['valid_from']) . "'" : 'NULL';
        $valid_to    = !empty($_POST['valid_to'])   ? "'" . database()->real_escape_string($_POST['valid_to'])   . "'" : 'NULL';

        if(!$id || empty($code)) die(json_encode(['success' => false, 'message' => 'Invalid data']));

        $v = database()->query("SELECT `id` FROM `shop_vouchers` WHERE `id` = {$id} AND `shop_id` = {$shop->id}")->fetch_object();
        if(!$v) die(json_encode(['success' => false, 'message' => 'Not found']));

        database()->query("UPDATE `shop_vouchers` SET
            `code`='" . database()->real_escape_string($code) . "',`discount_percentage`={$discount},
            `is_unlimited`={$is_unlimited},`quota`={$quota},`is_active`={$is_active},
            `item_id`={$item_id},`valid_from`={$valid_from},`valid_to`={$valid_to}
            WHERE `id`={$id} AND `shop_id`={$shop->id}");

        die(json_encode(['success' => true]));
    }

    private function voucher_delete($shop) {
        $id = (int)($_POST['id'] ?? 0);
        if(!$id) die(json_encode(['success' => false, 'message' => 'Invalid ID']));
        $v = database()->query("SELECT `id` FROM `shop_vouchers` WHERE `id`={$id} AND `shop_id`={$shop->id}")->fetch_object();
        if(!$v) die(json_encode(['success' => false, 'message' => 'Not found']));
        database()->query("DELETE FROM `shop_vouchers` WHERE `id`={$id}");
        die(json_encode(['success' => true]));
    }

    private function listing_create($shop) {
        $name        = input_clean($_POST['name'] ?? '');
        $description = input_clean($_POST['description'] ?? '');
        $item_ids    = isset($_POST['item_ids']) && is_array($_POST['item_ids']) ? array_map('intval', $_POST['item_ids']) : [];
        $datetime    = \Altum\Date::$date;

        if(empty($name)) die(json_encode(['success' => false, 'message' => 'Name required']));

        database()->query("INSERT INTO `shop_listings` (`shop_id`,`name`,`description`,`datetime`)
            VALUES ({$shop->id},'" . database()->real_escape_string($name) . "','" . database()->real_escape_string($description) . "','{$datetime}')");
        $listing_id = database()->insert_id;

        foreach($item_ids as $iid) {
            database()->query("UPDATE `shop_items` SET `listing_id`={$listing_id} WHERE `id`={$iid} AND `shop_id`={$shop->id}");
        }

        die(json_encode(['success' => true, 'id' => $listing_id]));
    }

    private function listing_update($shop) {
        $id          = (int)($_POST['id'] ?? 0);
        $name        = input_clean($_POST['name'] ?? '');
        $description = input_clean($_POST['description'] ?? '');
        $item_ids    = isset($_POST['item_ids']) && is_array($_POST['item_ids']) ? array_map('intval', $_POST['item_ids']) : [];

        if(!$id || empty($name)) die(json_encode(['success' => false, 'message' => 'Invalid data']));
        $l = database()->query("SELECT `id` FROM `shop_listings` WHERE `id`={$id} AND `shop_id`={$shop->id}")->fetch_object();
        if(!$l) die(json_encode(['success' => false, 'message' => 'Not found']));

        database()->query("UPDATE `shop_listings` SET `name`='" . database()->real_escape_string($name) . "',`description`='" . database()->real_escape_string($description) . "' WHERE `id`={$id} AND `shop_id`={$shop->id}");
        database()->query("UPDATE `shop_items` SET `listing_id`=NULL WHERE `listing_id`={$id} AND `shop_id`={$shop->id}");
        foreach($item_ids as $iid) {
            database()->query("UPDATE `shop_items` SET `listing_id`={$id} WHERE `id`={$iid} AND `shop_id`={$shop->id}");
        }
        die(json_encode(['success' => true]));
    }

    private function listing_delete($shop) {
        $id = (int)($_POST['id'] ?? 0);
        if(!$id) die(json_encode(['success' => false, 'message' => 'Invalid ID']));
        $l = database()->query("SELECT `id` FROM `shop_listings` WHERE `id`={$id} AND `shop_id`={$shop->id}")->fetch_object();
        if(!$l) die(json_encode(['success' => false, 'message' => 'Not found']));
        database()->query("UPDATE `shop_items` SET `listing_id`=NULL WHERE `listing_id`={$id} AND `shop_id`={$shop->id}");
        database()->query("DELETE FROM `shop_listings` WHERE `id`={$id}");
        die(json_encode(['success' => true]));
    }

    private function review_reply($shop) {
        $id = (int)($_POST['id'] ?? 0);
        $reply = input_clean($_POST['reply'] ?? '');
        if(!$id) die(json_encode(['success' => false, 'message' => 'Invalid ID']));
        $r = database()->query("SELECT r.`id` FROM `shop_reviews` r JOIN `shop_items` i ON r.item_id=i.id WHERE r.id={$id} AND i.shop_id={$shop->id}")->fetch_object();
        if(!$r) die(json_encode(['success' => false, 'message' => 'Not found']));
        
        database()->query("UPDATE `shop_reviews` SET `reply` = " . (empty($reply) ? 'NULL' : "'" . database()->real_escape_string($reply) . "'") . " WHERE `id`={$id}");
        die(json_encode(['success' => true]));
    }

    private function seller_report_review($shop) {
        $id = (int)($_POST['id'] ?? 0);
        $reason = input_clean($_POST['reason'] ?? '');
        if(!$id || !$reason) die(json_encode(['success' => false, 'message' => 'Data tidak lengkap']));
        
        $r = database()->query("SELECT r.`id` FROM `shop_reviews` r JOIN `shop_items` i ON r.item_id=i.id WHERE r.id={$id} AND i.shop_id={$shop->id}")->fetch_object();
        if(!$r) die(json_encode(['success' => false, 'message' => 'Not found']));
        
        database()->query("UPDATE `shop_reviews` SET `is_reported` = 1, `report_reason` = '" . database()->real_escape_string($reason) . "' WHERE `id`={$id}");
        die(json_encode(['success' => true]));
    }
}
