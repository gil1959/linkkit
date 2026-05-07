<?php defined('ALTUMCODE') || die() ?>

<header class="header pb-0">
    <div class="container">
        <div class="d-flex flex-column flex-lg-row align-items-center justify-content-lg-between">
            <div>
                <h1 class="h3"><i class="fas fa-fw fa-xs fa-shopping-cart text-primary-900 mr-2"></i> Shop</h1>
            </div>
        </div>
    </div>
</header>

<section class="container pt-5">
    <?= \Altum\Alerts::output_alerts() ?>

    <div class="card mb-4" style="max-width: 600px; margin: 0 auto;">
        <div class="card-body">
            <div class="text-center mb-5">
                <h2 class="h4 font-weight-bold">Create your first shop!</h2>
                <p class="text-muted">Please fill in the form below to create your first shop.</p>
            </div>

            <form action="" method="post" role="form">
                <input type="hidden" name="token" value="<?= \Altum\Csrf::get() ?>" />

                <div class="form-group">
                    <label for="name" class="font-weight-bold">Shop Title</label>
                    <input type="text" id="name" name="name" class="form-control" placeholder="Saidi Shop" required="required" />
                </div>

                <div class="form-group">
                    <label for="description" class="font-weight-bold">Shop Description</label>
                    <textarea id="description" name="description" class="form-control" rows="3" placeholder="We are selling all kind of goods..."></textarea>
                </div>

                <div class="form-group">
                    <label for="url" class="font-weight-bold">Shop URL</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><?= SITE_URL . 'shop/' ?></span>
                        </div>
                        <input type="text" id="url" name="url" class="form-control" placeholder="nfiki" required="required" />
                    </div>
                </div>

                <div class="form-group mt-4">
                    <label class="font-weight-bold">What type of items will you sell?</label>
                    <div class="row">
                        <?php 
                        $types = [
                            'E-book', 'Templates (CV, website, slides)',
                            'Online courses', 'Music & audio',
                            'Stock photos/videos', 'Fonts & design assets',
                            'Games & in-game items', 'Code & scripts',
                            'AI prompts/models', 'Other'
                        ];
                        foreach($types as $key => $type): ?>
                            <div class="col-6 mb-2">
                                <div class="custom-control custom-checkbox custom-control-inline">
                                    <input id="item_type_<?= $key ?>" name="item_types[]" type="checkbox" class="custom-control-input" value="<?= $type ?>">
                                    <label class="custom-control-label" for="item_type_<?= $key ?>"><?= $type ?></label>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <button type="submit" name="submit" class="btn btn-block btn-primary mt-5">Create Shop</button>
            </form>
        </div>
    </div>
</section>
