<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>📊 Data Penjualan</h4>

    <?php if (session('role') === 'cabang'): ?>
        <a href="<?= site_url('sales/create') ?>" class="btn btn-success">
            ➕ Transaksi Penjualan
        </a>
    <?php endif; ?>
</div>

<form method="get" class="row g-2 mb-3">

    <div class="col-md-3">
        <input type="text" name="keyword" class="form-control" placeholder="Invoice, Total, Cabang..."
            value="<?= esc($keyword ?? '') ?>">
    </div>

    <div class="col-md-2">
        <input type="date" name="date" class="form-control" value="<?= esc($date ?? '') ?>">
    </div>

    <?php if ($isPusat): ?>
        <div class="col-md-2">
            <select name="branch_id" class="form-control">
                <option value="">-- Semua Cabang --</option>
                <?php foreach ($branches as $b): ?>
                    <option value="<?= $b['id'] ?>" <?= ($branch == $b['id']) ? 'selected' : '' ?>>
                        <?= esc($b['name']) ?>
                    </option>
                <?php endforeach ?>
            </select>
        </div>
    <?php endif; ?>

    <div class="col-md-2">
        <select name="sort" class="form-control">
            <option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>>Terbaru</option>
            <option value="oldest" <?= $sort === 'oldest' ? 'selected' : '' ?>>Terlama</option>
            <option value="highest" <?= $sort === 'highest' ? 'selected' : '' ?>>Total Tertinggi</option>
            <option value="lowest" <?= $sort === 'lowest' ? 'selected' : '' ?>>Total Terendah</option>
            <option value="invoice_az" <?= $sort === 'invoice_az' ? 'selected' : '' ?>>Invoice A–Z</option>
            <option value="invoice_za" <?= $sort === 'invoice_za' ? 'selected' : '' ?>>Invoice Z–A</option>
        </select>
    </div>

    <div class="col-md-2 d-grid">
        <button class="btn btn-primary">Filter</button>
    </div>
</form>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success">
        <?= session()->getFlashdata('success') ?>
    </div>
<?php endif; ?>

<table class="table table-bordered table-striped" style="table-layout: auto; width: 100%;">
    <thead class="table-dark">
        <tr>
            <th>Nomor</th>
            <th>Invoice</th>
            <th>Tanggal & Waktu</th>
            <?php if ($isPusat): ?>
                <th>Cabang</th>
            <?php endif; ?>
            <th>Total</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>

        <?php if (empty($sales)): ?>
            <tr>
                <td colspan="<?= $isPusat ? 6 : 5 ?>" class="text-center">
                    Tidak ada data penjualan
                </td>
            </tr>
        <?php else: ?>

            <?php
            // 🔑 INI SAJA TAMBAHANNYA (AMAN)
            $no = 1 + ($pager->getCurrentPage('sales') - 1) * $pager->getPerPage('sales');
            ?>

            <?php foreach ($sales as $i => $sale): ?>
                <tr>
                    <td style="width:50px; text-align:center;">
                        <?= $no++ ?>
                    </td>
                    <td class="fw-semibold"><?= esc($sale['invoice']) ?></td>
                    <td>
                        <div><strong><?= date('d/m/Y', strtotime($sale['date'])) ?></strong></div>
                        <small class="text-muted"><i class="bi bi-clock"></i> <?= date('H:i:s', strtotime($sale['created_at'] ?? $sale['date'])) ?> WIB</small>
                    </td>
                    <?php if ($isPusat): ?>
                        <td><?= esc($sale['branch_name'] ?? '-') ?></td>
                    <?php endif; ?>
                    <td style="white-space: nowrap;">
                        Rp <?= number_format($sale['total'], 0, ',', '.') ?>
                    </td>
                    <td class="text-center">
                        <a href="<?= site_url('sales/detail/' . $sale['id']) ?>" class="btn btn-sm btn-info mb-1">Detail</a>
                        <a href="<?= site_url('sales/pdf_invoice/' . $sale['id']) ?>" class="btn btn-sm btn-danger"
                            target="_blank">Cetak</a>
                    </td>
                </tr>
            <?php endforeach ?>

        <?php endif; ?>
    </tbody>
</table>

<!-- 🔑 INI WAJIB AGAR PAGINATION MUNCUL -->
<?php if ($pager): ?>
    <div class="d-flex justify-content-center mt-3">
        <?= $pager->links('sales', 'pagination') ?>
    </div>
<?php endif; ?>

<?= $this->endSection() ?>