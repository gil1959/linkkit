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

        /* ── Restore draft dari session jika diarahkan balik dari settings ── */
        $draft = null;
        if(isset($_GET['restore_draft']) && !empty($_SESSION['pending_physical_product'])) {
            $draft = $_SESSION['pending_physical_product'];
            unset($_SESSION['pending_physical_product']);
        }

        if(!empty($_POST)) {
            /* Input validation */
            if(!\Altum\Csrf::check()) {
                Alerts::add_error(l('global.error_message.invalid_csrf_token'));
                redirect('shop-item-create');
            }

            $allowed_types = ['download_link', 'webhook_event', 'random_code', 'manual', 'physical'];

            $_POST['name']        = input_clean($_POST['name'] ?? '');
            // Allow safe HTML text tags from the rich text editor
            $_POST['description'] = strip_tags(
                $_POST['description'] ?? '',
                '<p><br><strong><em><u><s><h1><h2><h3><ul><ol><li><blockquote><a><span>'
            );
            $_POST['price']       = abs((float) ($_POST['price'] ?? 0));
            $_POST['type']        = in_array($_POST['type'] ?? '', $allowed_types) ? $_POST['type'] : 'download_link';
            $_POST['stock']       = (isset($_POST['stock']) && $_POST['stock'] !== '') ? abs((int) $_POST['stock']) : null;

            /* Physical product fields */
            $weight = null; $length = null; $width = null; $height = null;
            if($_POST['type'] === 'physical') {
                $weight = isset($_POST['weight']) && $_POST['weight'] !== '' ? abs((float)$_POST['weight']) : null;
                $length = isset($_POST['length']) && $_POST['length'] !== '' ? abs((int)$_POST['length']) : null;
                $width  = isset($_POST['width'])  && $_POST['width']  !== '' ? abs((int)$_POST['width'])  : null;
                $height = isset($_POST['height']) && $_POST['height'] !== '' ? abs((int)$_POST['height']) : null;

                /* Cek apakah origin city sudah diset di toko */
                if(empty($shop->origin_city_id)) {
                    /* Simpan data form ke session */
                    $_SESSION['pending_physical_product'] = $_POST;
                    Alerts::add_info('Isi dulu alamat kota asal pengiriman di pengaturan toko agar bisa jual produk fisik.');
                    header('Location: ' . url('shop') . '#shop_settings_origin');
                    die();
                }
            }

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
                    (`shop_id`, `type`, `download_links`, `name`, `description`, `image`, `price`, `stock`, `weight`, `length`, `width`, `height`, `status`, `datetime`)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, ?)
                ");
                $stmt->bind_param(
                    'isssssdidiiis',
                    $shop->id,
                    $_POST['type'],
                    $download_links,
                    $_POST['name'],
                    $_POST['description'],
                    $image,
                    $_POST['price'],
                    $_POST['stock'],
                    $weight,
                    $length,
                    $width,
                    $height,
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
        $this->add_view_content('content', $view->run([
            'shop'  => $shop,
            'draft' => $draft,
        ]));
    }
}
