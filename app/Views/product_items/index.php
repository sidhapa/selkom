<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="card shadow-sm border-0 mt-3">
    <div class="card-body">

        <!-- HEADER -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">📦 Produk</h4>
            <?php if ($isPusat): ?>
                <a href="<?= site_url('product-items/create') ?>" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Tambah Produk
                </a>
            <?php endif; ?>
        </div>

        <!-- ALERT: TIDAK BISA DIHAPUS (dengan panduan edit) -->
        <?php if (session('error_cannot_delete')): ?>
            <?php $errData = session('error_cannot_delete'); ?>
            <div class="alert alert-warning alert-dismissible fade show d-flex align-items-start gap-3" role="alert">
                <span style="font-size:1.5rem;">⚠️</span>
                <div>
                    <strong>Produk "<em><?= esc($errData['name']) ?></em>" tidak bisa dihapus</strong><br>
                    Produk ini sudah tercatat dalam data pembelian atau stok.<br>
                    Jika nama produk salah input, kamu bisa langsung <strong>mengedit namanya</strong> tanpa perlu menghapus —
                    semua data pembelian & stok akan tetap terhubung dengan nama yang benar.
                    <div class="mt-2">
                        <a href="<?= site_url('product-items/edit/' . $errData['id']) ?>" class="btn btn-sm btn-warning">
                            <i class="bi bi-pencil-square"></i> Edit Nama Produk Sekarang
                        </a>
                    </div>
                </div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- ALERT ERROR BIASA -->
        <?php if (session('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= session('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- ALERT SUCCESS -->
        <?php if (session('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= session('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- FILTER -->
        <form method="get" class="card card-body border-0 shadow-sm mb-4">
            <div class="row g-2 align-items-end">

                <div class="col-md-4">
                    <label class="form-label small text-muted">Pencarian</label>
                    <input type="text"
                           name="search"
                           class="form-control"
                           placeholder="Cari produk atau kategori..."
                           value="<?= esc($search ?? '') ?>">
                </div>

                <div class="col-md-3">
                    <label class="form-label small text-muted">Kategori</label>
                    <select name="category" class="form-select">
                        <option value="">-- Semua Kategori --</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>"
                                <?= ($category ?? '') == $cat['id'] ? 'selected' : '' ?>>
                                <?= esc($cat['name']) ?>
                            </option>
                        <?php endforeach ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label small text-muted">Urutkan</label>
                    <select name="sort" class="form-select">
                        <option value="latest" <?= ($sort ?? '') === 'latest' ? 'selected' : '' ?>>Terbaru</option>
                        <option value="oldest" <?= ($sort ?? '') === 'oldest' ? 'selected' : '' ?>>Terlama</option>
                        <option value="name_az" <?= ($sort ?? '') === 'name_az' ? 'selected' : '' ?>>Nama A–Z</option>
                        <option value="name_za" <?= ($sort ?? '') === 'name_za' ? 'selected' : '' ?>>Nama Z–A</option>
                        <option value="category_az" <?= ($sort ?? '') === 'category_az' ? 'selected' : '' ?>>Kategori A–Z</option>
                        <option value="category_za" <?= ($sort ?? '') === 'category_za' ? 'selected' : '' ?>>Kategori Z–A</option>
                    </select>
                </div>

                <div class="col-md-2 d-grid">
                    <button class="btn btn-secondary">
                        <i class="bi bi-funnel"></i> Terapkan
                    </button>
                </div>

            </div>
        </form>

        <!-- TABLE -->
        <div class="table-responsive">
            <table class="table table-striped align-middle mb-0">
                <thead class="table-light text-center">
                    <tr>
                        <th style="width:60px;">No</th>
                        <th>Nama Produk</th>
                        <th>Kategori</th>
                        <?php if ($isPusat): ?>
                            <th style="width:160px;">Aksi</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($products)): ?>
                        <?php
                        $no = 1 + ($pager->getCurrentPage('product_items') - 1)
                            * $pager->getPerPage('product_items');
                        foreach ($products as $row):
                        ?>
                            <tr>
                                <td class="text-center"><?= $no++ ?></td>
                                <td><?= esc($row['name']) ?></td>
                                <td><?= esc($row['category_name']) ?></td>
                                <?php if ($isPusat): ?>
                                    <td class="text-center">
                                        <a href="<?= site_url('product-items/edit/' . $row['id']) ?>"
                                           class="btn btn-sm btn-warning me-1">
                                            Edit
                                        </a>
                                        <a href="<?= site_url('product-items/delete/' . $row['id']) ?>"
                                           onclick="return confirm('Hapus produk ini?')"
                                           class="btn btn-sm btn-danger">
                                            Hapus
                                        </a>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="<?= $isPusat ? 4 : 3 ?>"
                                class="text-center py-4 text-muted">
                                <em>Tidak ada data produk ditemukan.</em>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- PAGINATION -->
        <div class="d-flex justify-content-center mt-3">
            <?= $pager->links('product_items', 'pagination') ?>
        </div>

    </div>
</div>

<?= $this->endSection() ?>
