<?php defined('ALTUMCODE') || die() ?>

<div class="d-flex justify-content-between mb-4">
    <h1 class="h3 mb-0 text-truncate">
        <i class="fas fa-fw fa-id-card text-primary mr-2"></i> Verifikasi Seller KTP
    </h1>
</div>

<?= \Altum\Alerts::output_alerts() ?>

<div class="row mb-4">
    <div class="col-12 col-md-4 mb-3 mb-md-0">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-body d-flex flex-column justify-content-center">
                <div class="text-muted font-weight-bold mb-1">Menunggu Review</div>
                <div class="h3 font-weight-bolder mb-0 text-warning"><?= $data->cnt['pending'] ?></div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4 mb-3 mb-md-0">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-body d-flex flex-column justify-content-center">
                <div class="text-muted font-weight-bold mb-1">Disetujui</div>
                <div class="h3 font-weight-bolder mb-0 text-success"><?= $data->cnt['verified'] ?></div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-body d-flex flex-column justify-content-center">
                <div class="text-muted font-weight-bold mb-1">Ditolak</div>
                <div class="h3 font-weight-bolder mb-0 text-danger"><?= $data->cnt['rejected'] ?></div>
            </div>
        </div>
    </div>
</div>

<div class="d-flex align-items-center mb-3">
    <div class="nav nav-custom">
        <a href="<?= url('admin/shop-verifications?status=pending') ?>" class="nav-link <?= $data->filter === 'pending' ? 'active' : '' ?>">Pending (<?= $data->cnt['pending'] ?>)</a>
        <a href="<?= url('admin/shop-verifications?status=verified') ?>" class="nav-link <?= $data->filter === 'verified' ? 'active' : '' ?>">Verified (<?= $data->cnt['verified'] ?>)</a>
        <a href="<?= url('admin/shop-verifications?status=rejected') ?>" class="nav-link <?= $data->filter === 'rejected' ? 'active' : '' ?>">Ditolak (<?= $data->cnt['rejected'] ?>)</a>
        <a href="<?= url('admin/shop-verifications?status=all') ?>" class="nav-link <?= $data->filter === 'all' ? 'active' : '' ?>">Semua</a>
    </div>
</div>

<div class="table-responsive table-custom-container">
    <table class="table table-custom">
        <thead>
        <tr>
            <th>#</th>
            <th>Seller</th>
            <th>NIK</th>
            <th>Dokumen</th>
            <th>Diajukan</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
        </thead>
        <tbody>
        <?php if(count($data->verifications)): ?>
            <?php foreach($data->verifications as $v): ?>
                <tr>
                    <td class="text-muted">#<?= $v->id ?></td>
                    <td>
                        <div class="d-flex flex-column">
                            <span class="font-weight-bold"><?= htmlspecialchars($v->full_name) ?></span>
                            <small class="text-muted"><?= htmlspecialchars($v->email) ?></small>
                            <small class="text-primary">@<?= htmlspecialchars($v->user_name) ?></small>
                        </div>
                    </td>
                    <td><code class="font-weight-bold"><?= htmlspecialchars($v->nik) ?></code></td>
                    <td>
                        <div class="d-flex align-items-center">
                            <a href="<?= UPLOADS_FULL_URL . 'shop_verifications/ktp/' . htmlspecialchars($v->ktp_image) ?>" target="_blank" class="mr-2" data-toggle="tooltip" title="Lihat Foto KTP">
                                <img src="<?= UPLOADS_FULL_URL . 'shop_verifications/ktp/' . htmlspecialchars($v->ktp_image) ?>" class="img-fluid rounded" style="width: 45px; height: 35px; object-fit: cover;" alt="KTP" />
                            </a>
                            <a href="<?= UPLOADS_FULL_URL . 'shop_verifications/selfie/' . htmlspecialchars($v->selfie_image) ?>" target="_blank" data-toggle="tooltip" title="Lihat Foto Selfie">
                                <img src="<?= UPLOADS_FULL_URL . 'shop_verifications/selfie/' . htmlspecialchars($v->selfie_image) ?>" class="img-fluid rounded" style="width: 45px; height: 35px; object-fit: cover;" alt="Selfie" />
                            </a>
                        </div>
                    </td>
                    <td>
                        <div class="d-flex flex-column">
                            <span><?= \Altum\Date::get($v->submitted_at, 1) ?></span>
                        </div>
                    </td>
                    <td>
                        <?php if($v->status === 'pending'): ?>
                            <span class="badge badge-warning"><i class="fas fa-clock fa-sm mr-1"></i> Pending</span>
                        <?php elseif($v->status === 'verified'): ?>
                            <span class="badge badge-success"><i class="fas fa-check fa-sm mr-1"></i> Verified</span>
                        <?php else: ?>
                            <span class="badge badge-danger"><i class="fas fa-times fa-sm mr-1"></i> Ditolak</span>
                            <?php if(!empty($v->rejection_reason)): ?>
                                <small class="text-muted d-block mt-1" data-toggle="tooltip" title="<?= htmlspecialchars($v->rejection_reason) ?>">
                                    <?= htmlspecialchars(substr($v->rejection_reason, 0, 30)) . (strlen($v->rejection_reason) > 30 ? '...' : '') ?>
                                </small>
                            <?php endif; ?>
                        <?php endif ?>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <?php if($v->status === 'pending' || $v->status === 'rejected'): ?>
                                <form action="<?= url('admin/shop-verifications/approve/' . $v->id) ?>" method="post" class="mr-2">
                                    <input type="hidden" name="token" value="<?= \Altum\Csrf::get() ?>" />
                                    <button type="submit" class="btn btn-sm btn-success" data-toggle="tooltip" title="Setujui Verifikasi" onclick="return confirm('Apakah Anda yakin ingin menyetujui verifikasi KTP ini?')">
                                        <i class="fas fa-check fa-sm"></i> Approve
                                    </button>
                                </form>
                            <?php endif ?>

                            <?php if($v->status === 'pending'): ?>
                                <button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#rejectModal_<?= $v->id ?>" data-tooltip="tooltip" title="Tolak Verifikasi">
                                    <i class="fas fa-times fa-sm"></i> Reject
                                </button>
                                
                                <div class="modal fade" id="rejectModal_<?= $v->id ?>" tabindex="-1" role="dialog" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title"><i class="fas fa-times-circle text-danger mr-1"></i> Tolak Verifikasi KTP</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <form action="<?= url('admin/shop-verifications/reject/' . $v->id) ?>" method="post">
                                                    <input type="hidden" name="token" value="<?= \Altum\Csrf::get() ?>" />
                                                    
                                                    <p class="text-muted">Berikan alasan penolakan agar user dapat memperbaiki dokumen mereka.</p>
                                                    
                                                    <div class="form-group">
                                                        <label for="reason_<?= $v->id ?>">Alasan Penolakan</label>
                                                        <textarea id="reason_<?= $v->id ?>" name="reason" class="form-control" rows="3" required placeholder="Contoh: Foto KTP buram / tidak terbaca..."></textarea>
                                                    </div>
                                                    
                                                    <div class="mt-4">
                                                        <button type="submit" class="btn btn-block btn-danger">Tolak Verifikasi</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif ?>
                        </div>
                    </td>
                </tr>
            <?php endforeach ?>
        <?php else: ?>
            <tr>
                <td colspan="7" class="text-center py-5">
                    <i class="fas fa-inbox text-muted fa-2x mb-2 d-block"></i>
                    <h5 class="text-muted mb-0">Tidak ada data</h5>
                </td>
            </tr>
        <?php endif ?>
        </tbody>
    </table>
</div>

<?php \Altum\Event::add_content(include_view(THEME_PATH . 'views/partials/universal_delete_modal_url.php', [
    'name' => 'shop_verification',
    'resource_id' => 'id',
    'has_dynamic_resource_name' => true,
    'path' => 'admin/shop-verifications/delete/'
]), 'modals'); ?>
