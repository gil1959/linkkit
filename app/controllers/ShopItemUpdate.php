<?php
/*
 * Shop Item Update Controller
 */

namespace Altum\Controllers;

use Altum\Title;
use Altum\Alerts;

class ShopItemUpdate extends Controller {

    public function index() {

        \Altum\Authentication::guard();

        $shop = database()->query("SELECT * FROM `shops` WHERE `user_id` = {$this->user->user_id}")->fetch_object() ?? null;
        if(!$shop) redirect('shop');

        $item_id = (int) ($_GET['item_id'] ?? 0);
        $item = database()->query("SELECT * FROM `shop_items` WHERE `id` = {$item_id} AND `shop_id` = {$shop->id}")->fetch_object() ?? null;
        if(!$item) {
            Alerts::add_error('Item not found.');
            redirect('shop');
        }

        if(!empty($_POST)) {
            if(!\Altum\Csrf::check()) {
                Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            }

            $_POST['name']        = input_clean($_POST['name'] ?? '');
            $_POST['description'] = input_clean($_POST['description'] ?? '');
            $_POST['price']       = abs((float) ($_POST['price'] ?? 0));
            $_POST['type']        = in_array($_POST['type'] ?? '', ['download_link', 'webhook_event', 'random_code', 'manual'])
                                    ? $_POST['type'] : 'download_link';
            $_POST['stock']       = (isset($_POST['stock']) && $_POST['stock'] !== '') ? abs((int) $_POST['stock']) : null;
            $_POST['status']      = isset($_POST['status']) ? 1 : 0;

            $download_links = !empty($_POST['download_links']) ? json_encode([$_POST['download_links']]) : $item->download_links;

            if(empty($_POST['name'])) {
                Alerts::add_error('Product name is required.');
            }

            /* Handle image upload */
            $image = \Altum\Uploads::process_upload($item->image, 'shop_items', 'image', 'image_remove', 5);

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {
                $stmt = database()->prepare("
                    UPDATE `shop_items` SET
                        `type` = ?,
                        `download_links` = ?,
                        `name` = ?,
                        `description` = ?,
                        `image` = ?,
                        `price` = ?,
                        `stock` = ?,
                        `status` = ?
                    WHERE `id` = ? AND `shop_id` = ?
                ");
                $stmt->bind_param(
                    'sssssdiiii',
                    $_POST['type'],
                    $download_links,
                    $_POST['name'],
                    $_POST['description'],
                    $image,
                    $_POST['price'],
                    $_POST['stock'],
                    $_POST['status'],
                    $item->id,
                    $shop->id
                );
                $stmt->execute();
                $stmt->close();

                Alerts::add_success('Product updated successfully!');
                redirect('shop-item-update?item_id=' . $item->id);
            }
        }

        /* Decode existing download link */
        $existing_download_link = '';
        if($item->download_links) {
            $links = json_decode($item->download_links, true);
            $existing_download_link = $links[0] ?? '';
        }

        Title::set('Edit Product - ' . $item->name);
        $view = new \Altum\View('shop_item_update/index', (array) $this);
        $this->add_view_content('content', $view->run([
            'shop' => $shop,
            'item' => $item,
            'existing_download_link' => $existing_download_link,
        ]));
    }
}
