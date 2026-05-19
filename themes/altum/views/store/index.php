<?php defined('ALTUMCODE') || die() ?>
<?php \Altum\Title::set(htmlspecialchars($data->shop->name) . ' — Store') ?>

<style>
*,*::before,*::after{box-sizing:border-box}
<?php
function format_sold($n) {
    if($n >= 1000000) return floor($n/1000000).'jt+';
    if($n >= 1000) return floor($n/1000).'rb+';
    return $n;
}
?>
body{background:#f8fafc;font-family:'Inter',sans-serif;color:#111827;margin:0}
/* ── TOPBAR ── */
.s-top{background:#fff;border-bottom:1px solid #e5e7eb;display:flex;align-items:center;padding:10px 20px;gap:14px;position:sticky;top:0;z-index:500;box-shadow:0 1px 6px rgba(0,0,0,.07)}
.s-top-logo{display:flex;align-items:center;gap:8px;text-decoration:none;color:#111827;font-weight:700;font-size:1rem;flex-shrink:0;white-space:nowrap}
.s-top-logo img{width:32px;height:32px;border-radius:8px;object-fit:cover}
.s-top-logo-icon{width:32px;height:32px;border-radius:8px;background:linear-gradient(135deg,#4f46e5,#818cf8);display:flex;align-items:center;justify-content:center;color:#fff;font-size:.9rem}
/* search: flex row icon + input */
.s-top-search{flex:1;min-width:0;max-width:440px}
.s-search-inner{display:flex;align-items:center;gap:8px;background:#f3f4f6;border:1.5px solid #e5e7eb;border-radius:50px;padding:7px 16px;transition:.2s}
.s-search-inner:focus-within{border-color:#6366f1;background:#fff;box-shadow:0 0 0 3px rgba(99,102,241,.08)}
.s-search-inner i{color:#9ca3af;font-size:.82rem;flex-shrink:0}
.s-search-inner input{border:none;background:transparent;outline:none;flex:1;min-width:0;font-size:.85rem;color:#111827}
.s-search-inner input::placeholder{color:#9ca3af}
.cart-btn{position:relative;display:flex;align-items:center;gap:6px;background:#eef2ff;color:#4f46e5;border:none;border-radius:10px;padding:8px 16px;font-size:.85rem;font-weight:600;cursor:pointer;transition:.2s;margin-left:auto;flex-shrink:0;white-space:nowrap}
.cart-btn:hover{background:#dde4ff}
.cart-badge{position:absolute;top:-6px;right:-6px;background:#ef4444;color:#fff;border-radius:50%;width:18px;height:18px;font-size:.65rem;display:flex;align-items:center;justify-content:center;font-weight:700;display:none}
/* ── COVER ── */
.s-cover{width:100%;height:220px;background:linear-gradient(135deg,#1e1b4b,#4f46e5);position:relative;overflow:hidden;margin-top:0}
.s-cover img{width:100%;height:100%;object-fit:cover;display:block}
.s-cover-dim{position:absolute;inset:0;background:linear-gradient(to bottom,rgba(0,0,0,.05),rgba(0,0,0,.4))}
@media(max-width:576px){.s-cover{height:130px}}
/* ── PROFILE ── */
.s-wrap{max-width:1080px;margin:0 auto;padding:0 20px}
/* Logo masih overlap cover, tapi teks mulai di bawah cover */
.s-profile{display:flex;align-items:flex-start;gap:16px;margin-top:0;margin-bottom:24px;position:relative;z-index:5;flex-wrap:wrap;padding-top:12px}
.s-avatar{width:92px;height:92px;border-radius:14px;border:4px solid #fff;box-shadow:0 4px 16px rgba(0,0,0,.12);object-fit:cover;background:#fff;flex-shrink:0;margin-top:-56px}
.s-avatar-icon{width:92px;height:92px;border-radius:14px;border:4px solid #fff;box-shadow:0 4px 16px rgba(0,0,0,.12);background:linear-gradient(135deg,#ede9fe,#c7d2fe);display:flex;align-items:center;justify-content:center;font-size:2rem;color:#4f46e5;flex-shrink:0;margin-top:-56px}
.s-info{flex:1;min-width:180px;padding-top:6px}
.s-name{font-size:1.4rem;font-weight:800;margin:0 0 4px}
.s-url{font-size:.78rem;color:#6366f1;text-decoration:none}
.s-desc{font-size:.85rem;color:#6b7280;margin:4px 0 0}
/* ── TABS BAR ── */
.s-bar{display:flex;align-items:center;gap:12px;flex-wrap:wrap;margin-bottom:24px;padding-bottom:4px}
.s-tabs{display:flex;gap:4px;background:#f3f4f6;border-radius:10px;padding:4px;flex-wrap:nowrap}
.s-tab{padding:7px 16px;font-size:.83rem;font-weight:600;color:#6b7280;border:none;background:transparent;cursor:pointer;border-radius:8px;white-space:nowrap;transition:background .18s,color .18s}
.s-tab.active{background:#fff;color:#4f46e5;box-shadow:0 1px 4px rgba(0,0,0,.08)}
/* ── PRODUCT GRID ── */
.s-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:18px;margin-bottom:40px}
.s-card{background:#fff;border-radius:14px;overflow:hidden;box-shadow:0 1px 4px rgba(0,0,0,.06);transition:transform .22s,box-shadow .22s;cursor:pointer;display:flex;flex-direction:column}
.s-card:hover{transform:translateY(-4px);box-shadow:0 8px 24px rgba(79,70,229,.1)}
.s-card-img{position:relative;padding-top:100%;background:#f3f4f6;overflow:hidden}
.s-card-img img,.s-card-img .s-no-img{position:absolute;inset:0;width:100%;height:100%;object-fit:cover}
.s-no-img{display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg,#ede9fe,#ddd6fe);font-size:2.2rem;color:#7c3aed}
.s-type-badge{position:absolute;top:8px;right:8px;background:rgba(79,70,229,.85);color:#fff;font-size:.65rem;font-weight:700;padding:3px 8px;border-radius:20px;z-index:2}
.s-quick-cart{position:absolute;bottom:8px;right:8px;width:34px;height:34px;background:#4f46e5;color:#fff;border:none;border-radius:50%;display:flex;align-items:center;justify-content:center;cursor:pointer;box-shadow:0 2px 8px rgba(79,70,229,.4);transition:.2s;font-size:.85rem}
.s-quick-cart:hover{background:#3730a3;transform:scale(1.1)}
.s-quick-share{position:absolute;bottom:8px;right:50px;width:34px;height:34px;background:#fff;color:#4f46e5;border:none;border-radius:50%;display:flex;align-items:center;justify-content:center;cursor:pointer;box-shadow:0 2px 8px rgba(0,0,0,.15);transition:.2s;font-size:.85rem}
.s-quick-share:hover{background:#f3f4f6;transform:scale(1.1)}
.s-card-body{padding:12px;flex:1;display:flex;flex-direction:column}
.s-card-name{font-weight:700;font-size:.88rem;color:#111827;margin-bottom:4px;line-height:1.3;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.s-card-price{font-weight:800;color:#4f46e5;font-size:.9rem}
.s-card-stock{font-size:.7rem;color:#9ca3af;margin-top:2px}
/* ── EMPTY ── */
.s-empty{text-align:center;padding:60px 20px;grid-column:1/-1}
.s-empty i{font-size:3rem;color:#d1d5db;display:block;margin-bottom:12px}
/* ── MODAL OVERLAY ── */
.s-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:800;backdrop-filter:blur(2px)}
.s-overlay.show{display:flex;align-items:center;justify-content:center}
/* ── PRODUCT DETAIL MODAL ── */
.s-modal{background:#fff;border-radius:18px;width:90%;max-width:560px;max-height:90vh;overflow-y:auto;padding:28px;position:relative;animation:modalIn .22s ease}
@keyframes modalIn{from{transform:scale(.94) translateY(12px);opacity:0}to{transform:none;opacity:1}}
.s-modal-close{position:absolute;top:14px;right:16px;background:none;border:none;font-size:1.3rem;cursor:pointer;color:#9ca3af}
.s-modal-close:hover{color:#111827}
.s-modal-img{width:100%;height:220px;object-fit:cover;border-radius:12px;margin-bottom:18px;background:#f3f4f6}
.s-modal-no-img{width:100%;height:220px;border-radius:12px;margin-bottom:18px;background:linear-gradient(135deg,#ede9fe,#ddd6fe);display:flex;align-items:center;justify-content:center;font-size:3.5rem;color:#7c3aed}
.s-modal-slider { position: relative; width: 100%; height: 220px; border-radius: 12px; margin-bottom: 18px; overflow: hidden; background: #f3f4f6; }
.s-modal-slider .slide-img { width: 100%; height: 100%; object-fit: cover; }
.s-modal-nav { position: absolute; top: 50%; transform: translateY(-50%); background: rgba(0,0,0,0.5); color: #fff; border: none; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: 0.2s; z-index: 10; }
.s-modal-nav:hover { background: rgba(0,0,0,0.8); }
.s-modal-nav.prev { left: 8px; }
.s-modal-nav.next { right: 8px; }
.s-modal-dots { position: absolute; bottom: 8px; left: 0; right: 0; display: flex; justify-content: center; gap: 6px; z-index: 10; }
.s-modal-dot { width: 8px; height: 8px; border-radius: 50%; background: rgba(255,255,255,0.5); cursor: pointer; transition: 0.2s; box-shadow: 0 1px 3px rgba(0,0,0,0.3); }
.s-modal-dot.active { background: #fff; transform: scale(1.2); }
.s-modal-name{font-size:1.2rem;font-weight:800;margin:0 0 6px}
.s-modal-price{font-size:1.4rem;font-weight:800;color:#4f46e5;margin-bottom:8px}
.s-modal-stock{font-size:.8rem;color:#6b7280;margin-bottom:12px}
.s-modal-desc{font-size:.88rem;color:#374151;line-height:1.6;margin-bottom:20px}
/* Rich text content styles for product description */
.s-modal-desc p{margin:0 0 .6em}
.s-modal-desc p:last-child{margin-bottom:0}
.s-modal-desc h1,.s-modal-desc h2,.s-modal-desc h3{font-weight:700;margin:.8em 0 .4em;line-height:1.3}
.s-modal-desc h1{font-size:1.15rem}.s-modal-desc h2{font-size:1.05rem}.s-modal-desc h3{font-size:.95rem}
.s-modal-desc strong{font-weight:700}.s-modal-desc em{font-style:italic}
.s-modal-desc u{text-decoration:underline}.s-modal-desc s{text-decoration:line-through}
.s-modal-desc ul,.s-modal-desc ol{padding-left:1.4em;margin:.4em 0}
.s-modal-desc ul{list-style:disc}.s-modal-desc ol{list-style:decimal}
.s-modal-desc li{margin-bottom:.2em}
.s-modal-desc blockquote{border-left:3px solid #c7d2fe;padding:.4em .8em;margin:.6em 0;color:#6b7280;font-style:italic}
.s-modal-desc a{color:#4f46e5;text-decoration:underline;word-break:break-word}
.s-modal-desc .ql-align-center{text-align:center}
.s-modal-desc .ql-align-right{text-align:right}
.s-modal-desc .ql-align-justify{text-align:justify}
.s-modal-actions{display:flex;gap:10px}
.btn-add-cart{flex:1;background:#eef2ff;color:#4f46e5;border:none;border-radius:10px;padding:11px;font-weight:700;font-size:.9rem;cursor:pointer;transition:.2s}
.btn-add-cart:hover{background:#dde4ff}
.btn-buy-now{flex:1;background:#4f46e5;color:#fff;border:none;border-radius:10px;padding:11px;font-weight:700;font-size:.9rem;cursor:pointer;transition:.2s}
.btn-buy-now:hover{background:#3730a3}
.btn-share-modal{background:#f3f4f6;color:#374151;border:none;border-radius:10px;padding:11px 16px;font-weight:700;font-size:.9rem;cursor:pointer;transition:.2s;display:flex;align-items:center;justify-content:center}
.btn-share-modal:hover{background:#e5e7eb}
/* ── CART POPUP ── */
.cart-panel{position:fixed;right:-420px;top:0;bottom:0;width:400px;max-width:95vw;background:#fff;box-shadow:-4px 0 24px rgba(0,0,0,.12);z-index:900;transition:right .3s cubic-bezier(.4,0,.2,1);display:flex;flex-direction:column}
.cart-panel.open{right:0}
.cart-panel-head{padding:16px 20px;border-bottom:1px solid #f3f4f6;display:flex;align-items:center;gap:10px}
.cart-panel-head h3{margin:0;font-size:1rem;font-weight:700;flex:1}
.cart-close{background:none;border:none;font-size:1.2rem;cursor:pointer;color:#9ca3af}
.cart-close:hover{color:#111}
.cart-select-all-row{padding:8px 16px;border-bottom:1px solid #f3f4f6;display:flex;align-items:center;gap:8px;font-size:.8rem;color:#374151;background:#f9fafb}
.cart-items{flex:1;overflow-y:auto;padding:8px 0}
.cart-item{display:flex;align-items:center;gap:10px;padding:10px 16px;border-bottom:1px solid #f3f4f6;transition:background .15s}
.cart-item:last-child{border-bottom:none}
.cart-item:hover{background:#f9fafb}
.cart-cb{width:16px;height:16px;accent-color:#4f46e5;cursor:pointer;flex-shrink:0}
.cart-item-img{width:48px;height:48px;border-radius:8px;object-fit:cover;background:#f3f4f6;flex-shrink:0}
.cart-item-info{flex:1;min-width:0}
.cart-item-name{font-weight:600;font-size:.82rem;margin-bottom:2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.cart-item-price{font-size:.78rem;color:#4f46e5;font-weight:700}
.cart-qty-ctrl{display:flex;align-items:center;gap:4px;flex-shrink:0}
.cart-qty-btn{width:24px;height:24px;border:1.5px solid #e2e8f0;border-radius:6px;background:#fff;cursor:pointer;font-size:.85rem;display:flex;align-items:center;justify-content:center;transition:.15s;color:#374151}
.cart-qty-btn:hover{background:#eef2ff;border-color:#a5b4fc;color:#4f46e5}
.cart-qty-val{font-size:.82rem;font-weight:700;min-width:28px;text-align:center;color:#1e293b}
.cart-item-remove{background:none;border:none;color:#ef4444;cursor:pointer;font-size:.9rem;padding:4px 6px;border-radius:6px;line-height:1;transition:background .15s;flex-shrink:0}
.cart-item-remove:hover{background:#fef2f2}
.cart-empty-msg{text-align:center;padding:40px 16px;color:#9ca3af}
.cart-empty-msg i{font-size:2rem;display:block;margin-bottom:8px}
.cart-footer{padding:14px 16px;border-top:1px solid #f3f4f6;display:flex;flex-direction:column;gap:8px}
.cart-total{display:flex;justify-content:space-between;font-weight:700;font-size:.88rem;margin-bottom:2px}
.cart-total span:last-child{color:#4f46e5}
.cart-selected-info{font-size:.75rem;color:#6b7280;text-align:center}
.btn-checkout{background:linear-gradient(135deg,#4f46e5,#6366f1);color:#fff;border:none;border-radius:10px;padding:12px;font-weight:700;font-size:.88rem;cursor:pointer;width:100%;transition:.2s}
.btn-checkout:hover{background:linear-gradient(135deg,#3730a3,#4f46e5)}
.cart-footer-row{display:flex;gap:8px}
.btn-clear{flex:1;background:#fef2f2;color:#ef4444;border:none;border-radius:10px;padding:9px;font-weight:600;font-size:.78rem;cursor:pointer;transition:.2s}
.btn-clear:hover{background:#fee2e2}
.btn-continue{flex:1;background:#f3f4f6;color:#374151;border:none;border-radius:10px;padding:9px;font-weight:600;font-size:.78rem;cursor:pointer;transition:.2s}
.btn-continue:hover{background:#e5e7eb}
/* ── Popup modal ── */
.co-popup{display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:1100;align-items:center;justify-content:center}
.co-popup.show{display:flex}
.co-popup-box{background:#fff;border-radius:16px;padding:28px 24px;max-width:320px;width:90%;text-align:center;box-shadow:0 8px 32px rgba(0,0,0,.15);animation:popIn .2s ease}
@keyframes popIn{from{transform:scale(.9);opacity:0}to{transform:scale(1);opacity:1}}
.co-popup-icon{font-size:2.5rem;margin-bottom:12px}
.co-popup-title{font-weight:800;font-size:1.05rem;color:#1e293b;margin-bottom:6px}
.co-popup-desc{font-size:.85rem;color:#6b7280;margin-bottom:20px}
.co-popup-btn{background:#4f46e5;color:#fff;border:none;border-radius:10px;padding:10px 24px;font-weight:700;font-size:.88rem;cursor:pointer}
/* ── FOOTER ── */
.s-footer{text-align:center;padding:20px;font-size:.75rem;color:#d1d5db;border-top:1px solid #f3f4f6;margin-top:20px}
.s-footer a{color:#9ca3af;text-decoration:none}
</style>

<!-- TOPBAR -->
<header class="s-top">
    <a class="s-top-logo" href="<?= SITE_URL . 'store/' . htmlspecialchars($data->shop->url) ?>">
        <?php if($data->shop->logo_image): ?>
            <img src="<?= \Altum\Uploads::get_full_url('shop_logos') . $data->shop->logo_image ?>" alt="logo">
        <?php else: ?>
            <div class="s-top-logo-icon"><i class="fas fa-shopping-bag"></i></div>
        <?php endif ?>
        <?= htmlspecialchars($data->shop->name) ?>
    </a>

    <div class="s-top-search">
        <div class="s-search-inner">
            <i class="fas fa-search"></i>
            <input type="text" id="sSearch" placeholder="Cari produk…" oninput="filterStore()">
        </div>
    </div>

    <div style="display:flex;gap:10px;margin-left:auto">
        <button class="cart-btn" onclick="toggleOrders()" style="background:#eef2ff;color:#4f46e5;margin-left:0">
            <i class="fas fa-receipt"></i>
            <span class="d-none d-sm-inline">Pesanan</span>
        </button>
        <button class="cart-btn" onclick="toggleCart()" style="margin-left:0">
            <i class="fas fa-shopping-cart"></i>
            <span id="cartLabel">Keranjang</span>
            <span class="cart-badge" id="cartBadge">0</span>
        </button>
    </div>
</header>


<!-- COVER -->
<div class="s-cover">
    <?php if($data->shop->cover_image): ?>
        <img src="<?= \Altum\Uploads::get_full_url('shop_covers') . $data->shop->cover_image ?>" alt="cover">
    <?php endif ?>
    <div class="s-cover-dim"></div>
</div>

<!-- PROFILE -->
<div class="s-wrap">
    <div class="s-profile">
        <?php if($data->shop->logo_image): ?>
            <img src="<?= \Altum\Uploads::get_full_url('shop_logos') . $data->shop->logo_image ?>" class="s-avatar" alt="logo">
        <?php else: ?>
            <div class="s-avatar-icon"><i class="fas fa-shopping-bag"></i></div>
        <?php endif ?>
        <div class="s-info">
            <h1 class="s-name">
                <?= htmlspecialchars($data->shop->name) ?>
                <?php if($data->shop_verified): ?>
                    <i class="fas fa-check-circle text-primary fa-xs ml-1" title="Toko Terverifikasi"></i>
                <?php endif; ?>
            </h1>
            <?php if($data->shop->description): ?>
                <p class="s-desc"><?= nl2br(htmlspecialchars($data->shop->description)) ?></p>
            <?php endif ?>
        </div>
    </div>

    <!-- TABS -->
    <div class="s-bar">
        <div class="s-tabs">
            <button class="s-tab active" data-cat="all">Semua</button>
            <?php if(!empty($data->listings)): ?>
                <?php foreach($data->listings as $l): ?>
                    <button class="s-tab" data-cat="list-<?= $l->id ?>"><?= htmlspecialchars($l->name) ?></button>
                <?php endforeach ?>
            <?php else: ?>
                <?php
                $cats = array_unique(array_map(fn($i) => $i->type, $data->items));
                foreach($cats as $c):
                ?>
                    <button class="s-tab" data-cat="<?= $c ?>"><?= ucwords(str_replace('_',' ',$c)) ?></button>
                <?php endforeach ?>
            <?php endif ?>
        </div>
    </div>

    <!-- PRODUCT GRID -->
    <div class="s-grid" id="sGrid">
        <?php if(count($data->items)): ?>
            <?php foreach($data->items as $item): ?>
                <div class="s-card"
                     data-name="<?= strtolower(htmlspecialchars($item->name)) ?>"
                     data-cat="<?= !empty($item->listing_id) ? 'list-'.$item->listing_id : $item->type ?>"
                     onclick="openDetail(<?= $item->id ?>)">
                    <div class="s-card-img">
                        <?php if(!empty($item->is_flash_sale)): ?>
                            <div style="position:absolute;top:8px;left:8px;background:#ef4444;color:#fff;font-size:.65rem;padding:3px 8px;border-radius:12px;font-weight:700;z-index:2;box-shadow:0 2px 4px rgba(0,0,0,0.1)"><i class="fas fa-bolt" style="margin-right:4px"></i>Flash Sale</div>
                        <?php endif ?>
                        <?php if(!empty($item->has_discount)): ?>
                            <div style="position:absolute;top:<?= !empty($item->is_flash_sale) ? '34px' : '8px' ?>;left:8px;background:#f59e0b;color:#fff;font-size:.65rem;padding:3px 8px;border-radius:12px;font-weight:700;z-index:2;box-shadow:0 2px 4px rgba(0,0,0,0.1)"><i class="fas fa-tag" style="margin-right:4px"></i>Diskon</div>
                        <?php endif ?>
                        <?php if($item->image): ?>
                            <img src="<?= \Altum\Uploads::get_full_url('shop_items') . $item->image ?>" alt="">
                        <?php else: ?>
                            <div class="s-no-img"><i class="fas fa-box"></i></div>
                        <?php endif ?>
                        <span class="s-type-badge"><?= $item->type === 'physical' ? 'Produk Fisik' : ucwords(str_replace('_',' ',$item->type)) ?></span>
                        <button class="s-quick-share" onclick="event.stopPropagation(); shareProduct(<?= $item->id ?>)" title="Bagikan Produk">
                            <i class="fas fa-share-alt"></i>
                        </button>
                        <button class="s-quick-cart" onclick="event.stopPropagation(); <?= !empty($item->has_variants) || !empty($item->is_flexible_amount) ? 'openDetail('.$item->id.')' : 'quickAddCart('.$item->id.')' ?>" title="<?= !empty($item->has_variants) ? 'Pilih Varian' : 'Tambah ke keranjang' ?>">
                            <i class="<?= !empty($item->has_variants) || !empty($item->is_flexible_amount) ? 'fas fa-list' : 'fas fa-cart-plus' ?>"></i>
                        </button>
                    </div>
                    <div class="s-card-body">
                        <div class="s-card-name" title="<?= htmlspecialchars($item->name) ?>"><?= htmlspecialchars($item->name) ?></div>
                        <div class="s-card-price">
                            <?php if(!empty($item->is_flexible_amount)): ?>
                                <span style="font-size:.7rem;color:#6b7280;font-weight:normal">Mulai dari</span> 
                            <?php endif ?>
                            <?php if(!empty($item->has_discount) && !empty($item->discount_price)): ?>
                                <div style="display:flex;flex-direction:column;gap:2px;">
                                    <span style="font-size:0.75rem; color:#9ca3af; text-decoration:line-through;">Rp <?= number_format($item->price,0,',','.') ?></span>
                                    <span style="color:#ef4444; font-weight:700;">Rp <?= number_format($item->discount_price,0,',','.') ?></span>
                                </div>
                            <?php else: ?>
                                Rp <?= number_format($item->price,0,',','.') ?>
                            <?php endif ?>
                        </div>
                        <div style="font-size: .75rem; color: #6b7280; display: flex; align-items: center; gap: 4px; margin-top: 4px;">
                            <i class="fas fa-star" style="color: #f59e0b;"></i> 
                            <span style="font-weight: 600; color: #374151;"><?= number_format($item->avg_rating ?? 0, 1) ?></span> 
                            <span>&middot;</span> 
                            <span><?= format_sold($item->total_sold ?? 0) ?> terjual</span>
                        </div>
                        <?php if($data->shop_verified): ?>
                        <div style="font-size: .75rem; color: #6b7280; display: flex; align-items: center; gap: 4px; margin-top: 4px;">
                            <i class="fas fa-shield-alt text-success"></i> <?= htmlspecialchars($data->shop->name) ?>
                        </div>
                        <?php endif; ?>
                        <div class="s-card-stock">
                            <?php if($item->stock === null): ?>
                                <i class="fas fa-infinity fa-xs"></i> Unlimited
                            <?php elseif($item->stock > 0): ?>
                                <i class="fas fa-check-circle fa-xs text-success"></i> <?= $item->stock ?> tersedia
                            <?php else: ?>
                                <i class="fas fa-times-circle fa-xs text-danger"></i> Habis
                            <?php endif ?>
                        </div>
                    </div>
                </div>
            <?php endforeach ?>
        <?php else: ?>
            <div class="s-empty">
                <i class="fas fa-store-slash"></i>
                <p>Belum ada produk tersedia</p>
            </div>
        <?php endif ?>
    </div>

    <div class="s-footer">Powered by <a href="<?= url('') ?>"><?= htmlspecialchars(settings()->main->title) ?></a></div>
</div>

<!-- PRODUCT DETAIL MODAL -->
<div class="s-overlay" id="detailOverlay" onclick="if(event.target===this)closeDetail()">
    <div class="s-modal" id="detailModal">
        <button class="s-modal-close" onclick="closeDetail()"><i class="fas fa-times"></i></button>
        <div id="modalContent"></div>
    </div>
</div>

<!-- CART PANEL -->
<div class="cart-panel" id="cartPanel">
    <div class="cart-panel-head">
        <h3><i class="fas fa-shopping-cart mr-2" style="color:#4f46e5"></i>Keranjang Belanja</h3>
        <button class="cart-close" onclick="toggleCart()"><i class="fas fa-times"></i></button>
    </div>
    <!-- Select All row -->
    <div class="cart-select-all-row" id="cartSelectAllRow" style="display:none">
        <input type="checkbox" id="cbSelectAll" class="cart-cb" onchange="toggleSelectAll(this.checked)">
        <label for="cbSelectAll" style="cursor:pointer;margin:0">Pilih Semua</label>
        <span id="cartSelectedCount" style="margin-left:auto;color:#4f46e5;font-weight:600"></span>
    </div>
    <div class="cart-items" id="cartItems"></div>
    <div class="cart-footer" id="cartFooter" style="display:none">
        <div class="cart-total">
            <span>Total (dipilih)</span>
            <span id="cartTotal">Rp 0</span>
        </div>
        <div class="cart-selected-info" id="cartSelectedInfo"></div>
        <button class="btn-checkout" onclick="doCheckout()">
            <i class="fas fa-bolt mr-1"></i>Checkout Sekarang
        </button>
        <div class="cart-footer-row">
            <button class="btn-clear" onclick="clearCart()"><i class="fas fa-trash mr-1"></i>Hapus Semua</button>
            <button class="btn-continue" onclick="toggleCart()"><i class="fas fa-arrow-left mr-1"></i>Lanjut Belanja</button>
        </div>
    </div>
</div>

<!-- POPUP: tidak ada produk dipilih -->
<div class="co-popup" id="popupNoSelect" onclick="if(event.target===this)this.classList.remove('show')">
    <div class="co-popup-box">
        <div class="co-popup-icon">🛒</div>
        <div class="co-popup-title">Belum ada produk dipilih</div>
        <div class="co-popup-desc">Centang produk yang ingin kamu beli terlebih dahulu.</div>
        <button class="co-popup-btn" onclick="document.getElementById('popupNoSelect').classList.remove('show')">Oke, Mengerti</button>
    </div>
</div>

<!-- ORDERS PANEL -->
<div class="cart-panel" id="ordersPanel">
    <div class="cart-panel-head">
        <h3><i class="fas fa-receipt mr-2" style="color:#4f46e5"></i>Cek Pesanan</h3>
        <button class="cart-close" onclick="toggleOrders()"><i class="fas fa-times"></i></button>
    </div>
    <div style="padding:20px;flex:1;overflow-y:auto">
        <div style="background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:16px;margin-bottom:20px;box-shadow:0 1px 6px rgba(0,0,0,.03)">
            <p style="font-size:.85rem;color:#64748b;margin:0 0 12px">Masukkan Nomor Invoice dan Email yang digunakan saat checkout untuk melihat status pesanan kamu.</p>
            <div style="margin-bottom:12px">
                <input type="text" id="chk_invoice" placeholder="Contoh: INV-..." style="width:100%;background:#f9fafb;color:#1e293b;border:1px solid #cbd5e1;border-radius:8px;padding:10px;font-size:.85rem;outline:none;transition:.2s" onfocus="this.style.borderColor='#6366f1';this.style.background='#fff'" onblur="this.style.borderColor='#cbd5e1';this.style.background='#f9fafb'">
            </div>
            <div style="margin-bottom:12px">
                <input type="email" id="chk_email" placeholder="Alamat Email" style="width:100%;background:#f9fafb;color:#1e293b;border:1px solid #cbd5e1;border-radius:8px;padding:10px;font-size:.85rem;outline:none;transition:.2s" onfocus="this.style.borderColor='#6366f1';this.style.background='#fff'" onblur="this.style.borderColor='#cbd5e1';this.style.background='#f9fafb'">
            </div>
            <button onclick="checkOrder()" id="btn_check_order" style="width:100%;background:#4f46e5;color:#fff;border:none;border-radius:8px;padding:10px;font-weight:600;font-size:.85rem;cursor:pointer;transition:.2s"><i class="fas fa-search mr-1"></i> Cek Pesanan</button>
            <div id="chk_msg" style="margin-top:10px;font-size:.8rem;text-align:center"></div>
        </div>

        <div id="chk_result" style="display:none;background:#fff;border:1px solid #e2e8f0;border-radius:12px;overflow:hidden">
            <!-- Render result here -->
        </div>
    </div>
</div>

<!-- EMBED PRODUCT DATA -->
<script>
var STORE_URL = '<?= SITE_URL ?>store-checkout/';
var PRODUCTS = <?= json_encode(array_map(function($i){
    return [
        'id'                  => $i->id,
        'name'                => $i->name,
        'price'               => (float)$i->price,
        'stock'               => $i->stock,
        'type'                => $i->type,
        'image'               => $i->image ? \Altum\Uploads::get_full_url('shop_items') . $i->image : null,
        'image2'              => isset($i->image2) && $i->image2 ? \Altum\Uploads::get_full_url('shop_items') . $i->image2 : null,
        'image3'              => isset($i->image3) && $i->image3 ? \Altum\Uploads::get_full_url('shop_items') . $i->image3 : null,
        'image4'              => isset($i->image4) && $i->image4 ? \Altum\Uploads::get_full_url('shop_items') . $i->image4 : null,
        'description'         => $i->description ?? '',
        'is_flexible_amount'  => !empty($i->is_flexible_amount),
        'has_variants'        => !empty($i->has_variants),
        'has_discount'        => !empty($i->has_discount),
        'discount_price'      => !empty($i->discount_price) ? (float)$i->discount_price : null,
        'is_flash_sale'       => !empty($i->is_flash_sale),
        'listing_id'          => $i->listing_id ?? null,
        'qty_per_transaction' => $i->qty_per_transaction ?? 0,
    ];
}, $data->items)) ?>;

var CART_KEY = 'lk_cart_<?= preg_replace('/[^a-z0-9]/i','_', $data->shop->url) ?>';
var cart = [];

/* ── localStorage helpers ── */
function saveCart(){
    try{ localStorage.setItem(CART_KEY, JSON.stringify(cart)); }catch(e){}
}
function loadCart(){
    try{
        var s = localStorage.getItem(CART_KEY);
        if(s){ cart = JSON.parse(s); }
    }catch(e){ cart = []; }
}

/* ── utils ── */
function fmtRp(n){ return 'Rp ' + Number(n).toLocaleString('id-ID'); }
function findProduct(id){ return PRODUCTS.find(function(p){ return p.id == id; }); }
function escHtml(s){ var d=document.createElement('div'); d.appendChild(document.createTextNode(s)); return d.innerHTML; }

/* ── tab filter ── */
document.querySelectorAll('.s-tab').forEach(function(tab){
    tab.addEventListener('click', function(){
        document.querySelectorAll('.s-tab').forEach(function(t){ t.classList.remove('active'); });
        this.classList.add('active');
        var cat = this.dataset.cat;
        document.querySelectorAll('#sGrid .s-card').forEach(function(card){
            card.style.display = (cat === 'all' || card.dataset.cat === cat) ? '' : 'none';
        });
    });
});
function filterStore(){
    var q = document.getElementById('sSearch').value.toLowerCase();
    document.querySelectorAll('#sGrid .s-card').forEach(function(card){
        card.style.display = card.dataset.name.includes(q) ? '' : 'none';
    });
}

/* ── product detail modal ── */
function openDetail(id){
    var p = findProduct(id);
    if(!p) return;
    
    var images = [];
    if(p.image) images.push(p.image);
    if(p.image2) images.push(p.image2);
    if(p.image3) images.push(p.image3);
    if(p.image4) images.push(p.image4);
    
    var imgHtml = '';
    if(images.length > 0) {
        var sliderHtml = images.map(function(img, idx) {
            return '<img class="s-modal-img slide-img" data-idx="'+idx+'" src="'+img+'" alt="" style="display:'+(idx===0?'block':'none')+'; margin-bottom: 0;">';
        }).join('');
        
        var navHtml = '';
        if(images.length > 1) {
            navHtml = '<button class="s-modal-nav prev" onclick="changeSlide(-1)"><i class="fas fa-chevron-left"></i></button>' +
                      '<button class="s-modal-nav next" onclick="changeSlide(1)"><i class="fas fa-chevron-right"></i></button>' +
                      '<div class="s-modal-dots">' +
                      images.map(function(img, idx) { return '<span class="s-modal-dot '+(idx===0?'active':'')+'" onclick="goToSlide('+idx+')"></span>'; }).join('') +
                      '</div>';
        }
        
        imgHtml = '<div class="s-modal-slider" id="productSlider" data-current="0" data-total="'+images.length+'">' + sliderHtml + navHtml + '</div>';
    } else {
        imgHtml = '<div class="s-modal-no-img"><i class="fas fa-box"></i></div>';
    }

    var stockHtml = p.stock === null
        ? 'Stok unlimited'
        : (p.stock > 0 ? p.stock+' tersedia' : 'Habis');
        
    var flashSaleBadge = p.is_flash_sale ? '<span style="background:#ef4444;color:#fff;font-size:.7rem;padding:3px 8px;border-radius:12px;font-weight:bold;margin-right:6px"><i class="fas fa-bolt" style="margin-right:4px"></i>Flash Sale</span>' : '';
    var discountBadge = (p.has_discount && p.discount_price) ? '<span style="background:#f59e0b;color:#fff;font-size:.7rem;padding:3px 8px;border-radius:12px;font-weight:bold;margin-right:6px"><i class="fas fa-tag" style="margin-right:4px"></i>Diskon</span>' : '';
    
    var finalPrice = (p.has_discount && p.discount_price) ? p.discount_price : p.price;
    var priceDisplay = '';
    if(p.has_discount && p.discount_price) {
        priceDisplay = '<div style="display:flex;align-items:center;gap:8px;"><span style="font-size:1.1rem;color:#ef4444;font-weight:700">' + fmtRp(p.discount_price) + '</span><span style="font-size:0.85rem;color:#9ca3af;text-decoration:line-through">' + fmtRp(p.price) + '</span></div>';
    } else {
        priceDisplay = p.is_flexible_amount ? '<span style="font-size:0.9rem;color:#6b7280;font-weight:normal">Mulai dari</span> ' + fmtRp(p.price) : fmtRp(p.price);
    }

    var ratingHtml = '<div style="font-size:.8rem;color:#6b7280;display:flex;align-items:center;gap:4px;margin-bottom:12px">'+
                     '<i class="fas fa-star" style="color:#f59e0b;"></i>'+
                     '<span style="font-weight:600;color:#374151;">'+(p.avg_rating?parseFloat(p.avg_rating).toFixed(1):'0.0')+'</span>'+
                     '<span>&middot;</span>'+
                     '<span>'+(p.total_sold>=1000000?Math.floor(p.total_sold/1000000)+'jt+':(p.total_sold>=1000?Math.floor(p.total_sold/1000)+'rb+':(p.total_sold||0)))+' terjual</span>'+
                     '</div>';

    var btnAddHtml = p.has_variants ? '' : '<button class="btn-add-cart" id="btnAddCart_'+id+'"><i class="fas fa-cart-plus" style="margin-right:4px"></i>Tambah ke Keranjang</button>';
    var btnBuyHtml = p.has_variants 
        ? '<button class="btn-buy-now" id="btnBuyNow_'+id+'"><i class="fas fa-list" style="margin-right:4px"></i>Pilih Varian</button>'
        : '<button class="btn-buy-now" id="btnBuyNow_'+id+'"><i class="fas fa-bolt" style="margin-right:4px"></i>Beli Sekarang</button>';

    var maxQty = p.stock !== null ? p.stock : 9999;
    if(p.qty_per_transaction > 0 && p.qty_per_transaction < maxQty) {
        maxQty = p.qty_per_transaction;
    }
    
    var inputsHtml = '';
    if (!p.has_variants) {
        var qtyInput = '<div style="flex:1"><label style="font-size:0.8rem;font-weight:700;display:block;margin-bottom:6px">Kuantitas</label><input type="number" id="s_qty_'+id+'" value="1" min="1" max="'+maxQty+'" style="width:100%;padding:8px 12px;border:1px solid #cbd5e1;border-radius:8px;outline:none" onfocus="this.style.borderColor=\'#6366f1\'" onblur="this.style.borderColor=\'#cbd5e1\'"></div>';
        
        var priceInput = '';
        if (p.is_flexible_amount) {
            priceInput = '<div style="flex:2"><label style="font-size:0.8rem;font-weight:700;display:block;margin-bottom:6px">Harga Anda (Min: '+fmtRp(finalPrice)+')</label><input type="number" id="s_price_'+id+'" value="'+finalPrice+'" min="'+finalPrice+'" style="width:100%;padding:8px 12px;border:1px solid #cbd5e1;border-radius:8px;outline:none" onfocus="this.style.borderColor=\'#6366f1\'" onblur="this.style.borderColor=\'#cbd5e1\'"></div>';
        }
        
        inputsHtml = '<div style="display:flex;gap:12px;margin-bottom:18px">' + qtyInput + priceInput + '</div>';
    }

    var btnShareHtml = '<button class="btn-share-modal" id="btnShareModal_'+id+'" onclick="shareProduct('+id+')" title="Bagikan Produk"><i class="fas fa-share-alt"></i></button>';

    document.getElementById('modalContent').innerHTML =
        imgHtml +
        (flashSaleBadge || discountBadge ? '<div style="margin-bottom:10px">' + flashSaleBadge + discountBadge + '</div>' : '') +
        '<p class="s-modal-name">'+escHtml(p.name)+'</p>'+
        '<p class="s-modal-price" style="margin-bottom:4px">'+priceDisplay+'</p>'+
        ratingHtml +
        '<p class="s-modal-stock">'+escHtml(stockHtml)+'</p>'+
        (p.description ? '<div class="s-modal-desc">'+p.description+'</div>' : '')+
        inputsHtml +
        '<div class="s-modal-actions">'+
            btnShareHtml +
            btnAddHtml +
            btnBuyHtml +
        '</div>' + 
        '<div id="modalReviewsContainer" style="margin-top:24px;border-top:1px solid #f3f4f6;padding-top:16px"><div style="text-align:center;color:#94a3b8;font-size:0.85rem">Memuat ulasan...</div></div>';
        
    /* Fetch reviews */
    fetch('<?= SITE_URL . 'shop-ajax?action=item_reviews&item_id=' ?>' + id)
    .then(r => r.json())
    .then(res => {
        var rContainer = document.getElementById('modalReviewsContainer');
        if(!res.success || !res.data || res.data.length === 0) {
            rContainer.innerHTML = '<div style="font-weight:700;margin-bottom:12px;font-size:1.1rem">Ulasan Pembeli</div><div style="font-size:0.85rem;color:#94a3b8">Belum ada ulasan untuk produk ini.</div>';
            return;
        }
        var totalRating = 0;
        var rHtml = res.data.map(function(r) {
            totalRating += r.rating;
            var stars = '';
            for(var i=1; i<=5; i++) {
                stars += '<i class="fas fa-star" style="color:' + (i <= r.rating ? '#f59e0b' : '#e5e7eb') + ';font-size:0.75rem"></i> ';
            }
            var verifyBadge = r.is_verified ? '<span style="background:#d1fae5;color:#059669;padding:2px 6px;border-radius:12px;font-size:0.65rem;font-weight:700;margin-left:6px" title="Verifikasi KTP"><i class="fas fa-check-circle"></i> Verified Purchase</span>' : '';
            var replyHtml = r.reply ? '<div style="background:#f8fafc;padding:10px;border-radius:8px;margin-top:10px;font-size:0.8rem;border-left:3px solid #4f46e5"><strong style="display:block;margin-bottom:4px;color:#1e293b">Balasan Penjual:</strong><span style="color:#475569">' + escHtml(r.reply) + '</span></div>' : '';
            var reportBtn = '<button onclick="reportReview(' + r.id + ')" style="background:none;border:none;color:#94a3b8;cursor:pointer;font-size:0.7rem;margin-top:8px;padding:0"><i class="fas fa-flag"></i> Laporkan</button>';
            
            return '<div style="padding:16px 0;border-bottom:1px solid #f3f4f6">' +
                '<div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:6px">' +
                    '<div><strong style="font-size:0.9rem;color:#1e293b">' + escHtml(r.name) + '</strong>' + verifyBadge + '</div>' +
                    '<div style="font-size:0.75rem;color:#94a3b8">' + r.datetime + '</div>' +
                '</div>' +
                '<div style="margin-bottom:8px">' + stars + '</div>' +
                '<div style="font-size:0.85rem;color:#374151;line-height:1.5">' + escHtml(r.review) + '</div>' +
                replyHtml +
                reportBtn +
            '</div>';
        }).join('');
        
        var avg = (totalRating / res.data.length).toFixed(1);
        rContainer.innerHTML = '<div style="display:flex;align-items:center;gap:12px;margin-bottom:16px"><div style="font-weight:800;font-size:1.2rem;color:#1e293b">Ulasan Pembeli</div><div style="background:#fef3c7;color:#d97706;padding:4px 10px;border-radius:12px;font-weight:700;font-size:0.85rem"><i class="fas fa-star mr-1"></i> ' + avg + ' / 5.0</div></div>' + rHtml;
    });

    /* attach listeners setelah render */
    if(document.getElementById('btnAddCart_'+id)) {
        document.getElementById('btnAddCart_'+id).onclick = function(){
            var q = document.getElementById('s_qty_'+id) ? parseInt(document.getElementById('s_qty_'+id).value) || 1 : 1;
            var pr = document.getElementById('s_price_'+id) ? parseFloat(document.getElementById('s_price_'+id).value) || finalPrice : finalPrice;
            
            if(q > maxQty) {
                alert('Maksimal pembelian ' + maxQty + ' item per transaksi.');
                return;
            }
            if(pr < finalPrice) {
                alert('Harga minimal adalah ' + fmtRp(finalPrice));
                return;
            }
            
            addCart(id, q, pr); 
            closeDetail(); 
            showCart();
        };
    }
    document.getElementById('btnBuyNow_'+id).onclick = function(){
        var q = document.getElementById('s_qty_'+id) ? parseInt(document.getElementById('s_qty_'+id).value) || 1 : 1;
        if(q > maxQty) { alert('Maksimal pembelian ' + maxQty + ' item per transaksi.'); return; }
        /* Gunakan PHP URL agar tidak bergantung pada SITE_URL JS */
        window.location.href = '<?= SITE_URL ?>store-checkout/' + id + '?qty=' + q;
    };
    document.getElementById('detailOverlay').classList.add('show');
    
    // Track product view
    fetch(SITE_URL + 'shop-ajax', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({
            action: 'track_product_view',
            shop_id: <?= $data->shop->id ?>,
            item_id: id
        })
    });
}

function reportReview(id) {
    var reason = prompt("Alasan melaporkan ulasan ini (spam, kata kasar, dll):");
    if(!reason) return;
    var params = new URLSearchParams({action:'report_review', review_id:id, reason:reason, token:'<?= \Altum\Csrf::get() ?>'});
    fetch('<?= SITE_URL . 'shop-ajax' ?>', {method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body:params})
    .then(r=>r.json()).then(res=>{
        if(res.success) alert('Ulasan berhasil dilaporkan.');
        else alert('Gagal melaporkan ulasan.');
    });
}

function closeDetail(){
    document.getElementById('detailOverlay').classList.remove('show');
}

function shareProduct(id) {
    var p = findProduct(id);
    if(!p) return;
    
    var shareUrl = STORE_URL + p.id;
    var shareData = {
        title: p.name,
        text: 'Lihat produk ini: ' + p.name,
        url: shareUrl
    };

    if (navigator.share) {
        navigator.share(shareData)
            .catch(function(err) { console.log('Share failed:', err); });
    } else {
        // Fallback
        var tempInput = document.createElement('input');
        document.body.appendChild(tempInput);
        tempInput.value = shareUrl;
        tempInput.select();
        document.execCommand('copy');
        document.body.removeChild(tempInput);
        alert('Tautan produk disalin ke clipboard!');
    }
}

function changeSlide(dir) {
    var slider = document.getElementById('productSlider');
    if(!slider) return;
    var total = parseInt(slider.getAttribute('data-total'));
    var current = parseInt(slider.getAttribute('data-current'));
    var next = current + dir;
    if(next >= total) next = 0;
    if(next < 0) next = total - 1;
    goToSlide(next);
}

function goToSlide(idx) {
    var slider = document.getElementById('productSlider');
    if(!slider) return;
    
    var slides = slider.querySelectorAll('.slide-img');
    var dots = slider.querySelectorAll('.s-modal-dot');
    
    slides.forEach(function(s) { s.style.display = 'none'; });
    dots.forEach(function(d) { d.classList.remove('active'); });
    
    slides[idx].style.display = 'block';
    if(dots[idx]) dots[idx].classList.add('active');
    
    slider.setAttribute('data-current', idx);
}

/* ── cart functions ── */
function addCart(id, qty, price){
    qty = qty || 1;
    id  = Number(id);
    var p = findProduct(id);
    if(!p) return;
    /* price dikumpulkan dari JS tapi TIDAK dikirim ke server — server ambil dari DB */
    var displayPrice = price != null ? Number(price) : ((p.has_discount && p.discount_price) ? p.discount_price : p.price);
    var exist = cart.find(function(c){ return c.id === id; });
    if(exist){
        exist.qty += qty;
    } else {
        cart.push({id:id, name:p.name, price:displayPrice, image:p.image, qty:qty, checked:true});
    }
    saveCart();
    updateCartUI();
}
function quickAddCart(id){
    addCart(id);
    showCart();
}
function clearCart(){
    cart = [];
    saveCart();
    updateCartUI();
}
function changeQty(id, delta){
    var item = cart.find(function(c){ return c.id === id; });
    if(!item) return;
    item.qty = Math.max(1, item.qty + delta);
    saveCart();
    updateCartUI();
}
function toggleSelectAll(checked){
    cart.forEach(function(c){ c.checked = checked; });
    saveCart();
    updateCartUI();
}
function updateSelected(){
    /* sync checked state dari checkbox ke cart array */
    document.querySelectorAll('.cart-cb-item').forEach(function(cb){
        var id = Number(cb.getAttribute('data-id'));
        var item = cart.find(function(c){ return c.id === id; });
        if(item) item.checked = cb.checked;
    });
    saveCart();
    /* update total display */
    var selected = cart.filter(function(c){ return c.checked; });
    var selTotal  = selected.reduce(function(s,c){ return s + c.price * c.qty; }, 0);
    document.getElementById('cartTotal').textContent = fmtRp(selTotal);
    document.getElementById('cartSelectedInfo').textContent = selected.length + ' dari ' + cart.length + ' produk dipilih';
    document.getElementById('cartSelectedCount').textContent = selected.length + '/' + cart.length;
    /* select all checkbox state */
    var cbAll = document.getElementById('cbSelectAll');
    if(cbAll) cbAll.checked = cart.length > 0 && cart.every(function(c){ return c.checked; });
}

/* ── render cart ── */
function updateCartUI(){
    var count = cart.reduce(function(s,c){ return s + c.qty; }, 0);
    /* badge */
    var badge = document.getElementById('cartBadge');
    badge.style.display = count > 0 ? 'flex' : 'none';
    badge.textContent = cart.length;

    var el      = document.getElementById('cartItems');
    var footer  = document.getElementById('cartFooter');
    var selRow  = document.getElementById('cartSelectAllRow');

    if(cart.length === 0){
        el.innerHTML = '<div class="cart-empty-msg"><i class="fas fa-shopping-cart"></i><p>Keranjang kosong</p></div>';
        footer.style.display = 'none';
        selRow.style.display = 'none';
        return;
    }

    footer.style.display = '';
    selRow.style.display = '';

    /* render items */
    el.innerHTML = cart.map(function(c){
        var imgHtml = c.image
            ? '<img class="cart-item-img" src="'+c.image+'" alt="">'
            : '<div class="cart-item-img" style="display:flex;align-items:center;justify-content:center;font-size:1.2rem;color:#7c3aed"><i class="fas fa-box"></i></div>';
        var isChecked = c.checked !== false;
        return '<div class="cart-item" data-item-id="'+c.id+'">' +
            '<input type="checkbox" class="cart-cb cart-cb-item" data-id="'+c.id+'" '+(isChecked?'checked':'')+' onchange="updateSelected()">' +
            imgHtml +
            '<div class="cart-item-info">' +
                '<div class="cart-item-name" title="'+escHtml(c.name)+'">'+escHtml(c.name)+'</div>' +
                '<div class="cart-item-price">'+fmtRp(c.price)+' / pcs</div>' +
            '</div>' +
            '<div class="cart-qty-ctrl">' +
                '<button class="cart-qty-btn" onclick="changeQty('+c.id+',-1)" type="button">−</button>' +
                '<span class="cart-qty-val">'+c.qty+'</span>' +
                '<button class="cart-qty-btn" onclick="changeQty('+c.id+',1)" type="button">+</button>' +
            '</div>' +
            '<button class="cart-item-remove" type="button" title="Hapus">🗑</button>' +
        '</div>';
    }).join('');

    /* attach remove buttons */
    el.querySelectorAll('.cart-item-remove').forEach(function(btn){
        btn.addEventListener('click', function(e){
            e.stopPropagation();
            var itemId = Number(this.closest('.cart-item').getAttribute('data-item-id'));
            cart = cart.filter(function(c){ return c.id !== itemId; });
            saveCart();
            updateCartUI();
        });
    });

    updateSelected();
}

function toggleCart(){
    document.getElementById('cartPanel').classList.toggle('open');
    document.getElementById('ordersPanel').classList.remove('open');
}
function showCart(){
    document.getElementById('cartPanel').classList.add('open');
    document.getElementById('ordersPanel').classList.remove('open');
}
function toggleOrders(){
    document.getElementById('ordersPanel').classList.toggle('open');
    document.getElementById('cartPanel').classList.remove('open');
}
function doCheckout(){
    /* Ambil item yang dicentang */
    var selected = cart.filter(function(c){ return c.checked !== false; });
    if(selected.length === 0){
        document.getElementById('popupNoSelect').classList.add('show');
        return;
    }
    if(selected.length === 1){
        /* single item → checkout biasa dengan qty */
        var c = selected[0];
        window.location.href = '<?= SITE_URL ?>store-checkout/' + c.id + '?qty=' + c.qty;
        return;
    }
    /* multi-item → POST tersembunyi ke StoreCartCheckout, harga dari DB */
    var form = document.createElement('form');
    form.method = 'POST';
    form.action = '<?= SITE_URL ?>store-cart-checkout';
    form.style.display = 'none';
    function addField(n, v){ var i=document.createElement('input');i.type='hidden';i.name=n;i.value=v;form.appendChild(i); }
    addField('token', '<?= \Altum\Csrf::get() ?>');
    addField('shop_url', '<?= $data->shop->url ?>');
    selected.forEach(function(c, i){
        addField('items['+i+'][id]',  c.id);
        addField('items['+i+'][qty]', c.qty);
        /* TIDAK kirim price → server ambil dari DB */
    });
    document.body.appendChild(form);
    form.submit();
}

/* ── Check Order ── */
function checkOrder() {
    var inv = document.getElementById('chk_invoice').value.trim();
    var eml = document.getElementById('chk_email').value.trim();
    var msg = document.getElementById('chk_msg');
    var res = document.getElementById('chk_result');
    var btn = document.getElementById('btn_check_order');

    if(!inv || !eml) {
        msg.innerHTML = '<span style="color:#ef4444">Invoice dan Email wajib diisi</span>';
        return;
    }

    btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> Mengecek...';
    btn.disabled = true;
    msg.innerHTML = '';
    res.style.display = 'none';

    var params = new URLSearchParams({
        action: 'buyer_check_order',
        shop_id: <?= $data->shop->id ?>,
        invoice: inv,
        email: eml
    });

    fetch('<?= SITE_URL . 'shop-ajax' ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: params
    })
    .then(r => r.json())
    .then(function(d) {
        btn.innerHTML = '<i class="fas fa-search mr-1"></i> Cek Pesanan';
        btn.disabled = false;

        if(!d.success) {
            msg.innerHTML = '<span style="color:#ef4444"><i class="fas fa-exclamation-circle"></i> ' + d.message + '</span>';
        } else {
            var o = d.data;
            var statHtml = '';
            if(o.status === 'paid') statHtml = '<span style="color:#059669;font-weight:700;font-size:.8rem;background:#d1fae5;padding:4px 8px;border-radius:6px"><i class="fas fa-check-circle mr-1"></i> Lunas</span>';
            else if(o.status === 'pending') statHtml = '<span style="color:#d97706;font-weight:700;font-size:.8rem;background:#fef3c7;padding:4px 8px;border-radius:6px"><i class="fas fa-clock mr-1"></i> Menunggu Bayar</span>';
            else statHtml = '<span style="color:#dc2626;font-weight:700;font-size:.8rem;background:#fee2e2;padding:4px 8px;border-radius:6px">' + o.status.toUpperCase() + '</span>';

            var imgHtml = o.item_image 
                ? '<img src="'+o.item_image+'" style="width:40px;height:40px;border-radius:6px;object-fit:cover;flex-shrink:0" alt="">' 
                : '<div style="width:40px;height:40px;border-radius:6px;background:#f1f5f9;display:flex;align-items:center;justify-content:center;color:#94a3b8"><i class="fas fa-box"></i></div>';

            var shipHtml = '';
            var typeName = o.item_type === 'physical' ? 'Produk Fisik' : o.item_type.replace(/_/g, ' ').toUpperCase();

            if(o.item_type === 'physical') {
                var trackStatus = o.shipping_status || 'diproses';
                var sDiproses = 'color:#059669;font-weight:bold';
                var sDikirim  = trackStatus === 'shipped' || trackStatus === 'delivered' ? 'color:#059669;font-weight:bold' : 'color:#94a3b8';
                var sDiterima = trackStatus === 'delivered' ? 'color:#059669;font-weight:bold' : 'color:#94a3b8';

                var trackerHtml = '<div style="display:flex;align-items:center;gap:8px;font-size:.75rem;margin-top:10px;background:#f1f5f9;padding:6px 12px;border-radius:20px;width:fit-content">'+
                                  '<span style="'+sDiproses+'">Diproses</span>'+
                                  '<i class="fas fa-chevron-right" style="color:#cbd5e1;font-size:.65rem"></i>'+
                                  '<span style="'+sDikirim+'">Dikirim</span>'+
                                  '<i class="fas fa-chevron-right" style="color:#cbd5e1;font-size:.65rem"></i>'+
                                  '<span style="'+sDiterima+'">Diterima</span>'+
                                  '</div>';

                var tracking = o.tracking_number ? '<div style="margin-top:8px;font-size:.8rem;color:#059669">Resi: <strong>'+o.tracking_number+'</strong></div>' : '';
                var addrStr = o.shipping_address ? '<div style="margin-top:8px;font-size:.75rem;color:#64748b;white-space:pre-wrap;line-height:1.4"><i class="fas fa-map-marker-alt text-danger"></i> '+o.shipping_address+'</div>' : '';
                
                shipHtml = '<div style="padding:16px;background:#f8fafc;border-top:1px solid #e2e8f0;font-size:.8rem"><div style="color:#475569;margin-bottom:4px"><strong>Pengiriman:</strong> '+(o.shipping_courier||'').toUpperCase()+' - '+o.shipping_service+'</div>' + trackerHtml + tracking + addrStr + '</div>';
            }

            var payBtn = '';
            if(o.status === 'pending' && o.checkout_url) {
                payBtn = '<div style="padding:12px;background:#fff"><a href="'+o.checkout_url+'" target="_blank" style="display:block;text-align:center;background:#4f46e5;color:#fff;border-radius:8px;padding:8px;text-decoration:none;font-weight:600;font-size:.85rem"><i class="fas fa-credit-card mr-1"></i> Bayar Sekarang</a></div>';
            }

            var reviewHtml = '';
            if(o.status === 'paid') {
                if(o.review) {
                    var starsHtml = '';
                    for(var i=1;i<=5;i++) starsHtml += '<i class="fas fa-star" style="color:'+(i<=o.review.rating?'#f59e0b':'#d1d5db')+'"></i> ';
                    var rText = o.review.review || 'Tidak ada teks';
                    var replyText = o.review.reply ? '<div style="background:#f8fafc;padding:10px;border-radius:8px;margin-top:10px;font-size:0.8rem;border-left:3px solid #4f46e5"><strong style="display:block;margin-bottom:4px;color:#1e293b">Balasan Penjual:</strong><span style="color:#475569">' + escHtml(o.review.reply) + '</span></div>' : '';
                    reviewHtml = '<div style="padding:16px;background:#fff;border-top:1px solid #e2e8f0"><div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px"><div style="font-weight:700;font-size:0.9rem">Ulasan Kamu</div><button onclick="editMyReview()" style="background:none;border:none;color:#4f46e5;font-size:0.8rem;font-weight:600;cursor:pointer"><i class="fas fa-edit"></i> Edit</button></div><div style="font-size:1.1rem;margin-bottom:6px">'+starsHtml+'</div><div style="font-size:0.85rem;color:#475569" id="myReviewText">'+escHtml(rText)+'</div>'+replyText+'</div>';
                    
                    window.currentReview = { invoice: o.invoice_number, rating: o.review.rating, review: o.review.review };
                } else {
                    reviewHtml = '<div style="padding:16px;background:#fff;border-top:1px solid #e2e8f0;text-align:center"><div style="font-size:0.85rem;color:#64748b;margin-bottom:10px">Kamu belum memberikan ulasan</div><a href="'+STORE_URL+'success/'+o.invoice_number+'" style="display:inline-block;background:#fef3c7;color:#d97706;padding:6px 14px;border-radius:8px;text-decoration:none;font-weight:700;font-size:0.8rem"><i class="fas fa-star mr-1"></i> Beri Ulasan</a></div>';
                }
            }

            res.innerHTML = 
                '<div style="padding:12px 16px;border-bottom:1px solid #e2e8f0;display:flex;justify-content:space-between;align-items:center">' +
                    '<div style="font-family:monospace;font-weight:700;color:#1e293b">' + o.invoice_number + '</div>' +
                    '<div>' + statHtml + '</div>' +
                '</div>' +
                '<div style="padding:16px">' +
                    '<div style="display:flex;gap:12px;margin-bottom:12px">' +
                        imgHtml + 
                        '<div>' +
                            '<div style="font-weight:600;font-size:.85rem;color:#1e293b">' + o.item_name + '</div>' +
                            '<div style="font-size:.7rem;color:#7c3aed;background:#ede9fe;padding:2px 6px;border-radius:8px;display:inline-block;margin-top:4px">' + typeName + '</div>' +
                            '<div style="font-size:.75rem;color:#64748b;margin-top:6px">' + o.datetime + '</div>' +
                        '</div>' +
                    '</div>' +
                    '<div style="display:flex;justify-content:space-between;font-size:.85rem;margin-top:16px;border-top:1px dashed #cbd5e1;padding-top:12px">' +
                        '<span style="color:#64748b">Total Belanja</span>' +
                        '<strong style="color:#4f46e5">Rp ' + Number(o.grand_total).toLocaleString('id-ID') + '</strong>' +
                    '</div>' +
                '</div>' +
                shipHtml + payBtn + reviewHtml;

            res.style.display = 'block';
        }
    })
    .catch(function(){
        btn.innerHTML = '<i class="fas fa-search mr-1"></i> Cek Pesanan';
        btn.disabled = false;
        msg.innerHTML = '<span style="color:#ef4444">Terjadi kesalahan sistem.</span>';
    });
}

function editMyReview() {
    var c = window.currentReview;
    if(!c) return;
    var newRating = prompt("Rating baru (1-5):", c.rating);
    if(newRating === null) return;
    newRating = parseInt(newRating);
    if(newRating < 1 || newRating > 5 || isNaN(newRating)) { alert("Rating tidak valid"); return; }
    var newReview = prompt("Isi ulasan baru:", c.review);
    if(newReview === null) return;
    
    var email = document.getElementById('chk_email').value;
    var params = new URLSearchParams({action:'buyer_edit_review', shop_id:'<?= $data->shop->id ?>', invoice:c.invoice, email:email, rating:newRating, review:newReview, token:'<?= \Altum\Csrf::get() ?>'});
    
    fetch('<?= SITE_URL . 'shop-ajax' ?>', {method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body:params})
    .then(r=>r.json()).then(res=>{
        if(res.success) {
            alert('Ulasan berhasil diperbarui!');
            checkOrder();
        } else {
            alert('Gagal mengedit: ' + (res.message || 'Error'));
        }
    });
}

/* ── init: load dari localStorage dulu ── */
loadCart();
updateCartUI();
</script>
