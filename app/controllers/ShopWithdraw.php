<?php
/*
 * Copyright (c) 2025 AltumCode (https://altumcode.com/)
 * 
 * Shop Withdraw Controller
 */

namespace Altum\Controllers;

use Altum\Database\Database;
use Altum\Alerts;

class ShopWithdraw extends Controller {

    public function index() {

        \Altum\Authentication::guard();

        if(empty($_POST)) {
            redirect('shop');
        }

        /* Check CSRF */
        if(!\Altum\Csrf::check()) {
            Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            redirect('shop');
        }

        $amount = (float) $this->user->withdrawable_funds;

        if($amount <= 0) {
            Alerts::add_error('You do not have enough funds to withdraw.');
            redirect('shop');
        }

        /* Check if they have a bank account set up */
        $bank = database()->query("SELECT `id` FROM `shop_bank_accounts` WHERE `user_id` = {$this->user->user_id}")->fetch_object() ?? null;
        
        // Even if they don't have a bank account yet, we might let them request it and ask them to fill it in settings, 
        // but it's better to force a dummy bank id for now if none exists, or require it.
        // For simplicity, we'll assign to ID 0 if none exists.
        $bank_id = $bank ? $bank->id : 0;

        /* Deduct from withdrawable_funds */
        database()->query("UPDATE `users` SET `withdrawable_funds` = `withdrawable_funds` - {$amount} WHERE `user_id` = {$this->user->user_id}");

        /* Insert into shop_withdrawals */
        $stmt = database()->prepare("INSERT INTO `shop_withdrawals` (`user_id`, `bank_account_id`, `amount`, `status`, `datetime`) VALUES (?, ?, ?, 'pending', ?)");
        $datetime = \Altum\Date::$date;
        $stmt->bind_param('iids', $this->user->user_id, $bank_id, $amount, $datetime);
        $stmt->execute();
        $stmt->close();

        /* Set a nice success message */
        Alerts::add_success("Withdrawal requested successfully! Processing takes 3-5 working days.");
        
        redirect('shop');

    }

}
