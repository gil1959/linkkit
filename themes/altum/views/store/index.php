<?php defined('ALTUMCODE') || die() ?>
<?php \Altum\Title::set(htmlspecialchars($data->shop->name) . ' — Store') ?>

<style>
*,*::before,*::after{box-sizing:border-box}
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
.s-type-badge{position:absolute;top:8px;left:8px;background:rgba(79,70,229,.85);color:#fff;font-size:.65rem;font-weight:700;padding:3px 8px;border-radius:20px}
.s-quick-cart{position:absolute;bottom:8px;right:8px;width:34px;height:34px;background:#4f46e5;color:#fff;border:none;border-radius:50%;display:flex;align-items:center;justify-content:center;cursor:pointer;box-shadow:0 2px 8px rgba(79,70,229,.4);transition:.2s;font-size:.85rem}
.s-quick-cart:hover{background:#3730a3;transform:scale(1.1)}
.s-card-body{padding:12px;flex:1;display:flex;flex-direction:column}
.s-card-name{font-weight:700;font-size:.88rem;color:#111827;margin-bottom:4px;line-height:1.3}
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
.s-modal-name{font-size:1.2rem;font-weight:800;margin:0 0 6px}
.s-modal-price{font-size:1.4rem;font-weight:800;color:#4f46e5;margin-bottom:8px}
.s-modal-stock{font-size:.8rem;color:#6b7280;margin-bottom:12px}
.s-modal-desc{font-size:.88rem;color:#374151;line-height:1.6;margin-bottom:20px}
.s-modal-actions{display:flex;gap:10px}
.btn-add-cart{flex:1;background:#eef2ff;color:#4f46e5;border:none;border-radius:10px;padding:11px;font-weight:700;font-size:.9rem;cursor:pointer;transition:.2s}
.btn-add-cart:hover{background:#dde4ff}
.btn-buy-now{flex:1;background:#4f46e5;color:#fff;border:none;border-radius:10px;padding:11px;font-weight:700;font-size:.9rem;cursor:pointer;transition:.2s}
.btn-buy-now:hover{background:#3730a3}
/* ── CART POPUP ── */
.cart-panel{position:fixed;right:-420px;top:0;bottom:0;width:380px;max-width:95vw;background:#fff;box-shadow:-4px 0 24px rgba(0,0,0,.12);z-index:900;transition:right .3s cubic-bezier(.4,0,.2,1);display:flex;flex-direction:column}
.cart-panel.open{right:0}
.cart-panel-head{padding:20px;border-bottom:1px solid #f3f4f6;display:flex;align-items:center;justify-content:space-between}
.cart-panel-head h3{margin:0;font-size:1rem;font-weight:700}
.cart-close{background:none;border:none;font-size:1.2rem;cursor:pointer;color:#9ca3af}
.cart-close:hover{color:#111}
.cart-items{flex:1;overflow-y:auto;padding:16px}
.cart-item{display:flex;align-items:center;gap:12px;padding:12px 0;border-bottom:1px solid #f3f4f6}
.cart-item:last-child{border-bottom:none}
.cart-item-img{width:52px;height:52px;border-radius:8px;object-fit:cover;background:#f3f4f6;flex-shrink:0}
.cart-item-info{flex:1}
.cart-item-name{font-weight:600;font-size:.85rem;margin-bottom:2px}
.cart-item-price{font-size:.8rem;color:#4f46e5;font-weight:700}
.cart-item-remove{background:none;border:none;color:#ef4444;cursor:pointer;font-size:1.1rem;padding:6px 8px;border-radius:6px;line-height:1;transition:background .15s}
.cart-item-remove:hover{background:#fef2f2}

.cart-empty-msg{text-align:center;padding:40px 16px;color:#9ca3af}
.cart-empty-msg i{font-size:2rem;display:block;margin-bottom:8px}
.cart-footer{padding:16px;border-top:1px solid #f3f4f6;display:flex;flex-direction:column;gap:8px}
.cart-total{display:flex;justify-content:space-between;font-weight:700;font-size:.9rem;margin-bottom:4px}
.btn-checkout{background:#4f46e5;color:#fff;border:none;border-radius:10px;padding:12px;font-weight:700;font-size:.9rem;cursor:pointer;width:100%;transition:.2s}
.btn-checkout:hover{background:#3730a3}
.cart-footer-row{display:flex;gap:8px}
.btn-clear{flex:1;background:#fef2f2;color:#ef4444;border:none;border-radius:10px;padding:10px;font-weight:600;font-size:.82rem;cursor:pointer;transition:.2s}
.btn-clear:hover{background:#fee2e2}
.btn-continue{flex:1;background:#f3f4f6;color:#374151;border:none;border-radius:10px;padding:10px;font-weight:600;font-size:.82rem;cursor:pointer;transition:.2s}
.btn-continue:hover{background:#e5e7eb}
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

    <button class="cart-btn" onclick="toggleCart()">
        <i class="fas fa-shopping-cart"></i>
        <span id="cartLabel">Keranjang</span>
        <span class="cart-badge" id="cartBadge">0</span>
    </button>
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
            <h1 class="s-name"><?= htmlspecialchars($data->shop->name) ?></h1>
            <?php if($data->shop->description): ?>
                <p class="s-desc"><?= nl2br(htmlspecialchars($data->shop->description)) ?></p>
            <?php endif ?>
        </div>
    </div>

    <!-- TABS -->
    <div class="s-bar">
        <div class="s-tabs">
            <button class="s-tab active" data-cat="all">Semua</button>
            <?php
            $cats = array_unique(array_map(fn($i) => $i->type, $data->items));
            foreach($cats as $c):
            ?>
                <button class="s-tab" data-cat="<?= $c ?>"><?= ucwords(str_replace('_',' ',$c)) ?></button>
            <?php endforeach ?>
        </div>
    </div>

    <!-- PRODUCT GRID -->
    <div class="s-grid" id="sGrid">
        <?php if(count($data->items)): ?>
            <?php foreach($data->items as $item): ?>
                <div class="s-card"
                     data-name="<?= strtolower(htmlspecialchars($item->name)) ?>"
                     data-cat="<?= $item->type ?>"
                     onclick="openDetail(<?= $item->id ?>)">
                    <div class="s-card-img">
                        <?php if($item->image): ?>
                            <img src="<?= \Altum\Uploads::get_full_url('shop_items') . $item->image ?>" alt="">
                        <?php else: ?>
                            <div class="s-no-img"><i class="fas fa-box"></i></div>
                        <?php endif ?>
                        <span class="s-type-badge"><?= ucwords(str_replace('_',' ',$item->type)) ?></span>
                        <button class="s-quick-cart" onclick="event.stopPropagation(); quickAddCart(<?= $item->id ?>)" title="Tambah ke keranjang">
                            <i class="fas fa-cart-plus"></i>
                        </button>
                    </div>
                    <div class="s-card-body">
                        <div class="s-card-name"><?= htmlspecialchars($item->name) ?></div>
                        <div class="s-card-price">Rp <?= number_format($item->price,0,',','.') ?></div>
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
    <div class="cart-items" id="cartItems"></div>
    <div class="cart-footer" id="cartFooter" style="display:none">
        <div class="cart-total">
            <span>Total</span>
            <span id="cartTotal" style="color:#4f46e5">Rp 0</span>
        </div>
        <button class="btn-checkout" onclick="doCheckout()">
            <i class="fas fa-bolt mr-1"></i>Checkout
        </button>
        <div class="cart-footer-row">
            <button class="btn-clear" onclick="clearCart()"><i class="fas fa-trash mr-1"></i>Hapus Semua</button>
            <button class="btn-continue" onclick="toggleCart()"><i class="fas fa-arrow-left mr-1"></i>Lanjut Belanja</button>
        </div>
    </div>
</div>

<!-- EMBED PRODUCT DATA -->
<script>
var STORE_URL = '<?= SITE_URL ?>store-checkout/';
var PRODUCTS = <?= json_encode(array_map(function($i){
    return [
        'id'    => $i->id,
        'name'  => $i->name,
        'price' => (float)$i->price,
        'stock' => $i->stock,
        'type'  => $i->type,
        'image' => $i->image ? \Altum\Uploads::get_full_url('shop_items') . $i->image : null,
        'description' => $i->description ?? '',
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
    var imgHtml = p.image
        ? '<img class="s-modal-img" src="'+p.image+'" alt="">'
        : '<div class="s-modal-no-img"><i class="fas fa-box"></i></div>';
    var stockHtml = p.stock === null
        ? 'Stok unlimited'
        : (p.stock > 0 ? p.stock+' tersedia' : 'Habis');
    document.getElementById('modalContent').innerHTML =
        imgHtml +
        '<p class="s-modal-name">'+escHtml(p.name)+'</p>'+
        '<p class="s-modal-price">'+fmtRp(p.price)+'</p>'+
        '<p class="s-modal-stock">'+escHtml(stockHtml)+'</p>'+
        (p.description ? '<p class="s-modal-desc">'+escHtml(p.description)+'</p>' : '')+
        '<div class="s-modal-actions">'+
            '<button class="btn-add-cart" id="btnAddCart_'+id+'"><i class="fas fa-cart-plus" style="margin-right:4px"></i>Tambah ke Keranjang</button>'+
            '<button class="btn-buy-now" id="btnBuyNow_'+id+'"><i class="fas fa-bolt" style="margin-right:4px"></i>Beli Sekarang</button>'+
        '</div>';
    /* attach listeners setelah render */
    document.getElementById('btnAddCart_'+id).onclick = function(){
        addCart(id); closeDetail(); showCart();
    };
    document.getElementById('btnBuyNow_'+id).onclick = function(){
        window.location = STORE_URL + id;
    };
    document.getElementById('detailOverlay').classList.add('show');
}
function closeDetail(){
    document.getElementById('detailOverlay').classList.remove('show');
}

/* ── cart functions ── */
function addCart(id){
    id = Number(id);
    var p = findProduct(id);
    if(!p) return;
    var exist = cart.find(function(c){ return c.id === id; });
    if(exist){ exist.qty++; }
    else{ cart.push({id:id, name:p.name, price:Number(p.price), image:p.image, qty:1}); }
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

/* ── render cart ── */
function updateCartUI(){
    var total = cart.reduce(function(s,c){ return s + c.price * c.qty; }, 0);
    var count = cart.reduce(function(s,c){ return s + c.qty; }, 0);

    /* badge */
    var badge = document.getElementById('cartBadge');
    badge.style.display = count > 0 ? 'flex' : 'none';
    badge.textContent = count;

    var el = document.getElementById('cartItems');
    var footer = document.getElementById('cartFooter');

    if(cart.length === 0){
        el.innerHTML = '<div class="cart-empty-msg"><i class="fas fa-shopping-cart"></i><p>Keranjang kosong</p></div>';
        footer.style.display = 'none';
        return;
    }

    footer.style.display = '';
    document.getElementById('cartTotal').textContent = fmtRp(total);

    /* render items */
    el.innerHTML = cart.map(function(c){
        var imgHtml = c.image
            ? '<img class="cart-item-img" src="'+c.image+'" alt="">'
            : '<div class="cart-item-img" style="display:flex;align-items:center;justify-content:center;font-size:1.2rem;color:#7c3aed"><i class="fas fa-box"></i></div>';
        return '<div class="cart-item" data-item-id="'+c.id+'">'+
            imgHtml+
            '<div class="cart-item-info">'+
                '<div class="cart-item-name">'+escHtml(c.name)+'</div>'+
                '<div class="cart-item-price">'+fmtRp(c.price)+' &times; '+c.qty+'</div>'+
            '</div>'+
            '<button class="cart-item-remove" type="button" title="Hapus item">🗑</button>'+
        '</div>';
    }).join('');

    /* ── attach click listener langsung ke setiap tombol hapus setelah render ── */
    el.querySelectorAll('.cart-item-remove').forEach(function(btn){
        btn.addEventListener('click', function(e){
            e.stopPropagation();
            var itemId = Number(this.closest('.cart-item').getAttribute('data-item-id'));
            cart = cart.filter(function(c){ return c.id !== itemId; });
            saveCart();
            updateCartUI();
        });
    });
}

function toggleCart(){
    document.getElementById('cartPanel').classList.toggle('open');
}
function showCart(){
    document.getElementById('cartPanel').classList.add('open');
}
function doCheckout(){
    if(cart.length === 0) return;
    window.location = STORE_URL + cart[0].id;
}

/* ── init: load dari localStorage dulu ── */
loadCart();
updateCartUI();
</script>
