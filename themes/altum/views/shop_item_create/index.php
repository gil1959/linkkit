<?php defined('ALTUMCODE') || die() ?>

<header class="header pb-0">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="custom-breadcrumbs small">
                <li><a href="<?= url('shop') ?>">Shop</a> <i class="fas fa-fw fa-angle-right"></i></li>
                <li class="active" aria-current="page">Add Product</li>
            </ol>
        </nav>
        <h1 class="h3 mb-3">Add Product</h1>
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
                            <input type="text" id="name" name="name" class="form-control" required="required" placeholder="E.g., Complete UI Kit" />
                        </div>

                        <div class="form-group">
                            <label for="description_editor">Product Description</label>
                            <!-- Hidden input that will carry the HTML to the server -->
                            <input type="hidden" id="description" name="description" value="" />

                            <!-- Quill toolbar -->
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

                            <small class="text-muted mt-1 d-block">Supports <strong>bold</strong>, <em>italic</em>, lists, headings, and hyperlinks. No image upload.</small>
                        </div>

                        <!-- Product Listing Dropdown -->
                        <div class="form-group">
                            <label for="listing_id">Product Listing</label>
                            <select id="listing_id" name="listing_id" class="form-control">
                                <option value="">No Listing</option>
                                <?php foreach($data->listings as $listing): ?>
                                    <option value="<?= $listing->id ?>"
                                        <?= isset($data->draft['listing_id']) && $data->draft['listing_id'] == $listing->id ? 'selected' : '' ?>>
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
                                    <input type="checkbox" class="custom-control-input" id="is_flexible_amount" name="is_flexible_amount" value="1">
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
                                    <input type="checkbox" class="custom-control-input" id="has_variants" name="has_variants" value="1">
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
                                        <input type="number" id="price" name="price" class="form-control" required="required" step="1" placeholder="50000" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label>Stock</label>
                                    <div class="d-flex align-items-center">
                                        <input type="number" id="stock" name="stock" class="form-control mr-2" placeholder="0" />
                                        <div class="custom-control custom-switch" style="white-space:nowrap">
                                            <input type="checkbox" class="custom-control-input" id="unlimited_stock" onchange="toggleStockInput(this)">
                                            <label class="custom-control-label" for="unlimited_stock">Unlimited</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Qty per Transaction -->
                        <div class="form-group">
                            <label for="qty_per_transaction"><i class="fas fa-sort-amount-up-alt fa-sm mr-1"></i> Qty per Transaction</label>
                            <input type="number" id="qty_per_transaction" name="qty_per_transaction" class="form-control" min="0" value="0" placeholder="0" />
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
                                    <input type="checkbox" class="custom-control-input" id="has_discount" name="has_discount" value="1" <?= isset($data->draft['has_discount']) && $data->draft['has_discount'] ? 'checked' : '' ?>>
                                    <label class="custom-control-label" for="has_discount"></label>
                                </div>
                            </div>
                            <div class="card-footer border-0" id="discount_price_wrapper" style="display:none; background:#f9fafb; border-top:1px solid #e5e7eb!important; border-bottom-left-radius:10px; border-bottom-right-radius:10px;">
                                <div class="form-group mb-0">
                                    <label for="discount_price">Harga Diskon (Rp)</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend"><span class="input-group-text">Rp</span></div>
                                        <input type="number" id="discount_price" name="discount_price" class="form-control" step="1" placeholder="Misal: 100000" value="<?= htmlspecialchars($data->draft['discount_price'] ?? '') ?>" />
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
                                    <input type="checkbox" class="custom-control-input" id="is_flash_sale" name="is_flash_sale" value="1">
                                    <label class="custom-control-label" for="is_flash_sale"></label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="type">Product Type</label>
                            <select id="type" name="type" class="form-control">
                                <option value="download_link" <?= isset($data->draft['type']) && $data->draft['type']=='download_link' ? 'selected' : '' ?>>Download Link (File/URL)</option>
                                <option value="webhook_event" <?= isset($data->draft['type']) && $data->draft['type']=='webhook_event' ? 'selected' : '' ?>>Webhook Event</option>
                                <option value="random_code"   <?= isset($data->draft['type']) && $data->draft['type']=='random_code'   ? 'selected' : '' ?>>Random Code (Ticket/Event)</option>
                                <option value="manual"        <?= isset($data->draft['type']) && $data->draft['type']=='manual'        ? 'selected' : '' ?>>Manual Process</option>
                                <option value="physical"      <?= isset($data->draft['type']) && $data->draft['type']=='physical'      ? 'selected' : '' ?>>📦 Produk Fisik (Pengiriman)</option>
                            </select>
                            <small class="text-muted mt-1 d-block">Determine how the product is delivered after payment.</small>
                        </div>

                        <div class="form-group" id="download_links_wrapper">
                            <label for="download_links">File Link (Google Drive, Dropbox, etc.)</label>
                            <input type="url" id="download_links" name="download_links" class="form-control" placeholder="https://" />
                            <small class="text-muted mt-1 d-block">This link will be automatically sent to the buyer's email upon successful payment.</small>
                        </div>

                        <!-- Produk Fisik: berat & dimensi -->
                        <div id="physical_fields_wrapper" style="display:none">
                            <div class="alert alert-info d-flex align-items-center" style="font-size:.85rem;padding:.6rem 1rem">
                                <i class="fas fa-info-circle mr-2"></i>
                                Produk fisik dikirim via <strong>JNE, TIKI, atau POS Indonesia</strong>. Ongkos kirim dihitung otomatis saat checkout.
                            </div>
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label for="weight">Berat Produk (gram) <span class="text-danger">*</span></label>
                                        <input type="number" id="weight" name="weight" class="form-control" min="1" placeholder="500" value="<?= htmlspecialchars($data->draft['weight'] ?? '') ?>" />
                                        <small class="text-muted">Termasuk berat kemasan</small>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="d-block">Dimensi (cm) — Opsional</label>
                                    <div class="row">
                                        <div class="col-4">
                                            <input type="number" name="length" class="form-control" placeholder="P" min="0" value="<?= htmlspecialchars($data->draft['length'] ?? '') ?>">
                                            <small class="text-muted text-center d-block">Panjang</small>
                                        </div>
                                        <div class="col-4">
                                            <input type="number" name="width" class="form-control" placeholder="L" min="0" value="<?= htmlspecialchars($data->draft['width'] ?? '') ?>">
                                            <small class="text-muted text-center d-block">Lebar</small>
                                        </div>
                                        <div class="col-4">
                                            <input type="number" name="height" class="form-control" placeholder="T" min="0" value="<?= htmlspecialchars($data->draft['height'] ?? '') ?>">
                                            <small class="text-muted text-center d-block">Tinggi</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    
                    <div class="col-12 col-md-4">
                        <div class="form-group">
                            <label for="image">Foto Produk Utama</label>
                            <input id="image" type="file" name="image" accept=".gif, .png, .jpg, .jpeg, .svg, .webp" class="form-control-file" />
                            <small class="text-muted mt-1 d-block">Recommended size 500x500px.</small>
                        </div>
                        <div class="form-group">
                            <label for="image2">Foto Produk 2</label>
                            <input id="image2" type="file" name="image2" accept=".gif, .png, .jpg, .jpeg, .svg, .webp" class="form-control-file" />
                        </div>
                        <div class="form-group">
                            <label for="image3">Foto Produk 3</label>
                            <input id="image3" type="file" name="image3" accept=".gif, .png, .jpg, .jpeg, .svg, .webp" class="form-control-file" />
                        </div>
                        <div class="form-group">
                            <label for="image4">Foto Produk 4</label>
                            <input id="image4" type="file" name="image4" accept=".gif, .png, .jpg, .jpeg, .svg, .webp" class="form-control-file" />
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" name="submit" class="btn btn-block btn-primary">Add Product</button>
                </div>
            </form>
        </div>
    </div>
</section>

<!-- Quill.js CSS -->
<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">

<style>
/* ── Quill Editor — integrated with app theme ── */

/* Toolbar */
#ql-toolbar.ql-snow {
    border: 1px solid var(--gray-200);
    border-top-left-radius: var(--border-radius);
    border-top-right-radius: var(--border-radius);
    background: var(--gray-50, #f8f9fa);
    padding: 6px 10px;
}

/* Editor area */
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
    transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out;
}

/* Focus ring — match Bootstrap form-control */
#description_editor:focus-within,
#ql-toolbar.ql-snow:focus-within {
    outline: none;
}
.ql-container.ql-snow.ql-focused,
#description_editor:focus-within {
    border-color: var(--primary);
    box-shadow: 0 0 0 .2rem rgba(var(--primary-rgb, 79,70,229), .15);
}
#ql-toolbar.ql-snow:has(+ #description_editor:focus-within),
#ql-toolbar.ql-snow:focus-within {
    border-color: var(--primary);
}

.ql-editor {
    min-height: 180px;
    padding: 10px 14px;
    color: var(--gray-900);
}

.ql-editor.ql-blank::before {
    color: var(--gray-400, #9ca3af);
    font-style: normal;
}

.ql-editor a {
    color: var(--primary);
    text-decoration: underline;
}

/* Toolbar icons */
.ql-snow .ql-stroke { stroke: var(--gray-600); }
.ql-snow .ql-fill  { fill:   var(--gray-600); }
.ql-snow .ql-picker { color:  var(--gray-700); }
.ql-snow .ql-picker-options { background: var(--white); border-color: var(--gray-200); }
.ql-snow .ql-picker-label:hover .ql-stroke,
.ql-snow .ql-toolbar button:hover .ql-stroke { stroke: var(--primary); }
.ql-snow .ql-toolbar button.ql-active .ql-stroke,
.ql-snow .ql-toolbar .ql-picker-label.ql-active .ql-stroke { stroke: var(--primary); }
.ql-snow .ql-toolbar button.ql-active .ql-fill { fill: var(--primary); }

/* Tooltip (link insert box) */
.ql-snow .ql-tooltip {
    z-index: 9999;
    background: var(--white);
    border: 1px solid var(--gray-200);
    border-radius: var(--border-radius);
    color: var(--gray-800);
    box-shadow: 0 4px 16px rgba(0,0,0,.08);
    padding: 6px 12px;
}
.ql-snow .ql-tooltip input[type="text"] {
    background: var(--white);
    border: 1px solid var(--gray-300);
    border-radius: var(--border-radius);
    color: var(--gray-800);
    padding: 2px 6px;
}

/* ── DARK MODE overrides ── */
[data-theme-style="dark"] #ql-toolbar.ql-snow {
    background: var(--gray-100);
    border-color: var(--gray-300);
}
[data-theme-style="dark"] #description_editor {
    background: var(--gray-50, #1a1d20);
    border-color: var(--gray-300);
    color: var(--gray-900);
}
[data-theme-style="dark"] .ql-editor {
    color: var(--gray-900);
}
[data-theme-style="dark"] .ql-editor.ql-blank::before {
    color: var(--gray-500);
}
[data-theme-style="dark"] .ql-snow .ql-stroke { stroke: var(--gray-700); }
[data-theme-style="dark"] .ql-snow .ql-fill  { fill:   var(--gray-700); }
[data-theme-style="dark"] .ql-snow .ql-picker { color:  var(--gray-800); }
[data-theme-style="dark"] .ql-snow .ql-picker-options {
    background: var(--gray-100);
    border-color: var(--gray-300);
    color: var(--gray-800);
}
[data-theme-style="dark"] .ql-snow .ql-tooltip {
    background: var(--gray-100);
    border-color: var(--gray-300);
    color: var(--gray-800);
    box-shadow: 0 4px 16px rgba(0,0,0,.3);
}
[data-theme-style="dark"] .ql-snow .ql-tooltip input[type="text"] {
    background: var(--gray-200);
    border-color: var(--gray-400);
    color: var(--gray-900);
}
</style>

<!-- Quill.js JS -->
<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>

<script>
    // Init Quill
    var quill = new Quill('#description_editor', {
        modules: { toolbar: '#ql-toolbar' },
        theme: 'snow'
    });

    <?php if(!empty($data->draft['description'])): ?>
    quill.root.innerHTML = <?= json_encode($data->draft['description']) ?>;
    <?php endif; ?>

    // Inject HTML into hidden input before form submit
    document.querySelector('form').addEventListener('submit', function() {
        var html = quill.root.innerHTML;
        if(html === '<p><br></p>') html = '';
        document.getElementById('description').value = html;
    });

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
        document.getElementById('download_links_wrapper').style.display    = (type === 'download_link') ? 'block' : 'none';
        document.getElementById('physical_fields_wrapper').style.display   = (type === 'physical')       ? 'block' : 'none';
        // weight required only for physical
        var weightInput = document.getElementById('weight');
        if(weightInput) weightInput.required = (type === 'physical');
    }

    var typeSelect = document.getElementById('type');
    typeSelect.addEventListener('change', function() {
        toggleProductTypeFields(this.value);
    });
    // Init on load (handles draft restore)
    toggleProductTypeFields(typeSelect.value);

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
