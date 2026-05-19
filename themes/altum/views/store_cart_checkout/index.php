<?php defined('ALTUMCODE') || die() ?>
<?php \Altum\Title::set('Checkout Keranjang — ' . htmlspecialchars($data->shop->name)) ?>

<style>
*,*::before,*::after{box-sizing:border-box}
body{background:#f0f2f8;font-family:'Inter',sans-serif;color:#1e293b;margin:0;min-height:100vh}
.co-topbar{background:#fff;border-bottom:1px solid #e2e8f0;padding:0 24px;height:54px;display:flex;align-items:center;gap:12px;position:sticky;top:0;z-index:10;box-shadow:0 1px 4px rgba(0,0,0,.05)}
.co-shop-name{font-weight:700;font-size:.95rem;color:#1e293b}
.co-back{display:inline-flex;align-items:center;gap:6px;color:#6b7280;text-decoration:none;font-size:.82rem;margin-left:auto;transition:.2s;padding:6px 10px;border-radius:8px}
.co-back:hover{background:#f3f4f6;color:#4f46e5}
.co-secure-badge{display:flex;align-items:center;gap:5px;font-size:.75rem;color:#16a34a;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:20px;padding:4px 10px}
.co-page{max-width:960px;margin:32px auto;padding:0 20px 60px;display:grid;grid-template-columns:1fr 340px;gap:24px;align-items:start}
@media(max-width:680px){.co-page{grid-template-columns:1fr;margin:16px auto}}
.co-card{background:#fff;border-radius:16px;box-shadow:0 2px 12px rgba(0,0,0,.06);overflow:hidden;margin-bottom:20px}
.co-card-head{padding:18px 22px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;gap:10px}
.co-step-num{width:26px;height:26px;background:#4f46e5;color:#fff;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.75rem;font-weight:700;flex-shrink:0}
.co-card-title{font-size:.95rem;font-weight:700;margin:0}
.co-card-body{padding:22px}
.form-group{margin-bottom:18px}
.form-label{display:flex;align-items:center;gap:4px;font-size:.8rem;font-weight:600;color:#374151;margin-bottom:7px}
.form-label .req{color:#ef4444}
.form-control{width:100%;border:1.5px solid #e2e8f0;border-radius:10px;padding:10px 14px;font-size:.88rem;color:#1e293b;outline:none;transition:.2s;background:#fafafa;font-family:inherit}
.form-control:focus{border-color:#6366f1;background:#fff;box-shadow:0 0 0 3px rgba(99,102,241,.1)}
/* cart items */
.cart-row{display:flex;align-items:center;gap:12px;padding:14px 0;border-bottom:1px solid #f1f5f9}
.cart-row:last-child{border-bottom:none}
.cart-img{width:52px;height:52px;border-radius:8px;object-fit:cover;background:#f3f4f6;flex-shrink:0}
.cart-img-ph{width:52px;height:52px;border-radius:8px;background:linear-gradient(135deg,#ede9fe,#ddd6fe);display:flex;align-items:center;justify-content:center;font-size:1.2rem;color:#7c3aed;flex-shrink:0}
.cart-info{flex:1}
.cart-name{font-weight:700;font-size:.88rem;color:#1e293b}
.cart-unit{font-size:.75rem;color:#6b7280}
.cart-price{font-weight:800;color:#4f46e5;font-size:.9rem;white-space:nowrap}
/* pm */
.pm-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(130px,1fr));gap:10px}
.pm-card{border:2px solid #e2e8f0;border-radius:12px;padding:12px 10px;cursor:pointer;display:flex;flex-direction:column;align-items:center;gap:6px;transition:.2s;position:relative;background:#fff;user-select:none}
.pm-card:hover{border-color:#a5b4fc;background:#f8f7ff}
.pm-card.selected{border-color:#4f46e5;background:#eef2ff}
.pm-card.selected::after{content:'✓';position:absolute;top:6px;right:8px;font-size:.65rem;font-weight:700;color:#4f46e5}
.pm-icon{width:44px;height:44px;object-fit:contain;border-radius:8px}
.pm-icon-ph{width:44px;height:44px;border-radius:8px;background:linear-gradient(135deg,#e0e7ff,#c7d2fe);display:flex;align-items:center;justify-content:center;font-size:.65rem;font-weight:700;color:#4f46e5;text-align:center;padding:2px}
.pm-name{font-size:.72rem;font-weight:600;color:#374151;text-align:center}
.pm-fee{font-size:.65rem;color:#94a3b8;text-align:center}
.pm-group-title{font-size:.72rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;margin:18px 0 8px;padding-bottom:6px;border-bottom:1px solid #f1f5f9}
.pm-group-title:first-child{margin-top:0}
input[type=radio].pm-radio{display:none}
.btn-pay{width:100%;background:linear-gradient(135deg,#4f46e5,#6366f1);color:#fff;border:none;border-radius:12px;padding:14px;font-weight:700;font-size:.95rem;cursor:pointer;transition:.2s;margin-top:20px;display:flex;align-items:center;justify-content:center;gap:8px}
.btn-pay:hover{background:linear-gradient(135deg,#3730a3,#4f46e5);transform:translateY(-1px);box-shadow:0 4px 16px rgba(79,70,229,.3)}
.co-summary{position:sticky;top:74px}
.co-divider{height:1px;background:#f1f5f9;margin:14px 0}
.co-row{display:flex;justify-content:space-between;font-size:.83rem;color:#64748b;margin-bottom:8px}
.co-total-row{display:flex;justify-content:space-between;font-weight:800;font-size:1rem;padding-top:14px;border-top:2px solid #f1f5f9;margin-top:4px}
.co-total-val{color:#4f46e5}
</style>

<!-- TOPBAR -->
<nav class="co-topbar">
    <?php if($data->shop->logo_image): ?>
        <img src="<?= \Altum\Uploads::get_full_url('shop_logos') . $data->shop->logo_image ?>" style="width:30px;height:30px;border-radius:8px;object-fit:cover" alt="">
    <?php else: ?>
        <div style="width:30px;height:30px;border-radius:8px;background:linear-gradient(135deg,#4f46e5,#818cf8);display:flex;align-items:center;justify-content:center;color:#fff;font-size:.8rem"><i class="fas fa-shopping-bag"></i></div>
    <?php endif ?>
    <span class="co-shop-name"><?= htmlspecialchars($data->shop->name) ?></span>
    <div class="co-secure-badge"><i class="fas fa-lock"></i> Pembayaran Aman</div>
    <a href="<?= SITE_URL . 'store/' . htmlspecialchars($data->shop->url) ?>" class="co-back">
        <i class="fas fa-arrow-left fa-sm"></i> Kembali ke Toko
    </a>
</nav>

<div class="co-page">
    <!-- LEFT: FORM -->
    <div>
        <?= \Altum\Alerts::output_alerts() ?>

        <form method="post" action="" id="cartCheckoutForm">
            <input type="hidden" name="token" value="<?= \Altum\Csrf::get() ?>">
            <input type="hidden" name="do_payment" value="1">
            <input type="hidden" name="shop_url" value="<?= htmlspecialchars($data->shop->url) ?>">
            <input type="hidden" name="payment_method" id="selectedMethod" value="QRIS">
            <!-- Re-submit cart items (validated server-side again) -->
            <?php foreach($data->post_items as $i => $pi): ?>
                <input type="hidden" name="items[<?= $i ?>][id]" value="<?= (int)($pi['id'] ?? 0) ?>">
                <input type="hidden" name="items[<?= $i ?>][qty]" value="<?= (int)($pi['qty'] ?? 1) ?>">
            <?php endforeach ?>

            <!-- STEP 1: Buyer Info -->
            <div class="co-card">
                <div class="co-card-head">
                    <div class="co-step-num">1</div>
                    <h2 class="co-card-title">Informasi Pembeli</h2>
                </div>
                <div class="co-card-body">
                    <div class="form-group">
                        <label class="form-label">Alamat Email <span class="req">*</span></label>
                        <input type="email" name="email" class="form-control" required placeholder="email@kamu.com">
                        <div style="font-size:.73rem;color:#94a3b8;margin-top:5px"><i class="fas fa-info-circle fa-xs"></i> Konfirmasi pesanan dikirim ke email ini.</div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nama Lengkap <span class="req">*</span></label>
                        <input type="text" name="full_name" class="form-control" required placeholder="John Doe">
                    </div>
                    <div class="form-group" style="margin-bottom:0">
                        <label class="form-label">No. WhatsApp</label>
                        <input type="text" name="phone" class="form-control" placeholder="08xxxxxxxxxx">
                    </div>
                </div>
            </div>

            <!-- STEP 2: Payment Method -->
            <div class="co-card">
                <div class="co-card-head">
                    <div class="co-step-num">2</div>
                    <h2 class="co-card-title">Pilih Metode Pembayaran</h2>
                </div>
                <div class="co-card-body">
                    <?php
                    $groups = [];
                    foreach($data->payment_channels as $ch) {
                        $g = $ch->group ?? 'Lainnya';
                        $groups[$g][] = $ch;
                    }
                    $first = true;
                    foreach($groups as $gname => $channels):
                    ?>
                    <div class="pm-group-title"><?= htmlspecialchars($gname) ?></div>
                    <div class="pm-grid">
                        <?php foreach($channels as $ch): ?>
                        <label class="pm-card <?= $first ? 'selected' : '' ?>" for="pm_<?= $ch->code ?>">
                            <input type="radio" class="pm-radio" name="_pm" id="pm_<?= $ch->code ?>"
                                   value="<?= $ch->code ?>" <?= $first ? 'checked' : '' ?>
                                   onchange="selectMethod('<?= $ch->code ?>')">
                            <?php if(!empty($ch->icon_url)): ?>
                                <img src="<?= htmlspecialchars($ch->icon_url) ?>" class="pm-icon" alt="<?= htmlspecialchars($ch->name) ?>"
                                     onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                                <div class="pm-icon-ph" style="display:none"><?= mb_substr($ch->name,0,3) ?></div>
                            <?php else: ?>
                                <div class="pm-icon-ph"><?= mb_substr($ch->name,0,3) ?></div>
                            <?php endif ?>
                            <div class="pm-name"><?= htmlspecialchars($ch->name) ?></div>
                        </label>
                        <?php $first = false; endforeach ?>
                    </div>
                    <?php endforeach ?>

                    <button type="submit" class="btn-pay">
                        <i class="fas fa-lock"></i>
                        Bayar Sekarang — Rp <?= number_format($data->grand_total, 0, ',', '.') ?>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- RIGHT: ORDER SUMMARY -->
    <div class="co-summary">
        <div class="co-card">
            <div class="co-card-head">
                <div class="co-step-num" style="background:#0ea5e9"><i class="fas fa-list-ul"></i></div>
                <h2 class="co-card-title">Ringkasan Pesanan (<?= count($data->cart_items) ?> produk)</h2>
            </div>
            <div class="co-card-body">
                <?php foreach($data->cart_items as $ci): ?>
                <div class="cart-row">
                    <?php if($ci['item']->image): ?>
                        <img src="<?= \Altum\Uploads::get_full_url('shop_items') . $ci['item']->image ?>" class="cart-img" alt="">
                    <?php else: ?>
                        <div class="cart-img-ph"><i class="fas fa-box"></i></div>
                    <?php endif ?>
                    <div class="cart-info">
                        <div class="cart-name"><?= htmlspecialchars($ci['item']->name) ?></div>
                        <div class="cart-unit">Rp <?= number_format($ci['unit_price'], 0, ',', '.') ?> × <?= $ci['qty'] ?></div>
                    </div>
                    <div class="cart-price">Rp <?= number_format($ci['subtotal'], 0, ',', '.') ?></div>
                </div>
                <?php endforeach ?>

                <div class="co-divider"></div>
                <div class="co-total-row">
                    <span>Total Bayar</span>
                    <span class="co-total-val">Rp <?= number_format($data->grand_total, 0, ',', '.') ?></span>
                </div>
                <p style="text-align:center;font-size:.72rem;color:#94a3b8;margin:14px 0 0">
                    <i class="fas fa-shield-alt fa-sm" style="color:#16a34a"></i>
                    Transaksi aman &amp; terenkripsi SSL
                </p>
            </div>
        </div>
    </div>
</div>

<script>
function selectMethod(code) {
    document.getElementById('selectedMethod').value = code;
    document.querySelectorAll('.pm-card').forEach(function(c){ c.classList.remove('selected'); });
    var radio = document.getElementById('pm_' + code);
    if(radio) radio.closest('.pm-card').classList.add('selected');
}
var firstRadio = document.querySelector('.pm-radio');
if(firstRadio) selectMethod(firstRadio.value);
</script>
