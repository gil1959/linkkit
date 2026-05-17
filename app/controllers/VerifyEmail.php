<?php
namespace Altum\Controllers;

use Altum\Alerts;
use Altum\Logger;
use Altum\Models\User;

defined('ALTUMCODE') || die();

class VerifyEmail extends Controller {

    public function index() {

        \Altum\Authentication::guard('guest');

        $email = isset($_GET['email']) ? query_clean($_GET['email']) : '';

        if(empty($email)) {
            redirect('login');
        }

        if(!empty($_POST)) {
            $_POST['otp'] = input_clean($_POST['otp'] ?? '');

            if(empty($_POST['otp'])) {
                Alerts::add_field_error('otp', l('global.error_message.empty_field'));
            }

            if(!\Altum\Csrf::check()) {
                Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            }

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {
                
                // Find user by email and otp
                $user = database()->query("SELECT `user_id`, `email`, `status` FROM `users` WHERE `email` = '" . database()->real_escape_string($email) . "' AND `email_activation_code` = '" . database()->real_escape_string($_POST['otp']) . "'")->fetch_object() ?? null;

                if(!$user) {
                    Alerts::add_error('Kode OTP salah atau email tidak valid.');
                } elseif($user->status != 0) {
                    Alerts::add_error('Akun ini sudah aktif.');
                } else {
                    /* Activate the user */
                    database()->query("UPDATE `users` SET `status` = 1, `email_activation_code` = '' WHERE `user_id` = {$user->user_id}");

                    /* Log the action */
                    Logger::users($user->user_id, 'activate.success');

                    /* Set a nice success message */
                    Alerts::add_success('Email berhasil diverifikasi! Silakan masuk.');

                    redirect('login');
                }
            }
        }

        /* Main View */
        $data = [
            'email' => $email
        ];

        $view = new \Altum\View('verify_email/index', (array) $this);
        $this->add_view_content('content', $view->run($data));

    }
}
