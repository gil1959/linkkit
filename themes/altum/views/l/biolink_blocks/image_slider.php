<?php defined('ALTUMCODE') || die() ?>

<?php
if(!\Altum\Event::exists_content_type_key('javascript', 'splide')) {
    \Altum\Event::add_content('<script src="' . ASSETS_FULL_URL . 'js/libraries/splide.min.js"></script>', 'javascript', 'splide');
}
if(!\Altum\Event::exists_content_type_key('head', 'splide')) {
    \Altum\Event::add_content('<link href="' . ASSETS_FULL_URL . 'css/libraries/splide.min.css" rel="stylesheet" media="screen,print">', 'head', 'splide');
}
?>

<div id="<?= 'biolink_block_id_' . $data->link->biolink_block_id ?>" data-biolink-block-id="<?= $data->link->biolink_block_id ?>" class="biolink-block-image-slider <?= $data->link->design->link_class ?>" style="<?= $data->link->design->link_style ?>">
    <div class="splide" id="splide_<?= $data->link->biolink_block_id ?>">
        <div class="splide__track" style="border-radius: inherit;">
            <ul class="splide__list">
                <?php foreach($data->link->settings->items as $item): ?>
                    <?php $image_src = !empty($item->image_url) ? $item->image_url : \Altum\Uploads::get_full_url('block_images') . $item->image; ?>
                    <li class="splide__slide">
                        <?php if($item->location_url): ?>
                            <a href="<?= $item->location_url ?>" data-location-url="<?= $item->location_url ?>" <?= $data->link->settings->open_in_new_tab ? 'target="_blank"' : '' ?>>
                                <img src="<?= $image_src ?>" alt="<?= $item->image_alt ?>" class="img-fluid w-100" style="height: <?= $data->link->settings->width_height ?>rem; object-fit: cover;">
                            </a>
                        <?php else: ?>
                            <img src="<?= $image_src ?>" alt="<?= $item->image_alt ?>" class="img-fluid w-100" style="height: <?= $data->link->settings->width_height ?>rem; object-fit: cover;">
                        <?php endif ?>
                    </li>
                <?php endforeach ?>
            </ul>
        </div>
    </div>
</div>

<?php ob_start() ?>
<script>
    'use strict';
    
    document.addEventListener('DOMContentLoaded', function() {
        if(typeof Splide !== 'undefined') {
            new Splide('#splide_<?= $data->link->biolink_block_id ?>', {
                type: 'loop',
                perPage: <?= $data->link->settings->display_multiple ? 2 : 1 ?>,
                gap: '<?= $data->link->settings->gap ?>rem',
                autoplay: <?= $data->link->settings->autoplay ? 'true' : 'false' ?>,
                interval: <?= ($data->link->settings->autoplay_interval ?? 5) * 1000 ?>,
                arrows: <?= $data->link->settings->display_arrows ? 'true' : 'false' ?>,
                pagination: <?= $data->link->settings->display_pagination ? 'true' : 'false' ?>,
                breakpoints: {
                    768: {
                        perPage: 1,
                    }
                }
            }).mount();
        }
    });
</script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript', 'splide_' . $data->link->biolink_block_id) ?>
