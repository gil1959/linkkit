<?php
/*
 * Copyright (c) 2025 AltumCode (https://altumcode.com/)
 * 
 * Store Controller - Public Shop Microsite
 */

namespace Altum\Controllers;

use Altum\Database\Database;
use Altum\Title;

class Store extends Controller {

    public function index() {

        $url = isset($this->params[0]) ? input_clean($this->params[0]) : null;

        if(!$url) {
            redirect();
        }

        /* Get the shop data */
        $shop = database()->query("SELECT * FROM `shops` WHERE `url` = '{$url}' AND `is_active` = 1")->fetch_object() ?? null;

        if(!$shop) {
            redirect();
        }

        /* Track shop view */
        database()->query("INSERT INTO `shop_statistics` (`shop_id`, `item_id`, `type`, `datetime`) VALUES ({$shop->id}, NULL, 'view', '" . \Altum\Date::$date . "')");

        /* Get shop listings (categories) */
        $listings_result = database()->query("SELECT * FROM `shop_listings` WHERE `shop_id` = {$shop->id}");
        $listings = [];
        while($row = $listings_result->fetch_object()) {
            $listings[] = $row;
        }

        /* Get shop items */
        /* Get shop items with rating & total sold */
        $items_result = database()->query("
            SELECT i.*, 
                (SELECT SUM(qty) FROM `shop_orders` WHERE `item_id` = i.id AND `status` = 'paid') as total_sold,
                (SELECT AVG(rating) FROM `shop_reviews` WHERE `item_id` = i.id AND `status` = 'approved') as avg_rating,
                (SELECT COUNT(*) FROM `shop_reviews` WHERE `item_id` = i.id AND `status` = 'approved') as total_reviews
            FROM `shop_items` i 
            WHERE i.shop_id = {$shop->id} AND i.status = 1 
            ORDER BY i.datetime DESC
        ");
        $items = [];
        $item_ids = [];
        $items_map = [];
        while($row = $items_result->fetch_object()) {
            $row->reviews = [];
            $row->review_count = 0;
            $row->rating_total = 0;
            $row->rating = 0;
            $items_map[$row->id] = $row;
            $item_ids[] = $row->id;
        }

        if(!empty($item_ids)) {
            $ids_str = implode(',', $item_ids);
            $reviews_result = database()->query("
                SELECT r.*, c.full_name AS customer_name, c.email AS customer_email 
                FROM `shop_reviews` r
                JOIN `shop_orders` o ON r.order_id = o.id
                JOIN `shop_customers` c ON o.customer_id = c.id
                WHERE r.item_id IN ({$ids_str})
                ORDER BY r.datetime DESC
            ");
            if ($reviews_result) {
                while($r = $reviews_result->fetch_object()) {
                    if(isset($items_map[$r->item_id])) {
                        $items_map[$r->item_id]->reviews[] = $r;
                        $items_map[$r->item_id]->review_count++;
                        $items_map[$r->item_id]->rating_total += $r->rating;
                    }
                }
            }
            
            foreach($items_map as $item) {
                if($item->review_count > 0) {
                    $item->rating = round($item->rating_total / $item->review_count, 1);
                }
                $items[] = $item;
            }
        }

        /* Check if shop owner is verified */
        $owner = database()->query("SELECT `verification_status` FROM `users` WHERE `user_id` = {$shop->user_id}")->fetch_object();
        $shop_verified = ($owner->verification_status ?? '') === 'verified';

        /* Set a custom title */
        Title::set($shop->name);

        /* Prepare the view */
        $data = [
            'shop' => $shop,
            'listings' => $listings,
            'items' => $items,
            'shop_verified' => $shop_verified
        ];

        /* Main View */
        $view = new \Altum\View('store/index', (array) $this);
        $this->add_view_content('content', $view->run($data));

    }

}
