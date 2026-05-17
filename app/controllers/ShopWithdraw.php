<?php
/*
 * Shop Withdraw Controller — with KTP verification gate & minimum Rp 50.000
 */

namespace Altum\Controllers;

use Altum\Alerts;

class ShopWithdraw extends Controller {

    public function index() {

        \Altum\Authentication::guard();

        if(empty($_POST)) {
            redirect('shop');
        }

        if(!\Altum\Csrf::check()) {
            Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            redirect('shop');
        }

        /* ── 1. Cek verifikasi KTP — redirect ke halaman verifikasi jika belum verified ── */
        /* Refresh user dari DB agar status terbaru */
        $fresh_user = database()->query("SELECT `withdrawable_funds`, `pending_funds`, `verification_status` FROM `users` WHERE `user_id` = {$this->user->user_id}")->fetch_object();
        $verification_status = $fresh_user->verification_status ?? 'unverified';

        if($verification_status !== 'verified') {
            if($verification_status === 'pending') {
                Alerts::add_info('Verifikasi KTP kamu sedang dalam proses review. Pencairan dana tersedia setelah disetujui admin.');
            } elseif($verification_status === 'rejected') {
                Alerts::add_error('Verifikasi KTP kamu ditolak. Ajukan ulang dokumen yang benar.');
            } else {
                Alerts::add_error('Kamu harus menyelesaikan verifikasi KTP terlebih dahulu sebelum mencairkan dana.');
            }
            redirect('shop-verification');
        }

        /* ── 2. Ambil saldo terbaru langsung dari DB ── */
        $input_amount = (float) ($_POST['amount'] ?? 0);
        $available_funds = (float) ($fresh_user->withdrawable_funds ?? 0);
        $minimum = 50000;

        if($input_amount < $minimum) {
            Alerts::add_error('Saldo minimal pencairan adalah Rp ' . number_format($minimum, 0, ',', '.') . '.');
            redirect('shop');
        }

        if($input_amount > $available_funds) {
            Alerts::add_error('Saldo tidak mencukupi. Saldo tersedia: Rp ' . number_format($available_funds, 0, ',', '.') . '.');
            redirect('shop');
        }

        /* ── 3. Cek rekening bank ── */
        $bank    = database()->query("SELECT `id` FROM `shop_bank_accounts` WHERE `user_id` = {$this->user->user_id}")->fetch_object() ?? null;
        $bank_id = $bank ? $bank->id : 0;

        if(!$bank) {
            Alerts::add_error('Tambahkan rekening bank terlebih dahulu di pengaturan toko.');
            redirect('shop');
        }

        /* ── 4. Proses pencairan — kurangi saldo & buat record withdrawal (status = pending, tunggu approve admin) ── */
        database()->query("UPDATE `users` SET `withdrawable_funds` = `withdrawable_funds` - {$input_amount} WHERE `user_id` = {$this->user->user_id}");

        $stmt     = database()->prepare("INSERT INTO `shop_withdrawals` (`user_id`, `bank_account_id`, `amount`, `status`, `datetime`) VALUES (?, ?, ?, 'pending', ?)");
        $datetime = \Altum\Date::$date;
        $stmt->bind_param('iids', $this->user->user_id, $bank_id, $input_amount, $datetime);
        $stmt->execute();
        $stmt->close();

        Alerts::add_success('Permintaan pencairan Rp ' . number_format($input_amount, 0, ',', '.') . ' berhasil diajukan! Menunggu persetujuan admin (3-5 hari kerja).');
        redirect('shop#transaction');
    }
}
