<?php
namespace Altum\Controllers;
defined('ALTUMCODE') || die();

class ShopAjax extends Controller {

    public function index() {
        \Altum\Authentication::guard();
        header('Content-Type: application/json');

        if(empty($_POST) || !\Altum\Csrf::check()) {
            die(json_encode(['success' => false, 'message' => 'Invalid request']));
        }

        $shop = database()->query("SELECT * FROM `shops` WHERE `user_id` = {$this->user->user_id}")->fetch_object() ?? null;
        if(!$shop) die(json_encode(['success' => false, 'message' => 'Shop not found']));

        $action = input_clean($_POST['action'] ?? '');

        switch($action) {
            case 'voucher_create':  $this->voucher_create($shop);  break;
            case 'voucher_update':  $this->voucher_update($shop);  break;
            case 'voucher_delete':  $this->voucher_delete($shop);  break;
            case 'listing_create':  $this->listing_create($shop);  break;
            case 'listing_update':  $this->listing_update($shop);  break;
            case 'listing_delete':  $this->listing_delete($shop);  break;
            case 'review_delete':   $this->review_delete($shop);   break;
            default: die(json_encode(['success' => false, 'message' => 'Unknown action']));
        }
    }

    private function voucher_create($shop) {
        $code        = strtoupper(input_clean($_POST['code'] ?? ''));
        $discount    = min(100, max(1, (int)($_POST['discount_percentage'] ?? 0)));
        $is_unlimited= isset($_POST['is_unlimited']) && $_POST['is_unlimited'] ? 1 : 0;
        $quota       = $is_unlimited ? 'NULL' : ((int)($_POST['quota'] ?? 0) > 0 ? (int)$_POST['quota'] : 'NULL');
        $is_active   = isset($_POST['is_active']) && $_POST['is_active'] ? 1 : 0;
        $item_id     = !empty($_POST['item_id']) ? (int)$_POST['item_id'] : 'NULL';
        $valid_from  = !empty($_POST['valid_from']) ? "'" . database()->real_escape_string($_POST['valid_from']) . "'" : 'NULL';
        $valid_to    = !empty($_POST['valid_to'])   ? "'" . database()->real_escape_string($_POST['valid_to'])   . "'" : 'NULL';
        $datetime    = \Altum\Date::$date;

        if(empty($code)) die(json_encode(['success' => false, 'message' => 'Voucher code required']));

        $exists = database()->query("SELECT `id` FROM `shop_vouchers` WHERE `shop_id` = {$shop->id} AND `code` = '" . database()->real_escape_string($code) . "'")->fetch_object();
        if($exists) die(json_encode(['success' => false, 'message' => 'Code already exists']));

        database()->query("INSERT INTO `shop_vouchers` (`shop_id`,`item_id`,`code`,`discount_percentage`,`is_unlimited`,`quota`,`is_active`,`valid_from`,`valid_to`,`datetime`)
            VALUES ({$shop->id},{$item_id},'" . database()->real_escape_string($code) . "',{$discount},{$is_unlimited},{$quota},{$is_active},{$valid_from},{$valid_to},'{$datetime}')");

        die(json_encode(['success' => true]));
    }

    private function voucher_update($shop) {
        $id          = (int)($_POST['id'] ?? 0);
        $code        = strtoupper(input_clean($_POST['code'] ?? ''));
        $discount    = min(100, max(1, (int)($_POST['discount_percentage'] ?? 0)));
        $is_unlimited= isset($_POST['is_unlimited']) && $_POST['is_unlimited'] ? 1 : 0;
        $quota       = $is_unlimited ? 'NULL' : ((int)($_POST['quota'] ?? 0) > 0 ? (int)$_POST['quota'] : 'NULL');
        $is_active   = isset($_POST['is_active']) && $_POST['is_active'] ? 1 : 0;
        $item_id     = !empty($_POST['item_id']) ? (int)$_POST['item_id'] : 'NULL';
        $valid_from  = !empty($_POST['valid_from']) ? "'" . database()->real_escape_string($_POST['valid_from']) . "'" : 'NULL';
        $valid_to    = !empty($_POST['valid_to'])   ? "'" . database()->real_escape_string($_POST['valid_to'])   . "'" : 'NULL';

        if(!$id || empty($code)) die(json_encode(['success' => false, 'message' => 'Invalid data']));

        $v = database()->query("SELECT `id` FROM `shop_vouchers` WHERE `id` = {$id} AND `shop_id` = {$shop->id}")->fetch_object();
        if(!$v) die(json_encode(['success' => false, 'message' => 'Not found']));

        database()->query("UPDATE `shop_vouchers` SET
            `code`='" . database()->real_escape_string($code) . "',`discount_percentage`={$discount},
            `is_unlimited`={$is_unlimited},`quota`={$quota},`is_active`={$is_active},
            `item_id`={$item_id},`valid_from`={$valid_from},`valid_to`={$valid_to}
            WHERE `id`={$id} AND `shop_id`={$shop->id}");

        die(json_encode(['success' => true]));
    }

    private function voucher_delete($shop) {
        $id = (int)($_POST['id'] ?? 0);
        if(!$id) die(json_encode(['success' => false, 'message' => 'Invalid ID']));
        $v = database()->query("SELECT `id` FROM `shop_vouchers` WHERE `id`={$id} AND `shop_id`={$shop->id}")->fetch_object();
        if(!$v) die(json_encode(['success' => false, 'message' => 'Not found']));
        database()->query("DELETE FROM `shop_vouchers` WHERE `id`={$id}");
        die(json_encode(['success' => true]));
    }

    private function listing_create($shop) {
        $name        = input_clean($_POST['name'] ?? '');
        $description = input_clean($_POST['description'] ?? '');
        $item_ids    = isset($_POST['item_ids']) && is_array($_POST['item_ids']) ? array_map('intval', $_POST['item_ids']) : [];
        $datetime    = \Altum\Date::$date;

        if(empty($name)) die(json_encode(['success' => false, 'message' => 'Name required']));

        database()->query("INSERT INTO `shop_listings` (`shop_id`,`name`,`description`,`datetime`)
            VALUES ({$shop->id},'" . database()->real_escape_string($name) . "','" . database()->real_escape_string($description) . "','{$datetime}')");
        $listing_id = database()->insert_id;

        foreach($item_ids as $iid) {
            database()->query("UPDATE `shop_items` SET `listing_id`={$listing_id} WHERE `id`={$iid} AND `shop_id`={$shop->id}");
        }

        die(json_encode(['success' => true, 'id' => $listing_id]));
    }

    private function listing_update($shop) {
        $id          = (int)($_POST['id'] ?? 0);
        $name        = input_clean($_POST['name'] ?? '');
        $description = input_clean($_POST['description'] ?? '');
        $item_ids    = isset($_POST['item_ids']) && is_array($_POST['item_ids']) ? array_map('intval', $_POST['item_ids']) : [];

        if(!$id || empty($name)) die(json_encode(['success' => false, 'message' => 'Invalid data']));
        $l = database()->query("SELECT `id` FROM `shop_listings` WHERE `id`={$id} AND `shop_id`={$shop->id}")->fetch_object();
        if(!$l) die(json_encode(['success' => false, 'message' => 'Not found']));

        database()->query("UPDATE `shop_listings` SET `name`='" . database()->real_escape_string($name) . "',`description`='" . database()->real_escape_string($description) . "' WHERE `id`={$id} AND `shop_id`={$shop->id}");
        database()->query("UPDATE `shop_items` SET `listing_id`=NULL WHERE `listing_id`={$id} AND `shop_id`={$shop->id}");
        foreach($item_ids as $iid) {
            database()->query("UPDATE `shop_items` SET `listing_id`={$id} WHERE `id`={$iid} AND `shop_id`={$shop->id}");
        }
        die(json_encode(['success' => true]));
    }

    private function listing_delete($shop) {
        $id = (int)($_POST['id'] ?? 0);
        if(!$id) die(json_encode(['success' => false, 'message' => 'Invalid ID']));
        $l = database()->query("SELECT `id` FROM `shop_listings` WHERE `id`={$id} AND `shop_id`={$shop->id}")->fetch_object();
        if(!$l) die(json_encode(['success' => false, 'message' => 'Not found']));
        database()->query("UPDATE `shop_items` SET `listing_id`=NULL WHERE `listing_id`={$id} AND `shop_id`={$shop->id}");
        database()->query("DELETE FROM `shop_listings` WHERE `id`={$id}");
        die(json_encode(['success' => true]));
    }

    private function review_delete($shop) {
        $id = (int)($_POST['id'] ?? 0);
        if(!$id) die(json_encode(['success' => false, 'message' => 'Invalid ID']));
        $r = database()->query("SELECT r.`id` FROM `shop_reviews` r JOIN `shop_items` i ON r.item_id=i.id WHERE r.id={$id} AND i.shop_id={$shop->id}")->fetch_object();
        if(!$r) die(json_encode(['success' => false, 'message' => 'Not found']));
        database()->query("DELETE FROM `shop_reviews` WHERE `id`={$id}");
        die(json_encode(['success' => true]));
    }
}
