<?php
/*
 * Admin Shop Withdrawals Controller
 * Routes:
 *   GET  admin/shop-withdrawals             → list
 *   POST admin/shop-withdrawals/approve/{id} → tandai paid
 *   POST admin/shop-withdrawals/reject/{id}  → kembalikan saldo
 *   GET  admin/shop-withdrawals/settle       → manual settle pending_funds
 */

namespace Altum\Controllers;

use Altum\Alerts;

defined('ALTUMCODE') || die();

class AdminShopWithdrawals extends Controller {

    public function index() {

        \Altum\Authentication::guard('admin');

        /* ── Manual settlement trigger (via ?action=settle) ── */
        if(isset($_GET['action']) && $_GET['action'] === 'settle') {
            $this->run_settlement();
            Alerts::add_success('Settlement selesai dijalankan. Saldo penjual telah diperbarui.');
            redirect('admin/shop-withdrawals');
        }

        $withdrawals_result = database()->query("
            SELECT w.*, u.name AS user_name, u.email AS user_email,
                   b.bank_name, b.account_number, b.account_name
            FROM `shop_withdrawals` w
            JOIN `users` u ON w.user_id = u.user_id
            LEFT JOIN `shop_bank_accounts` b ON b.user_id = w.user_id
            ORDER BY w.datetime DESC
        ");

        $withdrawals = [];
        if($withdrawals_result) {
            while($row = $withdrawals_result->fetch_object()) {
                $withdrawals[] = $row;
            }
        }

        /* Stats */
        $stats = database()->query("
            SELECT
                SUM(CASE WHEN status='pending' THEN amount ELSE 0 END) AS total_pending,
                SUM(CASE WHEN status='paid' THEN amount ELSE 0 END) AS total_paid,
                COUNT(CASE WHEN status='pending' THEN 1 END) AS cnt_pending
            FROM `shop_withdrawals`
        ")->fetch_object();

        \Altum\Title::set('Shop Withdrawals — Admin');
        $view = new \Altum\View('admin/shop-withdrawals/index', (array) $this);
        $this->add_view_content('content', $view->run([
            'withdrawals' => $withdrawals,
            'stats'       => $stats,
        ]));
    }

    /* ── Approve withdrawal: tandai 'paid' (admin sudah transfer ke user) ── */
    public function approve() {

        \Altum\Authentication::guard('admin');

        if(!\Altum\Csrf::check()) {
            Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            redirect('admin/shop-withdrawals');
        }

        $withdrawal_id = isset($this->params[0]) ? (int) $this->params[0] : 0;
        $withdrawal    = database()->query("SELECT * FROM `shop_withdrawals` WHERE `id` = {$withdrawal_id}")->fetch_object();

        if(!$withdrawal || $withdrawal->status !== 'pending') {
            Alerts::add_error('Withdrawal tidak ditemukan atau sudah diproses.');
            redirect('admin/shop-withdrawals');
        }

        /* Tandai paid (dana sudah dikirim admin ke rekening user) */
        database()->query("UPDATE `shop_withdrawals` SET `status` = 'paid' WHERE `id` = {$withdrawal_id}");

        Alerts::add_success('Withdrawal #' . $withdrawal_id . ' ditandai sebagai PAID (sudah transfer ke seller).');
        redirect('admin/shop-withdrawals');
    }

    /* ── Reject: kembalikan saldo ke withdrawable_funds ── */
    public function reject() {

        \Altum\Authentication::guard('admin');

        if(!\Altum\Csrf::check()) {
            Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            redirect('admin/shop-withdrawals');
        }

        $withdrawal_id = isset($this->params[0]) ? (int) $this->params[0] : 0;
        $withdrawal    = database()->query("SELECT * FROM `shop_withdrawals` WHERE `id` = {$withdrawal_id}")->fetch_object();

        if(!$withdrawal || $withdrawal->status !== 'pending') {
            Alerts::add_error('Withdrawal tidak ditemukan atau sudah diproses.');
            redirect('admin/shop-withdrawals');
        }

        /* Kembalikan saldo ke withdrawable_funds */
        database()->query("UPDATE `users` SET `withdrawable_funds` = `withdrawable_funds` + {$withdrawal->amount} WHERE `user_id` = {$withdrawal->user_id}");
        database()->query("UPDATE `shop_withdrawals` SET `status` = 'rejected' WHERE `id` = {$withdrawal_id}");

        Alerts::add_success('Withdrawal ditolak. Saldo Rp ' . number_format($withdrawal->amount, 0, ',', '.') . ' dikembalikan ke seller.');
        redirect('admin/shop-withdrawals');
    }

    /* ── Settlement: pindahkan pending_funds → withdrawable_funds untuk order yang sudah paid ── */
    private function run_settlement() {
        /*
         * Logic: Semua order yang status = 'paid' dan settle_status = 'unsettled'
         * dan sudah lebih dari 1 hari → pindahkan 95% grand_total ke withdrawable_funds seller
         * (5% adalah service fee platform)
         */
        $orders = database()->query("
            SELECT o.*, s.user_id AS seller_user_id
            FROM `shop_orders` o
            JOIN `shops` s ON o.shop_id = s.id
            WHERE o.status = 'paid'
              AND o.settle_status = 'unsettled'
        ");

        if(!$orders) return;

        $settled = 0;
        while($order = $orders->fetch_object()) {
            $seller_revenue = (float)$order->grand_total - (float)$order->service_fee;
            database()->query("UPDATE `users` SET
                `withdrawable_funds` = `withdrawable_funds` + {$seller_revenue},
                `pending_funds`      = GREATEST(0, `pending_funds` - {$seller_revenue})
                WHERE `user_id` = {$order->seller_user_id}");
            database()->query("UPDATE `shop_orders` SET `settle_status` = 'settled' WHERE `id` = {$order->id}");
            $settled++;
        }
    }
}
