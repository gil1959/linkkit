<?php defined('ALTUMCODE') || die() ?>

<?php if($data->view_mode === 'list'): ?>
<?php \Altum\Title::set('Pesanan Saya') ?>

<style>
*,*::before,*::after{box-sizing:border-box}
.orders-wrap{max-width:840px;margin:40px auto;padding:0 20px 80px}
.orders-hero{background:linear-gradient(135deg,#1e293b,#334155);border-radius:20px;padding:32px;color:#fff;margin-bottom:28px;display:flex;align-items:center;gap:20px}
.orders-hero-icon{width:56px;height:56px;border-radius:14px;background:rgba(255,255,255,.1);display:flex;align-items:center;justify-content:center;font-size:1.6rem;flex-shrink:0}
.orders-hero h1{font-size:1.3rem;font-weight:800;margin:0 0 6px}
.orders-hero p{font-size:.85rem;color:#94a3b8;margin:0}
.order-card{background:#fff;border-radius:16px;box-shadow:0 2px 12px rgba(0,0,0,.07);overflow:hidden;margin-bottom:14px;transition:.2s}
.order-card:hover{box-shadow:0 4px 20px rgba(0,0,0,.12);transform:translateY(-1px)}
.order-card-inner{padding:18px 22px;display:flex;align-items:center;gap:16px}
.order-item-img{width:52px;height:52px;border-radius:10px;object-fit:cover;flex-shrink:0}
.order-item-icon{width:52px;height:52px;border-radius:10px;background:linear-gradient(135deg,#ede9fe,#ddd6fe);display:flex;align-items:center;justify-content:center;font-size:1.2rem;color:#7c3aed;flex-shrink:0}
.order-info{flex:1;min-width:0}
.order-shop{font-size:.72rem;color:#94a3b8;margin-bottom:2px}
.order-name{font-weight:700;font-size:.92rem;color:#1e293b;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.order-meta{display:flex;align-items:center;gap:8px;margin-top:5px;flex-wrap:wrap}
.order-inv{font-size:.72rem;font-family:monospace;color:#6366f1;background:#eef2ff;padding:2px 8px;border-radius:6px}
.order-date{font-size:.73rem;color:#94a3b8}
.order-right{text-align:right;flex-shrink:0}
.order-total{font-weight:800;font-size:.95rem;color:#1e293b}
.badge{display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:20px;font-size:.72rem;font-weight:700}
.badge-paid{background:#d1fae5;color:#065f46}
.badge-pending{background:#fef3c7;color:#92400e}
.badge-shipped{background:#dbeafe;color:#1e40af}
.badge-delivered{background:#d1fae5;color:#065f46}
.badge-cancelled{background:#fee2e2;color:#991b1b}
.badge-default{background:#f1f5f9;color:#475569}
.btn-detail{display:inline-flex;align-items:center;gap:5px;background:#4f46e5;color:#fff;border-radius:8px;padding:6px 14px;font-size:.78rem;font-weight:600;text-decoration:none;margin-top:8px;transition:.2s}
.btn-detail:hover{background:#3730a3;color:#fff}
.empty-state{text-align:center;padding:60px 20px;background:#fff;border-radius:16px;box-shadow:0 2px 12px rgba(0,0,0,.07)}
.tracking-info{background:#eff6ff;border:1.5px solid #bfdbfe;border-radius:10px;padding:10px 14px;margin-top:8px;font-size:.78rem;color:#1e40af}
</style>

<div class="orders-wrap">
    <?= \Altum\Alerts::output_alerts() ?>

    <div class="orders-hero">
        <div class="orders-hero-icon"><i class="fas fa-shopping-bag"></i></div>
        <div>
            <h1>Pesanan Saya</h1>
            <p><?= count($data->orders) ?> pesanan ditemukan untuk akun ini</p>
        </div>
    </div>

    <?php if(empty($data->orders)): ?>
    <div class="empty-state">
        <div style="font-size:3rem;margin-bottom:16px;color:#94a3b8"><i class="fas fa-box-open"></i></div>
        <div style="font-weight:700;font-size:1.1rem;color:#1e293b;margin-bottom:8px">Belum ada pesanan</div>
        <div style="font-size:.85rem;color:#94a3b8">Pesananmu akan muncul di sini setelah kamu berbelanja di toko manapun yang menggunakan platform ini.</div>
    </div>
    <?php else: ?>
    <?php foreach($data->orders as $order): ?>
    <?php
        $badge_class = 'badge-default';
        $badge_label = ucfirst($order->status ?? '-');
        if($order->status === 'paid') {
            if(!empty($order->tracking_number)) {
                $badge_class = $order->shipping_status === 'delivered' ? 'badge-delivered' : 'badge-shipped';
                $badge_label = $order->shipping_status === 'delivered' ? 'Diterima' : 'Dikirim';
            } else {
                $badge_class = 'badge-paid';
                $badge_label = 'Lunas';
            }
        } elseif($order->status === 'pending') { $badge_class = 'badge-pending'; $badge_label = 'Menunggu Bayar'; }
        elseif($order->status === 'cancelled') { $badge_class = 'badge-cancelled'; $badge_label = 'Dibatalkan'; }
    ?>
    <div class="order-card">
        <div class="order-card-inner">
            <?php if(!empty($order->item_image)): ?>
                <img src="<?= \Altum\Uploads::get_full_url('shop_items') . $order->item_image ?>" class="order-item-img" alt="">
            <?php else: ?>
                <div class="order-item-icon"><i class="fas fa-box"></i></div>
            <?php endif; ?>

            <div class="order-info">
                <div class="order-shop"><?= htmlspecialchars($order->shop_name) ?></div>
                <div class="order-name"><?= htmlspecialchars($order->item_name) ?></div>
                <div class="order-meta">
                    <span class="order-inv"><?= htmlspecialchars($order->invoice_number) ?></span>
                    <span class="order-date"><?= date('d M Y', strtotime($order->datetime)) ?></span>
                    <span class="badge <?= $badge_class ?>"><?= $badge_label ?></span>
                </div>
                <?php if(!empty($order->tracking_number)): ?>
                <div class="tracking-info">
                    <i class="fas fa-truck fa-sm mr-1"></i>
                    Resi: <strong><?= htmlspecialchars($order->tracking_number) ?></strong>
                    · <?= strtoupper(htmlspecialchars($order->shipping_courier ?? '')) ?>
                    · <?= htmlspecialchars($order->shipping_service ?? '') ?>
                    &nbsp;<a href="https://www.cek-resi.id/?resi=<?= urlencode($order->tracking_number) ?>" target="_blank" style="color:#1d4ed8;font-weight:600">Lacak →</a>
                </div>
                <?php endif; ?>
            </div>

            <div class="order-right">
                <div class="order-total">Rp <?= number_format($order->grand_total, 0, ',', '.') ?></div>
                <?php if(!empty($order->shipping_cost) && $order->shipping_cost > 0): ?>
                <div style="font-size:.72rem;color:#94a3b8">Ongkir: Rp <?= number_format($order->shipping_cost, 0, ',', '.') ?></div>
                <?php endif; ?>
                <a href="<?= url('shop-orders/' . $order->invoice_number) ?>" class="btn-detail">
                    <i class="fas fa-eye fa-xs"></i> Detail
                </a>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php else: /* view_mode === detail */ ?>
<?php \Altum\Title::set('Detail Pesanan — ' . $data->order->invoice_number) ?>
<?php $order = $data->order; ?>

<style>
*,*::before,*::after{box-sizing:border-box}
.od-wrap{max-width:680px;margin:40px auto;padding:0 20px 80px}
.od-back{display:inline-flex;align-items:center;gap:6px;color:#6b7280;text-decoration:none;font-size:.83rem;margin-bottom:20px;padding:6px 12px;border-radius:8px;transition:.2s}
.od-back:hover{background:#f1f5f9;color:#4f46e5}
.od-card{background:#fff;border-radius:16px;box-shadow:0 2px 12px rgba(0,0,0,.07);overflow:hidden;margin-bottom:18px}
.od-card-head{padding:16px 22px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;gap:12px}
.od-card-icon{width:36px;height:36px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:.95rem}
.od-card-title{font-size:.92rem;font-weight:700;margin:0;flex:1}
.od-card-body{padding:22px}
.od-row{display:flex;justify-content:space-between;align-items:flex-start;font-size:.85rem;margin-bottom:12px;color:#374151}
.od-row:last-child{margin-bottom:0}
.od-label{color:#94a3b8;font-size:.8rem;margin-right:12px;flex-shrink:0}
.od-val{font-weight:600;text-align:right}
.od-total-row{display:flex;justify-content:space-between;font-weight:800;font-size:1rem;padding-top:14px;border-top:2px solid #f1f5f9;margin-top:8px}
.resi-box{background:linear-gradient(135deg,#f0fdf4,#dcfce7);border:2px solid #86efac;border-radius:14px;padding:20px;text-align:center}
.resi-number{font-size:1.4rem;font-weight:900;color:#059669;letter-spacing:.1em;font-family:monospace;margin:8px 0}
.btn-track{display:inline-flex;align-items:center;gap:8px;background:#059669;color:#fff;border-radius:10px;padding:10px 20px;text-decoration:none;font-weight:700;font-size:.88rem;transition:.2s;margin-top:10px}
.btn-track:hover{background:#047857;color:#fff}
.contact-buyer{display:flex;flex-direction:column;gap:10px}
.contact-item{display:flex;align-items:center;gap:12px;padding:12px;background:#f8fafc;border-radius:10px}
.contact-icon{width:36px;height:36px;border-radius:8px;background:#eef2ff;display:flex;align-items:center;justify-content:center;color:#4f46e5;flex-shrink:0}
.badge{padding:4px 10px;border-radius:20px;font-size:.72rem;font-weight:700}
.badge-paid{background:#d1fae5;color:#065f46}
.badge-pending{background:#fef3c7;color:#92400e}
.badge-shipped{background:#dbeafe;color:#1e40af}
</style>

<div class="od-wrap">
    <?= \Altum\Alerts::output_alerts() ?>

    <a href="<?= url('shop-orders') ?>" class="od-back">
        <i class="fas fa-arrow-left fa-sm"></i> Kembali ke Semua Pesanan
    </a>

    <!-- Status Hero -->
    <div style="background:<?= $order->status === 'paid' ? 'linear-gradient(135deg,#059669,#10b981)' : 'linear-gradient(135deg,#d97706,#f59e0b)' ?>;border-radius:20px;padding:28px;color:#fff;text-align:center;margin-bottom:20px">
        <div style="font-size:2rem;margin-bottom:8px"><i class="fas <?= $order->status === 'paid' ? 'fa-check-circle' : 'fa-clock' ?>"></i></div>
        <div style="font-size:1.2rem;font-weight:800;margin-bottom:6px">
            <?php if($order->status === 'paid' && !empty($order->tracking_number)): ?>
                <?= $order->shipping_status === 'delivered' ? 'Pesanan Diterima' : 'Pesanan Sedang Dikirim' ?>
            <?php elseif($order->status === 'paid'): ?>
                Pembayaran Berhasil
            <?php else: ?>
                Menunggu Pembayaran
            <?php endif; ?>
        </div>
        <div style="font-size:.78rem;color:rgba(255,255,255,.8);font-family:monospace"><?= htmlspecialchars($order->invoice_number) ?></div>
    </div>

    <!-- Resi / Tracking (jika sudah ada) -->
    <?php if(!empty($order->tracking_number)): ?>
    <div class="od-card">
        <div class="od-card-head">
            <div class="od-card-icon" style="background:#d1fae5;color:#059669"><i class="fas fa-truck"></i></div>
            <h2 class="od-card-title">Info Pengiriman</h2>
        </div>
        <div class="od-card-body">
            <div class="resi-box">
                <div style="font-size:.75rem;color:#059669;font-weight:700;text-transform:uppercase;letter-spacing:.05em">Nomor Resi</div>
                <div class="resi-number"><?= htmlspecialchars($order->tracking_number) ?></div>
                <div style="font-size:.82rem;color:#374151">
                    <?= strtoupper(htmlspecialchars($order->shipping_courier ?? '')) ?>
                    · <?= htmlspecialchars($order->shipping_service ?? '') ?>
                </div>
                <a href="https://www.cek-resi.id/?resi=<?= urlencode($order->tracking_number) ?>&courier=<?= urlencode($order->shipping_courier ?? '') ?>" target="_blank" class="btn-track">
                    <i class="fas fa-search fa-sm"></i> Lacak Paket
                </a>
            </div>
            <?php if(!empty($order->shipping_address)): ?>
            <div class="od-row" style="margin-top:16px">
                <span class="od-label"><i class="fas fa-map-marker-alt fa-xs mr-1"></i> Alamat Pengiriman</span>
                <span class="od-val"><?= nl2br(htmlspecialchars($order->shipping_address)) ?></span>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Detail Pesanan -->
    <div class="od-card">
        <div class="od-card-head">
            <div class="od-card-icon" style="background:#f1f5f9;color:#64748b"><i class="fas fa-receipt"></i></div>
            <h2 class="od-card-title">Detail Pesanan</h2>
        </div>
        <div class="od-card-body">
            <!-- Product -->
            <div style="display:flex;align-items:center;gap:14px;margin-bottom:18px;padding-bottom:16px;border-bottom:1px solid #f1f5f9">
                <?php if(!empty($order->item_image)): ?>
                    <img src="<?= \Altum\Uploads::get_full_url('shop_items') . $order->item_image ?>" style="width:54px;height:54px;border-radius:10px;object-fit:cover;flex-shrink:0" alt="">
                <?php else: ?>
                    <div style="width:54px;height:54px;border-radius:10px;background:linear-gradient(135deg,#ede9fe,#ddd6fe);display:flex;align-items:center;justify-content:center;font-size:1.3rem;flex-shrink:0;color:#7c3aed"><i class="fas fa-box"></i></div>
                <?php endif; ?>
                <div>
                    <div style="font-weight:700;font-size:.92rem"><?= htmlspecialchars($order->item_name) ?></div>
                    <div style="font-size:.75rem;color:#94a3b8"><?= htmlspecialchars($order->shop_name) ?></div>
                </div>
            </div>

            <div class="od-row"><span class="od-label">Invoice</span><span class="od-val" style="font-family:monospace;color:#4f46e5"><?= htmlspecialchars($order->invoice_number) ?></span></div>
            <div class="od-row"><span class="od-label">Tanggal Pesan</span><span class="od-val"><?= date('d M Y, H:i', strtotime($order->datetime)) ?></span></div>
            <div class="od-row"><span class="od-label">Status</span>
                <span class="badge <?= $order->status === 'paid' ? 'badge-paid' : 'badge-pending' ?>">
                    <?= $order->status === 'paid' ? 'Lunas' : 'Menunggu Bayar' ?>
                </span>
            </div>
            <div class="od-row"><span class="od-label">Harga Produk</span><span class="od-val">Rp <?= number_format($order->total_amount, 0, ',', '.') ?></span></div>
            <?php if(!empty($order->discount_amount) && $order->discount_amount > 0): ?>
            <div class="od-row"><span class="od-label">Diskon Voucher</span><span class="od-val" style="color:#059669">-Rp <?= number_format($order->discount_amount, 0, ',', '.') ?></span></div>
            <?php endif; ?>
            <?php if(!empty($order->shipping_cost) && $order->shipping_cost > 0): ?>
            <div class="od-row"><span class="od-label">Ongkos Kirim</span><span class="od-val">Rp <?= number_format($order->shipping_cost, 0, ',', '.') ?></span></div>
            <?php endif; ?>
            <div class="od-total-row"><span>Total Bayar</span><span style="color:#4f46e5">Rp <?= number_format($order->grand_total, 0, ',', '.') ?></span></div>
        </div>
    </div>

    <!-- Info Pembeli / Kontak -->
    <div class="od-card">
        <div class="od-card-head">
            <div class="od-card-icon" style="background:#ede9fe;color:#7c3aed"><i class="fas fa-user"></i></div>
            <h2 class="od-card-title">Info Pembeli</h2>
        </div>
        <div class="od-card-body">
            <div class="contact-buyer">
                <div class="contact-item">
                    <div class="contact-icon"><i class="fas fa-user fa-sm"></i></div>
                    <div>
                        <div style="font-size:.72rem;color:#94a3b8">Nama</div>
                        <div style="font-weight:600;font-size:.88rem"><?= htmlspecialchars($order->full_name) ?></div>
                    </div>
                </div>
                <div class="contact-item">
                    <div class="contact-icon"><i class="fas fa-envelope fa-sm"></i></div>
                    <div>
                        <div style="font-size:.72rem;color:#94a3b8">Email</div>
                        <div style="font-weight:600;font-size:.88rem"><?= htmlspecialchars($order->email) ?></div>
                    </div>
                </div>
                <?php if(!empty($order->phone)): ?>
                <div class="contact-item">
                    <div class="contact-icon"><i class="fab fa-whatsapp fa-sm"></i></div>
                    <div>
                        <div style="font-size:.72rem;color:#94a3b8">WhatsApp</div>
                        <a href="https://wa.me/<?= preg_replace('/\D/', '', $order->phone) ?>" target="_blank" style="font-weight:600;font-size:.88rem;color:#059669;text-decoration:none"><?= htmlspecialchars($order->phone) ?></a>
                    </div>
                </div>
                <?php endif; ?>
                <?php if(!empty($order->shipping_address)): ?>
                <div class="contact-item">
                    <div class="contact-icon"><i class="fas fa-map-marker-alt fa-sm"></i></div>
                    <div>
                        <div style="font-size:.72rem;color:#94a3b8">Alamat Pengiriman</div>
                        <div style="font-size:.85rem;color:#1e293b"><?= nl2br(htmlspecialchars($order->shipping_address)) ?></div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Kembali ke toko -->
    <?php if(!empty($order->shop_url)): ?>
    <a href="<?= SITE_URL . 'store/' . htmlspecialchars($order->shop_url) ?>" style="display:flex;align-items:center;justify-content:center;gap:8px;background:#f1f5f9;color:#475569;border-radius:12px;padding:12px;text-decoration:none;font-weight:600;font-size:.88rem;transition:.2s" onmouseover="this.style.background='#e2e8f0'" onmouseout="this.style.background='#f1f5f9'">
        <i class="fas fa-store"></i> Belanja Lagi di <?= htmlspecialchars($order->shop_name) ?>
    </a>
    <?php endif; ?>
</div>

<?php endif; ?>
