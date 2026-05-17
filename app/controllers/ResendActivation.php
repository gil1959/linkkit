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
use Altum\Captcha;
use Altum\Logger;

defined('ALTUMCODE') || die();

class ResendActivation extends Controller {

    public function index() {

        \Altum\Authentication::guard('guest');

        if(!settings()->users->email_confirmation) {
            throw_404();
        }

        $redirect = process_and_get_redirect_params() ?? 'dashboard';
        $redirect_append = $redirect ? '?redirect=' . $redirect : null;

        /* Default values */
        $values = [
            'email' => $_POST['email'] ?? $_GET['email'] ?? '',
        ];

        $values['email'] = input_clean_email($values['email']);

        /* Initiate captcha */
        $captcha = new Captcha();

        if(!empty($_POST)) {
            /* Clean the posted variable */
            $_POST['email'] = input_clean_email($_POST['email'] ?? '');
            $values['email'] = $_POST['email'];

            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* Check for any errors */
            if(!\Altum\Csrf::check()) {
                Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            }

            if(settings()->captcha->resend_activation_is_enabled && !$captcha->is_valid()) {
                Alerts::add_field_error('captcha', l('global.error_message.invalid_captcha'));
            }

            /* Make sure to check against the limiter */
            if(settings()->users->resend_activation_lockout_is_enabled) {
                $minutes_ago_datetime = (new \DateTime())->modify('-' . settings()->users->resend_activation_lockout_time . ' minutes')->format('Y-m-d H:i:s');

                $recent_fails = db()->where('ip', get_ip())->where('type', 'resend_activation.request_sent')->where('datetime', $minutes_ago_datetime, '>=')->getValue('users_logs', 'COUNT(*)');

                if($recent_fails >= settings()->users->resend_activation_lockout_max_retries) {
                    Alerts::add_error(sprintf(l('global.error_message.limit_try_again'), settings()->users->resend_activation_lockout_time, l('global.date.minutes')));
                    setcookie('resend_activation_lockout', 'true', time()+60*settings()->users->resend_activation_lockout_time, COOKIE_PATH);
                    $_COOKIE['resend_activation_lockout'] = 'true';
                }
            }

            /* If there are no errors, resend the activation link */
            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

                $user = db()->where('email', $_POST['email'])->getOne('users', ['user_id', 'status', 'name', 'email', 'language']);

                if($user && !$user->status) {
                    /* Generate new email code */
                    $email_code = sprintf("%06d", mt_rand(1, 999999));

                    /* Update the current activation email */
                    db()->where('user_id', $user->user_id)->update('users', ['email_activation_code' => $email_code]);

                    /* Prepare the email */
                    $subject = "Kirim Ulang: Kode Verifikasi Email Anda";
                    $body = "Halo " . $user->name . ",<br><br>Sesuai permintaan Anda, berikut adalah kode verifikasi email Anda yang baru: <b>" . $email_code . "</b><br><br>Silakan masukkan kode tersebut di halaman verifikasi.";

                    /* Send the email */
                    send_mail($_POST['email'], $subject, $body);

                    Logger::users($user->user_id, 'resend_activation.request_sent');
                }

                /* Redirect to email verify */
                redirect('verify-email?email=' . urlencode($_POST['email']));
            }
        }

        /* Prepare the view */
        $data = [
            'values'    => $values,
            'captcha'   => $captcha,
            'redirect_append' => $redirect_append,
        ];

        $view = new \Altum\View('resend-activation/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

}
