<?php defined('ALTUMCODE') || die() ?>
<?php \Altum\Title::set('Verifikasi Identitas — Seller') ?>

<style>
*,*::before,*::after{box-sizing:border-box}
.verif-wrap{max-width:640px;margin:40px auto;padding:0 20px 80px}
.verif-hero{background:linear-gradient(135deg,#1e1b4b,#312e81);border-radius:20px;padding:36px 32px;color:#fff;text-align:center;margin-bottom:28px}
.verif-hero h1{font-size:1.4rem;font-weight:800;margin:0 0 8px}
.verif-hero p{font-size:.88rem;color:#c7d2fe;margin:0}
.verif-badge{display:inline-flex;align-items:center;gap:8px;background:rgba(255,255,255,.12);border-radius:30px;padding:8px 18px;font-size:.82rem;font-weight:600;margin-bottom:20px}
.status-card{border-radius:16px;padding:24px 28px;margin-bottom:24px;display:flex;align-items:flex-start;gap:16px}
.status-card.pending{background:#fffbeb;border:2px solid #fde68a}
.status-card.verified{background:#f0fdf4;border:2px solid #86efac}
.status-card.rejected{background:#fef2f2;border:2px solid #fca5a5}
.status-icon{width:48px;height:48px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.4rem;flex-shrink:0}
.co-card{background:#fff;border-radius:16px;box-shadow:0 2px 12px rgba(0,0,0,.07);overflow:hidden;margin-bottom:20px}
.co-card-head{padding:18px 24px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;gap:12px}
.co-card-icon{width:38px;height:38px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:1rem}
.co-card-title{font-size:.95rem;font-weight:700;margin:0}
.co-card-body{padding:24px}
.form-group{margin-bottom:18px}
.form-label{display:block;font-size:.82rem;font-weight:600;color:#374151;margin-bottom:7px}
.form-control{width:100%;border:1.5px solid #e2e8f0;border-radius:10px;padding:10px 14px;font-size:.88rem;color:#1e293b;outline:none;transition:.2s;background:#fafafa;font-family:inherit}
.form-control:focus{border-color:#6366f1;background:#fff;box-shadow:0 0 0 3px rgba(99,102,241,.1)}
.upload-zone{border:2px dashed #cbd5e1;border-radius:14px;padding:28px 20px;text-align:center;cursor:pointer;transition:.25s;position:relative;background:#fafafa}
.upload-zone:hover{border-color:#6366f1;background:#f8f7ff}
.upload-zone input[type=file]{position:absolute;inset:0;opacity:0;cursor:pointer;width:100%;height:100%}
.upload-zone .uz-icon{font-size:2.2rem;margin-bottom:10px;color:#94a3b8}
.upload-zone .uz-text{font-size:.83rem;color:#64748b;font-weight:500}
.upload-zone .uz-hint{font-size:.74rem;color:#94a3b8;margin-top:4px}
.upload-zone.has-file{border-color:#4f46e5;background:#f0f0ff}
.upload-preview{width:100%;max-height:200px;object-fit:cover;border-radius:10px;margin-top:10px;display:none}
.btn-submit{width:100%;background:linear-gradient(135deg,#4f46e5,#6366f1);color:#fff;border:none;border-radius:14px;padding:14px;font-weight:800;font-size:1rem;cursor:pointer;transition:.2s;display:flex;align-items:center;justify-content:center;gap:10px;letter-spacing:.02em}
.btn-submit:hover{background:linear-gradient(135deg,#3730a3,#4f46e5);transform:translateY(-1px);box-shadow:0 6px 20px rgba(79,70,229,.35)}
.info-steps{display:flex;gap:12px;margin-bottom:24px}
.info-step{flex:1;background:#f8fafc;border-radius:12px;padding:16px;text-align:center}
.info-step-num{width:28px;height:28px;border-radius:50%;background:#4f46e5;color:#fff;font-size:.78rem;font-weight:700;display:flex;align-items:center;justify-content:center;margin:0 auto 8px}
.info-step-text{font-size:.75rem;color:#64748b;line-height:1.4}
@media(max-width:600px){.info-steps{flex-direction:column}.verif-hero{padding:24px 20px}}
</style>

<div class="verif-wrap">
    <?= \Altum\Alerts::output_alerts() ?>

    <div class="verif-hero">
        <div class="verif-badge"><i class="fas fa-shield-alt"></i> Verifikasi Seller</div>
        <h1>Verifikasi Identitas KTP</h1>
        <p>Diperlukan untuk proses pencairan dana saldo toko kamu</p>
    </div>

    <!-- Status display -->
    <?php if($data->status === 'verified'): ?>
    <div class="status-card verified">
        <div class="status-icon" style="background:#d1fae5"><i class="fas fa-check-circle" style="color:#059669"></i></div>
        <div>
            <div style="font-weight:800;color:#059669;font-size:1rem">Terverifikasi <i class="fas fa-check"></i></div>
            <div style="font-size:.85rem;color:#065f46;margin-top:4px">Identitasmu sudah diverifikasi. Kamu bisa melakukan pencairan dana.</div>
        </div>
    </div>

    <?php elseif($data->status === 'pending'): ?>
    <div class="status-card pending">
        <div class="status-icon" style="background:#fef3c7"><i class="fas fa-clock" style="color:#d97706"></i></div>
        <div>
            <div style="font-weight:800;color:#d97706;font-size:1rem">Sedang Direview</div>
            <div style="font-size:.85rem;color:#92400e;margin-top:4px">Dokumen kamu sedang dalam proses review oleh admin (1–3 hari kerja). Kamu akan mendapat notifikasi melalui email.</div>
        </div>
    </div>

    <?php elseif($data->status === 'rejected'): ?>
    <div class="status-card rejected">
        <div class="status-icon" style="background:#fee2e2"><i class="fas fa-times-circle" style="color:#dc2626"></i></div>
        <div>
            <div style="font-weight:800;color:#dc2626;font-size:1rem">Ditolak — Upload Ulang</div>
            <?php if(!empty($data->verification->rejection_reason)): ?>
            <div style="font-size:.85rem;color:#991b1b;margin-top:4px">Alasan: <strong><?= htmlspecialchars($data->verification->rejection_reason) ?></strong></div>
            <?php endif; ?>
            <div style="font-size:.82rem;color:#7f1d1d;margin-top:6px">Perbaiki dokumenmu dan upload ulang di bawah.</div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Steps info -->
    <div class="info-steps">
        <div class="info-step">
            <div class="info-step-num">1</div>
            <div class="info-step-text">Upload foto KTP jelas & terbaca</div>
        </div>
        <div class="info-step">
            <div class="info-step-num">2</div>
            <div class="info-step-text">Upload selfie sambil pegang KTP</div>
        </div>
        <div class="info-step">
            <div class="info-step-num">3</div>
            <div class="info-step-text">Admin review dalam 1–3 hari kerja</div>
        </div>
        <div class="info-step">
            <div class="info-step-num" style="background:#059669"><i class="fas fa-check"></i></div>
            <div class="info-step-text">Pencairan dana aktif</div>
        </div>
    </div>

    <!-- Form upload (disabled if pending/verified) -->
    <?php if($data->status !== 'verified' && $data->status !== 'pending'): ?>
    <form action="<?= url('shop-verification') ?>" method="post" enctype="multipart/form-data">
        <input type="hidden" name="token" value="<?= \Altum\Csrf::get() ?>">

        <div class="co-card">
            <div class="co-card-head">
                <div class="co-card-icon" style="background:#ede9fe;color:#7c3aed"><i class="fas fa-id-card"></i></div>
                <h2 class="co-card-title">Data Diri Sesuai KTP</h2>
            </div>
            <div class="co-card-body">
                <div class="form-group">
                    <label class="form-label">Nama Lengkap (sesuai KTP) <span style="color:#ef4444">*</span></label>
                    <input type="text" name="full_name" class="form-control" required placeholder="Nama sesuai KTP" value="<?= htmlspecialchars($data->verification->full_name ?? '') ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">Nomor Induk Kependudukan (NIK) <span style="color:#ef4444">*</span></label>
                    <input type="text" name="nik" class="form-control" required placeholder="16 digit NIK" maxlength="16" inputmode="numeric" value="<?= htmlspecialchars($data->verification->nik ?? '') ?>">
                    <small style="font-size:.74rem;color:#94a3b8">NIK terdiri dari 16 digit angka</small>
                </div>
            </div>
        </div>

        <div class="co-card">
            <div class="co-card-head">
                <div class="co-card-icon" style="background:#dbeafe;color:#1d4ed8"><i class="fas fa-camera"></i></div>
                <h2 class="co-card-title">Upload Dokumen</h2>
            </div>
            <div class="co-card-body">
                <div class="form-group">
                    <label class="form-label">Foto KTP <span style="color:#ef4444">*</span></label>
                    <div class="upload-zone" id="ktp_zone">
                        <input type="file" name="ktp_image" accept="image/*" onchange="previewVerifImg(this,'ktp_preview','ktp_zone')">
                        <div class="uz-icon"><i class="fas fa-id-card"></i></div>
                        <div class="uz-text">Klik atau drag foto KTP di sini</div>
                        <div class="uz-hint">JPG / PNG / WEBP · Maks 5MB · Pastikan semua teks terbaca jelas</div>
                    </div>
                    <img id="ktp_preview" class="upload-preview" alt="Preview KTP">
                </div>

                <div class="form-group" style="margin-top:20px">
                    <label class="form-label">Foto Selfie + KTP <span style="color:#ef4444">*</span></label>
                    <div class="alert alert-info" style="font-size:.8rem;padding:.6rem 1rem;border-radius:10px;margin-bottom:10px">
                        <i class="fas fa-info-circle mr-2"></i>
                        Foto selfie kamu <strong>sambil memegang KTP</strong> di dekat wajah. Pastikan wajah dan tulisan KTP terlihat jelas.
                    </div>
                    <div class="upload-zone" id="selfie_zone">
                        <input type="file" name="selfie_image" accept="image/*" onchange="previewVerifImg(this,'selfie_preview','selfie_zone')">
                        <div class="uz-icon"><i class="fas fa-user-circle"></i></div>
                        <div class="uz-text">Klik atau drag foto selfie + KTP di sini</div>
                        <div class="uz-hint">JPG / PNG / WEBP · Maks 5MB</div>
                    </div>
                    <img id="selfie_preview" class="upload-preview" alt="Preview Selfie">
                </div>
            </div>
        </div>

        <div class="alert alert-warning" style="font-size:.82rem;border-radius:12px;margin-bottom:20px">
            <i class="fas fa-lock mr-2"></i>
            <strong>Kerahasiaan terjamin.</strong> Data identitasmu hanya digunakan untuk keperluan verifikasi dan tidak akan disebarkan ke pihak lain.
        </div>

        <button type="submit" class="btn-submit">
            <i class="fas fa-paper-plane"></i> Kirim untuk Diverifikasi
        </button>
    </form>
    <?php endif; ?>

    <div style="text-align:center;margin-top:24px">
        <a href="<?= url('shop') ?>" style="color:#6b7280;font-size:.85rem;text-decoration:none">
            <i class="fas fa-arrow-left fa-sm mr-1"></i> Kembali ke Dashboard Toko
        </a>
    </div>
</div>

<script>
function previewVerifImg(input, previewId, zoneId) {
    if(!input.files || !input.files[0]) return;
    var reader = new FileReader();
    reader.onload = function(e) {
        var preview = document.getElementById(previewId);
        if(preview) { preview.src = e.target.result; preview.style.display = 'block'; }
        var zone = document.getElementById(zoneId);
        if(zone) zone.classList.add('has-file');
    };
    reader.readAsDataURL(input.files[0]);
}
</script>
