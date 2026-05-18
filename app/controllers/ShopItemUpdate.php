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

        /* Ambil daftar listing/kategori toko untuk dropdown Product Listing */
        $listings_result = database()->query("SELECT * FROM `shop_listings` WHERE `shop_id` = {$shop->id}");
        $listings = [];
        if ($listings_result) {
            while($row = $listings_result->fetch_object()) {
                $listings[] = $row;
            }
        }

        if(!empty($_POST)) {
            if(!\Altum\Csrf::check()) {
                Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            }

            $_POST['name']        = input_clean($_POST['name'] ?? '');
            // Allow safe HTML text tags from the rich text editor (no scripts/images)
            $_POST['description'] = strip_tags(
                $_POST['description'] ?? '',
                '<p><br><strong><em><u><s><h1><h2><h3><ul><ol><li><blockquote><a><span>'
            );
            $_POST['price']       = abs((float) ($_POST['price'] ?? 0));
            $_POST['type']        = in_array($_POST['type'] ?? '', ['download_link', 'webhook_event', 'random_code', 'manual', 'physical'])
                                    ? $_POST['type'] : 'download_link';
            $_POST['stock']       = (isset($_POST['stock']) && $_POST['stock'] !== '') ? abs((int) $_POST['stock']) : null;
            $_POST['status']      = isset($_POST['status']) ? 1 : 0;

            $_POST['listing_id']          = (isset($_POST['listing_id']) && $_POST['listing_id'] !== '') ? (int) $_POST['listing_id'] : null;
            $_POST['is_flexible_amount']  = isset($_POST['is_flexible_amount']) ? 1 : 0;
            $_POST['has_variants']        = isset($_POST['has_variants']) ? 1 : 0;
            $_POST['qty_per_transaction'] = (isset($_POST['qty_per_transaction']) && (int)$_POST['qty_per_transaction'] > 0) ? (int) $_POST['qty_per_transaction'] : null;
            $_POST['has_discount']        = isset($_POST['has_discount']) ? 1 : 0;
            $_POST['discount_price']      = isset($_POST['discount_price']) && $_POST['discount_price'] !== '' ? abs((float) $_POST['discount_price']) : null;
            $_POST['is_flash_sale']       = isset($_POST['is_flash_sale']) ? 1 : 0;

            /* Physical product fields */
            $weight = null; $length = null; $width = null; $height = null;
            if($_POST['type'] === 'physical') {
                $weight = isset($_POST['weight']) && $_POST['weight'] !== '' ? abs((float)$_POST['weight']) : null;
                $length = isset($_POST['length']) && $_POST['length'] !== '' ? abs((int)$_POST['length']) : null;
                $width  = isset($_POST['width'])  && $_POST['width']  !== '' ? abs((int)$_POST['width'])  : null;
                $height = isset($_POST['height']) && $_POST['height'] !== '' ? abs((int)$_POST['height']) : null;
            }

            $download_links = !empty($_POST['download_links']) ? json_encode([$_POST['download_links']]) : $item->download_links;

            if(empty($_POST['name'])) {
                Alerts::add_error('Product name is required.');
            }

            if($_POST['has_discount'] && $_POST['discount_price'] !== null && $_POST['discount_price'] >= $_POST['price']) {
                Alerts::add_error('Harga diskon harus lebih rendah dari harga normal.');
            }

            /* Handle image upload */
            $image = \Altum\Uploads::process_upload($item->image, 'shop_items', 'image', 'image_remove', 5);
            $image2 = \Altum\Uploads::process_upload($item->image2 ?? null, 'shop_items', 'image2', 'image2_remove', 5);
            $image3 = \Altum\Uploads::process_upload($item->image3 ?? null, 'shop_items', 'image3', 'image3_remove', 5);
            $image4 = \Altum\Uploads::process_upload($item->image4 ?? null, 'shop_items', 'image4', 'image4_remove', 5);

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {
                $stmt = database()->prepare("
                    UPDATE `shop_items` SET
                        `listing_id` = ?,
                        `type` = ?,
                        `download_links` = ?,
                        `name` = ?,
                        `description` = ?,
                        `image` = ?,
                        `image2` = ?,
                        `image3` = ?,
                        `image4` = ?,
                        `price` = ?,
                        `is_flexible_amount` = ?,
                        `has_variants` = ?,
                        `stock` = ?,
                        `qty_per_transaction` = ?,
                        `has_discount` = ?,
                        `discount_price` = ?,
                        `is_flash_sale` = ?,
                        `weight` = ?,
                        `length` = ?,
                        `width` = ?,
                        `height` = ?,
                        `status` = ?
                    WHERE `id` = ? AND `shop_id` = ?
                ");
                $stmt->bind_param(
                    'issssssssdiidiiidiiiiii',
                    $_POST['listing_id'],
                    $_POST['type'],
                    $download_links,
                    $_POST['name'],
                    $_POST['description'],
                    $image,
                    $image2,
                    $image3,
                    $image4,
                    $_POST['price'],
                    $_POST['is_flexible_amount'],
                    $_POST['has_variants'],
                    $_POST['stock'],
                    $_POST['qty_per_transaction'],
                    $_POST['has_discount'],
                    $_POST['discount_price'],
                    $_POST['is_flash_sale'],
                    $weight,
                    $length,
                    $width,
                    $height,
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
            'shop'                   => $shop,
            'item'                   => $item,
            'existing_download_link' => $existing_download_link,
            'listings'               => $listings,
        ]));
    }
}
