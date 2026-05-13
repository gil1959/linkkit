<?php
/*
 * ShopVerification Controller
 * Handles KTP + selfie upload for seller identity verification
 */

namespace Altum\Controllers;

use Altum\Title;
use Altum\Alerts;

class ShopVerification extends Controller {

    public function index() {

        \Altum\Authentication::guard();

        $result = database()->query("SELECT * FROM `shop_verifications` WHERE `user_id` = " . (int)$this->user->user_id);
        $verification = $result ? $result->fetch_object() : null;
        $status       = $this->user->verification_status ?? 'unverified';

        if(!empty($_POST)) {

            if(!\Altum\Csrf::check()) {
                Alerts::add_error(l('global.error_message.invalid_csrf_token'));
                redirect('shop#shop_settings');
            }

            /* Blokir jika sudah verified atau sedang pending */
            if($status === 'verified') {
                Alerts::add_info('KTP kamu sudah terverifikasi.');
                redirect('shop#shop_settings');
            }
            if($status === 'pending') {
                Alerts::add_info('Dokumenmu sedang dalam review admin.');
                redirect('shop#shop_settings');
            }

            $full_name = input_clean($_POST['full_name'] ?? '');
            $nik       = preg_replace('/\D/', '', $_POST['nik'] ?? '');

            if(empty($full_name)) {
                Alerts::add_error('Nama lengkap sesuai KTP wajib diisi.');
            }
            if(strlen($nik) !== 16) {
                Alerts::add_error('NIK harus 16 digit angka.');
            }

            /* Pastikan folder upload ada */
            $ktp_dir    = ROOT_PATH . 'uploads/shop_verifications/ktp/';
            $selfie_dir = ROOT_PATH . 'uploads/shop_verifications/selfie/';
            if(!is_dir($ktp_dir))    mkdir($ktp_dir, 0755, true);
            if(!is_dir($selfie_dir)) mkdir($selfie_dir, 0755, true);

            /* Proses upload KTP */
            $ktp_filename = null;
            if(isset($_FILES['ktp_image']) && $_FILES['ktp_image']['error'] === UPLOAD_ERR_OK) {
                $allowed = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
                $mime    = mime_content_type($_FILES['ktp_image']['tmp_name']);
                $size    = $_FILES['ktp_image']['size'];
                if(!in_array($mime, $allowed)) {
                    Alerts::add_error('Format KTP harus JPG, PNG, atau WEBP.');
                } elseif($size > 5 * 1024 * 1024) {
                    Alerts::add_error('Ukuran foto KTP maksimal 5MB.');
                } else {
                    $ext          = pathinfo($_FILES['ktp_image']['name'], PATHINFO_EXTENSION);
                    $ktp_filename = 'ktp_' . $this->user->user_id . '_' . time() . '.' . $ext;
                    move_uploaded_file($_FILES['ktp_image']['tmp_name'], $ktp_dir . $ktp_filename);
                }
            } else {
                Alerts::add_error('Foto KTP wajib diupload.');
            }

            /* Proses upload selfie */
            $selfie_filename = null;
            if(isset($_FILES['selfie_image']) && $_FILES['selfie_image']['error'] === UPLOAD_ERR_OK) {
                $allowed = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
                $mime    = mime_content_type($_FILES['selfie_image']['tmp_name']);
                $size    = $_FILES['selfie_image']['size'];
                if(!in_array($mime, $allowed)) {
                    Alerts::add_error('Format selfie harus JPG, PNG, atau WEBP.');
                } elseif($size > 5 * 1024 * 1024) {
                    Alerts::add_error('Ukuran foto selfie maksimal 5MB.');
                } else {
                    $ext             = pathinfo($_FILES['selfie_image']['name'], PATHINFO_EXTENSION);
                    $selfie_filename = 'selfie_' . $this->user->user_id . '_' . time() . '.' . $ext;
                    move_uploaded_file($_FILES['selfie_image']['tmp_name'], $selfie_dir . $selfie_filename);
                }
            } else {
                Alerts::add_error('Foto selfie dengan KTP wajib diupload.');
            }

            if(!Alerts::has_errors()) {
                $datetime    = \Altum\Date::$date;
                $full_name_e = database()->real_escape_string($full_name);
                $ktp_e       = database()->real_escape_string($ktp_filename);
                $selfie_e    = database()->real_escape_string($selfie_filename);
                $nik_e       = database()->real_escape_string($nik);

                if($verification) {
                    /* Update existing (re-submit after rejection) */
                    database()->query("UPDATE `shop_verifications` SET
                        `full_name`='$full_name_e', `nik`='$nik_e',
                        `ktp_image`='$ktp_e', `selfie_image`='$selfie_e',
                        `status`='pending', `rejection_reason`=NULL, `reviewed_at`=NULL, `reviewed_by`=NULL,
                        `submitted_at`='{$datetime}'
                        WHERE `user_id`={$this->user->user_id}");
                } else {
                    database()->query("INSERT INTO `shop_verifications`
                        (`user_id`,`full_name`,`nik`,`ktp_image`,`selfie_image`,`status`,`submitted_at`)
                        VALUES ({$this->user->user_id},'$full_name_e','$nik_e','$ktp_e','$selfie_e','pending','$datetime')");
                }

                database()->query("UPDATE `users` SET `verification_status` = 'pending' WHERE `user_id` = {$this->user->user_id}");

                Alerts::add_success('Dokumen berhasil diupload! Admin akan mereview dalam 1-3 hari kerja.');
                redirect('shop#shop_settings');
            }
        }
        
        /* If accessed via GET, redirect to shop settings where the new inline form is */
        redirect('shop#shop_settings');

        Title::set('Verifikasi Identitas Seller');
        $view = new \Altum\View('shop_verification/index', (array) $this);
        $this->add_view_content('content', $view->run([
            'verification' => $verification,
            'status'       => $status,
        ]));
    }
}
