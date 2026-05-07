<?php
namespace Altum\Controllers;
defined('ALTUMCODE') || die();

class StoreReviewCreate extends Controller {

    public function index() {
        header('Content-Type: application/json');

        if(empty($_POST)) die(json_encode(['success' => false, 'message' => 'Invalid request']));

        $invoice = input_clean($_POST['invoice'] ?? '');
        $rating  = min(5, max(1, (int)($_POST['rating'] ?? 5)));
        $review  = input_clean($_POST['review'] ?? '');

        if(empty($invoice)) die(json_encode(['success' => false, 'message' => 'Invoice required']));

        /* Validate order is paid */
        $order = database()->query("SELECT * FROM `shop_orders` WHERE `invoice_number`='" . database()->real_escape_string($invoice) . "' AND `status`='paid'")->fetch_object() ?? null;
        if(!$order) die(json_encode(['success' => false, 'message' => 'Order not found or not paid']));

        /* Check duplicate review */
        $exists = database()->query("SELECT `id` FROM `shop_reviews` WHERE `order_id`={$order->id}")->fetch_object();
        if($exists) die(json_encode(['success' => false, 'message' => 'Already reviewed']));

        $datetime = \Altum\Date::$date;
        $review_esc = database()->real_escape_string($review);

        database()->query("INSERT INTO `shop_reviews` (`order_id`,`item_id`,`rating`,`review`,`datetime`)
            VALUES ({$order->id},{$order->item_id},{$rating},'{$review_esc}','{$datetime}')");

        die(json_encode(['success' => true]));
    }
}
