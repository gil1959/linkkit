<?php defined('ALTUMCODE') || die() ?>

<header class="header pb-0">
    <div class="container">
        <div class="d-flex flex-column flex-lg-row align-items-center justify-content-lg-between">
            <div class="d-flex align-items-center w-100">
                <?php if($data->shop->logo_image): ?>
                    <img src="<?= UPLOADS_FULL_URL . 'shop_logos/' . $data->shop->logo_image ?>" style="width: 50px; height: 50px; border-radius: 10px; object-fit: cover;" class="mr-3" />
                <?php else: ?>
                    <div class="mr-3" style="width: 50px; height: 50px; background: #e9ecef; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-shopping-bag text-muted"></i>
                    </div>
                <?php endif ?>
                
                <div>
                    <h2 class="h5 mb-0 font-weight-bold"><?= htmlspecialchars($data->shop->name) ?></h2>
                    <a href="<?= SITE_URL . 'store/' . $data->shop->url ?>" target="_blank" class="text-muted small">
                        <?= SITE_URL . 'store/' . $data->shop->url ?> <i class="fas fa-external-link-alt fa-xs"></i>
                    </a>
                </div>



            </div>
        </div>

        <ul class="nav nav-custom nav-tabs mt-4" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" data-toggle="tab" href="#summary" role="tab">Summary</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#manage_items" role="tab">Manage Items</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#reviews" role="tab">Reviews</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#listing" role="tab">Listing</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#voucher" role="tab">Voucher</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#audience" role="tab">Audience</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#transaction" role="tab">Transaction</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#withdrawals" role="tab">Withdrawals</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#webhook_events" role="tab">Webhook Events</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#shop_settings" role="tab">Shop Settings</a>
            </li>
        </ul>
    </div>
</header>

<section class="container pt-4">
    <?= \Altum\Alerts::output_alerts() ?>

    <?php if(($data->verification_status ?? 'unverified') !== 'verified'): ?>
    <div class="alert d-flex align-items-center justify-content-between mb-4 p-3" style="background:linear-gradient(135deg,#fef3c7,#fffbeb);border:1.5px solid #fde68a;border-radius:14px">
        <div class="d-flex align-items-center">
            <div style="width:36px;height:36px;border-radius:10px;background:#fde68a;display:flex;align-items:center;justify-content:center;margin-right:12px;flex-shrink:0">
                <i class="fas fa-shield-alt" style="color:#92400e"></i>
            </div>
            <div>
                <?php if(($data->verification_status ?? 'unverified') === 'pending'): ?>
                    <strong style="color:#92400e">Verifikasi sedang diproses.</strong>
                    <span style="font-size:.85rem;color:#78350f"> Admin akan mereview dokumenmu. Pencairan dana aktif setelah disetujui.</span>
                <?php elseif(($data->verification_status ?? 'unverified') === 'rejected'): ?>
                    <strong style="color:#991b1b">Verifikasi ditolak.</strong>
                    <span style="font-size:.85rem;color:#7f1d1d"> Perbaiki dokumenmu dan upload ulang.</span>
                <?php else: ?>
                    <strong style="color:#92400e">Lengkapi Verifikasi KTP</strong>
                    <span style="font-size:.85rem;color:#78350f"> untuk mengaktifkan pencairan dana saldo toko.</span>
                <?php endif; ?>
            </div>
        </div>
        <button onclick="document.querySelector('[href=\'#shop_settings\']').click();" class="btn btn-sm font-weight-bold ml-3" style="background:#f59e0b;color:#fff;border-radius:10px;white-space:nowrap;border:none">
            <i class="fas fa-id-card fa-sm mr-1"></i>
            <?= ($data->verification_status ?? 'unverified') === 'pending' ? 'Lihat Status' : 'Verifikasi Sekarang' ?> <i class="fas fa-arrow-right fa-sm ml-1"></i>
        </button>
    </div>
    <?php endif; ?>

    <div class="tab-content">
        <!-- Summary Tab -->
        <div class="tab-pane fade show active" id="summary" role="tabpanel">
            <h2 class="h5 mb-4">Overall Shop Summary</h2>
            
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 col-md-6 mb-3 mb-md-0">
                            <div class="text-muted mb-1">All Time Revenue</div>
                            <div class="h3 mb-0">Rp <?= number_format($data->all_time_revenue, 0, ',', '.') ?></div>
                        </div>
                        <div class="col-12 col-md-6 border-md-left">
                            <div class="text-muted mb-1">Withdrawal Amount</div>
                            <div class="h3 mb-0">Rp <?= number_format($data->withdrawal_amount, 0, ',', '.') ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="row text-center border-bottom pb-3 mb-4">
                        <div class="col-6 col-md-3 border-right">
                            <div class="h4 mb-0">Rp <?= number_format($data->total_income, 0, ',', '.') ?></div>
                            <small class="text-muted">Total Income</small>
                        </div>
                        <div class="col-6 col-md-3 border-right">
                            <div class="h4 mb-0"><?= $data->total_transactions ?></div>
                            <small class="text-muted">Transaction</small>
                        </div>
                        <div class="col-6 col-md-3 border-right">
                            <div class="h4 mb-0"><?= number_format($data->total_product_views) ?></div>
                            <small class="text-muted">Product View</small>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="h4 mb-0"><?= $data->conversion_rate ?>%</div>
                            <small class="text-muted">Conversion</small>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center">
                            <h6 class="mb-0 text-muted mr-3">Total Views & Clicks</h6>
                            <div class="d-flex align-items-center mr-3">
                                <div style="width: 12px; height: 12px; border-radius: 50%; background: #f59e0b; margin-right: 5px;"></div>
                                <span class="text-muted small">Views <strong class="text-dark ml-1"><?= number_format($data->total_chart_views) ?></strong></span>
                            </div>
                            <div class="d-flex align-items-center">
                                <div style="width: 12px; height: 12px; border-radius: 50%; background: #10b981; margin-right: 5px;"></div>
                                <span class="text-muted small">Clicks <strong class="text-dark ml-1"><?= number_format($data->total_chart_clicks) ?></strong></span>
                            </div>
                        </div>
                        <div class="text-muted small"><i class="fas fa-calendar-alt"></i> Last 30 Days</div>
                    </div>
                    <div class="chart-container" style="position: relative; height:300px; width:100%;">
                        <canvas id="shopStatsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Manage Items Tab -->
        <div class="tab-pane fade" id="manage_items" role="tabpanel">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="flex-grow-1 mr-3">
                    <input type="text" class="form-control" placeholder="Search Query">
                </div>
                <div>
                    <button class="btn btn-outline-secondary mr-2"><i class="fas fa-filter fa-sm"></i> Filter</button>
                    <a href="<?= url('shop-item-create') ?>" class="btn btn-primary"><i class="fas fa-plus fa-sm mr-1"></i> Add Items</a>
                </div>
            </div>

            <div class="table-responsive table-custom-container">
                <table class="table table-custom">
                    <thead>
                    <tr>
                        <th>No</th>
                        <th>Item</th>
                        <th>Type</th>
                        <th>Sold</th>
                        <th>Revenue</th>
                        <th>Stock</th>
                        <th>Price</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                        <?php if(count($data->items) > 0): ?>
                            <?php foreach($data->items as $i => $item): ?>
                                <tr>
                                    <td><?= $i + 1 ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <?php if($item->image): ?>
                                                <img src="<?= UPLOADS_FULL_URL . 'shop_items/' . $item->image ?>" style="width:40px; height:40px; object-fit:cover; border-radius:5px;" class="mr-2">
                                            <?php else: ?>
                                                <div class="mr-2" style="width:40px; height:40px; background:#e9ecef; border-radius:5px; display:flex; align-items:center; justify-content:center;"><i class="fas fa-box text-muted"></i></div>
                                            <?php endif ?>
                                            <?= htmlspecialchars($item->name) ?>
                                        </div>
                                    </td>
                                    <td><span class="badge badge-light"><?= $item->type ?></span></td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td><?= $item->stock === null ? 'Unlimited' : $item->stock ?></td>
                                    <td>Rp <?= number_format($item->price, 0, ',', '.') ?></td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle dropdown-toggle-simple" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="fas fa-ellipsis-h"></i>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <a class="dropdown-item" href="<?= url('shop-item-update?item_id=' . $item->id) ?>">
                                                    <i class="fas fa-fw fa-edit fa-sm text-primary mr-2"></i> Edit
                                                </a>
                                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#item_detail_modal_<?= $item->id ?>">
                                                    <i class="fas fa-fw fa-eye fa-sm text-info mr-2"></i> Detail
                                                </a>
                                                <div class="dropdown-divider"></div>
                                                <a class="dropdown-item text-danger" href="#" data-toggle="modal" data-target="#item_delete_modal_<?= $item->id ?>">
                                                    <i class="fas fa-fw fa-trash fa-sm mr-2"></i> Hapus
                                                </a>
                                            </div>
                                        </div>
                                        <!-- Detail Modal -->
                                        <div class="modal fade" id="item_detail_modal_<?= $item->id ?>" tabindex="-1" role="dialog">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title"><i class="fas fa-box mr-2 text-primary"></i> <?= htmlspecialchars($item->name) ?></h5>
                                                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <?php if($item->image): ?>
                                                            <img src="<?= UPLOADS_FULL_URL . 'shop_items/' . $item->image ?>" class="img-fluid rounded mb-3" style="max-height:200px;object-fit:cover;width:100%;">
                                                        <?php endif ?>
                                                        <table class="table table-sm table-borderless">
                                                            <tr><th>Tipe</th><td><span class="badge badge-light"><?= $item->type ?></span></td></tr>
                                                            <tr><th>Harga</th><td>Rp <?= number_format($item->price, 0, ',', '.') ?></td></tr>
                                                            <tr><th>Stok</th><td><?= $item->stock === null ? 'Unlimited' : $item->stock ?></td></tr>
                                                            <tr><th>Status</th><td><span class="badge <?= $item->status ? 'badge-success' : 'badge-danger' ?>"><?= $item->status ? 'Aktif' : 'Nonaktif' ?></span></td></tr>
                                                            <tr><th>Deskripsi</th><td><?= nl2br(htmlspecialchars($item->description ?? '-')) ?></td></tr>
                                                        </table>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Tutup</button>
                                                        <a href="<?= url('shop-item-create?edit=' . $item->id) ?>" class="btn btn-sm btn-primary"><i class="fas fa-edit fa-sm mr-1"></i> Edit</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Delete Modal -->
                                        <div class="modal fade" id="item_delete_modal_<?= $item->id ?>" tabindex="-1" role="dialog" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Hapus Produk</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p class="text-muted">Apakah Anda yakin ingin menghapus produk <strong><?= htmlspecialchars($item->name) ?></strong>? Tindakan ini tidak dapat dibatalkan.</p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Batal</button>
                                                        <form action="<?= url('shop-item-delete') ?>" method="post">
                                                            <input type="hidden" name="token" value="<?= \Altum\Csrf::get() ?>" />
                                                            <input type="hidden" name="item_id" value="<?= $item->id ?>" />
                                                            <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-fw fa-trash mr-1"></i> Hapus Produk</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" class="text-center py-5">
                                    <i class="fas fa-info-circle text-muted mb-2 fa-2x"></i>
                                    <h5 class="text-muted">Nothing here...</h5>
                                    <p class="text-muted mb-0">No data found or query doesn't exists</p>
                                </td>
                            </tr>
                        <?php endif ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- REVIEWS TAB -->
        <div class="tab-pane fade" id="reviews" role="tabpanel">
            <h2 class="h5 mb-4">Reviews</h2>
            <?php if(count($data->reviews) > 0): ?>
            <div class="table-responsive table-custom-container">
                <table class="table table-custom">
                    <thead><tr>
                        <th>Produk</th><th>Pembeli</th><th>Rating</th><th>Ulasan</th><th>Tanggal</th><th></th>
                    </tr></thead>
                    <tbody>
                    <?php foreach($data->reviews as $review): ?>
                        <tr>
                            <td><?= htmlspecialchars($review->item_name) ?></td>
                            <td>
                                <div><?= htmlspecialchars($review->buyer_name) ?></div>
                                <small class="text-muted"><?= htmlspecialchars($review->buyer_email) ?></small>
                            </td>
                            <td>
                                <?php for($s=1;$s<=5;$s++): ?>
                                    <i class="fas fa-star fa-sm" style="color:<?= $s<=$review->rating?'#f59e0b':'#d1d5db' ?>"></i>
                                <?php endfor; ?>
                            </td>
                            <td style="max-width:200px">
                                <?= nl2br(htmlspecialchars($review->review ?? '-')) ?>
                                <?php if($review->reply): ?>
                                    <div class="mt-2 p-2 bg-light rounded" style="font-size:.8rem; border-left:2px solid #4f46e5;">
                                        <strong>Balasan Anda:</strong> <?= nl2br(htmlspecialchars($review->reply)) ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td><?= \Altum\Date::get($review->datetime, 1) ?></td>
                            <td>
                                <button type="button" class="btn btn-sm btn-outline-primary mb-1" onclick="openReplyModal(<?= $review->id ?>, '<?= htmlspecialchars(addslashes($review->reply ?? '')) ?>')">
                                    <i class="fas fa-reply fa-sm"></i> Balas
                                </button>
                                <br>
                                <button type="button" class="btn btn-sm <?= $review->is_reported ? 'btn-warning' : 'btn-outline-warning' ?>" <?= $review->is_reported ? 'disabled' : '' ?> onclick="openReportModal(<?= $review->id ?>)">
                                    <i class="fas fa-flag fa-sm"></i> <?= $review->is_reported ? 'Dilaporkan' : 'Laporkan' ?>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="card bg-light"><div class="card-body text-center text-muted"><i class="fas fa-star fa-2x mb-2 d-block"></i>Belum ada ulasan</div></div>
            <?php endif; ?>

            <!-- Reply Modal -->
            <div class="modal fade" id="replyModal" tabindex="-1" role="dialog">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"><i class="fas fa-reply mr-2 text-primary"></i> Balas Ulasan</h5>
                            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                        </div>
                        <form id="replyForm" onsubmit="event.preventDefault();submitReply()">
                            <div class="modal-body">
                                <input type="hidden" id="reply_review_id" name="id">
                                <div class="form-group">
                                    <label>Balasan Anda</label>
                                    <textarea id="reply_content" class="form-control" rows="4" required></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-primary">Simpan Balasan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Report Modal -->
            <div class="modal fade" id="reportModal" tabindex="-1" role="dialog">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"><i class="fas fa-flag mr-2 text-warning"></i> Laporkan Ulasan</h5>
                            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                        </div>
                        <form id="reportForm" onsubmit="event.preventDefault();submitReport()">
                            <div class="modal-body">
                                <input type="hidden" id="report_review_id" name="id">
                                <div class="form-group">
                                    <label>Alasan Melaporkan</label>
                                    <textarea id="report_reason" class="form-control" rows="3" required placeholder="Contoh: Spam, kata-kata kasar, dsb."></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-warning">Kirim Laporan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <script>
            function openReplyModal(id, currentReply) {
                document.getElementById('reply_review_id').value = id;
                document.getElementById('reply_content').value = currentReply || '';
                $('#replyModal').modal('show');
            }
            function submitReply() {
                var id = document.getElementById('reply_review_id').value;
                var reply = document.getElementById('reply_content').value;
                var params = new URLSearchParams({action:'review_reply', id:id, reply:reply, token:'<?= \Altum\Csrf::get() ?>'});
                fetch('<?= url('shop-ajax') ?>', {method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body:params})
                .then(r=>r.json()).then(res=>{
                    if(res.success) { location.reload(); }
                    else { alert('Gagal: ' + (res.message || 'Error')); }
                });
            }
            function openReportModal(id) {
                document.getElementById('report_review_id').value = id;
                document.getElementById('report_reason').value = '';
                $('#reportModal').modal('show');
            }
            function submitReport() {
                var id = document.getElementById('report_review_id').value;
                var reason = document.getElementById('report_reason').value;
                var params = new URLSearchParams({action:'seller_report_review', id:id, reason:reason, token:'<?= \Altum\Csrf::get() ?>'});
                fetch('<?= url('shop-ajax') ?>', {method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body:params})
                .then(r=>r.json()).then(res=>{
                    if(res.success) { location.reload(); }
                    else { alert('Gagal: ' + (res.message || 'Error')); }
                });
            }
            </script>
        </div>

        <!-- LISTING TAB -->
        <div class="tab-pane fade" id="listing" role="tabpanel">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="h5 mb-0">Listing</h2>
                    <p class="text-muted small mb-0">Kelompokkan produk ke dalam satu listing etalase</p>
                </div>
                <button class="btn btn-primary" data-toggle="modal" data-target="#listingCreateModal"><i class="fas fa-plus fa-sm mr-1"></i> Add new</button>
            </div>
            <?php if(count($data->listings) > 0): ?>
            <div class="table-responsive table-custom-container">
                <table class="table table-custom">
                    <thead><tr><th>Nama Listing</th><th>Produk</th><th>Deskripsi</th><th></th></tr></thead>
                    <tbody>
                    <?php foreach($data->listings as $lst): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($lst->name) ?></strong></td>
                            <td><span class="badge badge-primary"><?= $lst->item_count ?> item</span></td>
                            <td><?= htmlspecialchars($lst->description ?? '-') ?></td>
                            <td>
                                <button type="button" class="btn btn-sm btn-outline-primary mr-1" onclick="event.preventDefault();openListingEdit(<?= htmlspecialchars(json_encode($lst)) ?>)"><i class="fas fa-edit fa-sm"></i></button>
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="event.preventDefault();shopDeleteAjax('listing_delete',{id:<?= $lst->id ?>},this,'Listing berhasil dihapus!')"><i class="fas fa-trash fa-sm"></i></button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="card bg-light"><div class="card-body"><i class="fas fa-info-circle text-muted mr-2"></i><strong>No listing found</strong><br><small class="text-muted ml-4">Try to create new listing</small></div></div>
            <?php endif; ?>
        </div>

        <!-- VOUCHER TAB -->
        <div class="tab-pane fade" id="voucher" role="tabpanel">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="h5 mb-0">Voucher Settings</h2>
                    <p class="text-muted small mb-0">Voucher diskon untuk pembelian di toko kamu</p>
                </div>
                <button class="btn btn-primary" data-toggle="modal" data-target="#voucherCreateModal"><i class="fas fa-plus fa-sm mr-1"></i> Add new</button>
            </div>
            <?php if(count($data->vouchers) > 0): ?>
            <div class="table-responsive table-custom-container">
                <table class="table table-custom">
                    <thead><tr><th>Code</th><th>Diskon</th><th>Kuota</th><th>Periode</th><th>Produk</th><th>Status</th><th></th></tr></thead>
                    <tbody>
                    <?php foreach($data->vouchers as $vc): ?>
                        <?php $now=time(); $expired=$vc->valid_to && strtotime($vc->valid_to)<$now; ?>
                        <tr>
                            <td><code><?= htmlspecialchars($vc->code) ?></code></td>
                            <td><strong><?= $vc->discount_percentage ?>%</strong></td>
                            <td><?= $vc->is_unlimited ? '<span class="badge badge-info">Unlimited</span>' : ($vc->used.'/'.(($vc->quota!==null)?$vc->quota:'∞')) ?></td>
                            <td style="font-size:.8rem">
                                <?= $vc->valid_from ? date('d/m/Y',strtotime($vc->valid_from)) : '-' ?>
                                <?= ($vc->valid_from&&$vc->valid_to)?' – ':'' ?>
                                <?= $vc->valid_to ? date('d/m/Y',strtotime($vc->valid_to)) : '' ?>
                            </td>
                            <td><?= $vc->item_name ? htmlspecialchars($vc->item_name) : '<span class="text-muted">All items</span>' ?></td>
                            <td>
                                <?php if(!$vc->is_active): ?>
                                    <span class="badge badge-secondary">Nonaktif</span>
                                <?php elseif($expired): ?>
                                    <span class="badge badge-danger">Expired</span>
                                <?php else: ?>
                                    <span class="badge badge-success">Aktif</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-outline-primary mr-1" onclick="event.preventDefault();openVoucherEdit(<?= htmlspecialchars(json_encode($vc)) ?>)"><i class="fas fa-edit fa-sm"></i></button>
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="event.preventDefault();shopDeleteAjax('voucher_delete',{id:<?= $vc->id ?>},this,'Voucher berhasil dihapus!')"><i class="fas fa-trash fa-sm"></i></button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="card bg-light"><div class="card-body"><i class="fas fa-info-circle text-muted mr-2"></i><strong>No Voucher found</strong><br><small class="text-muted ml-4">Click Add New to create a new voucher</small></div></div>
            <?php endif; ?>
        </div>

        <!-- AUDIENCE TAB -->
        <div class="tab-pane fade" id="audience" role="tabpanel">
            <h2 class="h5 mb-4">Audience <span class="badge badge-secondary" style="font-size:.7rem"><?= count($data->audience) ?></span></h2>
            <?php if(count($data->audience) > 0): ?>
            <div class="table-responsive table-custom-container">
                <table class="table table-custom">
                    <thead><tr><th>Nama</th><th>Email</th><th>No. HP</th><th>Pesanan</th><th>Total Belanja</th><th>Pembelian Terakhir</th></tr></thead>
                    <tbody>
                    <?php foreach($data->audience as $aud): ?>
                        <tr>
                            <td><?= htmlspecialchars($aud->full_name) ?></td>
                            <td><?= htmlspecialchars($aud->email) ?></td>
                            <td><?= htmlspecialchars($aud->phone ?? '-') ?></td>
                            <td><span class="badge badge-primary"><?= $aud->total_orders ?></span></td>
                            <td>Rp <?= number_format($aud->total_spent,0,',','.') ?></td>
                            <td><?= $aud->last_purchase ? \Altum\Date::get($aud->last_purchase,1) : '-' ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="card bg-light"><div class="card-body text-center text-muted"><i class="fas fa-users fa-2x mb-2 d-block"></i>Belum ada pembeli</div></div>
            <?php endif; ?>
        </div>
        <div class="tab-pane fade" id="transaction" role="tabpanel">
            <div class="row mb-4">
                <div class="col-4 border-right">
                    <div class="text-muted">Total Income</div>
                    <div class="h4">Rp <?= number_format($data->total_income, 0, ',', '.') ?></div>
                </div>
                <div class="col-4 border-right">
                    <div class="text-muted">Saldo Tersedia</div>
                    <div class="h4 text-success">Rp <?= number_format($data->withdrawable_funds ?? 0, 0, ',', '.') ?></div>
                    <?php if(($data->pending_funds ?? 0) > 0): ?>
                    <small class="text-muted"><i class="fas fa-clock fa-xs mr-1"></i>Pending: Rp <?= number_format($data->pending_funds, 0, ',', '.') ?></small>
                    <?php endif; ?>
                </div>
                <div class="col-4">
                    <div class="text-muted">Pending Withdrawal</div>
                    <div class="h4 text-warning">Rp <?= number_format($data->total_pending_withdrawals ?? 0, 0, ',', '.') ?></div>
                    <small class="text-muted">Diproses 3-5 hari</small>
                </div>
            </div>
            
            <div class="mb-4 d-flex align-items-center">
                <?php if(($data->verification_status ?? 'unverified') !== 'verified'): ?>
                    <!-- Belum verified: tombol buka popup/redirect -->
                    <button type="button" class="btn btn-success" onclick="showVerifGate()">
                        <i class="fas fa-money-bill-wave fa-sm mr-1"></i> Withdraw Funds
                    </button>
                <?php else: ?>
                    <button type="button" class="btn btn-success" data-toggle="modal" data-target="#withdraw_modal">
                        <i class="fas fa-money-bill-wave fa-sm mr-1"></i> Withdraw Funds
                    </button>
                <?php endif; ?>
                <small class="text-muted ml-3"><i class="fas fa-info-circle mr-1"></i>Proses pencairan memakan waktu 3-5 hari kerja.</small>
            </div>

            <!-- Withdraw Modal -->
            <div class="modal fade" id="withdraw_modal" tabindex="-1" role="dialog">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"><i class="fas fa-money-bill-wave mr-2 text-success"></i> Withdraw Funds</h5>
                            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                        </div>
                        <form action="<?= url('shop-withdraw') ?>" method="post">
                            <div class="modal-body">
                                <input type="hidden" name="token" value="<?= \Altum\Csrf::get() ?>" />
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    Penarikan akan diproses dalam <strong>3-5 hari kerja</strong> dan ditinjau admin sebelum dikirim ke rekening Anda.
                                </div>
                                <div class="card bg-light mb-3">
                                    <div class="card-body py-2">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="text-muted">Saldo tersedia</span>
                                            <strong class="text-success h5 mb-0">Rp <?= number_format($data->withdrawable_funds ?? 0, 0, ',', '.') ?></strong>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="font-weight-bold" style="font-size:.85rem">Jumlah Penarikan (Rp)</label>
                                    <input type="number" name="amount" class="form-control"
                                           min="50000"
                                           max="<?= (int)($data->withdrawable_funds ?? 0) ?>"
                                           value="<?= (int)($data->withdrawable_funds ?? 0) ?>"
                                           required placeholder="Min Rp 50.000">
                                    <small class="text-muted">Minimum penarikan Rp 50.000</small>
                                </div>
                                <?php $bank = database()->query("SELECT * FROM `shop_bank_accounts` WHERE `user_id` = {$this->user->user_id}")->fetch_object() ?? null ?>
                                <?php if($bank): ?>
                                    <div class="card border-0 bg-light">
                                        <div class="card-body py-2">
                                            <small class="text-muted d-block mb-1">Dana akan dikirim ke rekening:</small>
                                            <strong><?= htmlspecialchars($bank->bank_name) ?></strong><br>
                                            <span><?= htmlspecialchars($bank->account_number) ?> &bull; <?= htmlspecialchars($bank->account_name) ?></span>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle mr-2"></i>
                                        Belum ada rekening bank. Tambahkan di tab <strong>Shop Settings &rarr; Bank Account</strong>.
                                    </div>
                                <?php endif ?>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-success" <?= (!$bank || ($data->withdrawable_funds ?? 0) < 50000) ? 'disabled' : '' ?>>
                                    <i class="fas fa-paper-plane fa-sm mr-1"></i> Ajukan Penarikan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="table-responsive table-custom-container">
                <table class="table table-custom">
                    <thead>
                    <tr>
                        <th>Invoice / Tgl</th>
                        <th>Pembeli</th>
                        <th>Produk</th>
                        <th>Total Bayar</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                    </thead>
                    <tbody>
                        <?php if(count($data->orders) > 0): ?>
                            <?php foreach($data->orders as $order): ?>
                                <tr>
                                    <td>
                                        <div style="font-family:monospace;font-size:.85rem;color:#4f46e5;font-weight:600"><?= htmlspecialchars($order->invoice_number) ?></div>
                                        <small class="text-muted"><?= date('d M Y, H:i', strtotime($order->datetime)) ?></small>
                                    </td>
                                    <td>
                                        <div style="font-weight:600;font-size:.85rem"><?= htmlspecialchars($order->full_name ?? 'Data Terhapus') ?></div>
                                        <div style="font-size:.75rem;color:#64748b"><?= htmlspecialchars($order->email ?? 'N/A') ?></div>
                                        <?php if(!empty($order->phone)): ?>
                                            <div style="font-size:.75rem;color:#059669"><i class="fab fa-whatsapp"></i> <?= htmlspecialchars($order->phone) ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div style="font-weight:600;font-size:.85rem"><?= htmlspecialchars($order->item_name ?? 'Produk Dihapus') ?></div>
                                        <span class="badge badge-light" style="font-size:.7rem"><?= ucwords(str_replace('_',' ', $order->item_type ?? 'unknown')) ?></span>
                                    </td>
                                    <td>
                                        <div style="font-weight:700;font-size:.85rem">Rp <?= number_format($order->grand_total, 0, ',', '.') ?></div>
                                        <div style="font-size:.7rem;color:#94a3b8">Fee: Rp <?= number_format($order->service_fee, 0, ',', '.') ?></div>
                                    </td>
                                    <td>
                                        <?php if($order->status === 'paid'): ?>
                                            <span class="badge badge-success"><i class="fas fa-check-circle mr-1"></i> Paid</span>
                                        <?php elseif($order->status === 'pending'): ?>
                                            <span class="badge badge-warning"><i class="fas fa-clock mr-1"></i> Pending</span>
                                        <?php else: ?>
                                            <span class="badge badge-danger"><?= ucfirst($order->status) ?></span>
                                        <?php endif ?>
                                    </td>
                                    <td>
                                        <div style="display:flex;gap:6px;flex-direction:column;align-items:flex-start">
                                            <button class="btn btn-sm btn-light" style="font-size:.75rem;padding:4px 8px" onclick="openOrderDetail(<?= htmlspecialchars(json_encode([
                                                'invoice' => $order->invoice_number,
                                                'date' => date('d M Y, H:i', strtotime($order->datetime)),
                                                'buyer_name' => $order->full_name,
                                                'buyer_email' => $order->email,
                                                'buyer_phone' => $order->phone ?? '',
                                                'item_name' => $order->item_name,
                                                'item_type' => $order->item_type ?? '',
                                                'qty' => $order->qty,
                                                'grand_total' => $order->grand_total,
                                                'service_fee' => $order->service_fee,
                                                'discount_amount' => $order->discount_amount ?? 0,
                                                'shipping_cost' => $order->shipping_cost ?? 0,
                                                'shipping_address' => $order->shipping_address ?? '',
                                                'shipping_courier' => $order->shipping_courier ?? '',
                                                'shipping_service' => $order->shipping_service ?? '',
                                                'tracking_number' => $order->tracking_number ?? '',
                                                'status' => $order->status
                                            ])) ?>)">
                                                <i class="fas fa-eye text-primary"></i> Detail
                                            </button>

                                            <?php if(($order->item_type ?? '') === 'physical' && $order->status === 'paid'): ?>
                                                <?php if(empty($order->tracking_number)): ?>
                                                    <button class="btn btn-sm btn-primary" style="font-size:.75rem;padding:4px 8px" onclick="openResiModal(<?= htmlspecialchars(json_encode([
                                                        'id' => $order->id,
                                                        'invoice' => $order->invoice_number,
                                                        'courier' => $order->shipping_courier,
                                                        'service' => $order->shipping_service,
                                                        'address' => $order->shipping_address
                                                    ])) ?>)">
                                                        <i class="fas fa-truck"></i> Kirim (Resi)
                                                    </button>
                                                <?php else: ?>
                                                    <div style="font-size:.75rem;color:#1e293b;margin-bottom:2px">
                                                        <i class="fas fa-truck text-muted"></i> <strong><?= strtoupper(htmlspecialchars($order->shipping_courier ?? '')) ?></strong> <?= htmlspecialchars($order->shipping_service ?? '') ?>
                                                    </div>
                                                    <code style="font-size:.8rem;color:#059669;background:#d1fae5;padding:2px 6px;border-radius:4px"><?= htmlspecialchars($order->tracking_number) ?></code>
                                                    <div style="margin-top:4px">
                                                        <span class="badge badge-<?= $order->shipping_status === 'delivered' ? 'success' : 'info' ?>"><i class="fas fa-box-open mr-1"></i> <?= ucfirst($order->shipping_status) ?></span>
                                                        <button class="btn btn-xs btn-outline-secondary ml-1" onclick="openResiModal(<?= htmlspecialchars(json_encode([
                                                            'id' => $order->id,
                                                            'invoice' => $order->invoice_number,
                                                            'courier' => $order->shipping_courier,
                                                            'service' => $order->shipping_service,
                                                            'address' => $order->shipping_address,
                                                            'tracking' => $order->tracking_number,
                                                            's_status' => $order->shipping_status
                                                        ])) ?>)"><i class="fas fa-edit"></i></button>
                                                    </div>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <i class="fas fa-inbox text-muted mb-2 fa-2x d-block"></i>
                                    <h5 class="text-muted">Belum ada pesanan</h5>
                                </td>
                            </tr>
                        <?php endif ?>
                    </tbody>
                </table>
            </div>

            <!-- Resi Modal -->
            <div class="modal fade" id="resi_modal" tabindex="-1" role="dialog">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"><i class="fas fa-truck mr-2 text-primary"></i> Update Resi Pengiriman</h5>
                            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                        </div>
                        <form id="resi_form" onsubmit="event.preventDefault();submitResi()">
                            <div class="modal-body">
                                <div class="alert alert-info" style="font-size:.85rem">
                                    <strong><i class="fas fa-info-circle mr-1"></i> Info:</strong> Memperbarui resi akan memperbarui status pesanan. Anda bisa langsung mengirimkan notifikasi via WhatsApp.
                                </div>
                                
                                <div class="bg-light p-3 rounded mb-3" style="font-size:.85rem">
                                    <div class="mb-1"><span class="text-muted">Invoice:</span> <strong id="resi_inv"></strong></div>
                                    <div class="mb-1"><span class="text-muted">Kurir:</span> <strong id="resi_courier" class="text-uppercase"></strong> - <span id="resi_service"></span></div>
                                    <div><span class="text-muted d-block mb-1">Alamat Pengiriman:</span> <div id="resi_address" style="background:#fff;padding:8px;border:1px solid #e2e8f0;border-radius:6px;color:#1e293b"></div></div>
                                </div>

                                <input type="hidden" id="resi_order_id" name="order_id">
                                
                                <div class="form-group">
                                    <label>Nomor Resi <span class="text-danger">*</span></label>
                                    <input type="text" id="resi_tracking_number" name="tracking_number" class="form-control" required placeholder="Masukkan nomor resi yang valid">
                                </div>
                                
                                <div class="form-group">
                                    <label>Status Pengiriman</label>
                                    <select id="resi_shipping_status" name="shipping_status" class="form-control">
                                        <option value="shipped">Sedang Dikirim (Shipped)</option>
                                        <option value="delivered">Telah Diterima (Delivered)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-primary" id="btnSubmitResi"><i class="fab fa-whatsapp mr-1"></i> Simpan & Kirim via WA</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Detail Pesanan Modal -->
            <div class="modal fade" id="order_detail_modal" tabindex="-1" role="dialog">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title font-weight-bold"><i class="fas fa-receipt text-primary mr-1"></i> Rincian Pesanan</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body" style="font-size:.85rem;">
                            <div class="mb-3 d-flex justify-content-between">
                                <div>
                                    <span class="text-muted d-block" style="font-size:.75rem">Invoice</span>
                                    <strong id="det_invoice" class="text-primary" style="font-family:monospace"></strong>
                                </div>
                                <div class="text-right">
                                    <span class="text-muted d-block" style="font-size:.75rem">Tanggal</span>
                                    <span id="det_date"></span>
                                </div>
                            </div>
                            
                            <h6 class="border-bottom pb-2 mb-2 font-weight-bold">Informasi Pembeli</h6>
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-user text-muted mr-2" style="width:16px"></i>
                                <span id="det_name" class="font-weight-bold"></span>
                            </div>
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div>
                                    <i class="fas fa-envelope text-muted mr-2" style="width:16px"></i>
                                    <span id="det_email"></span>
                                </div>
                                <a id="btn_email" href="#" class="btn btn-sm btn-outline-primary py-0 px-2" style="font-size:.75rem"><i class="fas fa-paper-plane mr-1"></i> Hubungi</a>
                            </div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div>
                                    <i class="fab fa-whatsapp text-success mr-2" style="width:16px"></i>
                                    <span id="det_phone"></span>
                                </div>
                                <a id="btn_wa" href="#" target="_blank" class="btn btn-sm btn-success py-0 px-2" style="font-size:.75rem"><i class="fab fa-whatsapp mr-1"></i> Hubungi Customer</a>
                            </div>
                            
                            <h6 class="border-bottom pb-2 mb-2 font-weight-bold">Produk</h6>
                            <div class="d-flex justify-content-between mb-1">
                                <div><strong id="det_item_name"></strong> <span id="det_item_type" class="badge badge-light ml-1"></span></div>
                                <div>x <span id="det_qty"></span></div>
                            </div>

                            <div id="det_shipping_wrap" style="display:none;padding:10px;border-radius:8px;border:1px solid rgba(0,0,0,0.1);margin-top:10px;margin-bottom:10px" class="bg-light">
                                <span class="d-block font-weight-bold text-muted mb-1" style="font-size:.75rem">PENGIRIMAN</span>
                                <div class="mb-1"><i class="fas fa-truck text-primary mr-1"></i> <strong id="det_courier" class="text-uppercase"></strong> - <span id="det_service"></span></div>
                                <div id="det_tracking_wrap" class="mb-1 text-success font-weight-bold" style="display:none"><i class="fas fa-barcode mr-1"></i> Resi: <span id="det_tracking"></span></div>
                                <div class="mt-2 text-muted" style="line-height:1.4;white-space:pre-wrap"><i class="fas fa-map-marker-alt text-danger mr-1"></i><span id="det_address"></span></div>
                            </div>

                            <h6 class="border-bottom pb-2 mb-2 mt-3 font-weight-bold">Rincian Harga</h6>
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted">Subtotal Produk</span>
                                <span id="det_subtotal"></span>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted">Ongkos Kirim</span>
                                <span id="det_shipping_cost"></span>
                            </div>
                            <div class="d-flex justify-content-between mb-1 text-success" id="det_discount_wrap">
                                <span>Diskon Voucher</span>
                                <span id="det_discount"></span>
                            </div>
                            <div class="d-flex justify-content-between mt-2 pt-2 border-top">
                                <strong>Total Bayar</strong>
                                <strong id="det_grand_total" class="text-primary" style="font-size:1.1rem"></strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <script>
            function openOrderDetail(data) {
                document.getElementById('det_invoice').textContent = data.invoice;
                document.getElementById('det_date').textContent = data.date;
                document.getElementById('det_name').textContent = data.buyer_name;
                document.getElementById('det_email').textContent = data.buyer_email;
                document.getElementById('det_phone').textContent = data.buyer_phone || '-';
                
                document.getElementById('btn_email').href = "mailto:" + data.buyer_email;
                if(data.buyer_phone) {
                    var waNum = data.buyer_phone.replace(/[^0-9]/g, '');
                    if(waNum.startsWith('0')) waNum = '62' + waNum.substring(1);
                    document.getElementById('btn_wa').href = "https://wa.me/" + waNum;
                    document.getElementById('btn_wa').style.display = 'inline-block';
                } else {
                    document.getElementById('btn_wa').style.display = 'none';
                }

                document.getElementById('det_item_name').textContent = data.item_name;
                var tType = data.item_type === 'physical' ? 'Produk Fisik' : data.item_type.replace(/_/g, ' ').toUpperCase();
                document.getElementById('det_item_type').textContent = tType;
                document.getElementById('det_qty').textContent = data.qty;

                var subtotal = data.grand_total - data.shipping_cost + data.discount_amount;
                document.getElementById('det_subtotal').textContent = 'Rp ' + subtotal.toLocaleString('id-ID');
                document.getElementById('det_shipping_cost').textContent = 'Rp ' + data.shipping_cost.toLocaleString('id-ID');
                
                if(data.discount_amount > 0) {
                    document.getElementById('det_discount_wrap').style.display = 'flex';
                    document.getElementById('det_discount').textContent = '-Rp ' + data.discount_amount.toLocaleString('id-ID');
                } else {
                    document.getElementById('det_discount_wrap').style.display = 'none';
                }
                document.getElementById('det_grand_total').textContent = 'Rp ' + data.grand_total.toLocaleString('id-ID');

                if(data.item_type === 'physical') {
                    document.getElementById('det_shipping_wrap').style.display = 'block';
                    document.getElementById('det_courier').textContent = data.shipping_courier;
                    document.getElementById('det_service').textContent = data.shipping_service;
                    document.getElementById('det_address').textContent = data.shipping_address;
                    if(data.tracking_number) {
                        document.getElementById('det_tracking_wrap').style.display = 'block';
                        document.getElementById('det_tracking').textContent = data.tracking_number;
                    } else {
                        document.getElementById('det_tracking_wrap').style.display = 'none';
                    }
                } else {
                    document.getElementById('det_shipping_wrap').style.display = 'none';
                }

                $('#order_detail_modal').modal('show');
            }

            function openResiModal(data) {
                document.getElementById('resi_order_id').value = data.id;
                document.getElementById('resi_inv').textContent = data.invoice;
                document.getElementById('resi_courier').textContent = data.courier || '-';
                document.getElementById('resi_service').textContent = data.service || '-';
                document.getElementById('resi_address').innerHTML = (data.address || '-').replace(/\n/g, '<br>');
                
                document.getElementById('resi_tracking_number').value = data.tracking || '';
                document.getElementById('resi_shipping_status').value = data.s_status || 'shipped';
                
                $('#resi_modal').modal('show');
            }

            function submitResi() {
                var btn = document.getElementById('btnSubmitResi');
                var originalText = btn.innerHTML;
                btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> Menyimpan...';
                btn.disabled = true;

                var params = new URLSearchParams({
                    order_id: document.getElementById('resi_order_id').value,
                    tracking_number: document.getElementById('resi_tracking_number').value,
                    shipping_status: document.getElementById('resi_shipping_status').value,
                    token: '<?= \Altum\Csrf::get() ?>'
                });

                fetch('<?= url('shop-ajax?action=update_tracking') ?>', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: params
                })
                .then(r => r.json())
                .then(res => {
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                    if(res.success) {
                        $('#resi_modal').modal('hide');
                        if(res.wa_url) {
                            window.open(res.wa_url, '_blank');
                        } else {
                            alert(res.message);
                        }
                        window.location.reload();
                    } else {
                        alert(res.message);
                    }
                }).catch(e => {
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                    alert('Terjadi kesalahan jaringan.');
                });
            }
            </script>
        </div>
        <!-- WITHDRAWALS TAB -->
        <div class="tab-pane fade" id="withdrawals" role="tabpanel">
            <div class="d-flex align-items-center mb-4">
                <div style="width:38px;height:38px;border-radius:10px;background:linear-gradient(135deg,#059669,#10b981);display:flex;align-items:center;justify-content:center;margin-right:12px;flex-shrink:0;">
                    <i class="fas fa-money-bill-wave text-white"></i>
                </div>
                <div>
                    <h2 class="h5 mb-0 font-weight-bold">Riwayat Penarikan Dana</h2>
                    <small class="text-muted">Pantau status pencairan saldo toko kamu</small>
                </div>
            </div>

            <div class="table-responsive table-custom-container">
                <table class="table table-custom">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Jumlah</th>
                        <th>Rekening Tujuan</th>
                        <th>Tanggal Pengajuan</th>
                        <th>Status</th>
                    </tr>
                    </thead>
                    <tbody>
                        <?php if(count($data->withdrawals ?? []) > 0): ?>
                            <?php foreach($data->withdrawals as $wd): ?>
                                <tr>
                                    <td class="text-muted">#<?= $wd->id ?></td>
                                    <td>
                                        <div style="font-weight:700;font-size:.9rem;color:#059669">Rp <?= number_format($wd->amount, 0, ',', '.') ?></div>
                                    </td>
                                    <td>
                                        <?php if(!empty($wd->bank_name)): ?>
                                            <div style="font-weight:600;font-size:.85rem"><?= htmlspecialchars($wd->bank_name) ?></div>
                                            <div style="font-size:.75rem;color:#64748b"><?= htmlspecialchars($wd->account_number) ?> &bull; <?= htmlspecialchars($wd->account_name) ?></div>
                                        <?php else: ?>
                                            <span class="text-muted">—</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div style="font-size:.85rem"><?= date('d M Y', strtotime($wd->datetime)) ?></div>
                                        <div style="font-size:.75rem;color:#64748b"><?= date('H:i', strtotime($wd->datetime)) ?></div>
                                    </td>
                                    <td>
                                        <?php if($wd->status === 'pending'): ?>
                                            <span class="badge badge-warning"><i class="fas fa-clock mr-1"></i> Sedang Diproses</span>
                                        <?php elseif($wd->status === 'paid'): ?>
                                            <span class="badge badge-success"><i class="fas fa-check-circle mr-1"></i> Berhasil Transfer</span>
                                        <?php elseif($wd->status === 'rejected'): ?>
                                            <span class="badge badge-danger"><i class="fas fa-times-circle mr-1"></i> Ditolak</span>
                                        <?php else: ?>
                                            <span class="badge badge-secondary"><?= ucfirst($wd->status) ?></span>
                                        <?php endif ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <i class="fas fa-wallet text-muted mb-2 fa-2x d-block"></i>
                                    <h5 class="text-muted">Belum ada riwayat penarikan</h5>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- WEBHOOK EVENTS TAB -->
        <div class="tab-pane fade" id="webhook_events" role="tabpanel">

            <!-- Header -->
            <div class="d-flex align-items-center mb-4">
                <div style="width:38px;height:38px;border-radius:10px;background:linear-gradient(135deg,#4f46e5,#818cf8);display:flex;align-items:center;justify-content:center;margin-right:12px;flex-shrink:0;">
                    <i class="fas fa-plug text-white"></i>
                </div>
                <div>
                    <h2 class="h5 mb-0 font-weight-bold">Webhook Events</h2>
                    <small class="text-muted">Kirim data otomatis ke sistem lain setiap ada pembelian</small>
                </div>
            </div>

            <!-- What is webhook -->
            <div class="card border-0 mb-3" style="background:linear-gradient(135deg,#eef2ff,#f0fdf4);border-radius:14px;">
                <div class="card-body pb-3">
                    <div class="d-flex align-items-start mb-3">
                        <i class="fas fa-info-circle text-primary mr-2 mt-1" style="font-size:1rem;flex-shrink:0;"></i>
                        <p class="mb-0 text-muted" style="font-size:.875rem;line-height:1.6;">
                            Webhook memungkinkan toko kamu <strong>memberi tahu sistem lain</strong> — seperti bot WhatsApp, Google Sheets, Make, atau Zapier — secara otomatis setiap kali pembeli berhasil melakukan pembayaran.
                        </p>
                    </div>
                    <div style="display:flex;border-radius:10px;border:1.5px solid #e0e7ff;overflow:hidden;">
                        <div class="p-3 text-center" style="flex:1;border-right:1.5px solid #e0e7ff;background:#fff;">
                            <div style="width:32px;height:32px;border-radius:50%;background:#4f46e5;color:#fff;display:flex;align-items:center;justify-content:center;margin:0 auto 8px;font-size:.78rem;font-weight:700;">1</div>
                            <div style="font-size:.78rem;font-weight:600;color:#1e293b;margin-bottom:4px;">Buat Produk</div>
                            <div style="font-size:.72rem;color:#64748b;">Pilih tipe <strong>Webhook Event</strong></div>
                        </div>
                        <div class="p-3 text-center" style="flex:1;border-right:1.5px solid #e0e7ff;background:#fff;">
                            <div style="width:32px;height:32px;border-radius:50%;background:#4f46e5;color:#fff;display:flex;align-items:center;justify-content:center;margin:0 auto 8px;font-size:.78rem;font-weight:700;">2</div>
                            <div style="font-size:.78rem;font-weight:600;color:#1e293b;margin-bottom:4px;">Isi Webhook URL</div>
                            <div style="font-size:.72rem;color:#64748b;">Di form edit produk</div>
                        </div>
                        <div class="p-3 text-center" style="flex:1;background:#fff;">
                            <div style="width:32px;height:32px;border-radius:50%;background:#059669;color:#fff;display:flex;align-items:center;justify-content:center;margin:0 auto 8px;">
                                <i class="fas fa-bolt" style="font-size:.75rem;"></i>
                            </div>
                            <div style="font-size:.78rem;font-weight:600;color:#1e293b;margin-bottom:4px;">Otomatis Kirim</div>
                            <div style="font-size:.72rem;color:#64748b;">Setiap transaksi berhasil</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payload Fields -->
            <div class="card border-0 mb-4" style="border-radius:14px;box-shadow:0 2px 8px rgba(0,0,0,.06);">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <i class="fas fa-code text-primary mr-2"></i>
                        <strong style="font-size:.875rem;">Data JSON yang Dikirim</strong>
                    </div>
                    <div class="row">
                        <?php
                        $pf = [
                            ['event',          'purchase_success',     'Jenis kejadian',    '#4f46e5'],
                            ['invoice',        'INV-SHOP-ABC123',      'Nomor invoice',     '#0891b2'],
                            ['customer_name',  'Budi Santoso',         'Nama pembeli',      '#7c3aed'],
                            ['customer_email', 'budi@email.com',       'Email pembeli',     '#7c3aed'],
                            ['product_id',     '5',                    'ID produk',         '#059669'],
                            ['product_name',   'Akun Netflix 1 Bulan', 'Nama produk',       '#059669'],
                            ['amount',         '50000',                'Total harga (Rp)',  '#d97706'],
                            ['datetime',       '2026-05-07 17:00:00',  'Waktu transaksi',   '#64748b'],
                        ];
                        foreach($pf as $f):
                        ?>
                        <div class="col-12 col-md-6 mb-2">
                            <div class="d-flex align-items-start p-2" style="border:1px solid #f1f5f9;border-radius:10px;background:#fafafa;">
                                <div style="flex-shrink:0;margin-right:10px;margin-top:1px;">
                                    <code style="background:<?= $f[3] ?>1a;color:<?= $f[3] ?>;padding:2px 7px;border-radius:6px;font-size:.72rem;white-space:nowrap;"><?= $f[0] ?></code>
                                </div>
                                <div style="min-width:0;">
                                    <div style="font-size:.72rem;color:#64748b;margin-bottom:1px;"><?= $f[2] ?></div>
                                    <code style="font-size:.7rem;color:#94a3b8;word-break:break-all;"><?= htmlspecialchars($f[1]) ?></code>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="mt-2 pt-2" style="border-top:1px solid #f1f5f9;">
                        <small class="text-muted">
                            <i class="fas fa-flask text-warning mr-1"></i>
                            <strong>Test:</strong> Gunakan <a href="https://webhook.site" target="_blank" class="text-primary">webhook.site</a> untuk melihat payload secara real-time.
                        </small>
                    </div>
                </div>
            </div>

            <!-- Event Log -->
            <div class="d-flex align-items-center justify-content-between mb-3">
                <strong style="font-size:.875rem;"><i class="fas fa-history text-muted mr-2"></i>Log Event</strong>
                <span class="badge badge-secondary" style="font-size:.7rem;"><?= count($data->webhook_events) ?> event</span>
            </div>

            <?php if(count($data->webhook_events) > 0): ?>
            <div style="display:flex;flex-direction:column;gap:8px;">
                <?php foreach($data->webhook_events as $wh): ?>
                <?php $ok = $wh->status_code >= 200 && $wh->status_code < 300; ?>
                <div class="card border-0" style="border-radius:12px;border-left:4px solid <?= $ok ? '#10b981' : '#ef4444' ?> !important;box-shadow:0 1px 5px rgba(0,0,0,.05);">
                    <div class="card-body py-3 px-3">
                        <div class="d-flex align-items-center justify-content-between flex-wrap" style="gap:8px;">
                            <div class="d-flex align-items-center" style="gap:10px;min-width:0;">
                                <div style="width:30px;height:30px;border-radius:8px;background:<?= $ok ? '#d1fae5' : '#fee2e2' ?>;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <i class="fas fa-<?= $ok ? 'check' : 'times' ?>" style="color:<?= $ok ? '#059669' : '#dc2626' ?>;font-size:.75rem;"></i>
                                </div>
                                <div style="min-width:0;">
                                    <div style="font-weight:600;font-size:.82rem;color:#1e293b;"><?= htmlspecialchars($wh->item_name ?? 'Produk tidak dikenal') ?></div>
                                    <div style="font-size:.7rem;color:#94a3b8;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:280px;" title="<?= htmlspecialchars($wh->webhook_url) ?>">
                                        <i class="fas fa-link fa-xs mr-1"></i><?= htmlspecialchars($wh->webhook_url) ?>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center" style="gap:6px;flex-shrink:0;">
                                <span style="font-size:.7rem;color:#94a3b8;white-space:nowrap;"><i class="fas fa-clock fa-xs mr-1"></i><?= \Altum\Date::get($wh->datetime, 1) ?></span>
                                <span class="badge badge-<?= $ok ? 'success' : 'danger' ?>"><?= $wh->status_code ?: 'Error' ?></span>
                                <button class="btn btn-sm btn-light" style="padding:2px 8px;font-size:.7rem;border-radius:6px;"
                                    onclick="var d=document.getElementById('whp_<?= $wh->id ?>');d.style.display=d.style.display==='none'?'block':'none'">
                                    <i class="fas fa-code fa-xs mr-1"></i>Payload
                                </button>
                            </div>
                        </div>
                        <div id="whp_<?= $wh->id ?>" style="display:none;margin-top:10px;">
                            <pre style="background:#1e293b;color:#a5b4fc;border-radius:8px;padding:12px;font-size:.73rem;margin:0;overflow-x:auto;max-height:180px;"><?= htmlspecialchars(json_encode(json_decode($wh->payload), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) ?></pre>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="card border-0 text-center" style="border-radius:14px;padding:40px 20px;background:#f8faff;">
                <div style="width:54px;height:54px;border-radius:50%;background:#e0e7ff;display:flex;align-items:center;justify-content:center;margin:0 auto 14px;">
                    <i class="fas fa-plug text-primary fa-lg"></i>
                </div>
                <div style="font-weight:600;color:#1e293b;margin-bottom:4px;">Belum ada event terkirim</div>
                <div style="font-size:.82rem;color:#94a3b8;">Buat produk bertipe Webhook Event untuk mulai menerima notifikasi otomatis.</div>
            </div>
            <?php endif; ?>
        </div>

        <div class="tab-pane fade" id="shop_settings" role="tabpanel">
            <?php
            $bank_account = database()->query("SELECT * FROM `shop_bank_accounts` WHERE `user_id` = {$this->user->user_id}")->fetch_object() ?? null;
            $notification_settings = json_decode($data->shop->notification_settings ?? '{}', true);
            ?>

            <!-- Verifikasi Identitas -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-id-card mr-2"></i> Verifikasi Identitas Seller</h5>
                </div>
                <div class="card-body">
                    <?php $vs = $data->verification_status ?? 'unverified'; ?>
                    
                    <?php if($vs === 'verified'): ?>
                        <div class="alert alert-success mb-0">
                            <i class="fas fa-check-circle mr-2"></i> <strong>Terverifikasi.</strong> Identitas kamu sudah terverifikasi. Kamu dapat melakukan pencairan dana kapan saja.
                        </div>
                    <?php elseif($vs === 'pending'): ?>
                        <div class="alert alert-warning mb-0">
                            <i class="fas fa-clock mr-2"></i> <strong>Sedang Direview.</strong> Dokumen kamu sedang direview oleh admin (1-3 hari kerja).
                        </div>
                    <?php elseif($vs === 'rejected' || $vs === 'unverified'): ?>
                        <?php if($vs === 'rejected'): ?>
                            <div class="alert alert-danger mb-4">
                                <i class="fas fa-times-circle mr-2"></i> <strong>Ditolak.</strong> Verifikasi sebelumnya ditolak. Silakan perbaiki dokumen dan upload ulang.
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info mb-4">
                                <i class="fas fa-info-circle mr-2"></i> <strong>Belum Verifikasi.</strong> Silakan lengkapi verifikasi identitas (KTP) untuk mengaktifkan fitur pencairan dana.
                            </div>
                        <?php endif; ?>

                        <form action="<?= url('shop-verification') ?>" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="token" value="<?= \Altum\Csrf::get() ?>">
                            
                            <div class="form-group">
                                <label for="full_name"><i class="fas fa-user mr-1"></i> Nama Lengkap (Sesuai KTP) <span class="text-danger">*</span></label>
                                <input type="text" id="full_name" name="full_name" class="form-control" required placeholder="Masukkan nama lengkap">
                            </div>

                            <div class="form-group">
                                <label for="nik"><i class="fas fa-id-badge mr-1"></i> Nomor Induk Kependudukan (NIK) <span class="text-danger">*</span></label>
                                <input type="text" id="nik" name="nik" class="form-control" required minlength="16" maxlength="16" pattern="[0-9]{16}" placeholder="16 Digit NIK">
                                <small class="form-text text-muted">Hanya masukkan 16 digit angka NIK tanpa spasi.</small>
                            </div>

                            <div class="form-group">
                                <label><i class="fas fa-image mr-1"></i> Foto KTP <span class="text-danger">*</span></label>
                                <!-- KTP Preview Dropzone -->
                                <div style="position:relative;width:100%;padding-top:56.25%;background:var(--gray-100, #f3f4f6);border-radius:10px;overflow:hidden;border:2px dashed var(--gray-300, #d1d5db);margin-bottom:8px;cursor:pointer;" onclick="document.getElementById('ktp_image').click()" id="ktp_dropzone">
                                    <img id="ktp_preview" src="" alt="KTP" style="position:absolute;inset:0;width:100%;height:100%;object-fit:contain;display:none;">
                                    <div id="ktp_placeholder" style="position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:6px;">
                                        <i class="fas fa-id-card" style="font-size:2rem;color:#9ca3af;"></i>
                                        <span style="font-size:.82rem;font-weight:500;">Klik untuk pilih foto KTP</span>
                                        <span style="font-size:.74rem;color:#9ca3af;">JPG / PNG / WEBP, maks. 5 MB</span>
                                    </div>
                                </div>
                                <input type="file" id="ktp_image" name="ktp_image" accept="image/*" required style="display:none;"
                                       onchange="previewShopImage(this,'ktp_preview','ktp_placeholder')">
                                <small class="form-text text-muted">Pastikan foto KTP jelas, terang, dan semua tulisan terbaca.</small>
                            </div>

                            <div class="form-group">
                                <label><i class="fas fa-camera mr-1"></i> Foto Selfie dengan KTP <span class="text-danger">*</span></label>
                                <!-- Selfie Preview Dropzone -->
                                <div style="position:relative;width:100%;padding-top:56.25%;background:var(--gray-100, #f3f4f6);border-radius:10px;overflow:hidden;border:2px dashed var(--gray-300, #d1d5db);margin-bottom:8px;cursor:pointer;" onclick="document.getElementById('selfie_image').click()" id="selfie_dropzone">
                                    <img id="selfie_preview" src="" alt="Selfie" style="position:absolute;inset:0;width:100%;height:100%;object-fit:contain;display:none;">
                                    <div id="selfie_placeholder" style="position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:6px;">
                                        <i class="fas fa-camera" style="font-size:2rem;color:#9ca3af;"></i>
                                        <span style="font-size:.82rem;font-weight:500;">Klik untuk pilih foto selfie</span>
                                        <span style="font-size:.74rem;color:#9ca3af;">Wajah + KTP dalam satu foto</span>
                                    </div>
                                </div>
                                <input type="file" id="selfie_image" name="selfie_image" accept="image/*" required style="display:none;"
                                       onchange="previewShopImage(this,'selfie_preview','selfie_placeholder')">
                                <small class="form-text text-muted">Pegang KTP di dekat wajah kamu. Pastikan wajah dan KTP tidak terpotong.</small>
                            </div>

                            <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane mr-1"></i> Kirim Verifikasi</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Shop Info -->
            <form action="<?= url('shop-settings-update') ?>" method="post" enctype="multipart/form-data" class="mb-5">
                <input type="hidden" name="token" value="<?= \Altum\Csrf::get() ?>" />
                <input type="hidden" name="section" value="info" />

                <h5 class="mb-3 border-bottom pb-2"><i class="fas fa-store fa-sm text-primary mr-2"></i> General Info</h5>

                <!-- Cover Image -->
                <div class="form-group">
                    <label class="font-weight-bold">Shop Cover Image</label>

                    <!-- Cover Preview: aspect ratio 3:1 -->
                    <div style="position:relative;width:100%;padding-top:33.33%;background:#f3f4f6;border-radius:10px;overflow:hidden;border:2px dashed <?= $data->shop->cover_image ? '#6366f1' : '#d1d5db' ?>;margin-bottom:10px;cursor:pointer;" onclick="document.getElementById('cover_image_input').click()" id="cover_dropzone">
                        <!-- Current / preview image -->
                        <img id="cover_preview"
                             src="<?= $data->shop->cover_image ? \Altum\Uploads::get_full_url('shop_covers') . $data->shop->cover_image : '' ?>"
                             alt="Cover"
                             style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;object-position:center;<?= !$data->shop->cover_image ? 'display:none' : '' ?>">
                        <!-- Placeholder -->
                        <div id="cover_placeholder_inner" style="position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:6px;<?= $data->shop->cover_image ? 'display:none!important' : '' ?>">
                            <i class="fas fa-cloud-upload-alt" style="font-size:1.8rem;color:#9ca3af;"></i>
                            <span style="font-size:.82rem;color:#6b7280;font-weight:500;">Klik untuk pilih foto cover</span>
                            <span style="font-size:.74rem;color:#9ca3af;">atau drag &amp; drop di sini</span>
                        </div>
                        <!-- Hover overlay -->
                        <div id="cover_hover_overlay" style="position:absolute;inset:0;background:rgba(79,70,229,.45);display:none;align-items:center;justify-content:center;border-radius:8px;">
                            <span style="color:#fff;font-size:.85rem;font-weight:600;"><i class="fas fa-pencil-alt mr-2"></i>Ganti Foto Cover</span>
                        </div>
                    </div>

                    <!-- Info box -->
                    <div style="background:#eef2ff;border:1px solid #c7d2fe;border-radius:8px;padding:10px 14px;margin-bottom:10px;display:flex;align-items:flex-start;gap:10px;">
                        <i class="fas fa-info-circle" style="color:#6366f1;margin-top:2px;flex-shrink:0;"></i>
                        <div style="font-size:.8rem;color:#4338ca;line-height:1.5;">
                            <strong>Rekomendasi ukuran cover:</strong> Rasio <strong>3:1</strong> (lebar × tinggi)<br>
                            Contoh: <strong>1500 × 500 px</strong> atau <strong>1200 × 400 px</strong><br>
                            <span style="color:#6366f1;">Format: JPG, PNG, WEBP · Maks. 5 MB</span>
                        </div>
                    </div>

                    <input type="file" id="cover_image_input" name="cover_image" accept="image/*" style="display:none;"
                           onchange="previewShopImage(this,'cover_preview','cover_placeholder_inner')">
                </div>

                <!-- Shop URL -->
                <div class="form-group">
                    <label class="font-weight-bold">Shop URL</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><?= SITE_URL ?>store/</span>
                        </div>
                        <input type="text" name="url" class="form-control" value="<?= htmlspecialchars($data->shop->url) ?>">
                    </div>
                </div>

                <!-- Shop Logo -->
                <!-- Shop Logo -->
                <div class="form-group">
                    <label class="font-weight-bold">Shop Logo</label>
                    <small class="d-block text-muted mb-2">Digunakan sebagai ikon toko di halaman store dan invoice (jika Whitelabel aktif).</small>

                    <div style="display:flex;align-items:flex-start;gap:20px;flex-wrap:wrap;">
                        <!-- Logo upload box: 1:1 ratio -->
                        <div style="position:relative;width:110px;height:110px;border-radius:14px;overflow:hidden;border:2px dashed <?= $data->shop->logo_image ? '#6366f1' : '#d1d5db' ?>;background:#f3f4f6;cursor:pointer;flex-shrink:0;"
                             onclick="document.getElementById('logo_image_input').click()"
                             onmouseenter="document.getElementById('logo_hover_overlay').style.display='flex'"
                             onmouseleave="document.getElementById('logo_hover_overlay').style.display='none'">
                            <img id="logo_preview"
                                 src="<?= $data->shop->logo_image ? \Altum\Uploads::get_full_url('shop_logos') . $data->shop->logo_image : '' ?>"
                                 alt="Logo"
                                 style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;<?= !$data->shop->logo_image ? 'display:none' : '' ?>">
                            <div id="logo_placeholder_inner" style="position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:4px;<?= $data->shop->logo_image ? 'display:none!important' : '' ?>">
                                <i class="fas fa-store" style="font-size:1.5rem;color:#9ca3af;"></i>
                                <span style="font-size:.68rem;color:#9ca3af;text-align:center;line-height:1.3;padding:0 4px;">Klik upload<br>logo</span>
                            </div>
                            <div id="logo_hover_overlay" style="position:absolute;inset:0;background:rgba(79,70,229,.5);display:none;align-items:center;justify-content:center;">
                                <i class="fas fa-pencil-alt" style="color:#fff;font-size:1.2rem;"></i>
                            </div>
                        </div>

                        <!-- Recommendation info -->
                        <div style="background:#eef2ff;border:1px solid #c7d2fe;border-radius:8px;padding:10px 14px;font-size:.8rem;color:#4338ca;line-height:1.6;flex:1;min-width:220px;align-self:center;">
                            <i class="fas fa-info-circle mr-1" style="color:#6366f1;"></i>
                            <strong>Rekomendasi ukuran logo:</strong> Rasio <strong>1:1</strong><br>
                            Contoh: <strong>500 × 500 px</strong> atau <strong>200 × 200 px</strong><br>
                            <span style="color:#6366f1;">Format: JPG, PNG, WEBP, SVG · Maks. 2 MB</span>
                        </div>
                    </div>

                    <input type="file" id="logo_image_input" name="logo_image" accept="image/*" style="display:none;"
                           onchange="previewShopImage(this,'logo_preview','logo_placeholder_inner')">
                </div>


                <div class="form-group">
                    <label class="font-weight-bold">Shop Title</label>
                    <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($data->shop->name) ?>">
                </div>
                <div class="form-group">
                    <label class="font-weight-bold">Shop Description</label>
                    <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($data->shop->description) ?></textarea>
                </div>

                <!-- Review Toggle -->
                <div class="form-group">
                    <label class="font-weight-bold d-block">Review</label>
                    <small class="text-muted d-block mb-2">Allow customers to leave a review</small>
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="is_review_enabled" name="is_review_enabled" value="1" <?= ($data->shop->is_review_enabled ?? 1) ? 'checked' : '' ?>>
                        <label class="custom-control-label" for="is_review_enabled">Enable Reviews</label>
                    </div>
                </div>

                <!-- Deactivate Shop -->
                <div class="form-group">
                    <label class="font-weight-bold d-block text-danger">Deactivate Shop</label>
                    <small class="text-muted d-block mb-2">Temporarily disable your shop. Shop access will be disabled, and items in this shop cannot be purchased.</small>
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="0" <?= !($data->shop->is_active ?? 1) ? 'checked' : '' ?>>
                        <label class="custom-control-label" for="is_active">Deactivate Shop</label>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary"><i class="fas fa-save fa-sm mr-1"></i> Save Changes</button>
            </form>

            <!-- Bank Account -->
            <form action="<?= url('shop-settings-update') ?>" method="post" enctype="multipart/form-data" class="mb-5">
                <input type="hidden" name="token" value="<?= \Altum\Csrf::get() ?>" />
                <input type="hidden" name="section" value="bank" />

                <h5 class="mb-1 border-bottom pb-2"><i class="fas fa-university fa-sm text-primary mr-2"></i> Bank Account</h5>
                <small class="text-muted d-block mb-3">Bank information settings for withdrawal process.</small>

                <?php if(!$bank_account): ?>
                    <p class="text-muted"><i class="fas fa-info-circle mr-1"></i> No information yet</p>
                <?php endif ?>

                <div class="row">
                    <div class="col-md-4 form-group">
                        <label>Bank Name</label>
                        <input type="text" name="bank_name" class="form-control" placeholder="BCA, BRI, Mandiri..." value="<?= htmlspecialchars($bank_account->bank_name ?? '') ?>">
                    </div>
                    <div class="col-md-4 form-group">
                        <label>Account Number</label>
                        <input type="text" name="account_number" class="form-control" placeholder="1234567890" value="<?= htmlspecialchars($bank_account->account_number ?? '') ?>">
                    </div>
                    <div class="col-md-4 form-group">
                        <label>Account Name</label>
                        <input type="text" name="account_name" class="form-control" placeholder="Nama pemilik rekening" value="<?= htmlspecialchars($bank_account->account_name ?? '') ?>">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save fa-sm mr-1"></i> Save Bank Info</button>
            </form>

            <!-- Email Notification -->
            <form action="<?= url('shop-settings-update') ?>" method="post">
                <input type="hidden" name="token" value="<?= \Altum\Csrf::get() ?>" />
                <input type="hidden" name="section" value="notification" />

                <h5 class="mb-1 border-bottom pb-2"><i class="fas fa-envelope fa-sm text-primary mr-2"></i> Email Notification</h5>
                <small class="text-muted d-block mb-3">Email notification management for your shop.</small>

                <div class="form-group">
                    <div class="custom-control custom-switch mb-2">
                        <input type="checkbox" class="custom-control-input" id="notify_purchase" name="notify_purchase" value="1" <?= ($notification_settings['notify_purchase'] ?? 1) ? 'checked' : '' ?>>
                        <label class="custom-control-label" for="notify_purchase">
                            <strong>Purchase Success Notification</strong><br>
                            <small class="text-muted">Send notification when an item is successfully purchased</small>
                        </label>
                    </div>
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="notify_review" name="notify_review" value="1" <?= ($notification_settings['notify_review'] ?? 1) ? 'checked' : '' ?>>
                        <label class="custom-control-label" for="notify_review">
                            <strong>Review Notification</strong><br>
                            <small class="text-muted">Send notification when a buyer leaves a rating for an item they purchased.</small>
                        </label>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save fa-sm mr-1"></i> Save Notifications</button>
            </form>

            <!-- ── Alamat Asal Pengiriman (Produk Fisik) ── -->
            <form action="<?= url('shop-settings-update') ?>" method="post" id="origin_city_form" class="mb-3">
                <input type="hidden" name="token" value="<?= \Altum\Csrf::get() ?>" />
                <input type="hidden" name="section" value="origin_city" />
                <input type="hidden" name="origin_city_id"   id="origin_city_id_input"   value="<?= htmlspecialchars($data->shop->origin_city_id ?? '') ?>">
                <input type="hidden" name="origin_city_name" id="origin_city_name_input" value="<?= htmlspecialchars($data->shop->origin_city_name ?? '') ?>">
                <input type="hidden" name="origin_province"  id="origin_province_input"  value="<?= htmlspecialchars($data->shop->origin_province ?? '') ?>">

                <h5 class="mb-1 border-bottom pb-2" id="shop_settings_origin">
                    <i class="fas fa-map-marker-alt fa-sm text-success mr-2"></i> Alamat Asal Pengiriman
                </h5>
                <small class="text-muted d-block mb-3">Digunakan untuk menghitung ongkos kirim produk fisik via RajaOngkir.</small>

                <?php if(!empty($data->shop->origin_city_name)): ?>
                <div class="alert alert-success d-flex align-items-center py-2 mb-3">
                    <i class="fas fa-check-circle mr-2"></i>
                    <span>Kota asal saat ini: <strong><?= htmlspecialchars($data->shop->origin_province) ?> — <?= htmlspecialchars($data->shop->origin_city_name) ?></strong></span>
                </div>
                <?php else: ?>
                <div class="alert alert-warning d-flex align-items-center py-2 mb-3">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <span>Belum ada kota asal. Isi sekarang untuk bisa menjual produk fisik.</span>
                </div>
                <?php endif; ?>

                <div class="row">
                    <div class="col-md-5 form-group">
                        <label class="font-weight-bold">Provinsi</label>
                        <select id="origin_province_select" class="form-control" onchange="loadOriginCities(this.value, this.options[this.selectedIndex].text)">
                            <option value="">-- Pilih Provinsi --</option>
                        </select>
                    </div>
                    <div class="col-md-5 form-group">
                        <label class="font-weight-bold">Kota / Kabupaten</label>
                        <select id="origin_city_select" class="form-control" onchange="selectOriginCity(this.value, this.options[this.selectedIndex].text)">
                            <option value="">-- Pilih Kota --</option>
                        </select>
                    </div>
                    <div class="col-md-2 form-group d-flex align-items-end">
                        <button type="submit" class="btn btn-success w-100">
                            <i class="fas fa-save fa-sm mr-1"></i> Simpan
                        </button>
                    </div>
                </div>
            </form>

            <script>
            (function() {
                var AJAX = '<?= url('shop-ajax') ?>';

                // Load provinces on page load
                fetch(AJAX + '?action=ongkir_provinces')
                .then(function(r){ return r.json(); })
                .then(function(res) {
                    if(!res.success) return;
                    var sel = document.getElementById('origin_province_select');
                    var currentProv = '<?= addslashes($data->shop->origin_province ?? '') ?>';
                    res.data.forEach(function(p) {
                        var opt = new Option(p.province, p.province_id);
                        if(p.province === currentProv) opt.selected = true;
                        sel.add(opt);
                    });
                    // Refresh Select2 if available
                    if (typeof $ !== 'undefined') {
                        $(sel).trigger('change.select2');
                    }
                    
                    // Auto-load cities if province already selected
                    if(sel.value) {
                        loadOriginCities(sel.value, sel.options[sel.selectedIndex].text, true);
                    }
                });

                window.loadOriginCities = function(province_id, province_name, skipProvSave) {
                    if(!province_id) return;
                    if(!skipProvSave) {
                        document.getElementById('origin_province_input').value = province_name;
                    }
                    var citySelect = document.getElementById('origin_city_select');
                    citySelect.innerHTML = '<option>Memuat...</option>';
                    fetch(AJAX + '?action=ongkir_cities&province_id=' + province_id)
                    .then(function(r){ return r.json(); })
                    .then(function(res) {
                        if(!res.success) return;
                        var currentCityId = '<?= addslashes($data->shop->origin_city_id ?? '') ?>';
                        citySelect.innerHTML = '<option value="">-- Pilih Kota --</option>';
                        res.data.forEach(function(c) {
                            var label = c.type + ' ' + c.city_name;
                            var opt   = new Option(label, c.city_id);
                            if(c.city_id === currentCityId) {
                                opt.selected = true;
                                document.getElementById('origin_city_id_input').value   = c.city_id;
                                document.getElementById('origin_city_name_input').value = label;
                            }
                            citySelect.add(opt);
                        });
                        
                        // Refresh Select2 if available
                        if (typeof $ !== 'undefined') {
                            $(citySelect).trigger('change.select2');
                        }
                    });
                };

                window.selectOriginCity = function(city_id, city_name) {
                    document.getElementById('origin_city_id_input').value   = city_id;
                    document.getElementById('origin_city_name_input').value = city_name;
                };
            })();
            </script>

        </div>
    </div>
</section>


<!-- VOUCHER CREATE MODAL -->
<div class="modal fade" id="voucherCreateModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title"><i class="fas fa-ticket-alt mr-2 text-primary"></i> <span id="vcModalTitle">Add New Voucher</span></h5><button type="button" class="close" data-dismiss="modal">&times;</button></div>
      <div class="modal-body">
        <input type="hidden" id="vcId" value="">
        <div class="row">
          <div class="col-md-8 form-group">
            <label class="font-weight-bold">Voucher Code</label>
            <input type="text" class="form-control" id="vcCode" placeholder="KODE123" style="text-transform:uppercase">
          </div>
          <div class="col-md-4 form-group d-flex flex-column justify-content-center">
            <label class="font-weight-bold d-block">Is Active</label>
            <div class="custom-control custom-switch mt-1">
              <input type="checkbox" class="custom-control-input" id="vcIsActive" checked>
              <label class="custom-control-label" for="vcIsActive">Aktif</label>
            </div>
          </div>
        </div>
        <div class="form-group">
          <label class="font-weight-bold">Period</label>
          <div class="row">
            <div class="col-6"><input type="date" class="form-control" id="vcFrom" placeholder="Valid From"></div>
            <div class="col-6"><input type="date" class="form-control" id="vcTo" placeholder="Valid To"></div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-4 form-group d-flex flex-column justify-content-center">
            <label class="font-weight-bold d-block">Is Unlimited</label>
            <div class="custom-control custom-switch mt-1">
              <input type="checkbox" class="custom-control-input" id="vcIsUnlimited" onchange="toggleVcQuota()">
              <label class="custom-control-label" for="vcIsUnlimited">Unlimited</label>
            </div>
          </div>
          <div class="col-md-4 form-group" id="vcQuotaGroup">
            <label class="font-weight-bold">Quota</label>
            <input type="number" class="form-control" id="vcQuota" min="0" value="0">
          </div>
          <div class="col-md-4 form-group">
            <label class="font-weight-bold">Discount</label>
            <div class="input-group">
              <input type="number" class="form-control" id="vcDiscount" min="1" max="100" value="10">
              <div class="input-group-append"><span class="input-group-text">%</span></div>
            </div>
          </div>
        </div>
        <div class="form-group">
          <label class="font-weight-bold">Use Product <small class="text-muted">(opsional)</small></label>
          <select class="form-control" id="vcItemId">
            <option value="">Select item (all items)</option>
            <?php foreach($data->items as $itm): ?>
              <option value="<?= $itm->id ?>"><?= htmlspecialchars($itm->name) ?></option>
            <?php endforeach; ?>
          </select>
          <small class="text-muted">Optionally select product to use this voucher</small>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" id="vcSaveBtn" onclick="saveVoucher()"><i class="fas fa-save fa-sm mr-1"></i> Simpan</button>
      </div>
    </div>
  </div>
</div>

<!-- LISTING CREATE MODAL -->
<div class="modal fade" id="listingCreateModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title"><i class="fas fa-layer-group mr-2 text-primary"></i> <span id="lstModalTitle">Add New Listing</span></h5><button type="button" class="close" data-dismiss="modal">&times;</button></div>
      <div class="modal-body">
        <input type="hidden" id="lstId" value="">
        <div class="form-group">
          <label class="font-weight-bold">Nama Listing</label>
          <input type="text" class="form-control" id="lstName" placeholder="Produk Digital">
        </div>
        <div class="form-group">
          <label class="font-weight-bold">Deskripsi</label>
          <textarea class="form-control" id="lstDesc" rows="2" placeholder="Opsional"></textarea>
        </div>
        <div class="form-group">
          <label class="font-weight-bold">Pilih Produk</label>
          <?php foreach($data->items as $itm): ?>
          <div class="custom-control custom-checkbox">
            <input type="checkbox" class="custom-control-input lst-item-check" id="lstItem_<?= $itm->id ?>" value="<?= $itm->id ?>">
            <label class="custom-control-label" for="lstItem_<?= $itm->id ?>"><?= htmlspecialchars($itm->name) ?></label>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" id="lstSaveBtn" onclick="saveListing()"><i class="fas fa-save fa-sm mr-1"></i> Simpan</button>
      </div>
    </div>
  </div>
</div>

<style>
/* ── Toast Notification ── */
#shop-toast-container{position:fixed;bottom:24px;right:24px;z-index:9999;display:flex;flex-direction:column-reverse;gap:10px;pointer-events:none}
.shop-toast{display:flex;align-items:center;gap:10px;background:#1e293b;color:#fff;border-radius:12px;padding:13px 18px;font-size:.85rem;font-weight:500;box-shadow:0 8px 24px rgba(0,0,0,.18);opacity:0;transform:translateY(12px);transition:opacity .25s,transform .25s;pointer-events:all;min-width:240px;max-width:360px}
.shop-toast.show{opacity:1;transform:translateY(0)}
.shop-toast.success .toast-icon{color:#34d399}
.shop-toast.error   .toast-icon{color:#f87171}
.shop-toast.warning .toast-icon{color:#fbbf24}
.toast-icon{font-size:1.1rem;flex-shrink:0}
</style>

<div id="shop-toast-container"></div>

<script>
var SHOP_AJAX_URL = '<?= url('shop-ajax') ?>';
var CSRF_TOKEN   = '<?= \Altum\Csrf::get() ?>';

function showToast(message, type) {
    type = type || 'success';
    var icons = { success: 'fa-check-circle', error: 'fa-times-circle', warning: 'fa-exclamation-circle' };
    var c = document.getElementById('shop-toast-container');
    var t = document.createElement('div');
    t.className = 'shop-toast ' + type;
    t.innerHTML = '<i class="fas ' + (icons[type]||icons.success) + ' toast-icon"></i><span>' + message + '</span>';
    c.appendChild(t);
    requestAnimationFrame(function(){ t.classList.add('show'); });
    setTimeout(function(){ t.classList.remove('show'); setTimeout(function(){ if(t.parentNode) t.parentNode.removeChild(t); }, 300); }, 3500);
}

function shopReload(tabHash) {
    location.hash = tabHash || '';
    location.reload();
}

(function(){
    var hash = window.location.hash;
    if(hash) { var tab = document.querySelector('a[href="' + hash + '"]'); if(tab && typeof $ !== 'undefined') $(tab).tab('show'); }
    if(typeof $ !== 'undefined') {
        $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) { history.replaceState(null, null, $(e.target).attr('href')); });
    }
})();

function shopDeleteAjax(action, extra, btn, successMsg) {
    if(!confirm('Yakin ingin menghapus?')) return;
    if(btn) btn.disabled = true;
    var data = Object.assign({action: action, token: CSRF_TOKEN}, extra);
    fetch(SHOP_AJAX_URL, {method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body: new URLSearchParams(data)})
    .then(function(r){ return r.json(); })
    .then(function(res){
        if(res.success) {
            showToast(successMsg || 'Berhasil dihapus!', 'success');
            var row = btn ? btn.closest('tr') : null;
            if(row) {
                row.style.transition = 'opacity 0.25s';
                row.style.opacity = '0';
                setTimeout(function(){
                    var tbody = row.parentNode;
                    row.remove();
                    if(tbody && tbody.querySelectorAll('tr').length === 0) {
                        var emptyRow = document.createElement('tr');
                        emptyRow.innerHTML = '<td colspan="20" class="text-center py-4 text-muted"><i class="fas fa-inbox fa-2x d-block mb-2"></i>Tidak ada data</td>';
                        tbody.appendChild(emptyRow);
                    }
                }, 270);
            }
        } else {
            showToast(res.message || 'Gagal menghapus', 'error');
            if(btn) btn.disabled = false;
        }
    }).catch(function(){ showToast('Request gagal. Coba lagi.', 'error'); if(btn) btn.disabled = false; });
}

function toggleVcQuota() {
    document.getElementById('vcQuotaGroup').style.display = document.getElementById('vcIsUnlimited').checked ? 'none' : '';
}
function openVoucherEdit(v) {
    document.getElementById('vcModalTitle').textContent = 'Edit Voucher';
    document.getElementById('vcId').value = v.id;
    document.getElementById('vcCode').value = v.code;
    document.getElementById('vcIsActive').checked = !!parseInt(v.is_active);
    document.getElementById('vcFrom').value = v.valid_from ? v.valid_from.split(' ')[0] : '';
    document.getElementById('vcTo').value   = v.valid_to   ? v.valid_to.split(' ')[0]   : '';
    document.getElementById('vcIsUnlimited').checked = !!parseInt(v.is_unlimited);
    document.getElementById('vcQuota').value = v.quota || 0;
    document.getElementById('vcDiscount').value = v.discount_percentage || 10;
    document.getElementById('vcItemId').value = v.item_id || '';
    toggleVcQuota();
    $('#voucherCreateModal').modal('show');
}
function saveVoucher() {
    var id = document.getElementById('vcId').value;
    var params = { action: id ? 'voucher_update' : 'voucher_create', token: CSRF_TOKEN, id: id,
        code: document.getElementById('vcCode').value,
        is_active: document.getElementById('vcIsActive').checked ? 1 : 0,
        valid_from: document.getElementById('vcFrom').value, valid_to: document.getElementById('vcTo').value,
        is_unlimited: document.getElementById('vcIsUnlimited').checked ? 1 : 0,
        quota: document.getElementById('vcQuota').value,
        discount_percentage: document.getElementById('vcDiscount').value,
        item_id: document.getElementById('vcItemId').value };
    var btn = document.getElementById('vcSaveBtn');
    if(btn) btn.disabled = true;
    fetch(SHOP_AJAX_URL, {method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body: new URLSearchParams(params)})
    .then(function(r){ return r.json(); }).then(function(res){
        if(res.success) {
            $('#voucherCreateModal').modal('hide');
            showToast(id ? 'Voucher berhasil diperbarui!' : 'Voucher berhasil dibuat!', 'success');
            setTimeout(function(){ shopReload('#voucher'); }, 900);
        } else { showToast(res.message || 'Gagal menyimpan voucher', 'error'); if(btn) btn.disabled = false; }
    }).catch(function(){ showToast('Request gagal', 'error'); if(btn) btn.disabled = false; });
}

function openListingEdit(l) {
    document.getElementById('lstModalTitle').textContent = 'Edit Listing';
    document.getElementById('lstId').value = l.id;
    document.getElementById('lstName').value = l.name;
    document.getElementById('lstDesc').value = l.description || '';
    document.querySelectorAll('.lst-item-check').forEach(function(cb){ cb.checked = false; });
    $('#listingCreateModal').modal('show');
}
function saveListing() {
    var id = document.getElementById('lstId').value;
    var itemIds = Array.from(document.querySelectorAll('.lst-item-check:checked')).map(function(cb){ return cb.value; });
    var params = new URLSearchParams({ action: id ? 'listing_update' : 'listing_create', token: CSRF_TOKEN, id: id,
        name: document.getElementById('lstName').value, description: document.getElementById('lstDesc').value });
    itemIds.forEach(function(v){ params.append('item_ids[]', v); });
    var btn = document.getElementById('lstSaveBtn');
    if(btn) btn.disabled = true;
    fetch(SHOP_AJAX_URL, {method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body: params})
    .then(function(r){ return r.json(); }).then(function(res){
        if(res.success) {
            $('#listingCreateModal').modal('hide');
            showToast(id ? 'Listing berhasil diperbarui!' : 'Listing berhasil dibuat!', 'success');
            setTimeout(function(){ shopReload('#listing'); }, 900);
        } else { showToast(res.message || 'Gagal menyimpan listing', 'error'); if(btn) btn.disabled = false; }
    }).catch(function(){ showToast('Request gagal', 'error'); if(btn) btn.disabled = false; });
}
</script>


<script>
/*
 * previewShopImage(input, imgId, placeholderId)
 * Setelah user pilih file:
 *   - tampilkan preview pada <img id=imgId>
 *   - sembunyikan placeholder id=placeholderId (opsional, boleh null)
 */
function previewShopImage(input, imgId, placeholderId) {
    if(!input.files || !input.files[0]) return;
    var reader = new FileReader();
    reader.onload = function(e) {
        var img = document.getElementById(imgId);
        if(img) { img.src = e.target.result; img.style.display = ''; }
        if(placeholderId) {
            var ph = document.getElementById(placeholderId);
            if(ph) ph.style.display = 'none';
        }
    };
    reader.readAsDataURL(input.files[0]);
}

/* Cover dropzone hover effect */
(function(){
    var dz = document.getElementById('cover_dropzone');
    if(!dz) return;
    var ov = document.getElementById('cover_hover_overlay');
    dz.addEventListener('mouseenter', function(){ if(ov) ov.style.display = 'flex'; });
    dz.addEventListener('mouseleave', function(){ if(ov) ov.style.display = 'none'; });

    /* Drag & Drop cover */
    dz.addEventListener('dragover', function(e){ e.preventDefault(); dz.style.borderColor = '#6366f1'; });
    dz.addEventListener('dragleave', function(){ dz.style.borderColor = '#d1d5db'; });
    dz.addEventListener('drop', function(e){
        e.preventDefault(); dz.style.borderColor = '#6366f1';
        var dt = e.dataTransfer;
        if(dt.files.length) {
            var inp = document.getElementById('cover_image_input');
            try { inp.files = dt.files; } catch(ex) {}
            previewShopImage({files: dt.files}, 'cover_preview', 'cover_placeholder_inner');
        }
    });
})();
</script>

<script>
/* ── Verification Gate Popup ── */
function showVerifGate() {
    var overlay = document.getElementById('verif_gate_overlay');
    if(!overlay) {
        overlay = document.createElement('div');
        overlay.id = 'verif_gate_overlay';
        overlay.style.cssText = 'position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:9999;display:flex;align-items:center;justify-content:center;padding:20px';
        overlay.innerHTML = '<div style="background:#fff;border-radius:20px;max-width:420px;width:100%;padding:32px;text-align:center;box-shadow:0 20px 60px rgba(0,0,0,.3)">' +
            '<div style="width:64px;height:64px;border-radius:50%;background:#fef3c7;display:flex;align-items:center;justify-content:center;margin:0 auto 16px">' +
                '<i class="fas fa-shield-alt" style="font-size:1.6rem;color:#d97706"></i>' +
            '</div>' +
            '<h5 style="font-weight:800;margin-bottom:8px">Verifikasi KTP Diperlukan</h5>' +
            '<p style="color:#6b7280;font-size:.88rem;margin-bottom:20px">Kamu perlu menyelesaikan verifikasi identitas KTP terlebih dahulu untuk bisa melakukan pencairan dana.</p>' +
            '<button onclick="document.getElementById(\'verif_gate_overlay\').remove(); document.querySelector(\'[href=\\\'#shop_settings\\\']\').click();" style="display:block;width:100%;background:linear-gradient(135deg,#f59e0b,#d97706);color:#fff;padding:13px;border-radius:12px;border:none;font-weight:700;margin-bottom:10px">' +
                '<i class="fas fa-id-card mr-2"></i>Verifikasi Sekarang' +
            '</button>' +
            '<button onclick="document.getElementById(\'verif_gate_overlay\').remove()" style="background:#f1f5f9;border:none;width:100%;padding:11px;border-radius:12px;cursor:pointer;font-weight:600;color:#475569">' +
                'Nanti Saja' +
            '</button>' +
        '</div>';
        overlay.addEventListener('click', function(e){ if(e.target === overlay) overlay.remove(); });
        document.body.appendChild(overlay);
    } else {
        overlay.style.display = 'flex';
    }
}
</script>

<?php \Altum\Event::add_content(function() use ($data) { ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    if(document.getElementById('shopStatsChart')) {
        let css = window.getComputedStyle(document.body)
        let views_color = css.getPropertyValue('--primary') || '#f59e0b'; // Yellow/Orange
        let clicks_color = '#10b981'; // Green
        let views_gradient = null;
        let clicks_gradient = null;

        let chart = document.getElementById('shopStatsChart').getContext('2d');

        views_gradient = chart.createLinearGradient(0, 0, 0, 250);
        views_gradient.addColorStop(0, 'rgba(245, 158, 11, .1)');
        views_gradient.addColorStop(1, 'rgba(245, 158, 11, 0.025)');

        clicks_gradient = chart.createLinearGradient(0, 0, 0, 250);
        clicks_gradient.addColorStop(0, 'rgba(16, 185, 129, .1)');
        clicks_gradient.addColorStop(1, 'rgba(16, 185, 129, 0.025)');

        new Chart(chart, {
            type: 'line',
            data: {
                labels: <?= $data->chart_labels ?>,
                datasets: [
                    {
                        label: 'Views',
                        data: <?= $data->chart_views ?>,
                        backgroundColor: views_gradient,
                        borderColor: views_color,
                        fill: true
                    },
                    {
                        label: 'Clicks',
                        data: <?= $data->chart_clicks ?>,
                        backgroundColor: clicks_gradient,
                        borderColor: clicks_color,
                        fill: true
                    }
                ]
            },
            options: {
                elements: {
                    line: { tension: 0.3 },
                    point: { radius: 4 }
                },
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: true }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 }
                    }
                }
            }
        });
    }
</script>
<?php }, 'javascript') ?>
