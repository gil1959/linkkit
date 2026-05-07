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

                <div class="ml-auto">
                    <a href="#" class="btn btn-primary"><i class="fas fa-arrow-up fa-sm mr-1"></i> Upgrade</a>
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

    <div class="tab-content">
        <!-- Summary Tab -->
        <div class="tab-pane fade show active" id="summary" role="tabpanel">
            <h2 class="h5 mb-4">Overall Shop Summary</h2>
            
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 col-md-6 mb-3 mb-md-0">
                            <div class="text-muted mb-1">All Time Revenue</div>
                            <div class="h3 mb-0">Rp 0</div>
                        </div>
                        <div class="col-12 col-md-6 border-md-left">
                            <div class="text-muted mb-1">Withdrawal Amount</div>
                            <div class="h3 mb-0">Rp 0</div>
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
                            <div class="h4 mb-0">0</div>
                            <small class="text-muted">Product View</small>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="h4 mb-0">0.00%</div>
                            <small class="text-muted">Conversion</small>
                        </div>
                    </div>

                    <div class="chart-container" style="height: 300px; display: flex; align-items: center; justify-content: center; background: #f8f9fa; border-radius: .25rem;">
                        <span class="text-muted">Chart placeholder</span>
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
                                                <a class="dropdown-item text-danger" href="#" onclick="return confirm('Hapus item ini?') && (window.location='<?= url('shop-item-delete/' . $item->id . '?token=' . \Altum\Csrf::get()) ?>')">
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
                            <td style="max-width:200px"><?= nl2br(htmlspecialchars($review->review ?? '-')) ?></td>
                            <td><?= \Altum\Date::get($review->datetime, 1) ?></td>
                            <td>
                                <button class="btn btn-sm btn-outline-danger" onclick="shopAjax('review_delete',{id:<?= $review->id ?>},this)">
                                    <i class="fas fa-trash fa-sm"></i>
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
                                <button class="btn btn-sm btn-outline-primary mr-1" onclick="openListingEdit(<?= htmlspecialchars(json_encode($lst)) ?>)"><i class="fas fa-edit fa-sm"></i></button>
                                <button class="btn btn-sm btn-outline-danger" onclick="shopAjax('listing_delete',{id:<?= $lst->id ?>},this)"><i class="fas fa-trash fa-sm"></i></button>
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
                                <button class="btn btn-sm btn-outline-primary mr-1" onclick="openVoucherEdit(<?= htmlspecialchars(json_encode($vc)) ?>)"><i class="fas fa-edit fa-sm"></i></button>
                                <button class="btn btn-sm btn-outline-danger" onclick="shopAjax('voucher_delete',{id:<?= $vc->id ?>},this)"><i class="fas fa-trash fa-sm"></i></button>
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
                    <div class="text-muted">Withdrawable funds</div>
                    <div class="h4">Rp <?= number_format($this->user->withdrawable_funds ?? 0, 0, ',', '.') ?></div>
                </div>
                <div class="col-4">
                    <div class="text-muted">Pending Withdrawal</div>
                    <div class="h4 text-warning">Rp <?= number_format($data->total_pending_withdrawals ?? 0, 0, ',', '.') ?></div>
                    <small class="text-muted">Processed in 3-5 days</small>
                </div>
            </div>
            
            <div class="mb-4 d-flex align-items-center">
                <button type="button" class="btn btn-success" data-toggle="modal" data-target="#withdraw_modal">
                    <i class="fas fa-money-bill-wave fa-sm mr-1"></i> Withdraw Funds
                </button>
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
                                    Penarikan akan diproses dalam <strong>3-5 hari kerja</strong> dan akan ditinjau oleh admin sebelum dikirim ke rekening Anda.
                                </div>
                                <div class="card bg-light mb-3">
                                    <div class="card-body py-2">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="text-muted">Saldo tersedia</span>
                                            <strong class="text-success h5 mb-0">Rp <?= number_format($this->user->withdrawable_funds ?? 0, 0, ',', '.') ?></strong>
                                        </div>
                                    </div>
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
                                        Belum ada rekening bank. Tambahkan di tab <strong>Shop Settings &rarr; Bank Account</strong> terlebih dahulu.
                                    </div>
                                <?php endif ?>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-success" <?= (!$bank || $this->user->withdrawable_funds <= 0) ? 'disabled' : '' ?>>
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
                        <th>Invoice</th>
                        <th>Customer</th>
                        <th>Item</th>
                        <th>Total</th>
                        <th>Fee</th>
                        <th>Date</th>
                        <th>Status</th>
                    </tr>
                    </thead>
                    <tbody>
                        <?php if(count($data->orders) > 0): ?>
                            <?php foreach($data->orders as $order): ?>
                                <tr>
                                    <td><?= $order->invoice_number ?></td>
                                    <td>
                                        <div><?= htmlspecialchars($order->full_name) ?></div>
                                        <small class="text-muted"><?= htmlspecialchars($order->email) ?></small>
                                    </td>
                                    <td><?= htmlspecialchars($order->item_name) ?></td>
                                    <td>Rp <?= number_format($order->grand_total, 0, ',', '.') ?></td>
                                    <td>Rp <?= number_format($order->service_fee, 0, ',', '.') ?></td>
                                    <td><?= \Altum\Date::get($order->datetime, 1) ?></td>
                                    <td>
                                        <?php if($order->status == 'paid'): ?>
                                            <span class="badge badge-success">Paid</span>
                                        <?php elseif($order->status == 'pending'): ?>
                                            <span class="badge badge-warning">Pending</span>
                                        <?php else: ?>
                                            <span class="badge badge-danger"><?= ucfirst($order->status) ?></span>
                                        <?php endif ?>
                                    </td>
                                </tr>
                            <?php endforeach ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center py-5">
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
        <!-- WEBHOOK EVENTS TAB -->
        <div class="tab-pane fade" id="webhook_events" role="tabpanel">
            <h2 class="h5 mb-3">Webhook Events</h2>
            <div class="card mb-4">
                <div class="card-body">
                    <p class="mb-2"><i class="fas fa-info-circle text-primary mr-1"></i> <strong>Cara kerja Webhook:</strong> Buat item bertipe <code>webhook_event</code>, isi <em>Webhook URL</em> di form edit item. Setiap kali pembeli membeli item tersebut dan pembayaran berhasil, sistem akan <strong>otomatis POST</strong> payload JSON ke URL tersebut.</p>
                    <p class="mb-0 small text-muted">Payload yang dikirim: <code>event, invoice, customer_name, customer_email, product_id, product_name, amount, datetime</code></p>
                </div>
            </div>
            <?php if(count($data->webhook_events) > 0): ?>
            <div class="table-responsive table-custom-container">
                <table class="table table-custom">
                    <thead><tr><th>Tanggal</th><th>Produk</th><th>Webhook URL</th><th>Status</th><th>Payload</th></tr></thead>
                    <tbody>
                    <?php foreach($data->webhook_events as $wh): ?>
                        <tr>
                            <td style="white-space:nowrap"><?= \Altum\Date::get($wh->datetime,1) ?></td>
                            <td><?= htmlspecialchars($wh->item_name ?? '-') ?></td>
                            <td><small class="text-truncate d-inline-block" style="max-width:200px" title="<?= htmlspecialchars($wh->webhook_url) ?>"><?= htmlspecialchars($wh->webhook_url) ?></small></td>
                            <td>
                                <?php if($wh->status_code >= 200 && $wh->status_code < 300): ?>
                                    <span class="badge badge-success"><?= $wh->status_code ?></span>
                                <?php else: ?>
                                    <span class="badge badge-danger"><?= $wh->status_code ?: 'Error' ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <button class="btn btn-xs btn-outline-secondary" onclick="alert(JSON.stringify(JSON.parse('<?= addslashes(htmlspecialchars($wh->payload)) ?>'),null,2))"><i class="fas fa-code fa-sm"></i></button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="card bg-light"><div class="card-body text-center text-muted"><i class="fas fa-plug fa-2x mb-2 d-block"></i>Belum ada webhook event dikirim</div></div>
            <?php endif; ?>
        </div>
        <div class="tab-pane fade" id="shop_settings" role="tabpanel">
            <?php
            $bank_account = database()->query("SELECT * FROM `shop_bank_accounts` WHERE `user_id` = {$this->user->user_id}")->fetch_object() ?? null;
            $notification_settings = json_decode($data->shop->notification_settings ?? '{}', true);
            ?>

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
            <form action="<?= url('shop-settings-update') ?>" method="post" class="mb-5">
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
        <button type="button" class="btn btn-primary" onclick="saveVoucher()"><i class="fas fa-save fa-sm mr-1"></i> Save</button>
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
        <button type="button" class="btn btn-primary" onclick="saveListing()"><i class="fas fa-save fa-sm mr-1"></i> Save</button>
      </div>
    </div>
  </div>
</div>

<script>
var SHOP_AJAX_URL = '<?= url('shop-ajax') ?>';
var CSRF_TOKEN   = '<?= \Altum\Csrf::get() ?>';

function shopAjax(action, extra, btn) {
    if(!confirm('Yakin?')) return;
    if(btn) btn.disabled = true;
    var data = Object.assign({action: action, token: CSRF_TOKEN}, extra);
    fetch(SHOP_AJAX_URL, {method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body: new URLSearchParams(data)})
    .then(r=>r.json()).then(function(res){
        if(res.success) { location.reload(); } else { alert(res.message || 'Error'); if(btn) btn.disabled=false; }
    }).catch(function(){ alert('Request failed'); if(btn) btn.disabled=false; });
}

/* Voucher */
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
    var params = {
        action: id ? 'voucher_update' : 'voucher_create',
        token: CSRF_TOKEN,
        id: id,
        code: document.getElementById('vcCode').value,
        is_active: document.getElementById('vcIsActive').checked ? 1 : 0,
        valid_from: document.getElementById('vcFrom').value,
        valid_to: document.getElementById('vcTo').value,
        is_unlimited: document.getElementById('vcIsUnlimited').checked ? 1 : 0,
        quota: document.getElementById('vcQuota').value,
        discount_percentage: document.getElementById('vcDiscount').value,
        item_id: document.getElementById('vcItemId').value,
    };
    fetch(SHOP_AJAX_URL, {method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body: new URLSearchParams(params)})
    .then(r=>r.json()).then(function(res){
        if(res.success) { location.reload(); } else { alert(res.message || 'Error'); }
    }).catch(function(){ alert('Request failed'); });
}

/* Listing */
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
    var params = new URLSearchParams({
        action: id ? 'listing_update' : 'listing_create',
        token: CSRF_TOKEN, id: id,
        name: document.getElementById('lstName').value,
        description: document.getElementById('lstDesc').value,
    });
    itemIds.forEach(function(v){ params.append('item_ids[]', v); });
    fetch(SHOP_AJAX_URL, {method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body: params})
    .then(r=>r.json()).then(function(res){
        if(res.success) { location.reload(); } else { alert(res.message || 'Error'); }
    }).catch(function(){ alert('Request failed'); });
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
