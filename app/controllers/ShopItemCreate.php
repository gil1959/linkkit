<?php
/*
 * Shop Item Create Controller
 */

namespace Altum\Controllers;

use Altum\Title;
use Altum\Alerts;

class ShopItemCreate extends Controller {

    public function index() {

        \Altum\Authentication::guard();

        $shop = database()->query("SELECT * FROM `shops` WHERE `user_id` = {$this->user->user_id}")->fetch_object() ?? null;
        if(!$shop) redirect('shop');

        if(!empty($_POST)) {
            /* Input validation */
            if(!\Altum\Csrf::check()) {
                Alerts::add_error(l('global.error_message.invalid_csrf_token'));
                redirect('shop-item-create');
            }

            $_POST['name']        = input_clean($_POST['name'] ?? '');
            $_POST['description'] = input_clean($_POST['description'] ?? '');
            $_POST['price']       = abs((float) ($_POST['price'] ?? 0));
            $_POST['type']        = in_array($_POST['type'] ?? '', ['download_link', 'webhook_event', 'random_code', 'manual'])
                                    ? $_POST['type'] : 'download_link';
            $_POST['stock']       = (isset($_POST['stock']) && $_POST['stock'] !== '') ? abs((int) $_POST['stock']) : null;

            $download_links = !empty($_POST['download_links']) ? json_encode([$_POST['download_links']]) : null;

            if(empty($_POST['name'])) {
                Alerts::add_error('Product name is required.');
            }

            /* Handle image upload */
            $image = \Altum\Uploads::process_upload(null, 'shop_items', 'image', 'image_remove', 5);

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {
                $datetime = \Altum\Date::$date;
                $stmt = database()->prepare("
                    INSERT INTO `shop_items`
                    (`shop_id`, `type`, `download_links`, `name`, `description`, `image`, `price`, `stock`, `status`, `datetime`)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1, ?)
                ");
                $stmt->bind_param(
                    'isssssdis',
                    $shop->id,
                    $_POST['type'],
                    $download_links,
                    $_POST['name'],
                    $_POST['description'],
                    $image,
                    $_POST['price'],
                    $_POST['stock'],
                    $datetime
                );
                $stmt->execute();
                $stmt->close();

                Alerts::add_success('Product added successfully!');
                redirect('shop');
            }
        }

        Title::set('Add Product - ' . $shop->name);
        $view = new \Altum\View('shop_item_create/index', (array) $this);
        $this->add_view_content('content', $view->run(['shop' => $shop]));
    }
}
