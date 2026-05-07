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

        /* Get shop listings (categories) */
        $listings_result = database()->query("SELECT * FROM `shop_listings` WHERE `shop_id` = {$shop->id}");
        $listings = [];
        while($row = $listings_result->fetch_object()) {
            $listings[] = $row;
        }

        /* Get shop items */
        // We will just fetch all active items for now
        $items_result = database()->query("SELECT * FROM `shop_items` WHERE `shop_id` = {$shop->id} AND `status` = 1 ORDER BY `datetime` DESC");
        $items = [];
        while($row = $items_result->fetch_object()) {
            $items[] = $row;
        }

        /* Set a custom title */
        Title::set($shop->name);

        /* Prepare the view */
        $data = [
            'shop' => $shop,
            'listings' => $listings,
            'items' => $items
        ];

        /* Main View */
        $view = new \Altum\View('store/index', (array) $this);
        $this->add_view_content('content', $view->run($data));

    }

}
