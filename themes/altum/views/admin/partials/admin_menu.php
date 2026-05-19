<?php defined('ALTUMCODE') || die() ?>

<div class="p-3 mt-3 p-lg-0 mt-lg-0">
    <nav class="navbar navbar-expand-lg navbar-light rounded-2x border border-gray-100 admin-navbar-top">
        <div
            class="navbar-brand text-truncate"
            data-logo
            data-light-value="<?= settings()->main->logo_light != '' ? settings()->main->logo_light_full_url : settings()->main->title ?>"
            data-light-class="<?= settings()->main->logo_light != '' ? 'img-fluid admin-navbar-logo-top' : '' ?>"
            data-light-tag="<?= settings()->main->logo_light != '' ? 'img' : 'span' ?>"
            data-dark-value="<?= settings()->main->logo_dark != '' ? settings()->main->logo_dark_full_url : settings()->main->title ?>"
            data-dark-class="<?= settings()->main->logo_dark != '' ? 'img-fluid admin-navbar-logo-top' : '' ?>"
            data-dark-tag="<?= settings()->main->logo_dark != '' ? 'img' : 'span' ?>"

            id="sidebar_title"
            tabindex="0"
            data-toggle="tooltip"
            data-placement="right"
            data-html="true"
            data-trigger="hover"
            data-delay='{ "hide": 5500 }'
            title="
            <div class='d-flex text-left flex-column'>
                <div class='mb-2'><a href='<?= url() ?>' class='text-gray-50 text-decoration-none'>🌐 &nbsp; <?= l('index.menu') ?></a></div>
                <div><a href='<?= url('dashboard') ?>' class='text-gray-50 text-decoration-none'>🖥️ &nbsp; <?= l('dashboard.menu') ?></a></div>
            </div>
            "
        >
            <?php if(settings()->main->{'logo_' . \Altum\ThemeStyle::get()} != ''): ?>
                <img src="<?= settings()->main->{'logo_' . \Altum\ThemeStyle::get() . '_full_url'} ?>" class="img-fluid admin-navbar-logo-top" alt="<?= l('global.accessibility.logo_alt') ?>" />
            <?php else: ?>
                <span><?= settings()->main->title ?></span>
            <?php endif ?>
        </div>

        <ul class="navbar-nav ml-auto d-flex align-items-center">
            <?php if(settings()->internal_notifications->admins_is_enabled): ?>
            <li class="nav-item dropdown mr-2" id="admin_bell_notifications">
                <a id="admin_bell_link" href="#" class="nav-link position-relative p-2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Notifikasi Admin">
                    <i class="fas fa-fw fa-bell"></i>
                    <span id="admin_bell_badge" class="badge badge-danger badge-pill position-absolute" style="top:4px;right:4px;min-width:16px;height:16px;padding:2px 4px;font-size:10px;display:none;">0</span>
                </a>
                <div id="admin_bell_content" class="dropdown-menu dropdown-menu-right shadow p-0" style="width:380px;max-width:95vw;border-radius:12px;overflow:hidden;">
                    <div class="px-3 py-2 border-bottom d-flex align-items-center justify-content-between bg-light">
                        <span class="font-weight-bold small"><i class="fas fa-bell mr-1 text-primary"></i> Notifikasi Admin</span>
                        <a href="<?= url('admin/internal-notifications') ?>" class="small text-muted">Lihat semua</a>
                    </div>
                    <div id="admin_bell_list" class="py-1" style="max-height:360px;overflow-y:auto;">
                        <div class="text-center text-muted py-3 small" id="admin_bell_loading"><i class="fas fa-circle-notch fa-spin mr-1"></i> Memuat...</div>
                    </div>
                </div>
            </li>
            <?php endif ?>
            <button class="btn navbar-custom-toggler" type="button" id="admin_menu_toggler" aria-controls="main_navbar" aria-expanded="false" aria-label="<?= l('global.accessibility.toggle_navigation') ?>">
                <i class="fas fa-fw fa-bars"></i>
            </button>
        </ul>

        <?php if(settings()->internal_notifications->admins_is_enabled): ?>
        <?php ob_start() ?>
        <script>
        'use strict';
        (function() {
            let bell_fetched = false;
            let unread_count = 0;

            /* Check unread count on load via polling */
            async function check_admin_notifications_count() {
                try {
                    let res = await fetch(`${url}admin/index/get_notifications_ajax`, { method: 'get' });
                    if (!res.ok) return;
                    let data = await res.json();
                    if (data.status !== 'success') return;

                    let notifs = data.details.internal_notifications || [];
                    unread_count = notifs.filter(n => !n.is_read).length;

                    let badge = document.querySelector('#admin_bell_badge');
                    if (badge) {
                        if (unread_count > 0) {
                            badge.textContent = unread_count > 9 ? '9+' : unread_count;
                            badge.style.display = 'inline-block';
                        } else {
                            badge.style.display = 'none';
                        }
                    }
                } catch(e) {}
            }

            /* Poll every 60 seconds */
            check_admin_notifications_count();
            setInterval(check_admin_notifications_count, 60000);

            /* Load notifications when dropdown opens */
            let bell_dropdown = document.querySelector('#admin_bell_notifications');
            if (bell_dropdown) {
                bell_dropdown.addEventListener('show.bs.dropdown', async function() {
                    if (bell_fetched) return;
                    bell_fetched = true;

                    try {
                        let res = await fetch(`${url}admin/index/get_notifications_ajax`, { method: 'get' });
                        if (!res.ok) throw new Error();
                        let data = await res.json();
                        if (data.status !== 'success') throw new Error();

                        let notifs = data.details.internal_notifications || [];
                        let list_el = document.querySelector('#admin_bell_list');

                        /* Hide badge after opening */
                        let badge = document.querySelector('#admin_bell_badge');
                        if (badge) badge.style.display = 'none';

                        if (!notifs.length) {
                            list_el.innerHTML = '<div class="text-center text-muted py-4 small"><i class="fas fa-check-circle mr-1 text-success"></i> Tidak ada notifikasi</div>';
                            return;
                        }

                        list_el.innerHTML = notifs.map(function(n) {
                            let icon = n.icon || 'fas fa-bell';
                            let unread_class = n.is_read ? '' : 'bg-primary-50';
                            let url_html = n.url ? `<a href="${n.url}" class="stretched-link"></a>` : '';
                            return `
                                <div class="px-3 py-2 border-bottom position-relative ${unread_class}" style="cursor:${n.url?'pointer':'default'}">
                                    <div class="d-flex align-items-start">
                                        <div class="mr-2 mt-1 flex-shrink-0">
                                            <span class="badge badge-light p-2"><i class="${icon} text-primary-900"></i></span>
                                        </div>
                                        <div class="flex-fill min-width-0">
                                            <div class="font-weight-bold small text-truncate">${n.title || ''}</div>
                                            <div class="text-muted" style="font-size:11px;">${n.description || ''}</div>
                                            <div class="text-muted" style="font-size:10px;margin-top:2px;">${n.datetime || ''}</div>
                                        </div>
                                    </div>
                                    ${url_html}
                                </div>
                            `;
                        }).join('');
                    } catch(e) {
                        document.querySelector('#admin_bell_list').innerHTML = '<div class="text-center text-muted py-3 small text-danger"><i class="fas fa-exclamation-circle mr-1"></i> Gagal memuat</div>';
                    }
                });

                /* Reset cache when dropdown closes so next open re-fetches */
                bell_dropdown.addEventListener('hidden.bs.dropdown', function() {
                    bell_fetched = false;
                });
            }
        })();
        </script>
        <?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>
        <?php endif ?>
    </nav>
</div>
