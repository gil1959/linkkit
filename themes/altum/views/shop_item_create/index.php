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
                            <label for="description">Product Description</label>
                            <textarea id="description" name="description" class="form-control" rows="4"></textarea>
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

<script>
    document.getElementById('type').addEventListener('change', function() {
        var dlWrapper = document.getElementById('download_links_wrapper');
        if(this.value === 'download_link') {
            dlWrapper.style.display = 'block';
        } else {
            dlWrapper.style.display = 'none';
        }
    });
</script>
