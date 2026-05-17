<?php defined('ALTUMCODE') || die() ?>

<div>
    <h2 class="h4">Pengaturan RajaOngkir</h2>
    <p class="text-muted">Konfigurasi API RajaOngkir untuk menghitung ongkos kirim secara otomatis.</p>

    <div class="form-group custom-control custom-switch">
        <input id="is_enabled" name="is_enabled" type="checkbox" class="custom-control-input" <?= settings()->rajaongkir->is_enabled ? 'checked="checked"' : null ?>>
        <label class="custom-control-label" for="is_enabled">Aktifkan RajaOngkir</label>
    </div>

    <div class="form-group">
        <label for="api_key"><i class="fas fa-fw fa-sm fa-key text-muted mr-1"></i> API Key</label>
        <input id="api_key" type="text" name="api_key" class="form-control" value="<?= settings()->rajaongkir->api_key ?? '' ?>" />
        <small class="form-text text-muted">Dapatkan API Key di website resmi <a href="https://rajaongkir.com/" target="_blank">RajaOngkir</a> atau partner komerce.</small>
    </div>

    <hr class="my-4">

    <h2 class="h5">Pilihan Ekspedisi</h2>
    <p class="text-muted">Pilih kurir yang ingin Anda aktifkan untuk pelanggan. Pastikan tipe akun API Anda mendukung kurir yang dipilih.</p>

    <div class="row">
        <?php
        $basic_couriers = [
            'jne' => 'JNE (Jalur Nugraha Ekakurir)',
            'tiki' => 'TIKI (Citra Van Titipan Kilat)',
            'pos' => 'POS Indonesia'
        ];
        
        $pro_couriers = [
            'rpx' => 'RPX Holding',
            'pandu' => 'Pandu Logistics',
            'wahana' => 'Wahana Prestasi Logistik',
            'sicepat' => 'SiCepat Express',
            'jnt' => 'J&T Express',
            'pahala' => 'Pahala Kencana Express',
            'sap' => 'SAP Express',
            'jet' => 'JET Express',
            'indah' => 'Indah Logistic',
            'dse' => '21 Express (DSE)',
            'slis' => 'Solusi Ekspres',
            'first' => 'First Logistics',
            'ncs' => 'Nusantara Card Semesta',
            'star' => 'Star Cargo',
            'ninja' => 'Ninja Xpress',
            'lion' => 'Lion Parcel',
            'idl' => 'IDL Cargo',
            'rex' => 'REX Express',
            'ide' => 'ID Express',
            'sentral' => 'Sentral Cargo',
            'anteraja' => 'AnterAja',
            'jxl' => 'J-Express'
        ];

        $active_couriers = settings()->rajaongkir->couriers ?? ['jne', 'tiki', 'pos'];

        foreach($basic_couriers as $code => $name):
            $is_checked = in_array($code, $active_couriers) ? 'checked="checked"' : '';
        ?>
        <div class="col-12 col-md-6 mb-3">
            <div class="form-group custom-control custom-switch m-0">
                <input id="courier_<?= $code ?>" name="couriers[]" type="checkbox" value="<?= $code ?>" class="custom-control-input" <?= $is_checked ?>>
                <label class="custom-control-label" for="courier_<?= $code ?>"><strong><?= strtoupper($code) ?></strong> <small class="text-muted">- <?= $name ?></small></label>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <hr class="my-4">

    <h2 class="h5">Ekspedisi Tambahan (Paket Pro)</h2>
    <div class="alert alert-info mt-2">
        <i class="fas fa-fw fa-info-circle mr-1"></i> <strong>Pemberitahuan Penting:</strong> Untuk menggunakan ekspedisi di bawah ini, pastikan akun RajaOngkir Anda sudah berlangganan <strong>Paket Pro</strong>. Jika Anda menggunakan paket Starter/Basic, mengaktifkan opsi di bawah dapat menyebabkan perhitungan ongkir gagal.
    </div>

    <div class="row">
        <?php
        foreach($pro_couriers as $code => $name):
            $is_checked = in_array($code, $active_couriers) ? 'checked="checked"' : '';
        ?>
        <div class="col-12 col-md-6 mb-3">
            <div class="form-group custom-control custom-switch m-0">
                <input id="courier_<?= $code ?>" name="couriers[]" type="checkbox" value="<?= $code ?>" class="custom-control-input" <?= $is_checked ?>>
                <label class="custom-control-label" for="courier_<?= $code ?>"><strong><?= strtoupper($code) ?></strong> <small class="text-muted">- <?= $name ?></small></label>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <button type="submit" name="submit" class="btn btn-lg btn-block btn-primary mt-4"><?= l('global.update') ?></button>
</div>
