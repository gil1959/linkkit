<?php defined('ALTUMCODE') || die() ?>

<?php \Altum\Title::set('Verifikasi Email') ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-5">
            
            <div class="card border-0 shadow-sm">
                <div class="card-body p-5">
                    <h1 class="h3 mb-1 text-center">Verifikasi Email</h1>
                    <p class="text-muted text-center mb-4">Kami telah mengirimkan 6 digit OTP ke email <strong><?= htmlspecialchars($data->email) ?></strong>.</p>

                    <?= \Altum\Alerts::output_alerts() ?>

                    <form action="" method="post" role="form">
                        <input type="hidden" name="token" value="<?= \Altum\Csrf::get() ?>" />

                        <div class="form-group">
                            <label for="otp">Kode OTP</label>
                            <input id="otp" type="text" name="otp" class="form-control form-control-lg text-center" placeholder="123456" maxlength="6" required="required" autofocus="autofocus" />
                        </div>

                        <div class="form-group mt-4">
                            <button type="submit" name="submit" class="btn btn-primary btn-block btn-lg">Verifikasi</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="text-center mt-4">
                <a href="<?= url('login') ?>" class="text-muted">Kembali ke Login</a>
            </div>

        </div>
    </div>
</div>
