<?php defined('ALTUMCODE') || die() ?>
<!DOCTYPE html>
<html lang="<?= \Altum\Language::$code ?>" dir="<?= l('direction') ?>">
<head>
    <title><?= \Altum\Title::get() ?></title>
    <base href="<?= SITE_URL ?>">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="<?= \Altum\Meta::$description ?>">
    <link href="<?= !empty(settings()->main->favicon) ? settings()->main->favicon_full_url : 'data:,' ?>" rel="icon">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="<?= ASSETS_FULL_URL . 'css/' . \Altum\ThemeStyle::get_file() . '?v=' . PRODUCT_CODE ?>" rel="stylesheet">
    <link href="<?= ASSETS_FULL_URL ?>css/custom.min.css?v=<?= PRODUCT_CODE ?>" rel="stylesheet">
    <?= \Altum\Event::get_content('head') ?>
</head>
<body>
    <?= $this->views['content'] ?>

    <script src="<?= ASSETS_FULL_URL ?>js/libraries/jquery.min.js?v=<?= PRODUCT_CODE ?>"></script>
    <script src="<?= ASSETS_FULL_URL ?>js/libraries/popper.min.js?v=<?= PRODUCT_CODE ?>"></script>
    <script src="<?= ASSETS_FULL_URL ?>js/libraries/bootstrap.min.js?v=<?= PRODUCT_CODE ?>"></script>
    <script src="<?= ASSETS_FULL_URL ?>js/libraries/fontawesome.min.js?v=<?= PRODUCT_CODE ?>" defer></script>
    <script src="<?= ASSETS_FULL_URL ?>js/libraries/fontawesome-solid.min.js?v=<?= PRODUCT_CODE ?>" defer></script>
    <?= \Altum\Event::get_content('javascript') ?>
</body>
</html>
