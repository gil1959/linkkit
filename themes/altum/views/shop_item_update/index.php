<?php defined('ALTUMCODE') || die() ?>

<header class="header pb-0">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="custom-breadcrumbs small">
                <li><a href="<?= url('shop') ?>">Shop</a> <i class="fas fa-fw fa-angle-right"></i></li>
                <li class="active" aria-current="page">Edit Product</li>
            </ol>
        </nav>
        <h1 class="h3 mb-3">Edit Product: <?= htmlspecialchars($data->item->name) ?></h1>
    </div>
</header>

<section class="container pt-4">
    <?= \Altum\Alerts::output_alerts() ?>

    <div class="card">
        <div class="card-body">
            <form action="" method="post" enctype="multipart/form-data" role="form">
                <input type="hidden" name="token" value="<?= \Altum\Csrf::get() ?>" />

                <div class="row">
                    <div class="col-12 col-md-8">
                        <div class="form-group">
                            <label for="name">Product Name</label>
                            <input type="text" id="name" name="name" class="form-control" required value="<?= htmlspecialchars($data->item->name) ?>" />
                        </div>

                        <div class="form-group">
                            <label for="description_editor">Product Description</label>
                            <input type="hidden" id="description" name="description" value="" />
                            <div id="ql-toolbar" class="ql-toolbar ql-snow">
                                <span class="ql-formats">
                                    <select class="ql-header">
                                        <option value="1">Heading 1</option>
                                        <option value="2">Heading 2</option>
                                        <option value="3">Heading 3</option>
                                        <option selected>Paragraph</option>
                                    </select>
                                </span>
                                <span class="ql-formats">
                                    <button class="ql-bold"></button>
                                    <button class="ql-italic"></button>
                                    <button class="ql-underline"></button>
                                    <button class="ql-strike"></button>
                                </span>
                                <span class="ql-formats">
                                    <button class="ql-list" value="bullet"></button>
                                    <button class="ql-list" value="ordered"></button>
                                </span>
                                <span class="ql-formats">
                                    <button class="ql-blockquote"></button>
                                </span>
                                <span class="ql-formats">
                                    <button class="ql-align" value=""></button>
                                    <button class="ql-align" value="center"></button>
                                    <button class="ql-align" value="right"></button>
                                    <button class="ql-align" value="justify"></button>
                                </span>
                                <span class="ql-formats">
                                    <button class="ql-link"></button>
                                </span>
                                <span class="ql-formats">
                                    <button class="ql-clean"></button>
                                </span>
                            </div>
                            <div id="description_editor" style="min-height: 180px;"></div>
                            <small class="text-muted mt-1 d-block">Supports <strong>bold</strong>, <em>italic</em>, lists, headings, and hyperlinks.</small>
                        </div>

                        <!-- Product Listing Dropdown -->
                        <div class="form-group">
                            <label for="listing_id">Product Listing</label>
                            <select id="listing_id" name="listing_id" class="form-control">
                                <option value="">No Listing</option>
                                <?php foreach($data->listings as $listing): ?>
                                    <option value="<?= $listing->id ?>"
                                        <?= (isset($data->item->listing_id) && $data->item->listing_id == $listing->id) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($listing->name) ?>
                                    </option>
                                <?php endforeach ?>
                            </select>
                            <small class="text-muted mt-1 d-block">Products that will be compiled in listing format</small>
                        </div>

                        <!-- Flexible Amount -->
                        <div class="card mb-3" style="border:1px solid #e5e7eb;border-radius:10px">
                            <div class="card-body py-3 d-flex align-items-center justify-content-between">
                                <div>
                                    <div><i class="fas fa-sliders-h text-primary mr-2"></i><strong>Flexible Amount</strong></div>
                                    <small class="text-muted">Let buyers determine the amount they want to pay.</small>
                                </div>
                                <div class="custom-control custom-switch ml-3">
                                    <input type="checkbox" class="custom-control-input" id="is_flexible_amount" name="is_flexible_amount" value="1" <?= !empty($data->item->is_flexible_amount) ? 'checked' : '' ?>>
                                    <label class="custom-control-label" for="is_flexible_amount"></label>
                                </div>
                            </div>
                        </div>

                        <!-- Product Variants -->
                        <div class="card mb-3" style="border:1px solid #e5e7eb;border-radius:10px">
                            <div class="card-body py-3 d-flex align-items-center justify-content-between">
                                <div>
                                    <div><i class="fas fa-layer-group text-primary mr-2"></i><strong>Product Variants</strong></div>
                                    <small class="text-muted">Add product variants with different prices.</small>
                                </div>
                                <div class="custom-control custom-switch ml-3">
                                    <input type="checkbox" class="custom-control-input" id="has_variants" name="has_variants" value="1" <?= !empty($data->item->has_variants) ? 'checked' : '' ?>>
                                    <label class="custom-control-label" for="has_variants"></label>
                                </div>
                            </div>
                        </div>

                        <!-- Price & Stock -->
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="price">Price (Rp)</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend"><span class="input-group-text">Rp</span></div>
                                        <input type="number" id="price" name="price" class="form-control" required step="1" value="<?= $data->item->price ?>" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label>Stock</label>
                                    <div class="d-flex align-items-center">
                                        <input type="number" id="stock" name="stock" class="form-control mr-2"
                                            value="<?= $data->item->stock !== null ? $data->item->stock : '' ?>"
                                            <?= $data->item->stock === null ? 'disabled placeholder="Unlimited"' : '' ?> />
                                        <div class="custom-control custom-switch" style="white-space:nowrap">
                                            <input type="checkbox" class="custom-control-input" id="unlimited_stock"
                                                onchange="toggleStockInput(this)"
                                                <?= $data->item->stock === null ? 'checked' : '' ?>>
                                            <label class="custom-control-label" for="unlimited_stock">Unlimited</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Qty per Transaction -->
                        <div class="form-group">
                            <label for="qty_per_transaction"><i class="fas fa-sort-amount-up-alt fa-sm mr-1"></i> Qty per Transaction</label>
                            <input type="number" id="qty_per_transaction" name="qty_per_transaction" class="form-control" min="0"
                                value="<?= htmlspecialchars($data->item->qty_per_transaction ?? 0) ?>" />
                            <small class="text-muted">Maximum items per transaction. Leave 0 for no limit.</small>
                        </div>

                        <!-- Discount -->
                        <div class="card mb-3" style="border:1px solid #e5e7eb;border-radius:10px">
                            <div class="card-body py-3 d-flex align-items-center justify-content-between">
                                <div>
                                    <div><i class="fas fa-tag text-warning mr-2"></i><strong>Discount</strong></div>
                                    <small class="text-muted">Offer price discounts to attract more buyers.</small>
                                </div>
                                <div class="custom-control custom-switch ml-3">
                                    <input type="checkbox" class="custom-control-input" id="has_discount" name="has_discount" value="1" <?= !empty($data->item->has_discount) ? 'checked' : '' ?>>
                                    <label class="custom-control-label" for="has_discount"></label>
                                </div>
                            </div>
                            <div class="card-footer border-0 bg-light" id="discount_price_wrapper" style="display:none; border-top:1px solid var(--gray-200)!important; border-bottom-left-radius:10px; border-bottom-right-radius:10px;">
                                <div class="form-group mb-0">
                                    <label for="discount_price">Harga Diskon (Rp)</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend"><span class="input-group-text">Rp</span></div>
                                        <input type="number" id="discount_price" name="discount_price" class="form-control" step="1" placeholder="Misal: 100000" value="<?= htmlspecialchars($data->item->discount_price ?? '') ?>" />
                                    </div>
                                    <small class="text-muted mt-1 d-block">Harga diskon harus lebih rendah dari harga normal.</small>
                                </div>
                            </div>
                        </div>

                        <!-- Flash Sale -->
                        <div class="card mb-3" style="border:1px solid #e5e7eb;border-radius:10px">
                            <div class="card-body py-3 d-flex align-items-center justify-content-between">
                                <div>
                                    <div><i class="fas fa-bolt text-warning mr-2"></i><strong>Flash Sale</strong></div>
                                    <small class="text-muted">Show flash sale badge and countdown to create purchase urgency.</small>
                                </div>
                                <div class="custom-control custom-switch ml-3">
                                    <input type="checkbox" class="custom-control-input" id="is_flash_sale" name="is_flash_sale" value="1" <?= !empty($data->item->is_flash_sale) ? 'checked' : '' ?>>
                                    <label class="custom-control-label" for="is_flash_sale"></label>
                                </div>
                            </div>
                        </div>

                        <!-- Product Type -->
                        <div class="form-group">
                            <label for="type">Product Type</label>
                            <select id="type" name="type" class="form-control">
                                <option value="download_link" <?= $data->item->type == 'download_link' ? 'selected' : '' ?>>Download Link (File/URL)</option>
                                <option value="webhook_event" <?= $data->item->type == 'webhook_event' ? 'selected' : '' ?>>Webhook Event</option>
                                <option value="random_code"   <?= $data->item->type == 'random_code'   ? 'selected' : '' ?>>Random Code (Ticket/Event)</option>
                                <option value="manual"        <?= $data->item->type == 'manual'        ? 'selected' : '' ?>>Manual Process</option>
                                <option value="physical"      <?= $data->item->type == 'physical'      ? 'selected' : '' ?>>&#x1F4E6; Produk Fisik (Pengiriman)</option>
                            </select>
                        </div>

                        <div class="form-group" id="download_links_wrapper" style="<?= $data->item->type !== 'download_link' ? 'display:none' : '' ?>">
                            <label for="download_links">File Link (Google Drive, Dropbox, etc.)</label>
                            <input type="url" id="download_links" name="download_links" class="form-control" placeholder="https://" value="<?= htmlspecialchars($data->existing_download_link) ?>" />
                            <small class="text-muted mt-1 d-block">This link will be automatically sent to the buyer's email upon successful payment.</small>
                        </div>

                        <!-- Produk Fisik: berat & dimensi -->
                        <div id="physical_fields_wrapper" style="<?= $data->item->type !== 'physical' ? 'display:none' : '' ?>">
                            <div class="alert alert-info d-flex align-items-center" style="font-size:.85rem;padding:.6rem 1rem">
                                <i class="fas fa-info-circle mr-2"></i>
                                Produk fisik dikirim via <strong>JNE, TIKI, atau POS Indonesia</strong>. Ongkos kirim dihitung otomatis saat checkout.
                            </div>
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label for="weight">Berat Produk (gram) <span class="text-danger">*</span></label>
                                        <input type="number" id="weight" name="weight" class="form-control" min="1" placeholder="500" value="<?= htmlspecialchars($data->item->weight ?? '') ?>" />
                                        <small class="text-muted">Termasuk berat kemasan</small>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="d-block">Dimensi (cm) &mdash; Opsional</label>
                                    <div class="row">
                                        <div class="col-4">
                                            <input type="number" name="length" class="form-control" placeholder="P" min="0" value="<?= htmlspecialchars($data->item->length ?? '') ?>">
                                            <small class="text-muted text-center d-block">Panjang</small>
                                        </div>
                                        <div class="col-4">
                                            <input type="number" name="width" class="form-control" placeholder="L" min="0" value="<?= htmlspecialchars($data->item->width ?? '') ?>">
                                            <small class="text-muted text-center d-block">Lebar</small>
                                        </div>
                                        <div class="col-4">
                                            <input type="number" name="height" class="form-control" placeholder="T" min="0" value="<?= htmlspecialchars($data->item->height ?? '') ?>">
                                            <small class="text-muted text-center d-block">Tinggi</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Status Active -->
                        <div class="form-group">
                            <label>Status</label>
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="status" name="status" value="1" <?= $data->item->status ? 'checked' : '' ?>>
                                <label class="custom-control-label" for="status">Active (visible in store)</label>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-4">
                        <div class="form-group">
                            <label>Foto Produk Utama</label>
                            <?php if($data->item->image): ?>
                                <div class="mb-2">
                                    <img id="current_image_preview" src="<?= \Altum\Uploads::get_full_url('shop_items') . $data->item->image ?>" class="img-fluid rounded" style="max-height: 200px; object-fit: cover; width: 100%;">
                                </div>
                                <div class="custom-control custom-checkbox mb-2">
                                    <input type="checkbox" class="custom-control-input" id="image_remove" name="image_remove">
                                    <label class="custom-control-label text-danger" for="image_remove">Hapus Foto Utama</label>
                                </div>
                            <?php else: ?>
                                <div id="image_placeholder" class="bg-light rounded d-flex align-items-center justify-content-center mb-2" style="height:150px;">
                                    <i class="fas fa-image fa-3x text-muted"></i>
                                </div>
                            <?php endif ?>
                            <input id="image" type="file" name="image" accept=".gif, .png, .jpg, .jpeg, .webp" class="form-control-file" onchange="previewImage(this, 'current_image_preview')" />
                            <small class="text-muted mt-1 d-block">Recommended size 500x500px.</small>
                        </div>

                        <div class="form-group border-top pt-3">
                            <label>Foto Produk 2</label>
                            <?php if(isset($data->item->image2) && $data->item->image2): ?>
                                <div class="mb-2">
                                    <img id="current_image2_preview" src="<?= \Altum\Uploads::get_full_url('shop_items') . $data->item->image2 ?>" class="img-fluid rounded" style="max-height: 200px; object-fit: cover; width: 100%;">
                                </div>
                                <div class="custom-control custom-checkbox mb-2">
                                    <input type="checkbox" class="custom-control-input" id="image2_remove" name="image2_remove">
                                    <label class="custom-control-label text-danger" for="image2_remove">Hapus Foto 2</label>
                                </div>
                            <?php endif ?>
                            <input id="image2" type="file" name="image2" accept=".gif, .png, .jpg, .jpeg, .webp" class="form-control-file" onchange="previewImage(this, 'current_image2_preview')" />
                        </div>

                        <div class="form-group border-top pt-3">
                            <label>Foto Produk 3</label>
                            <?php if(isset($data->item->image3) && $data->item->image3): ?>
                                <div class="mb-2">
                                    <img id="current_image3_preview" src="<?= \Altum\Uploads::get_full_url('shop_items') . $data->item->image3 ?>" class="img-fluid rounded" style="max-height: 200px; object-fit: cover; width: 100%;">
                                </div>
                                <div class="custom-control custom-checkbox mb-2">
                                    <input type="checkbox" class="custom-control-input" id="image3_remove" name="image3_remove">
                                    <label class="custom-control-label text-danger" for="image3_remove">Hapus Foto 3</label>
                                </div>
                            <?php endif ?>
                            <input id="image3" type="file" name="image3" accept=".gif, .png, .jpg, .jpeg, .webp" class="form-control-file" onchange="previewImage(this, 'current_image3_preview')" />
                        </div>

                        <div class="form-group border-top pt-3">
                            <label>Foto Produk 4</label>
                            <?php if(isset($data->item->image4) && $data->item->image4): ?>
                                <div class="mb-2">
                                    <img id="current_image4_preview" src="<?= \Altum\Uploads::get_full_url('shop_items') . $data->item->image4 ?>" class="img-fluid rounded" style="max-height: 200px; object-fit: cover; width: 100%;">
                                </div>
                                <div class="custom-control custom-checkbox mb-2">
                                    <input type="checkbox" class="custom-control-input" id="image4_remove" name="image4_remove">
                                    <label class="custom-control-label text-danger" for="image4_remove">Hapus Foto 4</label>
                                </div>
                            <?php endif ?>
                            <input id="image4" type="file" name="image4" accept=".gif, .png, .jpg, .jpeg, .webp" class="form-control-file" onchange="previewImage(this, 'current_image4_preview')" />
                        </div>
                    </div>
                </div>

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" name="submit" class="btn btn-primary mr-2">
                        <i class="fas fa-save fa-sm mr-1"></i> Save Changes
                    </button>
                    <a href="<?= url('shop') ?>" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</section>

<!-- Quill.js CSS -->
<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">

<style>
#ql-toolbar.ql-snow {
    border: 1px solid var(--gray-200);
    border-top-left-radius: var(--border-radius);
    border-top-right-radius: var(--border-radius);
    background: var(--gray-50, #f8f9fa);
    padding: 6px 10px;
}
#description_editor {
    background: var(--white);
    border: 1px solid var(--gray-200);
    border-top: none;
    border-bottom-left-radius: var(--border-radius);
    border-bottom-right-radius: var(--border-radius);
    font-family: inherit;
    font-size: .875rem;
    line-height: 1.6;
    color: var(--gray-900);
}
#description_editor:focus-within,
#ql-toolbar.ql-snow:focus-within { outline: none; }
.ql-container.ql-snow.ql-focused,
#description_editor:focus-within {
    border-color: var(--primary);
    box-shadow: 0 0 0 .2rem rgba(var(--primary-rgb, 79,70,229), .15);
}
.ql-editor { min-height: 180px; padding: 10px 14px; color: var(--gray-900); }
.ql-editor.ql-blank::before { color: var(--gray-400, #9ca3af); font-style: normal; }
.ql-editor a { color: var(--primary); text-decoration: underline; }
.ql-snow .ql-stroke { stroke: var(--gray-600); }
.ql-snow .ql-fill  { fill:   var(--gray-600); }
.ql-snow .ql-picker { color: var(--gray-700); }
.ql-snow .ql-picker-options { background: var(--white); border-color: var(--gray-200); }
.ql-snow .ql-picker-label:hover .ql-stroke,
.ql-snow .ql-toolbar button:hover .ql-stroke { stroke: var(--primary); }
.ql-snow .ql-toolbar button.ql-active .ql-stroke { stroke: var(--primary); }
.ql-snow .ql-toolbar button.ql-active .ql-fill { fill: var(--primary); }
.ql-snow .ql-tooltip {
    z-index: 9999; background: var(--white); border: 1px solid var(--gray-200);
    border-radius: var(--border-radius); color: var(--gray-800);
    box-shadow: 0 4px 16px rgba(0,0,0,.08); padding: 6px 12px;
}
.ql-snow .ql-tooltip input[type="text"] {
    background: var(--white); border: 1px solid var(--gray-300);
    border-radius: var(--border-radius); color: var(--gray-800); padding: 2px 6px;
}
[data-theme-style="dark"] #ql-toolbar.ql-snow { background: var(--gray-100); border-color: var(--gray-300); }
[data-theme-style="dark"] #description_editor { background: var(--gray-50, #1a1d20); border-color: var(--gray-300); color: var(--gray-900); }
[data-theme-style="dark"] .ql-editor { color: var(--gray-900); }
[data-theme-style="dark"] .ql-editor.ql-blank::before { color: var(--gray-500); }
[data-theme-style="dark"] .ql-snow .ql-stroke { stroke: var(--gray-700); }
[data-theme-style="dark"] .ql-snow .ql-fill  { fill: var(--gray-700); }
[data-theme-style="dark"] .ql-snow .ql-picker { color: var(--gray-800); }
[data-theme-style="dark"] .ql-snow .ql-picker-options { background: var(--gray-100); border-color: var(--gray-300); color: var(--gray-800); }
[data-theme-style="dark"] .ql-snow .ql-tooltip { background: var(--gray-100); border-color: var(--gray-300); color: var(--gray-800); box-shadow: 0 4px 16px rgba(0,0,0,.3); }
[data-theme-style="dark"] .ql-snow .ql-tooltip input[type="text"] { background: var(--gray-200); border-color: var(--gray-400); color: var(--gray-900); }
</style>

<!-- Quill.js JS -->
<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>

<script>
// Unlimited stock toggle
function toggleStockInput(cb) {
    var stockInput = document.getElementById('stock');
    if(cb.checked) {
        stockInput.value = '';
        stockInput.disabled = true;
        stockInput.placeholder = 'Unlimited';
    } else {
        stockInput.disabled = false;
        stockInput.placeholder = '0';
    }
}

// Product type toggle
function toggleProductTypeFields(type) {
    document.getElementById('download_links_wrapper').style.display  = (type === 'download_link') ? 'block' : 'none';
    document.getElementById('physical_fields_wrapper').style.display = (type === 'physical')       ? 'block' : 'none';
    var weightInput = document.getElementById('weight');
    if(weightInput) weightInput.required = (type === 'physical');
}
var typeSelect = document.getElementById('type');
typeSelect.addEventListener('change', function() { toggleProductTypeFields(this.value); });
toggleProductTypeFields(typeSelect.value);

function previewImage(input, previewId) {
    if(input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            var preview = document.getElementById(previewId);
            if(!preview) {
                preview = document.createElement('img');
                preview.id = previewId;
                preview.className = 'img-fluid rounded mb-2';
                preview.style = 'max-height:200px;object-fit:cover;width:100%';
                document.getElementById('image_placeholder') && document.getElementById('image_placeholder').replaceWith(preview);
            }
            preview.src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Init Quill
var quill = new Quill('#description_editor', {
    modules: { toolbar: '#ql-toolbar' },
    theme: 'snow'
});

(function() {
    var existing = <?= json_encode($data->item->description ?? '') ?>;
    if(existing) { quill.root.innerHTML = existing; }
})();

document.querySelector('form').addEventListener('submit', function() {
    var html = quill.root.innerHTML;
    if(html === '<p><br></p>') html = '';
    document.getElementById('description').value = html;
});

// Toggle discount price wrapper
function toggleDiscountPrice() {
    var isChecked = document.getElementById('has_discount').checked;
    document.getElementById('discount_price_wrapper').style.display = isChecked ? 'block' : 'none';
    var dpInput = document.getElementById('discount_price');
    if(dpInput) dpInput.required = isChecked;
}
document.getElementById('has_discount').addEventListener('change', toggleDiscountPrice);
toggleDiscountPrice();
</script>
