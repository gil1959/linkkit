<?php
/*
 * Copyright (c) 2025 AltumCode (https://altumcode.com/)
 * 
 * Shop Controller - Custom Implementation
 */

namespace Altum\Controllers;

use Altum\Database\Database;
use Altum\Title;
use Altum\Alerts;

class Shop extends Controller {

    public function index() {

        \Altum\Authentication::guard();

        /* Check if user already has a shop */
        $shop = database()->query("SELECT * FROM `shops` WHERE `user_id` = {$this->user->user_id}")->fetch_object() ?? null;

        if(!$shop) {
            /* Handle Shop Creation via POST */
            if(!empty($_POST)) {
                $_POST['name'] = input_clean($_POST['name']);
                $_POST['description'] = input_clean($_POST['description']);
                $_POST['url'] = get_slug($_POST['url']);
                $item_types = isset($_POST['item_types']) && is_array($_POST['item_types']) ? json_encode($_POST['item_types']) : json_encode([]);

                // Check if URL is taken
                $exists = database()->query("SELECT `id` FROM `shops` WHERE `url` = '{$_POST['url']}'")->fetch_object();

                if($exists) {
                    Alerts::add_error(l('global.error_message.url_taken') ?? 'URL is already taken');
                }

                if(!Alerts::has_field_errors() && !Alerts::has_errors()) {
                    /* Database query */
                    $stmt = database()->prepare("INSERT INTO `shops` (`user_id`, `name`, `description`, `url`, `item_types`, `datetime`) VALUES (?, ?, ?, ?, ?, ?)");
                    $datetime = \Altum\Date::$date;
                    $stmt->bind_param('isssss', $this->user->user_id, $_POST['name'], $_POST['description'], $_POST['url'], $item_types, $datetime);
                    $stmt->execute();
                    $stmt->close();

                    /* Set a nice success message */
                    Alerts::add_success(sprintf(l('global.success_message.create1'), 'Shop') ?? 'Shop created successfully');
                    
                    redirect('shop');
                }
            }

            /* Prepare the view */
            $data = [];

            /* Set a custom title */
            Title::set('Create Shop');

            /* Main View */
            $view = new \Altum\View('shop/create', (array) $this);
            $this->add_view_content('content', $view->run($data));

        } else {
            // Shop exists, display dashboard
            
            // Get basic shop stats
            $total_income = database()->query("SELECT SUM(`grand_total`) AS `total` FROM `shop_orders` WHERE `shop_id` = {$shop->id} AND `status` = 'paid'")->fetch_object()->total ?? 0;
            $total_transactions = database()->query("SELECT COUNT(*) AS `total` FROM `shop_orders` WHERE `shop_id` = {$shop->id} AND `status` = 'paid'")->fetch_object()->total ?? 0;
            $total_pending_withdrawals = database()->query("SELECT SUM(`amount`) AS `total` FROM `shop_withdrawals` WHERE `user_id` = {$this->user->user_id} AND `status` = 'pending'")->fetch_object()->total ?? 0;
            
            // Fetch Items
            $items_result = database()->query("SELECT * FROM `shop_items` WHERE `shop_id` = {$shop->id} ORDER BY `datetime` DESC");
            $items = [];
            while($row = $items_result->fetch_object()) {
                $items[] = $row;
            }

            // Fetch Orders/Transactions
            $orders_result = database()->query("
                SELECT o.*, c.email, c.full_name, i.name as item_name 
                FROM `shop_orders` o 
                JOIN `shop_customers` c ON o.customer_id = c.id 
                JOIN `shop_items` i ON o.item_id = i.id 
                WHERE o.shop_id = {$shop->id} 
                ORDER BY o.datetime DESC LIMIT 50
            ");
            $orders = [];
            while($row = $orders_result->fetch_object()) {
                $orders[] = $row;
            }

            // Fetch Vouchers
            $vouchers_result = database()->query("SELECT v.*, i.name as item_name FROM `shop_vouchers` v LEFT JOIN `shop_items` i ON v.item_id=i.id WHERE v.shop_id={$shop->id} ORDER BY v.datetime DESC");
            $vouchers = [];
            while($row = $vouchers_result->fetch_object()) { $vouchers[] = $row; }

            // Fetch Listings
            $listings_result = database()->query("SELECT l.*, COUNT(i.id) as item_count FROM `shop_listings` l LEFT JOIN `shop_items` i ON i.listing_id=l.id WHERE l.shop_id={$shop->id} GROUP BY l.id ORDER BY l.datetime DESC");
            $listings = [];
            while($row = $listings_result->fetch_object()) { $listings[] = $row; }

            // Fetch Reviews
            $reviews_result = database()->query("
                SELECT r.*, i.name as item_name, c.full_name as buyer_name, c.email as buyer_email
                FROM `shop_reviews` r
                JOIN `shop_items` i ON r.item_id=i.id
                JOIN `shop_orders` o ON r.order_id=o.id
                JOIN `shop_customers` c ON o.customer_id=c.id
                WHERE i.shop_id={$shop->id}
                ORDER BY r.datetime DESC LIMIT 100
            ");
            $reviews = [];
            while($row = $reviews_result->fetch_object()) { $reviews[] = $row; }

            // Fetch Audience
            $audience_result = database()->query("SELECT * FROM `shop_customers` WHERE `shop_id`={$shop->id} ORDER BY `total_spent` DESC LIMIT 200");
            $audience = [];
            while($row = $audience_result->fetch_object()) { $audience[] = $row; }

            // Fetch Webhook Events
            $webhook_events_result = database()->query("SELECT we.*, i.name as item_name FROM `shop_webhook_events` we LEFT JOIN `shop_items` i ON we.item_id=i.id WHERE we.shop_id={$shop->id} ORDER BY we.datetime DESC LIMIT 100");
            $webhook_events = [];
            while($row = $webhook_events_result->fetch_object()) { $webhook_events[] = $row; }

            $data = [
                'shop'                     => $shop,
                'total_income'             => $total_income,
                'total_transactions'       => $total_transactions,
                'total_pending_withdrawals'=> $total_pending_withdrawals,
                'items'                    => $items,
                'orders'                   => $orders,
                'vouchers'                 => $vouchers,
                'listings'                 => $listings,
                'reviews'                  => $reviews,
                'audience'                 => $audience,
                'webhook_events'           => $webhook_events,
            ];

            /* Set a custom title */
            Title::set($shop->name . ' - Shop Dashboard');

            /* Main View */
            $view = new \Altum\View('shop/index', (array) $this);
            $this->add_view_content('content', $view->run($data));
        }

    }

}
