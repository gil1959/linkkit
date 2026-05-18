<?php
/*
 * Shop Item Delete Controller
 */

namespace Altum\Controllers;

use Altum\Alerts;

class ShopItemDelete extends Controller {

    public function index() {
        \Altum\Authentication::guard();

        if(empty($_POST)) {
            redirect('shop');
        }

        /* Verify the CSRF token */
        if(!\Altum\Csrf::check()) {
            Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            redirect('shop');
        }

        $item_id = isset($_POST['item_id']) ? (int) $_POST['item_id'] : 0;

        /* Get shop details */
        $shop = database()->query("SELECT * FROM `shops` WHERE `user_id` = {$this->user->user_id}")->fetch_object() ?? null;
        if(!$shop) {
            redirect('shop');
        }

        /* Get item details and verify ownership */
        $item = database()->query("SELECT * FROM `shop_items` WHERE `id` = {$item_id} AND `shop_id` = {$shop->id}")->fetch_object() ?? null;
        if(!$item) {
            redirect('shop');
        }

        /* Delete images from file system */
        foreach(['image', 'image2', 'image3', 'image4'] as $image_key) {
            if(!empty($item->{$image_key}) && file_exists(UPLOADS_PATH . 'shop_items/' . $item->{$image_key})) {
                unlink(UPLOADS_PATH . 'shop_items/' . $item->{$image_key});
            }
        }

        /* Delete from database */
        database()->query("DELETE FROM `shop_items` WHERE `id` = {$item->id}");

        /* Also delete related orders or statistics if necessary (assuming ON DELETE CASCADE on foreign keys, but just in case, we leave it to DB schema or delete them manually if not set) */
        /* Currently keeping it simple for the item itself */

        /* Set a nice success message */
        Alerts::add_success('Produk berhasil dihapus.');

        redirect('shop');
    }
}
