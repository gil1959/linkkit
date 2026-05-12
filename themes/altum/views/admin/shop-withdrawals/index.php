<?php defined('ALTUMCODE') || die() ?>

<h1 class="h3 mb-4">
    <i class="fas fa-fw fa-money-bill-wave text-success mr-2"></i> Shop Withdrawals
</h1>

<?= \Altum\Alerts::output_alerts() ?>

<div class="table-responsive table-custom-container">
    <table class="table table-custom">
        <thead>
        <tr>
            <th>#</th>
            <th>User</th>
            <th>Jumlah</th>
            <th>Rekening Tujuan</th>
            <th>Tanggal</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
        </thead>
        <tbody>
        <?php if(count($data->withdrawals)): ?>
            <?php foreach($data->withdrawals as $w): ?>
                <tr>
                    <td><?= $w->id ?></td>
                    <td>
                        <div class="font-weight-bold"><?= htmlspecialchars($w->user_name) ?></div>
                        <small class="text-muted"><?= htmlspecialchars($w->user_email) ?></small>
                    </td>
                    <td><strong class="text-success">Rp <?= number_format($w->amount, 0, ',', '.') ?></strong></td>
                    <td>
                        <?php if(!empty($w->bank_name)): ?>
                            <div><strong><?= htmlspecialchars($w->bank_name) ?></strong></div>
                            <small class="text-muted"><?= htmlspecialchars($w->account_number) ?> &bull; <?= htmlspecialchars($w->account_name) ?></small>
                        <?php else: ?>
                            <span class="text-muted text-italic">—</span>
                        <?php endif ?>
                    </td>
                    <td><small><?= \Altum\Date::get($w->datetime, 1) ?></small></td>
                    <td>
                        <?php if($w->status === 'pending'): ?>
                            <span class="badge badge-warning">Pending</span>
                        <?php elseif($w->status === 'approved'): ?>
                            <span class="badge badge-success">Approved</span>
                        <?php else: ?>
                            <span class="badge badge-danger">Rejected</span>
                        <?php endif ?>
                    </td>
                    <td>
                        <?php if($w->status === 'pending'): ?>
                            <form action="<?= url('admin/shop-withdrawals/approve/' . $w->id) ?>" method="post" style="display:inline-block;margin:0;">
                                <input type="hidden" name="token" value="<?= \Altum\Csrf::get() ?>">
                                <button type="submit" class="btn btn-sm btn-success mr-1" onclick="return confirm('Setujui withdrawal ini?')">
                                    <i class="fas fa-check fa-sm"></i> Approve
                                </button>
                            </form>
                            <form action="<?= url('admin/shop-withdrawals/reject/' . $w->id) ?>" method="post" style="display:inline-block;margin:0;">
                                <input type="hidden" name="token" value="<?= \Altum\Csrf::get() ?>">
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Tolak & kembalikan saldo ke user?')">
                                    <i class="fas fa-times fa-sm"></i> Reject
                                </button>
                            </form>
                        <?php endif ?>
                    </td>
                </tr>
            <?php endforeach ?>
        <?php else: ?>
            <tr>
                <td colspan="7" class="text-center py-5">
                    <i class="fas fa-info-circle text-muted fa-2x mb-2 d-block"></i>
                    <h5 class="text-muted">Belum ada withdrawal request</h5>
                </td>
            </tr>
        <?php endif ?>
        </tbody>
    </table>
</div>
