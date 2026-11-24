<?php $userRole = session()->get('role'); ?>

<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<style>
    .content-box {
        background: #fff;
        border-radius: 12px;
        padding: 24px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
    }

    .section-title {
        font-size: 1.4rem;
        font-weight: 600;
        margin-bottom: 18px;
    }

    .filter-box {
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        padding: 14px;
        border-radius: 8px;
        margin-bottom: 18px;
    }
</style>

<?php
$labels = [
    'name_asc' => 'Nama A → Z',
    'name_desc' => 'Nama Z → A',
    'qty_high' => 'Jumlah Tertinggi',
    'qty_low' => 'Jumlah Terendah',
    'price_high' => 'Harga Tertinggi',
    'price_low' => 'Harga Terendah',
];
?>

<div class="content-box">
    <h2 class="section-title">📦 Kartu Stok Pusat</h2>

    <!-- FILTER & SEARCH -->
    <form method="get" action="/stock" class="filter-box row g-3 align-items-end">
        <div class="col-md-4">
            <label class="form-label fw-semibold">🔍 Pencarian Produk</label>
            <input type="text" name="search" value="<?= esc($search) ?>" class="form-control"
                placeholder="Nama Produk, Kategori, atau Harga...">
        </div>

        <div class="col-md-4">
            <label class="form-label fw-semibold">🎯 Urutkan</label>
            <select name="sort" id="sort" class="form-select shadow-sm border-secondary-subtle">
                <option value="">✨ Default (A-Z)</option>
                <optgroup label="📛 Nama Produk">
                    <option value="name_asc" <?= $sort == 'name_asc' ? 'selected' : '' ?>>A → Z (Nama)</option>
                    <option value="name_desc" <?= $sort == 'name_desc' ? 'selected' : '' ?>>Z → A (Nama)</option>
                </optgroup>
                <optgroup label="📦 Jumlah Stok">
                    <option value="qty_high" <?= $sort == 'qty_high' ? 'selected' : '' ?>>⬆️ Jumlah Tertinggi</option>
                    <option value="qty_low" <?= $sort == 'qty_low' ? 'selected' : '' ?>>⬇️ Jumlah Terendah</option>
                </optgroup>
                <optgroup label="💰 Harga Jual">
                    <option value="price_high" <?= $sort == 'price_high' ? 'selected' : '' ?>>💲 Harga Tertinggi</option>
                    <option value="price_low" <?= $sort == 'price_low' ? 'selected' : '' ?>>🪙 Harga Terendah</option>
                </optgroup>
            </select>
        </div>

        <div class="col-md-2 d-grid">
            <button class="btn btn-primary fw-semibold shadow-sm">🔍 Terapkan</button>
        </div>

        <div class="col-md-2 d-grid">
            <a href="/stock" class="btn btn-outline-secondary fw-semibold">✖ Reset</a>
        </div>
    </form>

    <!-- BADGE INFO -->
    <?php if ($search || $sort): ?>
        <div class="mb-3 mt-2">
            <span class="badge bg-primary me-2 px-3 py-2">Filter aktif</span>
            <?php if ($search): ?>
                <span class="badge bg-dark px-3 py-2">Pencarian: "<?= esc($search) ?>"</span>
            <?php endif; ?>
            <?php if ($sort): ?>
                <span class="badge bg-success px-3 py-2">
                    Sort: <?= $labels[$sort] ?? $sort ?>
                </span>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="table table-hover table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th>No</th>
                    <th>Nama Produk</th>
                    <th>Kategori</th>
                    <th class="text-center">Jumlah</th>
                    <th class="text-end">Harga Jual</th>
                    <?php if ($userRole === 'pusat'): ?>
                        <th class="text-center">Aksi</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
<?php if (!empty($stocks)): ?>
    <?php
        $currentPage = $pager->getCurrentPage('stocks');
        $perPage     = $pager->getPerPage('stocks');
        $no = 1 + ($currentPage - 1) * $perPage;
    ?>
    <?php foreach ($stocks as $stock): ?>
        <tr>
            <td><?= $no++ ?></td>
            <td class="fw-semibold"><?= esc($stock['product_name']) ?></td>
            <td>
                <span class="badge bg-info text-dark px-3 py-2">
                    <?= esc($stock['category_name']) ?>
                </span>
            </td>
            <td class="text-center fw-bold"><?= esc($stock['total_quantity']) ?></td>
            <td class="text-end">Rp <?= number_format($stock['sell_price'], 0, ',', '.') ?></td>
            <?php if ($userRole === 'pusat'): ?>
                <td class="text-center">
                    <a href="<?= site_url('stock/edit-price/' . $stock['product_item_id']) ?>" class="btn btn-sm btn-warning px-3">
                        Edit Harga
                    </a>
                </td>
            <?php endif; ?>
        </tr>
    <?php endforeach ?>
<?php else: ?>
    <tr>
        <td colspan="<?= ($userRole === 'pusat') ? 6 : 5 ?>" class="text-center py-3">
            <em>Data stok tidak ditemukan.</em>
        </td>
    </tr>
<?php endif ?>
            </tbody>
        </table>
    </div>

    <!-- PAGINATION RETAIN FILTER -->
    <div class="mt-3 d-flex justify-content-center">
        <?= $pager->only(['search', 'sort'])->links('stocks', 'pagination') ?>
    </div>
</div>

<!-- jQuery & Select2 CDN -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {
    $('#sort').select2({
        placeholder: "✨ Pilih Sorting",
        width: '100%',
        minimumResultsForSearch: 0 // search aktif
    });
});
</script>

<?= $this->endSection() ?>
