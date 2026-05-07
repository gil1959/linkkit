<?php
namespace Altum\Controllers;
defined('ALTUMCODE') || die();

class ShopVoucherValidate extends Controller {

    public function index() {
        header('Content-Type: application/json');

        $shop_id  = (int)($_POST['shop_id']  ?? 0);
        $item_id  = (int)($_POST['item_id']  ?? 0);
        $code     = strtoupper(input_clean($_POST['code'] ?? ''));
        $price    = (float)($_POST['price']  ?? 0);

        if(!$shop_id || !$item_id || empty($code)) {
            die(json_encode(['success' => false, 'message' => 'Invalid params']));
        }

        $now = date('Y-m-d H:i:s');
        $voucher = database()->query("
            SELECT * FROM `shop_vouchers`
            WHERE `shop_id`={$shop_id}
              AND `code`='" . database()->real_escape_string($code) . "'
              AND `is_active`=1
              AND (`valid_from` IS NULL OR `valid_from` <= '{$now}')
              AND (`valid_to`   IS NULL OR `valid_to`   >= '{$now}')
              AND (`item_id` IS NULL OR `item_id`={$item_id})
        ")->fetch_object() ?? null;

        if(!$voucher) die(json_encode(['success' => false, 'message' => 'Voucher tidak valid atau sudah kadaluarsa']));

        /* Quota check */
        if(!$voucher->is_unlimited && $voucher->quota !== null && $voucher->used >= $voucher->quota) {
            die(json_encode(['success' => false, 'message' => 'Kuota voucher habis']));
        }

        $discount_amount = round($price * $voucher->discount_percentage / 100);
        $final_price     = max(0, $price - $discount_amount);

        die(json_encode([
            'success'           => true,
            'voucher_id'        => $voucher->id,
            'discount_pct'      => $voucher->discount_percentage,
            'discount_amount'   => $discount_amount,
            'final_price'       => $final_price,
            'message'           => "Voucher berhasil! Diskon {$voucher->discount_percentage}%",
        ]));
    }
}
