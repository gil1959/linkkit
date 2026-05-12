<?php
/*
 * ShopOrders Controller — Buyer's order history panel
 * Route: shop-orders  /  shop-orders/{invoice}
 */

namespace Altum\Controllers;

use Altum\Title;

class ShopOrders extends Controller {

    public function index() {

        \Altum\Authentication::guard();

        $invoice = isset($this->params[0]) ? input_clean($this->params[0]) : null;

        if($invoice) {
            /* ── Detail satu pesanan ── */
            $order = database()->query("
                SELECT o.*, i.name AS item_name, i.image AS item_image, i.type AS item_type,
                       s.name AS shop_name, s.url AS shop_url, s.logo_image AS shop_logo,
                       c.email, c.full_name, c.phone
                FROM `shop_orders` o
                JOIN `shop_items`    i ON o.item_id     = i.id
                JOIN `shops`         s ON o.shop_id     = s.id
                JOIN `shop_customers` c ON o.customer_id = c.id
                WHERE o.invoice_number = '" . database()->real_escape_string($invoice) . "'
                  AND c.email = '" . database()->real_escape_string($this->user->email) . "'
            ")->fetch_object() ?? null;

            if(!$order) redirect('shop-orders');

            Title::set('Detail Pesanan — ' . $invoice);
            $view = new \Altum\View('shop_orders/index', (array) $this);
            $this->add_view_content('content', $view->run([
                'order'       => $order,
                'view_mode'   => 'detail',
            ]));

        } else {
            /* ── Daftar semua pesanan (based on email login) ── */
            $orders = database()->query("
                SELECT o.*, i.name AS item_name, i.image AS item_image, i.type AS item_type,
                       s.name AS shop_name, s.url AS shop_url, s.logo_image AS shop_logo
                FROM `shop_orders` o
                JOIN `shop_items`    i ON o.item_id     = i.id
                JOIN `shops`         s ON o.shop_id     = s.id
                JOIN `shop_customers` c ON o.customer_id = c.id
                WHERE c.email = '" . database()->real_escape_string($this->user->email) . "'
                ORDER BY o.datetime DESC
                LIMIT 100
            ")->fetch_all(MYSQLI_ASSOC);

            $orders = array_map(fn($r) => (object) $r, $orders);

            Title::set('Pesanan Saya');
            $view = new \Altum\View('shop_orders/index', (array) $this);
            $this->add_view_content('content', $view->run([
                'orders'    => $orders,
                'view_mode' => 'list',
            ]));
        }
    }
}
