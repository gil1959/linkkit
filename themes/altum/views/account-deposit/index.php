<?php defined('ALTUMCODE') || die() ?>

<div class="container">
    <?= \Altum\Alerts::output_alerts() ?>

    <nav aria-label="breadcrumb">
        <ol class="custom-breadcrumbs small">
            <li>
                <a href="<?= url() ?>"><?= l('index.breadcrumb') ?></a><i class="fas fa-fw fa-angle-right"></i>
            </li>
            <li class="active" aria-current="page">Deposit Saldo</li>
        </ol>
    </nav>

    <h1 class="h4 text-truncate mb-4">Deposit Saldo</h1>

    <div class="card">
        <div class="card-body">
            <form action="" method="post" role="form">
                <input type="hidden" name="token" value="<?= \Altum\Csrf::get() ?>" />

                <div class="form-group">
                    <label for="amount"><i class="fas fa-fw fa-sm fa-coins text-muted mr-1"></i> Nominal Deposit</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Rp</span>
                        </div>
                        <input type="number" id="amount" name="amount" class="form-control" value="50000" min="10000" step="100" required="required" />
                    </div>
                    <small class="form-text text-muted">Minimal deposit adalah Rp 10.000</small>
                </div>

                <button type="submit" name="submit" class="btn btn-block btn-primary">Lanjut ke Pembayaran</button>
            </form>
        </div>
    </div>
</div>
