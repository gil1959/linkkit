<?php
/*
 * Admin Shop Withdrawals Controller
 */

namespace Altum\Controllers;

use Altum\Alerts;

defined('ALTUMCODE') || die();

class AdminShopWithdrawals extends Controller {

    public function index() {

        \Altum\Authentication::guard('admin');

        $withdrawals_result = database()->query("
            SELECT w.*, u.name AS user_name, u.email AS user_email,
                   b.bank_name, b.account_number, b.account_name
            FROM `shop_withdrawals` w
            JOIN `users` u ON w.user_id = u.user_id
            LEFT JOIN `shop_bank_accounts` b ON b.user_id = w.user_id
            ORDER BY w.datetime DESC
        ");

        if(!$withdrawals_result) {
            $withdrawals = [];
        } else {
            $withdrawals = [];
            while($row = $withdrawals_result->fetch_object()) {
                $withdrawals[] = $row;
            }
        }

        \Altum\Title::set('Shop Withdrawals');

        $view = new \Altum\View('admin/shop-withdrawals/index', (array) $this);
        $this->add_view_content('content', $view->run(['withdrawals' => $withdrawals]));
    }

    public function approve() {

        \Altum\Authentication::guard('admin');

        if(!\Altum\Csrf::check('global_token')) {
            Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            redirect('admin/shop-withdrawals');
        }

        $withdrawal_id = isset($this->params[0]) ? (int) $this->params[0] : 0;
        $withdrawal = database()->query("SELECT * FROM `shop_withdrawals` WHERE `id` = {$withdrawal_id}")->fetch_object();

        if(!$withdrawal || $withdrawal->status !== 'pending') {
            Alerts::add_error('Withdrawal tidak ditemukan atau sudah diproses.');
            redirect('admin/shop-withdrawals');
        }

        database()->query("UPDATE `shop_withdrawals` SET `status` = 'approved' WHERE `id` = {$withdrawal_id}");
        Alerts::add_success('Withdrawal berhasil disetujui.');
        redirect('admin/shop-withdrawals');
    }

    public function reject() {

        \Altum\Authentication::guard('admin');

        if(!\Altum\Csrf::check('global_token')) {
            Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            redirect('admin/shop-withdrawals');
        }

        $withdrawal_id = isset($this->params[0]) ? (int) $this->params[0] : 0;
        $withdrawal = database()->query("SELECT * FROM `shop_withdrawals` WHERE `id` = {$withdrawal_id}")->fetch_object();

        if(!$withdrawal || $withdrawal->status !== 'pending') {
            Alerts::add_error('Withdrawal tidak ditemukan atau sudah diproses.');
            redirect('admin/shop-withdrawals');
        }

        database()->query("UPDATE `users` SET `withdrawable_funds` = `withdrawable_funds` + {$withdrawal->amount} WHERE `user_id` = {$withdrawal->user_id}");
        database()->query("UPDATE `shop_withdrawals` SET `status` = 'rejected' WHERE `id` = {$withdrawal_id}");
        Alerts::add_success('Withdrawal ditolak dan saldo dikembalikan ke user.');
        redirect('admin/shop-withdrawals');
    }
}
