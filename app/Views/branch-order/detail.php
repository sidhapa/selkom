<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php $role = session('role'); ?>

<div class="container mt-4">
    <h4 class="mb-4 text-center">📦 Detail Permintaan Stok Cabang</h4>

    <!-- FLASH MESSAGES -->
    <?php if (session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= session('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= session('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- INFO HEADER -->
    <div class="card mb-4 shadow-sm border-0">
        <div class="card-body">
            <div class="row text-center text-md-start g-3">
                <div class="col-md-3">
                    <span class="text-muted small">Kode Permintaan</span><br>
                    <strong><?= esc($order['request_code']) ?></strong>
                </div>
                <div class="col-md-3">
                    <span class="text-muted small">Cabang Pemohon</span><br>
                    <strong><?= esc($order['branch_name'] ?? '-') ?></strong>
                    <?php if (!empty($order['requester_name'])): ?>
                        <br><small class="text-muted">(Oleh: <?= esc($order['requester_name']) ?>)</small>
                    <?php endif; ?>
                </div>
                <div class="col-md-3">
                    <span class="text-muted small">Waktu Permintaan</span><br>
                    <strong><?= date('d/m/Y H:i', strtotime($order['requested_at'])) ?></strong>
                </div>
                <div class="col-md-3">
                    <span class="text-muted small">Status Permintaan</span><br>
                    <?php if ($order['status'] === 'pending'): ?>
                        <span class="badge bg-warning text-dark px-3 py-2">⏳ Menunggu Persetujuan Pusat</span>
                    <?php elseif ($order['status'] === 'approved'): ?>
                        <span class="badge bg-info text-dark px-3 py-2">🚚 Disetujui / Dalam Pengiriman</span>
                    <?php elseif ($order['status'] === 'received'): ?>
                        <span class="badge bg-success px-3 py-2">✅ Selesai (Sudah Diterima Cabang)</span>
                    <?php elseif ($order['status'] === 'rejected'): ?>
                        <span class="badge bg-danger px-3 py-2">❌ Ditolak</span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- DETAIL PROSES WAKTU -->
            <div class="mt-3 pt-3 border-top row g-2 small text-muted">
                <?php if (!empty($order['approved_at'])): ?>
                    <div class="col-md-6">
                        <strong>Diproses Pusat:</strong> <?= date('d/m/Y H:i', strtotime($order['approved_at'])) ?>
                        <?php if (!empty($order['approver_name'])): ?>
                            (oleh <?= esc($order['approver_name']) ?>)
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($order['received_at'])): ?>
                    <div class="col-md-6">
                        <strong>Diterima Cabang:</strong> <?= date('d/m/Y H:i', strtotime($order['received_at'])) ?>
                        <?php if (!empty($order['receiver_name'])): ?>
                            (oleh <?= esc($order['receiver_name']) ?>)
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- CATATAN DARI PUSAT -->
            <?php if ($order['status'] === 'rejected' && !empty($order['note'])): ?>
                <div class="alert alert-danger mt-3 mb-0">
                    <strong>Alasan / Catatan Penolakan dari Pusat:</strong><br>
                    <?= nl2br(esc($order['note'])) ?>
                </div>
            <?php endif; ?>

            <?php if (in_array($order['status'], ['approved', 'received']) && !empty($order['note'])): ?>
                <div class="alert alert-primary mt-3 mb-0">
                    <strong>💬 Catatan / Pesan dari Pusat:</strong><br>
                    <?= nl2br(esc($order['note'])) ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- KHUSUS ROLE CABANG: TOMBOL KONFIRMASI PENERIMAAN BARANG -->
    <?php if ($role === 'cabang' && $order['status'] === 'approved'): ?>
        <div class="card border-success mb-4 shadow-sm bg-light">
            <div class="card-body p-4">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                    <div>
                        <h5 class="text-success fw-bold mb-1">📦 Konfirmasi Barang Sudah Sampai</h5>
                        <p class="text-muted mb-0 small">
                            Pusat telah menyetujui dan mengirim barang ini. Jika fisik barang telah sampai di cabang dan diperiksa, silakan klik tombol di samping untuk <strong>memasukkan barang ke stok cabang</strong>.
                        </p>
                    </div>
                    <form method="post" action="<?= base_url('branch-order/receive/' . $order['id']) ?>">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-success btn-lg px-4 shadow"
                                onclick="return confirm('Konfirmasi penerimaan barang?\n\nSetelah dikonfirmasi, stok fisik akan langsung tercatat masuk ke stok cabang ini.')">
                            <i class="bi bi-check-circle-fill me-1"></i> Sudah Diterima di Cabang
                        </button>
                    </form>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- JIKA ROLE PUSAT & STATUS PENDING: FORM APPROVAL + EDIT + TAMBAH PRODUK LAIN -->
    <?php if ($role === 'pusat' && $order['status'] === 'pending'): ?>
        <form method="post" action="<?= base_url('branch-order/approve/' . $order['id']) ?>">
            <?= csrf_field() ?>

            <div class="card mb-4 shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">Daftar Produk Permintaan Cabang</h5>
                    <small class="text-muted">Pusat dapat menyesuaikan jumlah, atau menghapus produk yang tidak disetujui.</small>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover text-center align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width:50px;">No</th>
                                    <th>Nama Produk</th>
                                    <th>Kategori</th>
                                    <th style="width:130px;">Jumlah Dikirim</th>
                                    <th style="width:120px;">Stok Cabang</th>
                                    <th style="width:120px;">Stok Pusat</th>
                                    <th style="width:90px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="detail-body">
                                <?php foreach ($details as $i => $d): ?>
                                    <tr>
                                        <td><?= $i + 1 ?></td>
                                        <td class="text-start ps-3 fw-semibold"><?= esc($d['product_name']) ?></td>
                                        <td><?= esc($d['category_name'] ?? '-') ?></td>
                                        <td>
                                            <input type="number" name="quantity[<?= $d['detail_id'] ?>]"
                                                   value="<?= $d['quantity'] ?>" min="1" max="<?= $d['central_stock'] ?? 0 ?>"
                                                   class="form-control form-control-sm text-center fw-bold">
                                        </td>
                                        <td><span class="badge bg-secondary"><?= $d['branch_stock'] ?? 0 ?></span></td>
                                        <td><span class="badge bg-info text-dark"><?= $d['central_stock'] ?? 0 ?></span></td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-outline-danger btn-remove-row" title="Hapus / Tolak Produk Ini">
                                                Hapus
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- TAMBAH PRODUK BARU OLEH PUSAT -->
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">➕ Tambahkan Produk Lain dari Pusat (Opsional)</h5>
                    <small class="text-muted">Pusat dapat menambahkan produk lain yang diinginkan pusat untuk dikirim ke cabang.</small>
                </div>
                <div class="card-body">
                    <div class="row g-2 align-items-end">
                        <div class="col-md-5">
                            <label class="form-label small fw-semibold">Pilih Produk Pusat</label>
                            <select id="new-product" class="form-select">
                                <option value="">-- Pilih Produk yang Tersedia di Pusat --</option>
                                <?php foreach ($allProducts as $p): ?>
                                    <option value="<?= $p['product_item_id'] ?>"
                                            data-category="<?= esc($p['category_name'] ?? '-') ?>"
                                            data-central-stock="<?= $p['central_stock'] ?>"
                                            data-branch-stock="<?= $p['branch_stock'] ?? 0 ?>">
                                        <?= esc($p['product_name']) ?> (Stok Pusat: <?= $p['central_stock'] ?> | Stok Cabang: <?= $p['branch_stock'] ?? 0 ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label small fw-semibold">Jumlah Tambahan</label>
                            <input type="number" id="new-quantity" class="form-control" min="1" placeholder="Jumlah">
                        </div>

                        <div class="col-md-2">
                            <label class="form-label small fw-semibold">Stok Cabang</label>
                            <input type="text" id="branch-stock-preview" class="form-control bg-light" readonly placeholder="-">
                        </div>

                        <div class="col-md-2 d-grid">
                            <button type="button" id="add-product" class="btn btn-primary">
                                + Masukkan
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CATATAN / KOMENTAR DARI PUSAT -->
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-body">
                    <label class="form-label fw-bold">💬 Pesan / Catatan untuk Cabang (Opsional)</label>
                    <textarea name="note" class="form-control" rows="3"
                              placeholder="Tulis pesan atau catatan untuk cabang (misal: alasan penambahan barang, info pengiriman, dll). Tidak wajib diisi."><?= esc($order['note'] ?? '') ?></textarea>
                    <small class="text-muted">Pesan ini akan terbaca oleh user cabang saat melihat detail permintaan.</small>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <a href="<?= base_url('branch-order') ?>" class="btn btn-secondary">
                    ← Kembali
                </a>

                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-danger px-4" data-bs-toggle="modal" data-bs-target="#rejectModal">
                        ❌ Tolak Permintaan
                    </button>

                    <button type="submit" class="btn btn-success px-4"
                            onclick="return confirm('Setujui pengiriman ini?\n\nStok di pusat akan otomatis dikurangi dan menunggu konfirmasi terima dari cabang.')">
                        ✅ Setujui & Kirim ke Cabang
                    </button>
                </div>
            </div>
        </form>

    <?php else: ?>
        <!-- VIEW ONLY (JIKA SUDAH DIPROSES ATAU DILIHAT OLEH CABANG) -->
        <div class="card mb-4 shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold">Daftar Produk</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover text-center align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width:60px;">No</th>
                                <th>Nama Produk</th>
                                <th>Kategori</th>
                                <th style="width:140px;">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($details as $i => $d): ?>
                                <tr>
                                    <td><?= $i + 1 ?></td>
                                    <td class="text-start ps-3 fw-semibold"><?= esc($d['product_name']) ?></td>
                                    <td><?= esc($d['category_name'] ?? '-') ?></td>
                                    <td>
                                        <span class="badge bg-primary fs-6 px-3 py-1"><?= $d['quantity'] ?></span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="mb-4">
            <a href="<?= base_url('branch-order') ?>" class="btn btn-secondary">
                ← Kembali ke Daftar Permintaan
            </a>
        </div>
    <?php endif; ?>

    <!-- MODAL REJECT (CATATAN TIDAK WAJIB) -->
    <div class="modal fade" id="rejectModal" tabindex="-1">
        <div class="modal-dialog">
            <form method="post" action="<?= base_url('branch-order/reject/' . $order['id']) ?>">
                <?= csrf_field() ?>
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-danger">Tolak Permintaan Stok Cabang</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p class="small text-muted mb-2">
                            Apakah Anda yakin ingin menolak permintaan stok ini? Anda dapat memberikan catatan alasan penolakan (opsional).
                        </p>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Alasan / Catatan Penolakan (Opsional)</label>
                            <textarea name="note" class="form-control" rows="3" placeholder="Contoh: Stok pusat sedang dialokasikan untuk kebutuhan lain, dll."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Ya, Tolak Permintaan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('new-product')?.addEventListener('change', function(e) {
    const selectedOption = e.target.options[e.target.selectedIndex];
    document.getElementById('branch-stock-preview').value = selectedOption ? (selectedOption.dataset.branchStock ?? 0) : '';
});

document.getElementById('add-product')?.addEventListener('click', function() {
    const productSelect = document.getElementById('new-product');
    const qtyInput      = document.getElementById('new-quantity');

    if (!productSelect.value || qtyInput.value <= 0) {
        alert('Silakan pilih produk dan isi jumlah dengan benar.');
        return;
    }

    const opt          = productSelect.options[productSelect.selectedIndex];
    const maxCentral   = parseInt(opt.dataset.centralStock) || 0;
    const requestedQty = parseInt(qtyInput.value) || 0;

    if (requestedQty > maxCentral) {
        alert('Jumlah melebihi stok yang ada di pusat (Maksimal: ' + maxCentral + ')');
        return;
    }

    const productName = opt.text.split('(')[0].trim();
    const category    = opt.dataset.category;
    const branchStock = opt.dataset.branchStock ?? 0;

    const tr = document.createElement('tr');
    tr.className = 'table-info'; // penanda produk tambahan pusat
    tr.innerHTML = `
        <td></td>
        <td class="text-start ps-3 fw-semibold">
            ${productName} <span class="badge bg-primary small ms-1">+ Ditambah Pusat</span>
        </td>
        <td>${category}</td>
        <td>
            <input type="number" name="new_quantity[${productSelect.value}]" value="${requestedQty}" min="1" max="${maxCentral}"
                   class="form-control form-control-sm text-center fw-bold">
        </td>
        <td><span class="badge bg-secondary">${branchStock}</span></td>
        <td><span class="badge bg-info text-dark">${maxCentral}</span></td>
        <td>
            <button type="button" class="btn btn-sm btn-outline-danger btn-remove-row">Hapus</button>
        </td>
    `;

    document.getElementById('detail-body').appendChild(tr);
    renumberRows();

    // Reset input
    productSelect.value = '';
    qtyInput.value = '';
    document.getElementById('branch-stock-preview').value = '';
});

document.addEventListener('click', function(e) {
    if (e.target.classList.contains('btn-remove-row')) {
        e.target.closest('tr').remove();
        renumberRows();
    }
});

function renumberRows() {
    const rows = document.querySelectorAll('#detail-body tr');
    rows.forEach((r, i) => {
        r.children[0].innerText = i + 1;
    });
}
</script>

<?= $this->endSection() ?>
