<?php defined('ALTUMCODE') || die() ?>
<?php \Altum\Title::set('Pesanan — ' . htmlspecialchars($data->shop->name)) ?>
<?php
/* ── Decode fulfilled content ── */
$fulfilled = $data->order->fulfilled_content ?? null;
$download_links = [];
$random_code    = null;

if($data->item->type === 'download_link' && $fulfilled) {
    $decoded = json_decode($fulfilled, true);
    $download_links = is_array($decoded) ? $decoded : [$fulfilled];
}
if($data->item->type === 'random_code' && $fulfilled && $fulfilled !== 'OUT_OF_STOCK') {
    $random_code = $fulfilled;
}

$is_paid        = in_array($data->order->status, ['paid']);
$is_pending     = in_array($data->order->status, ['pending']);
$is_offline_payment = ($data->order->payment_processor === 'offline_payment');
?>

<style>
*,*::before,*::after{box-sizing:border-box}
body{background:#f0f2f8;font-family:'Inter',sans-serif;color:#1e293b;margin:0;min-height:100vh}
/* topbar */
.co-topbar{background:#fff;border-bottom:1px solid #e2e8f0;padding:0 24px;height:54px;display:flex;align-items:center;gap:12px;position:sticky;top:0;z-index:10;box-shadow:0 1px 4px rgba(0,0,0,.05)}
.co-shop-logo{width:30px;height:30px;border-radius:8px;object-fit:cover}
.co-shop-logo-icon{width:30px;height:30px;border-radius:8px;background:linear-gradient(135deg,#4f46e5,#818cf8);display:flex;align-items:center;justify-content:center;color:#fff;font-size:.8rem}
.co-shop-name{font-weight:700;font-size:.95rem;color:#1e293b}
.co-back{display:inline-flex;align-items:center;gap:6px;color:#6b7280;text-decoration:none;font-size:.82rem;margin-left:auto;transition:.2s;padding:6px 10px;border-radius:8px}
.co-back:hover{background:#f3f4f6;color:#4f46e5}
/* layout */
.co-page{max-width:640px;margin:40px auto;padding:0 20px 60px}
/* status hero */
.status-hero{background:#fff;border-radius:20px;box-shadow:0 4px 20px rgba(0,0,0,.07);padding:36px 32px;text-align:center;margin-bottom:24px;position:relative;overflow:hidden}
.status-hero::before{content:'';position:absolute;inset:0;background:linear-gradient(135deg,rgba(79,70,229,.03),rgba(16,185,129,.03));pointer-events:none}
.status-icon{width:72px;height:72px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:1.75rem;margin:0 auto 16px}
.status-icon.success{background:#d1fae5}
.status-icon.pending{background:#fef3c7}
.status-icon.proof{background:#dbeafe}
.status-title{font-size:1.4rem;font-weight:800;margin-bottom:6px}
.status-sub{font-size:.85rem;color:#64748b}
.invoice-badge{display:inline-block;background:#f1f5f9;border-radius:8px;padding:6px 14px;font-size:.78rem;color:#475569;font-family:monospace;margin-top:12px;letter-spacing:.03em}
/* card */
.co-card{background:#fff;border-radius:16px;box-shadow:0 2px 10px rgba(0,0,0,.06);margin-bottom:16px;overflow:hidden}
.co-card-head{padding:16px 22px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;gap:10px}
.co-card-icon{width:34px;height:34px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:.9rem;flex-shrink:0}
.co-card-title{font-size:.92rem;font-weight:700;margin:0;flex:1}
.co-card-body{padding:20px 22px}
/* delivery */
.delivery-box{background:linear-gradient(135deg,#f8faff,#eef2ff);border:1.5px solid #c7d2fe;border-radius:12px;padding:18px;position:relative}
.delivery-label{font-size:.7rem;font-weight:700;color:#6366f1;text-transform:uppercase;letter-spacing:.08em;margin-bottom:10px}
.download-link{display:flex;align-items:center;gap:10px;background:#fff;border:1.5px solid #e2e8f0;border-radius:10px;padding:10px 14px;text-decoration:none;color:#4f46e5;font-weight:600;font-size:.85rem;margin-bottom:8px;transition:.2s}
.download-link:last-child{margin-bottom:0}
.download-link:hover{border-color:#6366f1;background:#f8f7ff}
.download-link i{color:#6366f1;font-size:1rem;flex-shrink:0}
.code-box{background:#1e1b4b;border-radius:12px;padding:18px;text-align:center;color:#fff}
.code-val{font-size:1.6rem;font-weight:800;letter-spacing:.15em;font-family:monospace;color:#a5b4fc;margin:8px 0}
.code-hint{font-size:.72rem;color:#818cf8}
.btn-copy{background:#4f46e5;color:#fff;border:none;border-radius:8px;padding:8px 18px;font-size:.8rem;font-weight:600;cursor:pointer;transition:.2s;margin-top:10px}
.btn-copy:hover{background:#3730a3}
/* offline */
.instructions-box{background:#fffbeb;border:1.5px solid #fde68a;border-radius:12px;padding:16px;font-size:.85rem;color:#78350f;white-space:pre-wrap;line-height:1.7}
.upload-area{border:2px dashed #cbd5e1;border-radius:12px;padding:24px;text-align:center;cursor:pointer;transition:.2s;position:relative}
.upload-area:hover{border-color:#6366f1;background:#f8f7ff}
.upload-area input[type=file]{position:absolute;inset:0;opacity:0;cursor:pointer;width:100%;height:100%}
.upload-icon{font-size:2rem;color:#94a3b8;margin-bottom:8px}
.upload-text{font-size:.82rem;color:#64748b}
.btn-upload{width:100%;background:#4f46e5;color:#fff;border:none;border-radius:12px;padding:12px;font-weight:700;font-size:.9rem;cursor:pointer;transition:.2s;margin-top:14px;display:flex;align-items:center;justify-content:center;gap:8px}
.btn-upload:hover{background:#3730a3}
/* order summary */
.summary-row{display:flex;justify-content:space-between;font-size:.83rem;margin-bottom:10px;color:#64748b}
.summary-row:last-child{margin-bottom:0}
.summary-total{display:flex;justify-content:space-between;font-weight:800;font-size:1rem;border-top:2px solid #f1f5f9;padding-top:14px;margin-top:4px}
.product-row{display:flex;gap:12px;align-items:center;margin-bottom:16px}
.product-img{width:50px;height:50px;border-radius:10px;object-fit:cover;background:#f1f5f9;flex-shrink:0}
.product-img-icon{width:50px;height:50px;border-radius:10px;background:linear-gradient(135deg,#ede9fe,#ddd6fe);display:flex;align-items:center;justify-content:center;font-size:1.2rem;color:#7c3aed;flex-shrink:0}
.product-name{font-weight:700;font-size:.88rem}
.product-type{font-size:.72rem;color:#7c3aed;background:#ede9fe;padding:2px 8px;border-radius:10px;display:inline-block;margin-top:3px}
/* back to store */
.btn-store{display:flex;align-items:center;justify-content:center;gap:8px;background:#f1f5f9;color:#475569;border-radius:12px;padding:12px;font-weight:600;font-size:.88rem;text-decoration:none;transition:.2s}
.btn-store:hover{background:#e2e8f0;color:#1e293b}
/* proof uploaded */
.proof-success{background:#f0fdf4;border:1.5px solid #86efac;border-radius:12px;padding:14px;display:flex;align-items:center;gap:10px;font-size:.85rem;color:#166534}
</style>

<!-- TOPBAR -->
<nav class="co-topbar">
    <?php if($data->shop->logo_image): ?>
        <img src="<?= \Altum\Uploads::get_full_url('shop_logos') . $data->shop->logo_image ?>" class="co-shop-logo" alt="">
    <?php else: ?>
        <div class="co-shop-logo-icon"><i class="fas fa-shopping-bag"></i></div>
    <?php endif ?>
    <span class="co-shop-name"><?= htmlspecialchars($data->shop->name) ?></span>
    <a href="<?= SITE_URL . 'store/' . htmlspecialchars($data->shop->url) ?>" class="co-back">
        <i class="fas fa-arrow-left fa-sm"></i> Kembali ke Toko
    </a>
</nav>

<div class="co-page">

    <?= \Altum\Alerts::output_alerts() ?>

    <!-- STATUS HERO -->
    <div class="status-hero">
        <?php if($is_paid): ?>
            <div class="status-icon success"><i class="fas fa-check-circle" style="color:#059669"></i></div>
            <div class="status-title" style="color:#059669">Pembayaran Berhasil!</div>
            <div class="status-sub">Produk kamu sudah siap. Cek detail pengiriman di bawah.</div>
        <?php else: ?>
            <div class="status-icon pending"><i class="fas fa-clock" style="color:#d97706"></i></div>
            <?php if($is_offline_payment): ?>
                <div class="status-title" style="color:#d97706">Menunggu Verifikasi</div>
                <div class="status-sub">Bukti pembayaran kamu sudah diterima. Admin akan memverifikasi dalam 1x24 jam.</div>
            <?php else: ?>
                <div class="status-title" style="color:#d97706">Menunggu Pembayaran</div>
                <div class="status-sub">Selesaikan pembayaran kamu. Link pembayaran juga sudah dikirim ke email kamu.</div>
            <?php endif ?>
            <?php if(!empty($data->order->checkout_url) && !$is_offline_payment): ?>
            <div style="margin-top:16px;display:flex;flex-direction:column;gap:10px;max-width:320px;margin-left:auto;margin-right:auto">
                <a href="<?= htmlspecialchars($data->order->checkout_url) ?>"
                   style="display:flex;align-items:center;justify-content:center;gap:8px;background:#4f46e5;color:#fff;padding:14px 24px;border-radius:14px;text-decoration:none;font-weight:700;font-size:.95rem;box-shadow:0 4px 12px rgba(79,70,229,.3);transition:.2s"
                   onmouseover="this.style.background='#3730a3'" onmouseout="this.style.background='#4f46e5'">
                    <i class="fas fa-credit-card"></i> Bayar Sekarang
                </a>
                <a href="<?= url('store-checkout-success/' . $data->order->invoice_number) ?>"
                   style="display:flex;align-items:center;justify-content:center;gap:8px;background:#f1f5f9;color:#475569;padding:11px 24px;border-radius:12px;text-decoration:none;font-size:.85rem;font-weight:600">
                    <i class="fas fa-sync-alt fa-sm"></i> Sudah Bayar? Cek Status
                </a>
            </div>
            <?php endif ?>
        <?php endif ?>
        <div class="invoice-badge"><?= htmlspecialchars($data->order->invoice_number) ?></div>
    </div>


    <?php if($is_paid): ?>
    <!-- ── PRODUCT DELIVERY ── -->
    <div class="co-card">
        <div class="co-card-head">
            <div class="co-card-icon" style="background:#d1fae5;color:#059669"><i class="fas fa-box-open"></i></div>
            <h2 class="co-card-title">Produk Kamu</h2>
        </div>
        <div class="co-card-body">
            <?php if($data->item->type === 'download_link' && !empty($download_links)): ?>
                <div class="delivery-box">
                    <div class="delivery-label"><i class="fas fa-download fa-sm"></i> Link Download</div>
                    <?php foreach($download_links as $i => $link): ?>
                    <a href="<?= htmlspecialchars($link) ?>" target="_blank" rel="noopener" class="download-link">
                        <i class="fas fa-file-download"></i>
                        <span>File <?= $i + 1 ?><?= count($download_links) > 1 ? '' : ' — Klik untuk Download' ?></span>
                        <i class="fas fa-external-link-alt fa-xs" style="margin-left:auto;color:#94a3b8"></i>
                    </a>
                    <?php endforeach ?>
                </div>
                <p style="font-size:.75rem;color:#94a3b8;margin:10px 0 0"><i class="fas fa-info-circle fa-xs"></i> Link download berlaku selama produk aktif. Simpan sebelum kadaluarsa.</p>

            <?php elseif($data->item->type === 'random_code' && $random_code): ?>
                <div class="delivery-box">
                    <div class="delivery-label"><i class="fas fa-key fa-sm"></i> Kode Produk</div>
                    <div class="code-box">
                        <div style="font-size:.75rem;color:#818cf8">Kode unik kamu:</div>
                        <div class="code-val" id="prodCode"><?= htmlspecialchars($random_code) ?></div>
                        <div class="code-hint">Simpan kode ini — hanya ditampilkan sekali</div>
                        <button class="btn-copy" onclick="copyCode()"><i class="fas fa-copy fa-sm"></i> Salin Kode</button>
                    </div>
                </div>

            <?php elseif($data->item->type === 'webhook_event'): ?>
                <div class="delivery-box">
                    <div class="delivery-label"><i class="fas fa-bolt fa-sm"></i> Proses Otomatis</div>
                    <p style="font-size:.85rem;color:#475569;margin:0">Pesanan kamu sedang diproses secara otomatis oleh sistem penjual. Kamu akan dihubungi via email <strong><?= htmlspecialchars($data->order->email) ?></strong>.</p>
                </div>

            <?php elseif($data->item->type === 'physical'): ?>
                <div class="delivery-box">
                    <div class="delivery-label"><i class="fas fa-truck fa-sm"></i> Pengiriman Fisik</div>
                    <p style="font-size:.85rem;color:#475569;margin:0">Pesanan fisik kamu sedang disiapkan oleh penjual. Nomor resi pengiriman akan dikirimkan ke email <strong><?= htmlspecialchars($data->order->email) ?></strong> jika pesanan sudah dikirim.</p>
                </div>

            <?php elseif($data->item->type === 'manual'): ?>
                <div class="delivery-box">
                    <div class="delivery-label"><i class="fas fa-user fa-sm"></i> Proses Manual</div>
                    <p style="font-size:.85rem;color:#475569;margin:0">Penjual akan memproses pesanan kamu secara manual. Pantau email <strong><?= htmlspecialchars($data->order->email) ?></strong> untuk update.</p>
                </div>

            <?php else: ?>
                <p style="font-size:.85rem;color:#64748b">Produk sedang disiapkan. Cek email kamu untuk instruksi lebih lanjut.</p>
            <?php endif ?>
        </div>
    </div>
    <?php endif ?>



    <!-- ── ORDER SUMMARY ── -->
    <div class="co-card">
        <div class="co-card-head">
            <div class="co-card-icon" style="background:#f1f5f9;color:#64748b"><i class="fas fa-receipt"></i></div>
            <h2 class="co-card-title">Detail Pesanan</h2>
        </div>
        <div class="co-card-body">
            <div class="product-row">
                <?php if($data->item->image): ?>
                    <img src="<?= \Altum\Uploads::get_full_url('shop_items') . $data->item->image ?>" class="product-img" alt="">
                <?php else: ?>
                    <div class="product-img-icon"><i class="fas fa-box"></i></div>
                <?php endif ?>
                <div>
                    <div class="product-name"><?= htmlspecialchars($data->item->name) ?></div>
                    <span class="product-type"><?= ucwords(str_replace('_', ' ', $data->item->type)) ?></span>
                </div>
            </div>
            <hr style="border:none;border-top:1px solid #f1f5f9;margin:0 0 14px">
            <div class="summary-row"><span>Pembeli</span><span><?= htmlspecialchars($data->order->full_name) ?></span></div>
            <div class="summary-row"><span>Email</span><span><?= htmlspecialchars($data->order->email) ?></span></div>
            <div class="summary-row"><span>Tanggal Pesan</span><span><?= date('d M Y H:i', strtotime($data->order->datetime)) ?></span></div>
            <div class="summary-row"><span>Status</span>
                <span style="font-weight:700;color:<?= $is_paid ? '#059669' : '#d97706' ?>">
                    <?= $is_paid ? 'Lunas' : ($is_offline_payment ? 'Menunggu Verifikasi' : 'Menunggu Bayar') ?>
                </span>
            </div>
            
            <hr style="border:none;border-top:1px dashed #e2e8f0;margin:12px 0 14px">
            
            <div class="summary-row">
                <span>Harga Produk <?= $data->order->qty > 1 ? '(x' . $data->order->qty . ')' : '' ?></span>
                <span>Rp <?= number_format($data->order->total_amount, 0, ',', '.') ?></span>
            </div>
            
            <?php if($data->order->discount_amount > 0): ?>
            <div class="summary-row" style="color:#16a34a">
                <span>Diskon Voucher</span>
                <span>-Rp <?= number_format($data->order->discount_amount, 0, ',', '.') ?></span>
            </div>
            <?php endif; ?>
            
            <?php if($data->order->shipping_cost > 0): ?>
            <div class="summary-row">
                <span>Ongkos Kirim (<?= strtoupper($data->order->shipping_courier) ?>)</span>
                <span>Rp <?= number_format($data->order->shipping_cost, 0, ',', '.') ?></span>
            </div>
            <?php endif; ?>
            
            <div class="summary-total">
                <span>Total Bayar</span>
                <span style="color:#4f46e5">Rp <?= number_format($data->order->grand_total, 0, ',', '.') ?></span>
            </div>
        </div>
    </div>

    <!-- Kembali ke toko -->
    <a href="<?= SITE_URL . 'store/' . htmlspecialchars($data->shop->url) ?>" class="btn-store">
        <i class="fas fa-store"></i> Belanja Lagi di <?= htmlspecialchars($data->shop->name) ?>
    </a>

    <?php if($is_paid && $data->shop->is_review_enabled): ?>
    <!-- REVIEW FORM -->
    <div class="co-card" style="margin-top:16px" id="reviewSection">
        <div class="co-card-head">
            <div class="co-card-icon" style="background:#fef3c7;color:#f59e0b"><i class="fas fa-star"></i></div>
            <h2 class="co-card-title">Beri Ulasan</h2>
        </div>
        <div class="co-card-body">
            <div id="reviewForm">
                <div style="margin-bottom:12px">
                    <div style="font-size:.82rem;font-weight:600;margin-bottom:6px">Rating</div>
                    <div id="starRating" style="display:flex;gap:6px;font-size:1.6rem;cursor:pointer">
                        <?php for($s=1;$s<=5;$s++): ?>
                        <span class="star" data-val="<?= $s ?>" style="color:#d1d5db" onclick="setRating(<?= $s ?>)">★</span>
                        <?php endfor; ?>
                    </div>
                </div>
                <textarea id="reviewText" placeholder="Tulis ulasan kamu (opsional)..." style="width:100%;border:1.5px solid #e2e8f0;border-radius:10px;padding:10px 14px;font-size:.85rem;font-family:inherit;resize:vertical;min-height:80px;outline:none"></textarea>
                <button onclick="submitReview()" style="margin-top:10px;background:#4f46e5;color:#fff;border:none;border-radius:12px;padding:10px 24px;font-weight:700;cursor:pointer;display:flex;align-items:center;gap:8px">
                    <i class="fas fa-paper-plane fa-sm"></i> Kirim Ulasan
                </button>
                <div id="reviewMsg" style="margin-top:8px;font-size:.8rem"></div>
            </div>
        </div>
    </div>
    <script>
    var selectedRating = 5;
    function setRating(val) {
        selectedRating = val;
        document.querySelectorAll('.star').forEach(function(s,i){
            s.style.color = i < val ? '#f59e0b' : '#d1d5db';
        });
    }
    setRating(5);
    function submitReview() {
        var review = document.getElementById('reviewText').value;
        var params = new URLSearchParams({invoice:'<?= htmlspecialchars($data->order->invoice_number) ?>', rating:selectedRating, review:review});
        fetch('<?= url('store-review-create') ?>', {method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body:params})
        .then(r=>r.json()).then(function(res){
            if(res.success) {
                document.getElementById('reviewForm').innerHTML = '<div style="text-align:center;padding:16px"><div style="font-size:2rem;color:#f59e0b"><i class="fas fa-star"></i></div><div style="font-weight:700;color:#059669">Terima kasih atas ulasan kamu!</div></div>';
            } else {
                document.getElementById('reviewMsg').innerHTML = '<span style="color:#ef4444">' + (res.message||'Error') + '</span>';
            }
        });
    }
    </script>
    <?php endif; ?>

</div>

<script>
function copyCode() {
    var code = document.getElementById('prodCode');
    if(code) {
        navigator.clipboard.writeText(code.textContent).then(function(){
            var btn = document.querySelector('.btn-copy');
            btn.innerHTML = '<i class="fas fa-check fa-sm"></i> Tersalin!';
            btn.style.background = '#059669';
            setTimeout(function(){ btn.innerHTML = '<i class="fas fa-copy fa-sm"></i> Salin Kode'; btn.style.background = ''; }, 2000);
        });
    }
}

<?php if($is_pending && !empty($data->order->payment_id)): ?>
/* Auto-refresh jika masih pending setelah balik dari Tripay */
(function() {
    var maxTries = 6;
    var tries    = 0;
    var interval = setInterval(function() {
        tries++;
        if(tries >= maxTries) { clearInterval(interval); return; }
        fetch(window.location.href, { method: 'GET', headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function(r) { return r.text(); })
            .then(function(html) {
                /* Cek apakah di response sudah ada 'Pembayaran Berhasil' */
                if(html.indexOf('Pembayaran Berhasil') !== -1 || html.indexOf('fa-check-circle') !== -1) {
                    clearInterval(interval);
                    window.location.reload();
                }
            })
            .catch(function(){});
    }, 5000); /* Cek setiap 5 detik */
})();
<?php endif ?>
</script>
