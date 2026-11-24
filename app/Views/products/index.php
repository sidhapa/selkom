<?php $userRole = session()->get('role'); ?>

<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<style>
    .content-box {
        background: #fff;
        border-radius: 12px;
        padding: 25px;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.06);
        border: 1px solid #e6e6e6;
    }

    h2 {
        font-weight: 700;
        margin-bottom: 18px;
    }

    .filter-box {
        background: #f9fafb;
        padding: 15px;
        border-radius: 8px;
        border: 1px solid #e5e7eb;
    }

    .badge-category {
        background: #eef2ff;
        color: #4338ca;
        padding: 4px 7px;
        border-radius: 6px;
        font-size: 12px;
    }

    .badge-supplier {
        background: #ecfdf5;
        color: #047857;
        padding: 4px 7px;
        border-radius: 6px;
        font-size: 12px;
    }
</style>

<div class="content-box">
    <h2>📦 Data Produk</h2>
<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success">
        <?= esc(session()->getFlashdata('success')) ?>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger">
        <?= esc(session()->getFlashdata('error')) ?>
    </div>
<?php endif; ?>

    <!-- Filter & Search -->
    <form method="get" action="/products" class="filter-box row g-3 mb-3">

        <!-- Search -->
        <div class="col-md-5">
            <input type="text" name="search" value="<?= esc($search) ?>" class="form-control"
                placeholder="🔍 Cari nama produk, kategori, atau supplier">
        </div>

        <!-- Sorting -->
        <div class="col-md-4">
            <select name="sort" class="form-select" onchange="this.form.submit()">
                <option value="">✨ Urutkan Produk</option>
                <option value="price_desc" <?= $sort === 'price_desc' ? 'selected' : '' ?>>💰 Harga Tertinggi</option>
                <option value="price_asc" <?= $sort === 'price_asc' ? 'selected' : '' ?>>💰 Harga Terendah</option>
                <option value="qty_desc" <?= $sort === 'qty_desc' ? 'selected' : '' ?>>📦 Stok Terbanyak</option>
                <option value="qty_asc" <?= $sort === 'qty_asc' ? 'selected' : '' ?>>📦 Stok Tersedikit</option>
            </select>
        </div>

        <!-- Buttons -->
        <div class="col-md-3 d-flex gap-2">
            <button class="btn btn-primary fw-semibold flex-fill">
                🔍 Terapkan
            </button>
            <a href="/products" class="btn btn-outline-secondary fw-semibold flex-fill">
                ✖ Reset
            </a>
        </div>
    </form>

    <!-- Tambah Produk -->
    <?php if ($userRole === 'pusat'): ?>
        <a href="/products/create" class="btn btn-success mb-3">
            + Tambah Produk
        </a>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th width="50">No</th>
                    <th>Produk</th>
                    <th>Kategori</th>
                    <th>Supplier</th>
                    <th class="text-center">Qty</th>
                    <th class="text-end">Harga</th>
                    <?php if ($userRole === 'pusat'): ?>
                        <th class="text-center" width="120">Aksi</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php if ($products): ?>
                    <?php
                    $no = 1 + ($pager->getCurrentPage('products') - 1) * $pager->getPerPage('products');
                    ?>
                    <?php foreach ($products as $prod): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td>
                                <strong><?= esc($prod['product_name']) ?></strong>
                            </td>
                            <td>
                                <span class="badge-category">
                                    <?= esc($prod['category_name']) ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge-supplier">
                                    <?= esc($prod['supplier_name']) ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <?= esc($prod['quantity']) ?>
                            </td>
                            <td class="text-end">
                                Rp <?= number_format($prod['price'], 0, ',', '.') ?>
                            </td>

                            <?php if ($userRole === 'pusat'): ?>
                                <td class="text-center">
                                    <a href="/products/edit/<?= $prod['id'] ?>" class="btn btn-warning btn-sm">
                                        Edit
                                    </a>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="<?= $userRole === 'pusat' ? 7 : 6 ?>" class="text-center text-muted py-3">
                            ❗ Data produk tidak ditemukan
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?= $pager->links('products', 'pagination') ?>

</div>

<?= $this->endSection() ?>