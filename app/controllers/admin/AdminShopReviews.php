<?php
namespace Altum\Controllers;
defined('ALTUMCODE') || die();

class AdminShopReviews extends Controller {

    public function index() {
        $status = isset($_GET['status']) ? input_clean($_GET['status']) : 'all';

        $where = "WHERE 1=1";
        if($status === 'reported') {
            $where .= " AND `is_reported` = 1";
        } elseif($status === 'hidden') {
            $where .= " AND `status` = 'hidden'";
        }

        $reviews_result = database()->query("
            SELECT r.*, i.name as item_name, s.name as shop_name, c.email as buyer_email, c.full_name as buyer_name
            FROM `shop_reviews` r
            JOIN `shop_items` i ON r.item_id = i.id
            JOIN `shops` s ON i.shop_id = s.id
            JOIN `shop_orders` o ON r.order_id = o.id
            JOIN `shop_customers` c ON o.customer_id = c.id
            {$where}
            ORDER BY r.id DESC
            LIMIT 50
        ");

        $reviews = [];
        while($row = $reviews_result->fetch_object()) {
            $reviews[] = $row;
        }

        $data = [
            'reviews' => $reviews,
            'status' => $status
        ];

        /* Main View */
        $view = new \Altum\View('admin/shop_reviews/index', (array) $this);
        $this->add_view_content('content', $view->run($data));
    }

    public function hide() {
        \Altum\Authentication::guard();
        if(!\Altum\Csrf::check()) { die('CSRF invalid'); }

        $id = (int)$this->params[0];
        database()->query("UPDATE `shop_reviews` SET `status` = 'hidden' WHERE `id` = {$id}");
        \Altum\Alerts::add_success('Review hidden successfully.');
        redirect('admin/shop-reviews');
    }

    public function unreport() {
        \Altum\Authentication::guard();
        if(!\Altum\Csrf::check()) { die('CSRF invalid'); }

        $id = (int)$this->params[0];
        database()->query("UPDATE `shop_reviews` SET `is_reported` = 0, `report_reason` = NULL, `status` = 'approved' WHERE `id` = {$id}");
        \Altum\Alerts::add_success('Review marked as safe and un-reported.');
        redirect('admin/shop-reviews');
    }

    public function delete() {
        \Altum\Authentication::guard();
        if(!\Altum\Csrf::check()) { die('CSRF invalid'); }

        $id = (int)$this->params[0];
        database()->query("DELETE FROM `shop_reviews` WHERE `id` = {$id}");
        \Altum\Alerts::add_success('Review deleted permanently.');
        redirect('admin/shop-reviews');
    }
}
