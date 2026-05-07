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
                            <label for="description">Product Description</label>
                            <textarea id="description" name="description" class="form-control" rows="4"><?= htmlspecialchars($data->item->description) ?></textarea>
                        </div>

                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="price">Price (Rp)</label>
                                    <input type="number" id="price" name="price" class="form-control" required step="1" value="<?= $data->item->price ?>" />
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="stock">Stock (Leave empty for unlimited)</label>
                                    <input type="number" id="stock" name="stock" class="form-control" value="<?= $data->item->stock ?>" />
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="type">Product Type</label>
                            <select id="type" name="type" class="form-control">
                                <option value="download_link" <?= $data->item->type == 'download_link' ? 'selected' : '' ?>>Download Link (File/URL)</option>
                                <option value="webhook_event" <?= $data->item->type == 'webhook_event' ? 'selected' : '' ?>>Webhook Event</option>
                                <option value="random_code" <?= $data->item->type == 'random_code' ? 'selected' : '' ?>>Random Code (Ticket/Event)</option>
                                <option value="manual" <?= $data->item->type == 'manual' ? 'selected' : '' ?>>Manual Process</option>
                            </select>
                        </div>

                        <div class="form-group" id="download_links_wrapper" style="<?= $data->item->type !== 'download_link' ? 'display:none' : '' ?>">
                            <label for="download_links">File Link (Google Drive, Dropbox, etc.)</label>
                            <input type="url" id="download_links" name="download_links" class="form-control" placeholder="https://" value="<?= htmlspecialchars($data->existing_download_link) ?>" />
                            <small class="text-muted mt-1 d-block">This link will be automatically sent to the buyer's email upon successful payment.</small>
                        </div>

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
                            <label>Current Image</label>
                            <?php if($data->item->image): ?>
                                <div class="mb-2">
                                    <img id="current_image_preview" src="<?= \Altum\Uploads::get_full_url('shop_items') . $data->item->image ?>" class="img-fluid rounded" style="max-height: 200px; object-fit: cover; width: 100%;">
                                </div>
                                <div class="custom-control custom-checkbox mb-2">
                                    <input type="checkbox" class="custom-control-input" id="image_remove" name="image_remove">
                                    <label class="custom-control-label text-danger" for="image_remove">Remove image</label>
                                </div>
                            <?php else: ?>
                                <div id="image_placeholder" class="bg-light rounded d-flex align-items-center justify-content-center mb-2" style="height:150px;">
                                    <i class="fas fa-image fa-3x text-muted"></i>
                                </div>
                            <?php endif ?>
                            <label for="image" class="mt-1">Replace Image</label>
                            <input id="image" type="file" name="image" accept=".gif, .png, .jpg, .jpeg, .webp" class="form-control-file" onchange="previewImage(this, 'current_image_preview')" />
                            <small class="text-muted mt-1 d-block">Recommended size 500x500px.</small>
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

<script>
document.getElementById('type').addEventListener('change', function() {
    document.getElementById('download_links_wrapper').style.display = this.value === 'download_link' ? 'block' : 'none';
});

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
</script>
