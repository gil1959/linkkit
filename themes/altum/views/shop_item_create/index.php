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

                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="price">Price (Rp)</label>
                                    <input type="number" id="price" name="price" class="form-control" required="required" step="1" placeholder="50000" />
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="stock">Stock (Leave empty for unlimited)</label>
                                    <input type="number" id="stock" name="stock" class="form-control" />
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="type">Product Type</label>
                            <select id="type" name="type" class="form-control">
                                <option value="download_link">Download Link (File/URL)</option>
                                <option value="webhook_event">Webhook Event</option>
                                <option value="random_code">Random Code (Ticket/Event)</option>
                                <option value="manual">Manual Process</option>
                            </select>
                            <small class="text-muted mt-1 d-block">Determine how the product is delivered after payment.</small>
                        </div>

                        <div class="form-group" id="download_links_wrapper">
                            <label for="download_links">File Link (Google Drive, Dropbox, etc.)</label>
                            <input type="url" id="download_links" name="download_links" class="form-control" placeholder="https://" />
                            <small class="text-muted mt-1 d-block">This link will be automatically sent to the buyer's email upon successful payment.</small>
                        </div>

                    </div>
                    
                    <div class="col-12 col-md-4">
                        <div class="form-group">
                            <label for="image">Product Image</label>
                            <input id="image" type="file" name="image" accept=".gif, .png, .jpg, .jpeg, .svg" class="form-control-file" />
                            <small class="text-muted mt-1 d-block">Recommended size 500x500px.</small>
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

    // Inject HTML into hidden input before form submit
    document.querySelector('form').addEventListener('submit', function() {
        var html = quill.root.innerHTML;
        // Treat empty editor as empty string
        if(html === '<p><br></p>') html = '';
        document.getElementById('description').value = html;
    });

    // Product type toggle
    document.getElementById('type').addEventListener('change', function() {
        var dlWrapper = document.getElementById('download_links_wrapper');
        dlWrapper.style.display = (this.value === 'download_link') ? 'block' : 'none';
    });
</script>
