<?php
/*
 * @copyright Copyright (c) 2024 AltumCode (https://altumcode.com/)
 *
 * This software is exclusively sold through https://altumcode.com/ by the AltumCode author.
 * Downloading this product from any other sources and running it without a proper license is illegal,
 *  except the personal contributions under MIT License.
 *
 * There are licenses for each local contributions.
 *
 * dYO? View all other existing AltumCode projects via https://altumcode.com/
 * dY"  Get in touch for support or general queries via https://altumcode.com/contact
 * dY"  Download the latest version via https://altumcode.com/downloads
 *
 * dY? X/Twitter: https://x.com/AltumCode
 * dY"~ Facebook: https://facebook.com/altumcode
 * dY", Instagram: https://instagram.com/altumcode
 */

namespace Altum\Controllers;

use Altum\Alerts;
use Altum\Title;

defined('ALTUMCODE') || die();

class AccountDeposit extends Controller {

    public function index() {

        \Altum\Authentication::guard();

        if(!empty($_POST)) {
            /* Check for any errors */
            if(!\Altum\Csrf::check()) {
                Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            }

            $amount = (float) ($_POST['amount'] ?? 0);

            if($amount < 10000) {
                Alerts::add_error('Minimal deposit adalah Rp 10.000');
            }

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {
                /* Set session and redirect to pay/deposit */
                $_SESSION['deposit_amount'] = $amount;
                redirect('pay/deposit');
            }
        }

        /* Set a custom title */
        Title::set('Deposit Saldo');

        /* Prepare the View */
        $data = [];

        $view = new \Altum\View('account-deposit/index', (array) $this);

        $this->add_info_message();

        $this->view($view->run($data));

    }

}
