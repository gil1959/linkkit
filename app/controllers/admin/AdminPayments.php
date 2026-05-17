<?php
/*
 * Copyright (c) 2026 AltumCode (https://altumcode.com/)
 *
 * This software is licensed exclusively by AltumCode and is sold only via https://altumcode.com/.
 * Unauthorized distribution, modification, or use of this software without a valid license is not permitted and may be subject to applicable legal actions.
 *
 * 🌍 View all other existing AltumCode projects via https://altumcode.com/
 * 📧 Get in touch for support or general queries via https://altumcode.com/contact
 * 📤 Download the latest version via https://altumcode.com/downloads
 *
 * 🐦 X/Twitter: https://x.com/AltumCode
 * 📘 Facebook: https://facebook.com/altumcode
 * 📸 Instagram: https://instagram.com/altumcode
 */

namespace Altum\Controllers;

use Altum\Alerts;
use Altum\Date;
use Altum\Models\Payments;

defined('ALTUMCODE') || die();

class AdminPayments extends Controller {

    public function index() {



        $payment_processors = require APP_PATH . 'includes/payment_processors.php';

        /* Prepare the filtering system */
        $filters = (new \Altum\Filters(['id', 'status', 'plan_id', 'user_id', 'type', 'processor', 'frequency', 'taxes_ids'], ['payment_id', 'code'], ['id', 'total_amount', 'email', 'datetime', 'name'], [], ['taxes_ids' => 'json_contains'], allowed_datetime_fields: ['datetime']));
        $filters->set_default_order_by('id', $this->user->preferences->default_order_type ?? settings()->main->default_order_type);
        $filters->set_default_results_per_page($this->user->preferences->default_results_per_page ?? settings()->main->default_results_per_page);

        /* Prepare the paginator */
        $total_rows = database()->query("SELECT COUNT(*) AS `total` FROM `payments` WHERE 1 = 1 {$filters->get_sql_where()}")->fetch_object()->total ?? 0;
        $paginator = (new \Altum\Paginator($total_rows, $filters->get_results_per_page(), $_GET['page'] ?? 1, url('admin/payments?' . $filters->get_get() . '&page={{PAGE}}')));

        /* Get the data */
        $payments = [];
        $payments_result = database()->query("
            SELECT
                `payments`.*, `users`.`name` AS `user_name`, `users`.`email` AS `user_email`, `users`.`avatar` AS `user_avatar`
            FROM
                `payments`
            LEFT JOIN
                `users` ON `payments`.`user_id` = `users`.`user_id`
            WHERE
                1 = 1
                {$filters->get_sql_where('payments')}
                {$filters->get_sql_order_by('payments')}

            {$paginator->get_sql_limit()}
        ");
        while($row = $payments_result->fetch_object()) {
            $row->plan = json_decode($row->plan ?? '');
            $payments[] = $row;
        }

        /* Export handler */
        process_export_json($payments, ['id','user_id','plan_id','payment_id','email','name','processor','type','frequency','billing','taxes_ids','base_amount','code','discount_amount','total_amount','total_amount_default_currency','currency','status','plan','business','payment_proof','payment_proof_url','refunds','refunded_total','refunded_status','datetime']);
        process_export_csv_new($payments, ['id','user_id','plan_id','payment_id','email','name','processor','type','frequency','billing','taxes_ids','base_amount','code','discount_amount','total_amount','total_amount_default_currency','currency','status','plan','business','payment_proof','payment_proof_url','refunds','refunded_total','refunded_status','datetime'], ['billing','taxes_ids','plan','business','refunds']);

        /* Requested plan details */
        $plans = (new \Altum\Models\Plan())->get_plans();

        /* Prepare the pagination view */
        $pagination = (new \Altum\View('partials/admin_pagination', (array) $this))->run(['paginator' => $paginator]);

        /* Main View */
        $data = [
            'payments' => $payments,
            'plans' => $plans,
            'pagination' => $pagination,
            'filters' => $filters,
            'payment_processors' => $payment_processors,
        ];

        $view = new \Altum\View('admin/payments/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }


    public function delete() {



        $payment_id = isset($this->params[0]) ? (int) $this->params[0] : null;

        //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

        if(!\Altum\Csrf::check('global_token')) {
            Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            redirect('admin/payments');
        }

        if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

            $payment = db()->where('id', $payment_id)->getOne('payments', ['payment_proof']);

            /* Delete the saved proof, if any */
            \Altum\Uploads::delete_uploaded_file($payment->payment_proof, 'offline_payment_proofs');

            /* Delete the payment */
            db()->where('id', $payment_id)->delete('payments');

            /* Set a nice success message */
            Alerts::add_success(l('global.success_message.delete2'));

        }

        redirect('admin/payments');
    }

    public function approve() {



        $payment_id = (isset($this->params[0])) ? (int) $this->params[0] : null;

        //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

        if(!\Altum\Csrf::check('global_token')) {
            Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            redirect('admin/payments');
        }

        if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

            /* details about the payment */
            $payment = db()->where('id', $payment_id)->getOne('payments');

            /* details about the user who paid */
            $user = db()->where('user_id', $payment->user_id)->getOne('users');

            /* plan that the user has paid for */
            $plan = (new \Altum\Models\Plan())->get_plan_by_id($payment->plan_id);

            /* Make sure the code that was potentially used exists */
            $codes_code = db()->where('code', $payment->code)->where('type', 'discount')->getOne('codes');

            $is_shop_order = (strpos($payment->code, 'shop_order_') === 0);

            if($codes_code && !$is_shop_order) {
                /* Check if we should insert the usage of the code or not */
                if(!db()->where('user_id', $payment->user_id)->where('code_id', $codes_code->code_id)->has('redeemed_codes')) {

                    /* Update the code usage */
                    db()->where('code_id', $codes_code->code_id)->update('codes', ['redeemed' => db()->inc()]);

                    if($user) {
                        /* Add log for the redeemed code */
                        db()->insert('redeemed_codes', [
                            'code_id' => $codes_code->code_id,
                            'user_id' => $user->user_id,
                            'datetime' => get_date()
                        ]);
                    }
                }
            }

                send_mail($user->email, $email_template->subject, $email_template->body, ['anti_phishing_code' => $user->anti_phishing_code, 'language' => $user->language]);
            }

            if($is_shop_order) {
                $order_id = (int) str_replace('shop_order_', '', $payment->code);
                $order = db()->where('id', $order_id)->getOne('shop_orders');
                if($order) {
                    $datetime = \Altum\Date::$date;
                    db()->where('id', $order_id)->update('shop_orders', [
                        'status' => 'paid',
                        'settle_status' => 'unsettled',
                        'paid_date' => $datetime
                    ]);
                    
                    database()->query("UPDATE `shop_customers` SET `total_orders` = `total_orders` + 1, `total_spent` = `total_spent` + {$order->grand_total} WHERE `id` = {$order->customer_id}");
                    $seller_revenue = $order->grand_total - $order->service_fee;
                    database()->query("UPDATE `users` SET `pending_funds` = `pending_funds` + {$seller_revenue} WHERE `user_id` = {$payment->user_id}");
                    
                    $item = db()->where('id', $order->item_id)->getOne('shop_items');
                    if($item) {
                        $fulfilled_content = null;
                        if($item->type === 'download_link') {
                            $links = json_decode($item->download_links ?? '[]', true) ?: [];
                            $fulfilled_content = json_encode($links);
                        } elseif($item->type === 'random_code') {
                            $codes = json_decode($item->download_links ?? '[]', true) ?: [];
                            if(!empty($codes)) {
                                $code = array_shift($codes);
                                $remaining = database()->real_escape_string(json_encode(array_values($codes)));
                                database()->query("UPDATE `shop_items` SET `download_links` = '{$remaining}', `stock` = GREATEST(0, COALESCE(`stock`, 0) - 1) WHERE `id` = {$item->id}");
                                $fulfilled_content = $code;
                            } else {
                                $fulfilled_content = 'OUT_OF_STOCK';
                            }
                        } elseif($item->type === 'webhook_event') {
                            $fulfilled_content = 'webhook_pending';
                        } elseif($item->type === 'manual') {
                            $fulfilled_content = 'manual_pending';
                        } elseif($item->type === 'physical') {
                            $fulfilled_content = 'physical_pending';
                        }
                        
                        if($fulfilled_content !== null) {
                            $fc = database()->real_escape_string($fulfilled_content);
                            database()->query("UPDATE `shop_orders` SET `fulfilled_content` = '{$fc}' WHERE `id` = {$order_id}");
                        }
                        database()->query("UPDATE `shop_items` SET `sales` = `sales` + 1 WHERE `id` = {$item->id}");
                    }
                }
            } else if($user) {

            /* Send webhook notification if needed */
            if(settings()->webhooks->payment_new) {
                fire_and_forget('post', settings()->webhooks->payment_new, [
                    'user_id' => $user->user_id,
                    'email' => $user->email,
                    'name' => $user->name,
                    'plan_id' => $plan->plan_id,
                    'plan_expiration_date' => $plan_expiration_date,
                    'payment_id' => $payment_id,
                    'payment_processor' => $payment->processor,
                    'payment_type' => $payment->type,
                    'payment_frequency' => $payment->frequency,
                    'payment_total_amount' => $payment->total_amount,
                    'payment_currency' => $payment->currency,
                    'payment_code' => $payment->code,
                    'datetime' => get_date(),
                ], signature: true);
            }

            /* Currency exchange in case its needed */
            $total_amount_default_currency = $payment->total_amount;

            if(settings()->payment->default_currency != $payment->currency && settings()->payment->currency_exchange_api_key) {
                try {
                    $response = \Unirest\Request::get('https://api.freecurrencyapi.com/v1/latest?apikey=' . settings()->payment->currency_exchange_api_key . '&base_currency=' . $payment->currency . '&currencies=' . settings()->payment->default_currency);

                    if($response->code == 200) {
                        $total_amount_default_currency = $payment->total_amount * $response->body->data->{settings()->payment->default_currency};
                        $total_amount_default_currency = number_format($total_amount_default_currency, 2, '.', '');
                    }
                } catch (\Exception $exception) {
                    /* :) */
                }
            }

            /* Update the payment */
            db()->where('id', $payment_id)->update('payments', [
                'total_amount_default_currency' => $total_amount_default_currency,
                'status' => 'paid',
            ]);

            /* Affiliate */
            (new Payments())->affiliate_payment_check($payment_id, $total_amount_default_currency, settings()->payment->default_currency, $user);

            /* Set a nice success message */
            Alerts::add_success(l('admin_payment_approve_modal.success_message'));

        }

        redirect('admin/payments');
    }

	public function cancel() {



		$payment_id = (isset($this->params[0])) ? (int) $this->params[0] : null;

		//ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

		if(!\Altum\Csrf::check('global_token')) {
			Alerts::add_error(l('global.error_message.invalid_csrf_token'));
			redirect('admin/payments');
		}

		if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

			/* details about the payment */
			$payment = db()->where('id', $payment_id)->getOne('payments');

            /* details about the user who paid */
            $user = db()->where('user_id', $payment->user_id)->getOne('users');
            $is_shop_order = (strpos($payment->code, 'shop_order_') === 0);

            if($is_shop_order) {
                $order_id = (int) str_replace('shop_order_', '', $payment->code);
                db()->where('id', $order_id)->update('shop_orders', ['status' => 'failed']);
            } else if($user) {
                /* Send notification to the user */
                $email_template = get_email_template(
                    [],
                    l('global.emails.user_payment_cancelled.subject'),
                    [
                        '{{NAME}}' => $user->name,
                        '{{PAYMENT_ID}}' => $payment->id,
                        '{{PLANS_LINK}}' => url('plan'),
                        '{{USER_PAYMENTS_LINK}}' => url('account-payments'),
                    ],
                    l('global.emails.user_payment_cancelled.body')
                );

                send_mail($user->email, $email_template->subject, $email_template->body, ['anti_phishing_code' => $user->anti_phishing_code, 'language' => $user->language]);
            }

			/* Update the payment */
			db()->where('id', $payment_id)->update('payments', [
				'status' => 'cancelled',
			]);


			/* Set a nice success message */
			Alerts::add_success(l('admin_payment_cancel_modal.success_message'));

		}

		redirect('admin/payments');
	}

    public function refund() {



        if (empty($_POST)) {
            throw_404();
        }

        $payment_id = (int) $_POST['id'];
        $amount = (float) $_POST['amount'];
        $reason = input_clean($_POST['reason'], 512);
        $origin = in_array($_POST['origin'], ['manual', 'chargeback']) ? $_POST['origin'] : 'manual';

        //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

        if(!\Altum\Csrf::check()) {
            Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            redirect('admin/payments');
        }

        if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

            /* details about the payment */
            $payment = db()->where('id', $payment_id)->getOne('payments');

            /* Get previous refunds if any */
            $payment->refunds = (array) json_decode($payment->refunds ?? '[]');

            /* Calculate and generate details */
            $remaining_amount = number_format($payment->total_amount - $payment->refunded_total, 2, '.', '');

            if($remaining_amount <= 0 || ($remaining_amount - $_POST['amount']) < 0) {
                redirect('admin/payments');
            }

            /* Generate refunds */
            $refund = [
                'id' => count($payment->refunds) + 1,
                'amount' => $_POST['amount'],
                'reason' => $reason,
                'origin' => $origin,
                'datetime' => get_date()
            ];

            $payment->refunds[] = $refund;

            /* Refunds json */
            $refunds = json_encode($payment->refunds);

            /* Other payment refund details */
            $refunded_total = number_format($payment->refunded_total + $_POST['amount'], 2, '.', '');
            $refunded_status = $refunded_total >= $payment->total_amount ? 'fully_refunded' : 'partially_refunded';

            /* Update the payment */
            db()->where('id', $payment_id)->update('payments', [
                'status' => 'refunded',
                'refunded_total' => $refunded_total,
                'refunded_status' => $refunded_status,
                'refunds' => $refunds
            ]);

            /* Set a nice success message */
            Alerts::add_success(l('admin_payment_refund_modal.success_message'));

        }

        redirect('admin/payments');
    }

}
