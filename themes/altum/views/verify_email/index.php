<?php defined('ALTUMCODE') || die() ?>

<?php \Altum\Title::set('Verifikasi Email') ?>

<?= \Altum\Alerts::output_alerts() ?>

<h1 class="h5">Verifikasi Email</h1>
<p class="text-muted">Kami telah mengirimkan 6 digit OTP ke email <strong><?= htmlspecialchars($data->email) ?></strong>.</p>

<form action="" method="post" class="mt-4" role="form">
    <input type="hidden" name="token" value="<?= \Altum\Csrf::get() ?>" />

    <div class="form-group">
        <label for="otp">Kode OTP</label>
        <input id="otp" type="text" name="otp" class="form-control form-control-lg text-center <?= \Altum\Alerts::has_field_errors('otp') ? 'is-invalid' : null ?>" placeholder="123456" maxlength="6" required="required" autofocus="autofocus" />
        <?= \Altum\Alerts::output_field_error('otp') ?>
    </div>

    <div class="form-group mt-4">
        <button type="submit" name="submit" class="btn btn-primary btn-block my-1">Verifikasi</button>
    </div>
</form>

<div class="mt-5 text-center text-muted">
    <a href="<?= url('resend-activation?email=' . urlencode($data->email)) ?>" class="font-weight-bold">Kirim Ulang Kode OTP</a>
    <br><br>
    <a href="<?= url('login') ?>" class="text-muted">Kembali ke Login</a>
</div>
