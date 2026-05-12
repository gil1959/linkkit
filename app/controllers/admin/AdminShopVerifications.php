<?php
/*
 * Admin ShopVerifications Controller
 * Route: admin/shop-verifications  /  admin/shop-verifications/{id}
 */

namespace Altum\Controllers;

use Altum\Alerts;

class AdminShopVerifications extends Controller {

    public function index() {

        \Altum\Authentication::guard('admin');

        $action    = isset($this->params[0]) ? input_clean($this->params[0]) : null;
        $verify_id = isset($this->params[1]) ? (int)$this->params[1] : 0;

        /* ── Handle approval / rejection actions ── */
        if($action === 'approve' && $verify_id) {
            if(!\Altum\Csrf::check('token')) {
                Alerts::add_error('Invalid CSRF token.');
            } else {
                $v = database()->query("SELECT * FROM `shop_verifications` WHERE `id` = {$verify_id}")->fetch_object();
                if($v) {
                    $reviewed_at = \Altum\Date::$date;
                    database()->query("UPDATE `shop_verifications` SET
                        `status` = 'verified', `rejection_reason` = NULL,
                        `reviewed_at` = '{$reviewed_at}', `reviewed_by` = {$this->user->user_id}
                        WHERE `id` = {$verify_id}");
                    database()->query("UPDATE `users` SET `verification_status` = 'verified' WHERE `user_id` = {$v->user_id}");
                    Alerts::add_success('Verifikasi #' . $verify_id . ' telah disetujui.');

                    /* Email notifikasi ke seller */
                    try {
                        $user = database()->query("SELECT `email`,`name` FROM `users` WHERE `user_id` = {$v->user_id}")->fetch_object();
                        if($user) {
                            $html = '<!DOCTYPE html><html><body style="font-family:Inter,sans-serif;background:#f8fafc;padding:20px;margin:0">
<div style="max-width:520px;margin:0 auto;background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,.08)">
<div style="background:linear-gradient(135deg,#059669,#10b981);padding:28px;text-align:center">
<div style="width:52px;height:52px;background:rgba(255,255,255,.2);border-radius:50%;display:inline-flex;align-items:center;justify-content:center;margin-bottom:12px">
<svg style="width:28px;height:28px;fill:none;stroke:#fff;stroke-width:3;stroke-linecap:round;stroke-linejoin:round" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"></polyline></svg>
</div>
<h1 style="color:#fff;font-size:1.15rem;margin:0">Verifikasi KTP Disetujui!</h1>
</div>
<div style="padding:28px">
<p style="color:#374151">Halo <strong>' . htmlspecialchars($user->name) . '</strong>,</p>
<p style="color:#374151">Selamat! Identitasmu telah berhasil diverifikasi. Kamu sekarang bisa melakukan <strong>pencairan dana</strong> dari saldo toko.</p>
<a href="' . url('shop') . '" style="display:block;background:#059669;color:#fff;text-align:center;padding:14px;border-radius:12px;text-decoration:none;font-weight:700;margin-top:20px">Buka Dashboard Toko</a>
</div></div></body></html>';
                            send_mail($user->email, 'Verifikasi KTP Disetujui', $html);
                        }
                    } catch(\Exception $e) {}
                }
            }
            redirect('admin/shop-verifications');
        }

        if($action === 'reject' && $verify_id) {
            if(!\Altum\Csrf::check('token')) {
                Alerts::add_error('Invalid CSRF token.');
            } else {
                $reason = input_clean($_POST['rejection_reason'] ?? 'Dokumen tidak valid.');
                $v = database()->query("SELECT * FROM `shop_verifications` WHERE `id` = {$verify_id}")->fetch_object();
                if($v) {
                    $reviewed_at = \Altum\Date::$date;
                    $reason_esc  = database()->real_escape_string($reason);
                    database()->query("UPDATE `shop_verifications` SET
                        `status` = 'rejected', `rejection_reason` = '$reason_esc',
                        `reviewed_at` = '{$reviewed_at}', `reviewed_by` = {$this->user->user_id}
                        WHERE `id` = {$verify_id}");
                    database()->query("UPDATE `users` SET `verification_status` = 'rejected' WHERE `user_id` = {$v->user_id}");
                    Alerts::add_success('Verifikasi #' . $verify_id . ' telah ditolak.');

                    /* Email notifikasi ke seller */
                    try {
                        $user = database()->query("SELECT `email`,`name` FROM `users` WHERE `user_id` = {$v->user_id}")->fetch_object();
                        if($user) {
                            $html = '<!DOCTYPE html><html><body style="font-family:Inter,sans-serif;background:#f8fafc;padding:20px;margin:0">
<div style="max-width:520px;margin:0 auto;background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,.08)">
<div style="background:linear-gradient(135deg,#dc2626,#ef4444);padding:28px;text-align:center">
<div style="width:52px;height:52px;background:rgba(255,255,255,.2);border-radius:50%;display:inline-flex;align-items:center;justify-content:center;margin-bottom:12px">
<svg style="width:28px;height:28px;fill:none;stroke:#fff;stroke-width:3;stroke-linecap:round;stroke-linejoin:round" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
</div>
<h1 style="color:#fff;font-size:1.15rem;margin:0">Verifikasi KTP Ditolak</h1>
</div>
<div style="padding:28px">
<p style="color:#374151">Halo <strong>' . htmlspecialchars($user->name) . '</strong>,</p>
<p style="color:#374151">Mohon maaf, verifikasi KTP kamu <strong>ditolak</strong> dengan alasan berikut:</p>
<div style="background:#fef2f2;border:1px solid #fca5a5;border-radius:10px;padding:14px;margin:14px 0;font-size:.85rem;color:#991b1b">' . htmlspecialchars($reason) . '</div>
<p style="color:#374151;font-size:.88rem">Perbaiki dokumenmu dan upload ulang melalui dashboard toko.</p>
<a href="' . url('shop-verification') . '" style="display:block;background:#dc2626;color:#fff;text-align:center;padding:14px;border-radius:12px;text-decoration:none;font-weight:700;margin-top:16px">Upload Ulang Dokumen</a>
</div></div></body></html>';
                            send_mail($user->email, 'Verifikasi KTP Ditolak', $html);
                        }
                    } catch(\Exception $e) {}
                }
            }
            redirect('admin/shop-verifications');
        }

        /* ── List semua verifikasi ── */
        $filter    = input_clean($_GET['status'] ?? 'pending');
        $status_q  = in_array($filter, ['pending','verified','rejected']) ? "WHERE v.`status` = '{$filter}'" : '';

        $verifications = database()->query("
            SELECT v.*, u.email, u.name AS user_name
            FROM `shop_verifications` v
            JOIN `users` u ON v.user_id = u.user_id
            {$status_q}
            ORDER BY v.submitted_at DESC
            LIMIT 100
        ")->fetch_all(MYSQLI_ASSOC);

        $verifications = array_map(fn($r) => (object)$r, $verifications);

        $counts = database()->query("
            SELECT `status`, COUNT(*) as cnt FROM `shop_verifications` GROUP BY `status`
        ")->fetch_all(MYSQLI_ASSOC);
        $cnt = ['pending' => 0, 'verified' => 0, 'rejected' => 0];
        foreach($counts as $c) $cnt[$c['status']] = (int)$c['cnt'];

        \Altum\Title::set('Verifikasi Seller — Admin');
        $view = new \Altum\View('admin/shop_verifications/index', (array) $this);
        $this->add_view_content('content', $view->run([
            'verifications' => $verifications,
            'filter'        => $filter,
            'cnt'           => $cnt,
        ]));
    }
}
