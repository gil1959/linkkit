<?php defined('ALTUMCODE') || die() ?>

<div>
    <?php if(false): ?>
        <div class="alert alert-primary" role="alert">
            You need to own the Extended License in order to activate the payment system.
        </div>
    <?php endif ?>

    <div class="<?= null ?>">
        <div class="alert alert-info mb-3"><?= sprintf(l('admin_settings.documentation'), '<a href="' . PRODUCT_DOCUMENTATION_URL . '#' . \Altum\Router::$method . '" target="_blank">', '</a>') ?></div>

        <div class="alert alert-warning mb-3">
            <i class="fas fa-fw fa-exclamation-triangle mr-1"></i>
            <strong>Callback URL Tripay:</strong> <code><?= SITE_URL ?>webhook-tripay</code><br>
            Masukkan URL ini di dashboard Tripay &rarr; menu <strong>Konfigurasi &rarr; Callback URL</strong>.
        </div>

        <div class="form-group custom-control custom-switch">
            <input id="is_enabled" name="is_enabled" type="checkbox" class="custom-control-input" <?= settings()->tripay->is_enabled ? 'checked="checked"' : null?>>
            <label class="custom-control-label" for="is_enabled"><?= l('admin_settings.tripay.is_enabled') ?></label>
        </div>

        <div class="form-group">
            <label for="mode"><?= l('admin_settings.payment.mode') ?></label>
            <select id="mode" name="mode" class="custom-select">
                <option value="production" <?= (settings()->tripay->mode ?? 'production') == 'production' ? 'selected="selected"' : null ?>>Production</option>
                <option value="sandbox" <?= (settings()->tripay->mode ?? 'production') == 'sandbox' ? 'selected="selected"' : null ?>>Sandbox</option>
            </select>
            <small class="form-text text-muted">Gunakan <strong>Sandbox</strong> untuk testing, <strong>Production</strong> untuk live.</small>
        </div>

        <div class="form-group">
            <label for="merchant_code"><?= l('admin_settings.tripay.merchant_code') ?></label>
            <input id="merchant_code" type="text" name="merchant_code" class="form-control" value="<?= settings()->tripay->merchant_code ?? '' ?>" placeholder="Contoh: T12345" />
            <small class="form-text text-muted">Merchant Code dari dashboard Tripay &rarr; menu <strong>Profil</strong>.</small>
        </div>

        <div class="form-group">
            <label for="api_key"><?= l('admin_settings.tripay.api_key') ?></label>
            <input id="api_key" type="text" name="api_key" class="form-control" value="<?= settings()->tripay->api_key ?? '' ?>" placeholder="API Key dari Tripay" />
            <small class="form-text text-muted">Diperoleh dari menu <strong>API & Integrasi</strong> di dashboard Tripay.</small>
        </div>

        <div class="form-group">
            <label for="private_key"><?= l('admin_settings.tripay.private_key') ?></label>
            <input id="private_key" type="password" name="private_key" class="form-control" value="<?= settings()->tripay->private_key ?? '' ?>" placeholder="Private Key dari Tripay" />
            <small class="form-text text-muted">Digunakan untuk generate signature transaksi. Jaga kerahasiaannya.</small>
        </div>

        <div class="form-group">
            <label><i class="fas fa-fw fa-sm fa-coins text-muted mr-1"></i> <?= l('admin_settings.payment.currencies') ?></label>
            <div class="row">
                <?php foreach((array) settings()->payment->currencies as $currency => $currency_data): ?>
                    <div class="col-12 col-lg-4">
                        <div class="custom-control custom-checkbox my-2">
                            <input id="<?= 'currency_' . $currency ?>" name="currencies[]" value="<?= $currency ?>" type="checkbox" class="custom-control-input" <?= in_array($currency, settings()->tripay->currencies ?? []) ? 'checked="checked"' : null ?>>
                            <label class="custom-control-label d-flex align-items-center" for="<?= 'currency_' . $currency ?>">
                                <span><?= $currency ?></span>
                            </label>
                        </div>
                    </div>
                <?php endforeach ?>
            </div>
        </div>
    </div>
</div>

<button type="submit" name="submit" class="btn btn-lg btn-block btn-primary mt-4"><?= l('global.update') ?></button>
