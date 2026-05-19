<?php defined('ALTUMCODE') || die() ?>
<?php \Altum\Title::set('Checkout — ' . htmlspecialchars($data->shop->name)) ?>

<style>
*,*::before,*::after{box-sizing:border-box}
body{background:#f0f2f8;font-family:'Inter',sans-serif;color:#1e293b;margin:0;min-height:100vh}

/* ── mini topbar ── */
.co-topbar{background:#fff;border-bottom:1px solid #e2e8f0;padding:0 24px;height:54px;display:flex;align-items:center;gap:12px;position:sticky;top:0;z-index:10;box-shadow:0 1px 4px rgba(0,0,0,.05)}
.co-shop-logo{width:30px;height:30px;border-radius:8px;object-fit:cover}
.co-shop-logo-icon{width:30px;height:30px;border-radius:8px;background:linear-gradient(135deg,#4f46e5,#818cf8);display:flex;align-items:center;justify-content:center;color:#fff;font-size:.8rem}
.co-shop-name{font-weight:700;font-size:.95rem;color:#1e293b}
.co-back{display:inline-flex;align-items:center;gap:6px;color:#6b7280;text-decoration:none;font-size:.82rem;margin-left:auto;transition:.2s;padding:6px 10px;border-radius:8px}
.co-back:hover{background:#f3f4f6;color:#4f46e5}
.co-secure-badge{display:flex;align-items:center;gap:5px;font-size:.75rem;color:#16a34a;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:20px;padding:4px 10px;margin-left:8px}

/* ── layout ── */
.co-page{max-width:960px;margin:32px auto;padding:0 20px 60px;display:grid;grid-template-columns:1fr 340px;gap:24px;align-items:start}
@media(max-width:680px){.co-page{grid-template-columns:1fr;margin:16px auto}}

/* ── cards ── */
.co-card{background:#fff;border-radius:16px;box-shadow:0 2px 12px rgba(0,0,0,.06);overflow:hidden}
.co-card-head{padding:18px 22px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;gap:10px}
.co-step-num{width:26px;height:26px;background:#4f46e5;color:#fff;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.75rem;font-weight:700;flex-shrink:0}
.co-card-title{font-size:.95rem;font-weight:700;margin:0}
.co-card-body{padding:22px}

/* ── form ── */
.form-group{margin-bottom:18px}
.form-group:last-of-type{margin-bottom:0}
.form-label{display:flex;align-items:center;gap:4px;font-size:.8rem;font-weight:600;color:#374151;margin-bottom:7px}
.form-label .req{color:#ef4444}
.form-control{width:100%;border:1.5px solid #e2e8f0;border-radius:10px;padding:10px 14px;font-size:.88rem;color:#1e293b;outline:none;transition:.2s;background:#fafafa;font-family:inherit}
.form-control:focus{border-color:#6366f1;background:#fff;box-shadow:0 0 0 3px rgba(99,102,241,.1)}
.form-control:disabled{background:#f1f5f9;color:#94a3b8;cursor:not-allowed;border-color:#e2e8f0;opacity:1}
.form-hint{font-size:.73rem;color:#94a3b8;margin-top:5px}

/* ── payment methods ── */
.pm-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(130px,1fr));gap:10px}
.pm-card{border:2px solid #e2e8f0;border-radius:12px;padding:12px 10px;cursor:pointer;display:flex;flex-direction:column;align-items:center;gap:6px;transition:.2s;position:relative;background:#fff;user-select:none}
.pm-card:hover{border-color:#a5b4fc;background:#f8f7ff}
.pm-card.selected{border-color:#4f46e5;background:#eef2ff}
.pm-card.selected::after{content:'✓';position:absolute;top:6px;right:8px;font-size:.65rem;font-weight:700;color:#4f46e5}
.pm-icon{width:44px;height:44px;object-fit:contain;border-radius:8px}
.pm-icon-ph{width:44px;height:44px;border-radius:8px;background:linear-gradient(135deg,#e0e7ff,#c7d2fe);display:flex;align-items:center;justify-content:center;font-size:.65rem;font-weight:700;color:#4f46e5;text-align:center;line-height:1.2;padding:2px}
.pm-name{font-size:.72rem;font-weight:600;color:#374151;text-align:center;line-height:1.3}
.pm-fee{font-size:.65rem;color:#94a3b8;text-align:center}
.pm-group-title{font-size:.72rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;margin:18px 0 8px;padding-bottom:6px;border-bottom:1px solid #f1f5f9}
.pm-group-title:first-child{margin-top:0}
input[type=radio].pm-radio{display:none}

/* ── submit ── */
.btn-pay{width:100%;background:linear-gradient(135deg,#4f46e5,#6366f1);color:#fff;border:none;border-radius:12px;padding:14px;font-weight:700;font-size:.95rem;cursor:pointer;transition:.2s;margin-top:20px;display:flex;align-items:center;justify-content:center;gap:8px;letter-spacing:.01em}
.btn-pay:hover{background:linear-gradient(135deg,#3730a3,#4f46e5);transform:translateY(-1px);box-shadow:0 4px 16px rgba(79,70,229,.3)}
.btn-pay:active{transform:none}

/* ── order summary ── */
.co-summary{position:sticky;top:74px}
.order-product{display:flex;align-items:center;gap:12px;margin-bottom:18px}
.order-img{width:60px;height:60px;border-radius:10px;object-fit:cover;background:#f1f5f9;flex-shrink:0}
.order-img-icon{width:60px;height:60px;border-radius:10px;background:linear-gradient(135deg,#ede9fe,#ddd6fe);display:flex;align-items:center;justify-content:center;font-size:1.4rem;color:#7c3aed;flex-shrink:0}
.order-name{font-weight:700;font-size:.9rem;color:#1e293b;margin-bottom:2px}
.order-type{font-size:.73rem;color:#7c3aed;background:#ede9fe;padding:2px 8px;border-radius:10px;display:inline-block}
.co-divider{height:1px;background:#f1f5f9;margin:14px 0}
.co-row{display:flex;justify-content:space-between;font-size:.83rem;color:#64748b;margin-bottom:10px}
.co-row:last-of-type{margin-bottom:0}
.co-total-row{display:flex;justify-content:space-between;font-weight:800;font-size:1rem;padding-top:14px;border-top:2px solid #f1f5f9;margin-top:4px}
.co-total-label{color:#1e293b}
.co-total-val{color:#4f46e5}

/* ── selected method preview ── */
.selected-method{background:#f8f7ff;border:1.5px solid #a5b4fc;border-radius:10px;padding:10px 14px;display:flex;align-items:center;gap:10px;margin-top:14px;display:none}
.selected-method.show{display:flex}
.selected-method-name{font-size:.83rem;font-weight:600;color:#4f46e5}

/* ── test mode banner ── */
.test-banner{background:#fffbeb;border:1px solid #fde68a;border-radius:10px;padding:10px 14px;font-size:.78rem;color:#92400e;display:flex;align-items:center;gap:8px;margin-bottom:20px}
</style>

<!-- TOPBAR -->
<nav class="co-topbar">
    <?php if($data->shop->logo_image): ?>
        <img src="<?= \Altum\Uploads::get_full_url('shop_logos') . $data->shop->logo_image ?>" class="co-shop-logo" alt="">
    <?php else: ?>
        <div class="co-shop-logo-icon"><i class="fas fa-shopping-bag"></i></div>
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

        <?php if($data->is_demo): ?>
        <div class="test-banner">
            <i class="fas fa-flask"></i>
            <span><strong>Mode Demo</strong> — Tidak ada gateway aktif. Pembayaran akan disimulasikan. Aktifkan Tripay / Midtrans di panel admin untuk transaksi nyata.</span>
        </div>
        <?php endif ?>

        <form action="<?= SITE_URL ?>store-checkout/<?= (int)$data->item->id ?>?qty=<?= (int)$data->qty ?>" method="post" id="checkoutForm" enctype="multipart/form-data">
            <input type="hidden" name="token" value="<?= \Altum\Csrf::get() ?>">
            <input type="hidden" name="payment_method" id="selectedMethod" value="QRIS">
            <input type="hidden" name="_qty" value="<?= (int)$data->qty ?>">

            <!-- STEP 1: Buyer Info -->
            <div class="co-card" style="margin-bottom:20px">
                <div class="co-card-head">
                    <div class="co-step-num">1</div>
                    <h2 class="co-card-title">Informasi Pembeli</h2>
                </div>
                <div class="co-card-body">
                    <div class="form-group">
                        <label class="form-label">Alamat Email <span class="req">*</span></label>
                        <input type="email" name="email" class="form-control" required placeholder="email@kamu.com">
                        <div class="form-hint"><i class="fas fa-info-circle fa-xs"></i> Produk digital akan dikirim ke email ini.</div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nama Lengkap <span class="req">*</span></label>
                        <input type="text" name="full_name" class="form-control" required placeholder="John Doe">
                    </div>
                    <div class="form-group">
                        <label class="form-label">No. WhatsApp</label>
                        <input type="text" name="phone" class="form-control" placeholder="08xxxxxxxxxx">
                        <div class="form-hint">Opsional — untuk notifikasi pesanan.</div>
                    </div>
                </div>
            </div>

            <?php if($data->item->type === 'physical'): ?>
            <!-- STEP 2: Shipping (Only Physical) -->
            <div class="co-card" style="margin-bottom:20px">
                <div class="co-card-head">
                    <div class="co-step-num">2</div>
                    <h2 class="co-card-title">Pengiriman</h2>
                </div>
                <div class="co-card-body">
                    <div class="form-group">
                        <label class="form-label">Alamat Lengkap <span class="req">*</span></label>
                        <textarea name="shipping_address" class="form-control" rows="3" required placeholder="Jalan, RT/RW, Kelurahan, Kecamatan, Kode Pos"></textarea>
                    </div>
                    
                    <div class="form-row" style="display:flex;gap:16px;margin-bottom:18px">
                        <div class="form-group" style="flex:1;margin-bottom:0">
                            <label class="form-label">Provinsi <span class="req">*</span></label>
                            <select id="destProvince" class="form-control" required>
                                <option value="">-- Pilih Provinsi --</option>
                            </select>
                            <input type="hidden" name="shipping_province" id="shippingProvinceName" value="">
                        </div>

                        <div class="form-group" style="flex:1;margin-bottom:0">
                            <label class="form-label">Kota Tujuan <span class="req">*</span></label>
                            <select name="dest_city_id" id="destCity" class="form-control" required disabled>
                                <option value="">-- Pilih Kota/Kabupaten --</option>
                            </select>
                            <input type="hidden" name="shipping_city" id="shippingCityName" value="">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Kurir Ekspedisi <span class="req">*</span></label>
                        <select id="shippingCourier" class="form-control" required disabled onchange="loadShippingCosts()">
                            <option value="">-- Pilih Kurir --</option>
                            <?php foreach(\Altum\Libraries\RajaOngkir::get_couriers() as $code => $name): ?>
                                <option value="<?= $code ?>"><?= strtoupper($code) ?> - <?= $name ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>

                    <div class="form-group" id="shippingServiceContainer" style="display:none">
                        <label class="form-label">Layanan Pengiriman <span class="req">*</span></label>
                        <div id="shippingServicesList" style="display:flex;flex-direction:column;gap:10px"></div>
                        <input type="hidden" name="shipping_courier" id="selectedCourierInput" value="">
                        <input type="hidden" name="shipping_service" id="selectedServiceInput" value="">
                        <input type="hidden" name="shipping_cost" id="selectedCostInput" value="0">
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- STEP <?= $data->item->type === 'physical' ? '3' : '2' ?>: Payment Method -->
            <div class="co-card">
                <div class="co-card-head">
                    <div class="co-step-num"><?= $data->item->type === 'physical' ? '3' : '2' ?></div>
                    <h2 class="co-card-title">Pilih Metode Pembayaran</h2>
                </div>
                <div class="co-card-body">
                    <?php
                    /* group channels by group */
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
                                   onchange="selectMethod('<?= $ch->code ?>', '<?= addslashes($ch->name) ?>', this.closest('.pm-card').querySelector('.pm-logo-wrap'))"
                            >
                            <div class="pm-logo-wrap">
                                <?php if(!empty($ch->icon_url)): ?>
                                    <img
                                        src="<?= htmlspecialchars($ch->icon_url) ?>"
                                        class="pm-icon"
                                        alt="<?= htmlspecialchars($ch->name) ?>"
                                        onerror="this.style.display='none';this.nextElementSibling.style.display='flex'"
                                    >
                                    <div class="pm-icon-ph" style="display:none"><?= mb_substr($ch->name,0,3) ?></div>
                                <?php else: ?>
                                    <div class="pm-icon-ph"><?= mb_substr($ch->name,0,3) ?></div>
                                <?php endif ?>
                            </div>
                            <div class="pm-name"><?= htmlspecialchars($ch->name) ?></div>
                            <?php
                            $flat = $ch->total_fee->flat ?? 0;
                            $pct  = $ch->total_fee->percent ?? 0;
                            if($flat > 0)      $fee_label = 'Biaya Rp ' . number_format($flat,0,',','.');
                            elseif($pct > 0)   $fee_label = 'Biaya ' . $pct . '%';
                            else               $fee_label = 'Tanpa biaya';
                            ?>
                            <div class="pm-fee"><?= $fee_label ?></div>
                        </label>
                        <?php $first = false; endforeach ?>
                    </div>
                    <?php endforeach ?>

                    <div id="offline_payment_processor_wrapper" style="display: none; margin-top: 20px;">
                        <label class="form-label"><?= l('pay.custom_plan.offline_payment_instructions') ?></label>
                        <div style="background:#f8fafc;border-radius:12px;padding:16px;margin-bottom:16px;font-size:0.85rem;color:#374151;">
                            <?= nl2br(settings()->offline_payment->instructions) ?>
                        </div>

                        <label class="form-label"><?= l('pay.custom_plan.offline_payment_proof') ?> <span class="req">*</span></label>
                        <input id="offline_payment_proof" type="file" name="offline_payment_proof" accept="<?= \Altum\Uploads::get_whitelisted_file_extensions_accept('offline_payment_proofs') ?>" class="form-control" />
                        <div class="form-hint"><?= sprintf(l('global.accessibility.whitelisted_file_extensions'), \Altum\Uploads::get_whitelisted_file_extensions_accept('offline_payment_proofs')) . ' ' . sprintf(l('global.accessibility.file_size_limit'), settings()->offline_payment->proof_size_limit) ?></div>
                    </div>


                    <button type="submit" class="btn-pay" id="btnPay">
                        <i class="fas fa-lock"></i>
                        <span id="btnPayText">Bayar Sekarang</span>
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
                <h2 class="co-card-title">Ringkasan Pesanan</h2>
            </div>
            <div class="co-card-body">
                <div class="order-product">
                    <?php if($data->item->image): ?>
                        <img src="<?= \Altum\Uploads::get_full_url('shop_items') . $data->item->image ?>" class="order-img" alt="">
                    <?php else: ?>
                        <div class="order-img-icon"><i class="fas fa-box"></i></div>
                    <?php endif ?>
                    <div>
                        <div class="order-name"><?= htmlspecialchars($data->item->name) ?></div>
                        <span class="order-type"><?= ucwords(str_replace('_',' ', $data->item->type)) ?></span>
                        <div class="mt-2 d-flex align-items-center" style="gap:8px;flex-wrap:wrap;">
                            <button id="share_copy_btn" type="button" class="btn btn-sm btn-outline-secondary" title="Salin link" style="border-radius:8px;padding:4px 10px;font-size:12px;" onclick="coShareProduct()">
                                <i class="fas fa-link mr-1"></i>Salin Link
                            </button>
                            <a href="https://wa.me/?text=<?= urlencode($data->item->name . ' - ' . (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-sm" style="background:#25D366;color:#fff;border-radius:8px;padding:4px 10px;font-size:12px;text-decoration:none;">
                                <i class="fab fa-whatsapp mr-1"></i>WhatsApp
                            </a>
                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode((isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-sm" style="background:#1877F2;color:#fff;border-radius:8px;padding:4px 10px;font-size:12px;text-decoration:none;">
                                <i class="fab fa-facebook mr-1"></i>Facebook
                            </a>
                        </div>
                        <script>
                        function coShareProduct() {
                            var url = window.location.href;
                            var btn = document.getElementById('share_copy_btn');
                            if (navigator.share) {
                                navigator.share({title: <?= json_encode($data->item->name) ?>, url: url});
                            } else {
                                navigator.clipboard.writeText(url).then(function() {
                                    btn.innerHTML = '<i class="fas fa-check mr-1"></i>Tersalin!';
                                    setTimeout(function() { btn.innerHTML = '<i class="fas fa-link mr-1"></i>Salin Link'; }, 2000);
                                }).catch(function() {
                                    var ta = document.createElement('textarea');
                                    ta.value = url;
                                    document.body.appendChild(ta);
                                    ta.select();
                                    document.execCommand('copy');
                                    document.body.removeChild(ta);
                                    btn.innerHTML = '<i class="fas fa-check mr-1"></i>Tersalin!';
                                    setTimeout(function() { btn.innerHTML = '<i class="fas fa-link mr-1"></i>Salin Link'; }, 2000);
                                });
                            }
                        }
                        </script>
                    </div>
                </div>

                <div class="co-divider"></div>

                <div class="co-row">
                    <span>Harga produk</span>
                    <?php if(!empty($data->item->has_discount) && !empty($data->item->discount_price) && $data->price == $data->item->discount_price): ?>
                        <div style="display:flex; flex-direction:column; align-items:flex-end; gap:2px;">
                            <span style="font-size:0.75rem; color:#9ca3af; text-decoration:line-through;">Rp <?= number_format($data->item->price,0,',','.') ?></span>
                            <span>Rp <?= number_format($data->price,0,',','.') ?></span>
                        </div>
                    <?php else: ?>
                        <span>Rp <?= number_format($data->price,0,',','.') ?></span>
                    <?php endif ?>
                </div>
                <div class="co-row">
                    <span>Kuantitas</span>
                    <span><?= number_format($data->qty,0,',','.') ?></span>
                </div>
                <div class="co-row">
                    <span>Biaya platform</span>
                    <span style="color:#16a34a;font-weight:600">Gratis</span>
                </div>

                <!-- Voucher code input -->
                <div style="margin:14px 0">
                    <div style="display:flex;gap:8px">
                        <input type="text" id="voucherInput" placeholder="Kode voucher" style="flex:1;border:1.5px solid #e2e8f0;border-radius:10px;padding:8px 12px;font-size:.83rem;outline:none;transition:.2s;font-family:inherit;text-transform:uppercase" oninput="this.value=this.value.toUpperCase()">
                        <button type="button" onclick="applyVoucher()" style="background:#4f46e5;color:#fff;border:none;border-radius:10px;padding:8px 14px;font-size:.8rem;font-weight:600;cursor:pointer;white-space:nowrap">Pakai</button>
                    </div>
                    <div id="voucherMsg" style="font-size:.75rem;margin-top:5px"></div>
                    <input type="hidden" name="voucher_code" id="voucherCodeHidden">
                </div>

                <div id="discountRow" class="co-row" style="display:none;color:#16a34a">
                    <span>Diskon Voucher</span>
                    <span id="discountVal">-Rp 0</span>
                </div>

                <!-- selected method preview -->
                <div class="selected-method show" id="methodPreview">

                    <div class="preview-logo-wrap" id="previewLogoWrap"
                         style="width:36px;height:36px;border-radius:8px;overflow:hidden;flex-shrink:0;background:#f1f5f9;display:flex;align-items:center;justify-content:center">
                        <img id="previewLogoImg" src="" alt="" style="width:100%;height:100%;object-fit:contain"
                             onerror="this.style.display='none';document.getElementById('previewLogoText').style.display='block'">
                        <span id="previewLogoText" style="display:none;font-size:.7rem;font-weight:700;color:#4f46e5;text-align:center;padding:2px"></span>
                    </div>
                    <div>
                        <div style="font-size:.72rem;color:#94a3b8">Metode Bayar</div>
                        <div class="selected-method-name" id="methodName"></div>
                    </div>
                </div>

                        <?php if($data->item->type === 'physical'): ?>
                        <div class="summary-row" id="summaryOngkirRow" style="display:none;margin-top:4px">
                            <span>Ongkos Kirim (<span id="summaryCourierName"></span>)</span>
                            <span id="summaryOngkirVal" style="color:#1e293b">Rp 0</span>
                        </div>
                        <?php endif; ?>

                        <div class="summary-total">
                            <span>Total Bayar</span>
                            <span class="co-total-val" id="grandTotalLabel">Rp <?= number_format($data->base_total, 0, ',', '.') ?></span>
                        </div>

                <p style="text-align:center;font-size:.72rem;color:#94a3b8;margin:14px 0 0">
                    <i class="fas fa-shield-alt fa-sm" style="color:#16a34a"></i>
                    Transaksi aman &amp; terenkripsi SSL
                </p>
            </div>
        </div>

        <!-- shop info -->
        <div style="margin-top:14px;text-align:center;font-size:.75rem;color:#94a3b8">
            Toko ini menggunakan <strong style="color:#6366f1"><?= htmlspecialchars(settings()->main->title) ?></strong><br>
            sebagai platform jual-beli digital
        </div>
    </div>
</div>

<script>
/* Build channel data map: code -> {name, iconUrl} */
var CHANNEL_MAP = <?= json_encode(array_reduce($data->payment_channels, function($carry, $ch) {
    $carry[$ch->code] = [
        'name'    => $ch->name,
        'iconUrl' => $ch->icon_url ?? '',
    ];
    return $carry;
}, [])) ?>;

function selectMethod(code, name, logoWrapEl) {
    /* update hidden input */
    document.getElementById('selectedMethod').value = code;
    /* update card selection */
    document.querySelectorAll('.pm-card').forEach(function(c){ c.classList.remove('selected'); });
    var radio = document.getElementById('pm_' + code);
    if(radio) radio.closest('.pm-card').classList.add('selected');
    /* update preview */
    var info = CHANNEL_MAP[code] || {name: name, iconUrl: ''};
    document.getElementById('methodName').textContent = info.name;
    var img = document.getElementById('previewLogoImg');
    var txt = document.getElementById('previewLogoText');
    if(info.iconUrl) {
        img.src = info.iconUrl;
        img.style.display = '';
        txt.style.display = 'none';
    } else {
        img.style.display = 'none';
        txt.textContent = name.substring(0, 3).toUpperCase();
        txt.style.display = 'block';
    }
    
    var wrapper = document.getElementById('offline_payment_processor_wrapper');
    var proof_input = document.getElementById('offline_payment_proof');
    if(code === 'offline_payment') {
        wrapper.style.display = 'block';
        proof_input.required = true;
    } else {
        wrapper.style.display = 'none';
        proof_input.required = false;
    }

    var btnText = document.getElementById('btnPayText');
    if(btnText) btnText.textContent = 'Bayar Sekarang';
}

/* init with first channel */
var firstRadio = document.querySelector('.pm-radio');
if(firstRadio) {
    firstRadio.checked = true;
    selectMethod(firstRadio.value, firstRadio.closest('.pm-card').querySelector('.pm-name').textContent);
}

var BASE_PRICE = <?= (float)$data->base_total ?>;

function applyVoucher() {
    var code = document.getElementById('voucherInput').value.trim();
    var msg  = document.getElementById('voucherMsg');
    if(!code) { msg.innerHTML = '<span style="color:#ef4444">Masukkan kode voucher</span>'; return; }
    msg.innerHTML = '<span style="color:#94a3b8">Memvalidasi...</span>';

    var params = new URLSearchParams({
        shop_id: <?= $data->shop->id ?>,
        item_id: <?= $data->item->id ?>,
        code: code,
        price: BASE_PRICE
    });

    fetch('<?= url('shop-voucher-validate') ?>', {method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body: params})
    .then(r=>r.json()).then(function(res){
        if(res.success) {
            msg.innerHTML = '<span style="color:#16a34a"><i class="fas fa-check"></i> ' + res.message + '</span>';
            document.getElementById('voucherCodeHidden').value = code;
            document.getElementById('discountRow').style.display = 'flex';
            document.getElementById('discountVal').textContent = '-Rp ' + res.discount_amount.toLocaleString('id-ID');
            document.querySelector('.co-total-val').textContent = 'Rp ' + res.final_price.toLocaleString('id-ID');
            document.getElementById('voucherInput').style.borderColor = '#16a34a';
        } else {
            msg.innerHTML = '<span style="color:#ef4444"><i class="fas fa-times"></i> ' + res.message + '</span>';
            document.getElementById('voucherCodeHidden').value = '';
            document.getElementById('discountRow').style.display = 'none';
            document.querySelector('.co-total-val').textContent = 'Rp ' + BASE_PRICE.toLocaleString('id-ID');
            document.getElementById('voucherInput').style.borderColor = '#ef4444';
        }
    }).catch(function(){ msg.innerHTML = '<span style="color:#ef4444">Gagal validasi</span>'; });
}

/* Shipping Logic */
<?php if($data->item->type === 'physical'): ?>
var SHOP_ORIGIN_CITY = <?= (int)($data->shop->origin_city_id ?? 0) ?>;
var ITEM_WEIGHT      = <?= (int)($data->item->weight ?? 1000) ?> * <?= (int)$data->qty ?>;
var ITEM_BASE_PRICE  = BASE_PRICE;
var CURRENT_DISCOUNT = 0;
var CURRENT_ONGKIR   = 0;

document.addEventListener('DOMContentLoaded', function(){
    loadDestProvinces();
    
    document.getElementById('destProvince').addEventListener('change', function(){
        document.getElementById('destCity').disabled = !this.value;
        document.getElementById('shippingCourier').disabled = true;
        document.getElementById('shippingCourier').value = '';
        if(this.value) {
            document.getElementById('shippingProvinceName').value = this.options[this.selectedIndex].text;
            loadDestCities(this.value);
        } else {
            document.getElementById('shippingProvinceName').value = '';
            document.getElementById('destCity').innerHTML = '<option value="">-- Pilih Kota/Kabupaten --</option>';
        }
    });

    document.getElementById('destCity').addEventListener('change', function(){
        document.getElementById('shippingCourier').disabled = !this.value;
        if(this.value) {
            document.getElementById('shippingCityName').value = this.options[this.selectedIndex].text;
            if(document.getElementById('shippingCourier').value) {
                loadShippingCosts();
            }
        } else {
            document.getElementById('shippingCityName').value = '';
        }
    });
});

function loadDestProvinces() {
    var select = document.getElementById('destProvince');
    select.innerHTML = '<option value="">Loading provinsi...</option>';
    fetch('<?= url('shop-ajax?action=ongkir_provinces') ?>')
    .then(r=>r.json()).then(res=>{
        if(res.success) {
            select.innerHTML = '<option value="">-- Pilih Provinsi --</option>';
            res.data.forEach(p => {
                select.innerHTML += `<option value="${p.province_id}">${p.province}</option>`;
            });
        }
    });
}

function loadDestCities(province_id) {
    var select = document.getElementById('destCity');
    select.innerHTML = '<option value="">Loading kota...</option>';
    fetch('<?= url('shop-ajax?action=ongkir_cities&province_id=') ?>' + province_id)
    .then(r=>r.json()).then(res=>{
        if(res.success) {
            select.innerHTML = '<option value="">-- Pilih Kota/Kabupaten --</option>';
            res.data.forEach(c => {
                select.innerHTML += `<option value="${c.city_id}">${c.city_name}</option>`;
            });
        }
    });
}

function loadShippingCosts() {
    var dest    = document.getElementById('destCity').value;
    var courier = document.getElementById('shippingCourier').value;
    if(!dest || !courier) return;

    var container = document.getElementById('shippingServiceContainer');
    var list      = document.getElementById('shippingServicesList');
    
    container.style.display = 'block';
    list.innerHTML = '<div style="font-size:.85rem;color:#64748b">Menghitung ongkir...</div>';
    
    // Reset inputs & total
    document.getElementById('selectedCourierInput').value = '';
    document.getElementById('selectedServiceInput').value = '';
    document.getElementById('selectedCostInput').value = '0';
    updateGrandTotal(0, '');

    fetch(`<?= url('shop-ajax?action=ongkir_cost') ?>&origin=${SHOP_ORIGIN_CITY}&dest=${dest}&weight=${ITEM_WEIGHT}`)
    .then(r=>r.json()).then(res=>{
        if(res.success && res.data[courier] && res.data[courier].length > 0) {
            list.innerHTML = '';
            res.data[courier].forEach(svc => {
                var cost = svc.cost[0].value;
                var etd  = svc.cost[0].etd ? `(${svc.cost[0].etd} hari)` : '';
                var id   = `svc_${courier}_${svc.service}`;
                list.innerHTML += `
                    <label style="display:flex;align-items:center;padding:12px;border:1.5px solid #e2e8f0;border-radius:10px;cursor:pointer;background:#fafafa;transition:.2s" onmouseover="this.style.borderColor='#6366f1'" onmouseout="if(!this.querySelector('input').checked) this.style.borderColor='#e2e8f0'">
                        <input type="radio" name="temp_service" value="${svc.service}" data-cost="${cost}" data-courier="${courier}" onchange="selectShippingService(this)" style="margin-right:12px;width:18px;height:18px">
                        <div style="flex:1">
                            <div style="font-weight:700;font-size:.88rem;color:#1e293b">${svc.service} <span style="font-weight:400;color:#64748b;font-size:.8rem">${etd}</span></div>
                            <div style="font-size:.75rem;color:#94a3b8">${svc.description}</div>
                        </div>
                        <div style="font-weight:800;color:#4f46e5;font-size:.95rem">Rp ${cost.toLocaleString('id-ID')}</div>
                    </label>
                `;
            });
        } else {
            list.innerHTML = '<div style="font-size:.85rem;color:#ef4444">Layanan tidak tersedia untuk rute ini.</div>';
        }
    }).catch(err => {
        list.innerHTML = '<div style="font-size:.85rem;color:#ef4444">Gagal mengambil data ongkir.</div>';
    });
}

function selectShippingService(radio) {
    var cost = parseInt(radio.getAttribute('data-cost'));
    var courier = radio.getAttribute('data-courier');
    var service = radio.value;

    document.getElementById('selectedCourierInput').value = courier;
    document.getElementById('selectedServiceInput').value = service;
    document.getElementById('selectedCostInput').value = cost;
    
    // style all labels
    var labels = document.getElementById('shippingServicesList').querySelectorAll('label');
    labels.forEach(l => {
        l.style.borderColor = '#e2e8f0';
        l.style.background  = '#fafafa';
    });
    radio.closest('label').style.borderColor = '#4f46e5';
    radio.closest('label').style.background  = '#f0f0ff';

    updateGrandTotal(cost, courier.toUpperCase());
}

function updateGrandTotal(shippingCost, courierName) {
    CURRENT_ONGKIR = shippingCost;
    var currentDiscountStr = document.getElementById('discountVal').textContent.replace(/[^0-9]/g, '');
    CURRENT_DISCOUNT = currentDiscountStr ? parseInt(currentDiscountStr) : 0;
    
    var grandTotal = Math.max(0, ITEM_BASE_PRICE - CURRENT_DISCOUNT) + CURRENT_ONGKIR;
    
    // Update labels
    document.getElementById('grandTotalLabel').textContent = 'Rp ' + grandTotal.toLocaleString('id-ID');
    
    var row = document.getElementById('summaryOngkirRow');
    if(CURRENT_ONGKIR > 0) {
        row.style.display = 'flex';
        document.getElementById('summaryCourierName').textContent = courierName;
        document.getElementById('summaryOngkirVal').textContent = 'Rp ' + CURRENT_ONGKIR.toLocaleString('id-ID');
    } else {
        row.style.display = 'none';
    }
}
<?php endif; ?>
</script>
