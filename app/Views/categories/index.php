<?php $userRole = session()->get('role'); ?>

<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<style>
    .content-box {
        background: #fff;
        border-radius: 10px;
        padding: 25px;
        box-shadow: 0px 2px 6px rgba(0, 0, 0, 0.08);
        border: 1px solid #e0e0e0;
    }

    h2 {
        font-weight: 600;
        margin-bottom: 20px;
    }

    .table th,
    .table td {
        vertical-align: middle;
    }
</style>

<div class="content-box">

    <h2>📂 Kategori Produk</h2>

    <!-- ALERT -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Form Pencarian & Sorting -->
    <form method="get" action="/categories" class="row mb-3 g-2 align-items-end">

        <div class="col-md-4">
            <label class="form-label fw-semibold">🔍 Pencarian</label>
            <input type="text" name="search" value="<?= esc($search ?? '') ?>" class="form-control"
                placeholder="Cari Nama Kategori...">
        </div>

        <div class="col-md-3">
            <label class="form-label fw-semibold">🎯 Urutkan</label>
            <select name="sort" class="form-select">
                <option value="">✨ Default (A → Z)</option>
                <option value="name_asc" <?= ($sort ?? '') === 'name_asc' ? 'selected' : '' ?>>A → Z</option>
                <option value="name_desc" <?= ($sort ?? '') === 'name_desc' ? 'selected' : '' ?>>Z → A</option>
            </select>
        </div>

        <div class="col-md-2 d-grid">
            <button class="btn btn-primary fw-semibold">Terapkan</button>
        </div>

        <div class="col-md-2 d-grid">
            <a href="/categories" class="btn btn-outline-secondary fw-semibold">Reset</a>
        </div>
    </form>

    <!-- Badge Filter Aktif -->
    <?php if (!empty($search) || !empty($sort)): ?>
        <div class="mb-3 mt-2">
            <span class="badge bg-primary me-2 px-3 py-2">Filter aktif</span>

            <?php if (!empty($search)): ?>
                <span class="badge bg-dark px-3 py-2">Pencarian: "<?= esc($search) ?>"</span>
            <?php endif; ?>

            <?php if (!empty($sort)): ?>
                <span class="badge bg-success px-3 py-2">
                    Urut: <?= $sort === 'name_asc' ? 'A → Z' : 'Z → A' ?>
                </span>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php if ($userRole === 'pusat'): ?>
        <a href="/categories/create" class="btn btn-success mb-3">+ Tambah Kategori</a>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="table table-hover table-bordered align-middle">
            <thead class="table-light text-center">
                <tr>
                    <th style="width:60px;">No</th>
                    <th>Nama Kategori</th>
                    <?php if ($userRole === 'pusat'): ?>
                        <th style="width:130px;">Aksi</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($categories)): ?>
                    <?php
                    $no = 1 + ($pager->getCurrentPage('categories') - 1) * $pager->getPerPage('categories');
                    ?>
                    <?php foreach ($categories as $cat): ?>
                        <tr>
                            <td class="text-center"><?= $no++ ?></td>
                            <td><?= esc($cat['name']) ?></td>

                            <?php if ($userRole === 'pusat'): ?>
                                <td class="text-center">
                                    <a href="/categories/edit/<?= $cat['id'] ?>" class="btn btn-sm btn-warning">
                                        Edit
                                    </a>
                                    <a href="/categories/delete/<?= $cat['id'] ?>" class="btn btn-sm btn-danger"
                                        onclick="return confirm('Yakin ingin menghapus kategori ini?')">
                                        Hapus
                                    </a>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="<?= $userRole === 'pusat' ? 3 : 2 ?>" class="text-center text-muted py-3">
                            <em>Tidak ada data kategori ditemukan.</em>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        <?= $pager->links('categories', 'pagination') ?>
    </div>

</div>

<?= $this->endSection() ?>