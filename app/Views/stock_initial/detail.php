<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php $userRole = session()->get('role'); ?>

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

    .badge-title {
        font-size: 0.75rem;
        padding: 6px 10px;
        margin-right: 6px;
    }
</style>

<div class="content-box">
    <h2 class="section-title">📦 Kartu Stok Cabang: <?= esc($branch['name']) ?></h2>


    <!-- FILTER & SEARCH -->
    <form method="get" action="/stockinitial/detail/<?= $branch['id'] ?>" class="filter-box row g-3 align-items-end">

        <!-- Search -->
        <div class="col-md-4">
            <label class="form-label fw-semibold">🔍 Pencarian Produk</label>
            <input type="text" name="search" value="<?= esc($search ?? '') ?>" class="form-control"
                placeholder="Nama Produk atau Jumlah...">
        </div>

        <!-- Sorting -->
        <div class="col-md-4">
            <label class="form-label fw-semibold">🎯 Urutkan</label>
            <select name="sort" class="form-select shadow-sm border-secondary-subtle">
                <option value="">✨ Default (A-Z)</option>

                <optgroup label="📛 Nama Produk">
                    <option value="name_asc" <?= ($sort == 'name_asc') ? 'selected' : '' ?>>A → Z</option>
                    <option value="name_desc" <?= ($sort == 'name_desc') ? 'selected' : '' ?>>Z → A</option>
                </optgroup>

                <?php if ($userRole === 'pusat'): ?>
                    <optgroup label="📅 Tanggal Input">
                        <option value="date_new" <?= ($sort == 'date_new') ? 'selected' : '' ?>>🆕 Terbaru</option>
                        <option value="date_old" <?= ($sort == 'date_old') ? 'selected' : '' ?>>📜 Terlama</option>
                    </optgroup>
                <?php endif; ?>
            </select>
        </div>

        <!-- Date Filter (Only Pusat) -->
        <?php if ($userRole === 'pusat'): ?>
            <div class="col-md-3">
                <label class="form-label fw-semibold">📅 Filter Tanggal</label>
                <input type="date" name="date" value="<?= esc($date ?? '') ?>" class="form-control">
            </div>
        <?php endif; ?>

        <div class="col-md-2 d-grid">
            <button class="btn btn-primary fw-semibold shadow-sm">
                🔍 Terapkan
            </button>
        </div>

        <div class="col-md-2 d-grid">
            <a href="/stockinitial/detail/<?= $branch['id'] ?>" class="btn btn-outline-secondary fw-semibold">
                ✖ Reset
            </a>
        </div>
    </form>

    <!-- Badge Info Filter Active -->
    <?php if ($search || $sort || $date): ?>
        <div class="mb-3 mt-2">
            <span class="badge bg-primary badge-title">Filter aktif</span>

            <?php if ($search): ?>
                <span class="badge bg-dark badge-title">🔍 <?= esc($search) ?></span>
            <?php endif; ?>

            <?php if ($sort): ?>
                <span class="badge bg-success badge-title">
                    Sort:
                    <?php
                    $labels = [
                        'name_asc' => 'A → Z',
                        'name_desc' => 'Z → A',
                        'date_new' => 'Terbaru',
                        'date_old' => 'Terlama',
                    ];
                    echo $labels[$sort] ?? 'Custom';
                    ?>
                </span>
            <?php endif; ?>

            <?php if ($date): ?>
                <span class="badge bg-info text-dark badge-title">📅 <?= esc($date) ?></span>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <!-- Table Stok Awal -->
    <div class="table-responsive">
        <table class="table table-hover table-bordered align-middle">
            <thead class="table-light text-center">
    <tr>
        <th style="width: 50px;">No</th>
        <th>Nama Produk</th>
        <th style="width: 220px;">Kategori</th>
        <th style="width: 120px;">Jumlah</th>
    </tr>
</thead>

            <tbody>
    <?php if (!empty($stockInitial)): ?>
        <?php foreach ($stockInitial as $i => $row): ?>
            <tr>
                <td class="text-center"><?= $i + 1 ?></td>

                <td class="fw-semibold">
                    <?= esc($row['product_name']) ?>
                </td>

                <td class="text-center">
                    <?php if (!empty($row['category_name'])): ?>
                        <span class="badge bg-info text-dark px-3 py-2">
                            <?= esc($row['category_name']) ?>
                        </span>
                    <?php else: ?>
                        <span class="badge bg-secondary px-3 py-2">
                            Tidak dikategorikan
                        </span>
                    <?php endif; ?>
                </td>

                <td class="text-center fw-bold">
                    <?= esc($row['quantity']) ?>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td colspan="4" class="text-center py-3">
                <em>Tidak ada data stok awal.</em>
            </td>
        </tr>
    <?php endif; ?>
</tbody>

        </table>
    </div>

    <!-- Tombol Kembali hanya untuk PUSAT -->
    <?php if ($userRole === 'pusat'): ?>
        <a href="/stockinitial" class="btn btn-secondary mt-3">⬅ Kembali</a>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>