<?php
/*
 * Shop Settings Update Controller
 * Handles: info (cover, logo, url, toggles), bank account, notification settings
 *
 * NOTE: Menggunakan header() langsung (BUKAN redirect()) untuk menghindari
 * bug dimana redirect() override tujuan berdasarkan $_GET['original_request'].
 */

namespace Altum\Controllers;

use Altum\Alerts;

class ShopSettingsUpdate extends Controller {

    public function index() {

        \Altum\Authentication::guard();

        if(empty($_POST)) {
            header('Location: ' . url('shop'));
            die();
        }

        if(!\Altum\Csrf::check()) {
            Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            header('Location: ' . url('shop') . '#shop_settings');
            die();
        }

        $shop = database()->query("SELECT * FROM `shops` WHERE `user_id` = {$this->user->user_id}")->fetch_object() ?? null;
        if(!$shop) {
            header('Location: ' . url('shop'));
            die();
        }

        $section = input_clean($_POST['section'] ?? '');

        switch($section) {

            case 'info':
                $name              = input_clean($_POST['name'] ?? $shop->name);
                $description       = strip_tags(input_clean($_POST['description'] ?? ''));
                $url               = get_slug($_POST['url'] ?? $shop->url);
                $is_review_enabled = isset($_POST['is_review_enabled']) ? 1 : 0;
                $is_active         = isset($_POST['is_active']) ? 0 : 1;

                /* Check URL uniqueness */
                if($url !== $shop->url) {
                    $exists = database()->query("SELECT `id` FROM `shops` WHERE `url` = '{$url}' AND `id` != {$shop->id}")->fetch_object();
                    if($exists) {
                        Alerts::add_error('URL sudah dipakai toko lain.');
                        header('Location: ' . url('shop') . '#shop_settings');
                        die();
                    }
                }

                /* Handle cover image upload */
                $cover_image = \Altum\Uploads::process_upload(
                    $shop->cover_image ?? null, 'shop_covers', 'cover_image', 'cover_image_remove', 5
                );

                /* Handle logo image upload */
                $logo_image = \Altum\Uploads::process_upload(
                    $shop->logo_image ?? null, 'shop_logos', 'logo_image', 'logo_image_remove', 2
                );

                if(!Alerts::has_errors()) {
                    $stmt = database()->prepare("
                        UPDATE `shops` SET
                            `name` = ?, `description` = ?, `url` = ?,
                            `cover_image` = ?, `logo_image` = ?,
                            `is_review_enabled` = ?, `is_active` = ?
                        WHERE `id` = ?
                    ");
                    $stmt->bind_param('sssssiii',
                        $name, $description, $url,
                        $cover_image, $logo_image,
                        $is_review_enabled, $is_active,
                        $shop->id
                    );
                    $stmt->execute();
                    $stmt->close();
                    Alerts::add_success('Pengaturan toko berhasil disimpan.');
                }
                header('Location: ' . url('shop') . '#shop_settings');
                die();

            case 'bank':
                $bank_name      = input_clean($_POST['bank_name'] ?? '');
                $account_number = input_clean($_POST['account_number'] ?? '');
                $account_name   = input_clean($_POST['account_name'] ?? '');

                $existing = database()->query("SELECT `id` FROM `shop_bank_accounts` WHERE `user_id` = {$this->user->user_id}")->fetch_object();
                if($existing) {
                    $stmt = database()->prepare("UPDATE `shop_bank_accounts` SET `bank_name`=?, `account_number`=?, `account_name`=? WHERE `user_id`=?");
                    $stmt->bind_param('sssi', $bank_name, $account_number, $account_name, $this->user->user_id);
                } else {
                    $stmt = database()->prepare("INSERT INTO `shop_bank_accounts` (`user_id`, `bank_name`, `account_number`, `account_name`) VALUES (?, ?, ?, ?)");
                    $stmt->bind_param('isss', $this->user->user_id, $bank_name, $account_number, $account_name);
                }
                $stmt->execute();
                $stmt->close();
                Alerts::add_success('Informasi bank berhasil disimpan.');
                header('Location: ' . url('shop') . '#shop_settings');
                die();

            case 'notification':
                $notification_settings = json_encode([
                    'purchase_success' => isset($_POST['notify_purchase']) ? 1 : 0,
                    'notify_review'    => isset($_POST['notify_review'])   ? 1 : 0,
                ]);
                $stmt = database()->prepare("UPDATE `shops` SET `notification_settings` = ? WHERE `id` = ?");
                $stmt->bind_param('si', $notification_settings, $shop->id);
                $stmt->execute();
                $stmt->close();
                Alerts::add_success('Pengaturan notifikasi berhasil disimpan.');
                header('Location: ' . url('shop') . '#shop_settings');
                die();
            case 'origin_city':
                $origin_city_id   = (int)($_POST['origin_city_id'] ?? 0);
                $origin_city_name = input_clean($_POST['origin_city_name'] ?? '');
                $origin_province  = input_clean($_POST['origin_province'] ?? '');

                if(!$origin_city_id || empty($origin_city_name)) {
                    Alerts::add_error('Pilih kota asal pengiriman terlebih dahulu.');
                    header('Location: ' . url('shop') . '#shop_settings');
                    die();
                }

                $stmt = database()->prepare("UPDATE `shops` SET `origin_city_id`=?, `origin_city_name`=?, `origin_province`=? WHERE `id`=?");
                $stmt->bind_param('issi', $origin_city_id, $origin_city_name, $origin_province, $shop->id);
                $stmt->execute();
                $stmt->close();
                Alerts::add_success('Alamat asal pengiriman berhasil disimpan.');

                /* Jika ada draft produk fisik yang pending, redirect balik ke create */
                if(!empty($_SESSION['pending_physical_product'])) {
                    header('Location: ' . url('shop-item-create') . '?restore_draft=1');
                } else {
                    header('Location: ' . url('shop') . '#shop_settings');
                }
                die();

        }

        /* Fallback — tetap di shop settings */
        header('Location: ' . url('shop') . '#shop_settings');
        die();
    }
}
