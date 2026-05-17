<?php defined('ALTUMCODE') || die() ?>

<div class="d-flex justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-fw fa-xs fa-star text-primary-900 mr-2"></i> Shop Reviews</h1>
</div>

<?= \Altum\Alerts::output_alerts() ?>

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Reviews</h6>
        <div class="dropdown no-arrow">
            <a class="dropdown-toggle btn btn-sm btn-outline-secondary" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-filter fa-sm fa-fw text-gray-400"></i> Filter
            </a>
            <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in">
                <div class="dropdown-header">Filter by Status:</div>
                <a class="dropdown-item <?= $data->status == 'all' ? 'active' : '' ?>" href="<?= url('admin/shop-reviews?status=all') ?>">All Reviews</a>
                <a class="dropdown-item <?= $data->status == 'reported' ? 'active' : '' ?>" href="<?= url('admin/shop-reviews?status=reported') ?>">Reported</a>
                <a class="dropdown-item <?= $data->status == 'hidden' ? 'active' : '' ?>" href="<?= url('admin/shop-reviews?status=hidden') ?>">Hidden</a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Product & Shop</th>
                        <th>Buyer</th>
                        <th>Rating</th>
                        <th>Review Text</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!count($data->reviews)): ?>
                        <tr><td colspan="6" class="text-center">No reviews found.</td></tr>
                    <?php else: ?>
                        <?php foreach($data->reviews as $r): ?>
                        <tr>
                            <td>
                                <strong><?= htmlspecialchars($r->item_name) ?></strong><br>
                                <span class="text-muted"><i class="fas fa-store fa-sm"></i> <?= htmlspecialchars($r->shop_name) ?></span>
                            </td>
                            <td>
                                <?= htmlspecialchars($r->buyer_name) ?><br>
                                <small class="text-muted"><?= htmlspecialchars($r->buyer_email) ?></small>
                            </td>
                            <td>
                                <?php for($s=1;$s<=5;$s++): ?>
                                    <i class="fas fa-star fa-sm" style="color:<?= $s<=$r->rating?'#f59e0b':'#d1d5db' ?>"></i>
                                <?php endfor; ?>
                            </td>
                            <td style="max-width:300px">
                                <?= nl2br(htmlspecialchars($r->review ?? '-')) ?>
                                <?php if($r->reply): ?>
                                    <div class="mt-2 pl-2 border-left text-muted small">
                                        <strong>Reply:</strong> <?= nl2br(htmlspecialchars($r->reply)) ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($r->is_reported): ?>
                                    <span class="badge badge-warning"><i class="fas fa-flag"></i> Reported</span>
                                    <div class="small text-danger mt-1">Reason: <?= htmlspecialchars($r->report_reason) ?></div>
                                <?php elseif($r->status === 'hidden'): ?>
                                    <span class="badge badge-secondary">Hidden</span>
                                <?php else: ?>
                                    <span class="badge badge-success">Approved</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-ellipsis-h"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <?php if($r->is_reported): ?>
                                            <a class="dropdown-item" href="<?= url('admin/shop-reviews/unreport/' . $r->id . '?token=' . \Altum\Csrf::get()) ?>">
                                                <i class="fas fa-check fa-sm fa-fw mr-2 text-success"></i> Mark as Safe
                                            </a>
                                        <?php endif; ?>
                                        
                                        <?php if($r->status !== 'hidden'): ?>
                                            <a class="dropdown-item" href="<?= url('admin/shop-reviews/hide/' . $r->id . '?token=' . \Altum\Csrf::get()) ?>">
                                                <i class="fas fa-eye-slash fa-sm fa-fw mr-2 text-warning"></i> Hide Review
                                            </a>
                                        <?php endif; ?>

                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item text-danger" href="#" data-toggle="modal" data-target="#deleteModal" data-url="<?= url('admin/shop-reviews/delete/' . $r->id . '?token=' . \Altum\Csrf::get()) ?>">
                                            <i class="fas fa-trash fa-sm fa-fw mr-2"></i> Delete
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal-->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ready to Delete?</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">Select "Delete" below if you are ready to permanently delete this review.</div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                <a class="btn btn-danger" id="deleteBtn" href="#">Delete</a>
            </div>
        </div>
    </div>
</div>
<script>
$('#deleteModal').on('show.bs.modal', function (event) {
  var button = $(event.relatedTarget);
  var url = button.data('url');
  var modal = $(this);
  modal.find('#deleteBtn').attr('href', url);
});
</script>
