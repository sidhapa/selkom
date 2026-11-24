<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="card shadow-sm border-0 mt-3">
    <div class="card-body">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="fw-bold mb-0">🏢 Data Cabang</h4>

            <div class="d-flex gap-2">
                <!-- toggle show deleted -->
                <form method="get" action="/branches" class="d-flex align-items-center">
                    <input type="hidden" name="search" value="<?= esc($search ?? '') ?>">
                    <input type="hidden" name="sort" value="<?= esc($sort ?? '') ?>">
                    <input type="hidden" name="order" value="<?= esc($order ?? '') ?>">
                    <?php $checked = isset($showDeleted) && $showDeleted ? 'checked' : ''; ?>
                    <div class="form-check form-switch me-2">
                        <input class="form-check-input" type="checkbox" id="showDeleted" name="show_deleted" value="1" <?= $checked ?> onchange="this.form.submit()">
                        <label class="form-check-label small" for="showDeleted">Tampilkan Terhapus</label>
                    </div>
                </form>

                <?php if ($userRole === 'pusat'): ?>
                        <a href="/branches/create" class="btn btn-primary btn-sm">
                            ➕ Tambah Cabang
                        </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Filter -->
        <form method="get" action="/branches" class="row g-2 mb-3">
            <div class="col-md-4">
                <input type="text" name="search" value="<?= esc($search ?? '') ?>" class="form-control"
                       placeholder="🔍 Cari Nama / Alamat / Kode Kota...">
            </div>

            <div class="col-md-3">
                <select name="sort" class="form-select">
                    <option value="">Urutkan Berdasarkan</option>
                    <option value="name" <?= ($sort === 'name') ? 'selected' : '' ?>>🌟 Nama Cabang</option>
                    <option value="address" <?= ($sort === 'address') ? 'selected' : '' ?>>📍 Alamat</option>
                    <option value="city_code" <?= ($sort === 'city_code') ? 'selected' : '' ?>>🏙️ Kode Kota</option>
                </select>
            </div>

            <div class="col-md-2">
                <select name="order" class="form-select">
                    <option value="ASC" <?= ($order === 'ASC') ? 'selected' : '' ?>>A → Z</option>
                    <option value="DESC" <?= ($order === 'DESC') ? 'selected' : '' ?>>Z → A</option>
                </select>
            </div>

            <div class="col-md-3 d-flex gap-2">
                <button class="btn btn-outline-primary flex-fill">Terapkan</button>
                <a href="/branches" class="btn btn-outline-secondary flex-fill">Reset</a>
            </div>
        </form>

        <!-- Alerts -->
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

        <!-- Table -->
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                <tr>
                    <th style="width:60px;">No</th>
                    <th>Nama Cabang</th>
                    <th>Alamat</th>
                    <th style="width:140px;">Kode Kota</th>
                    <?php if ($userRole === 'pusat'): ?>
                            <th style="width:120px;" class="text-center">Aksi</th>
                    <?php endif; ?>
                </tr>
                </thead>

                <tbody>
                <?php if (!empty($branches)): ?>
                        <?php $no = 1 + ($pager->getCurrentPage('branches') - 1) * $pager->getPerPage('branches'); ?>
                        <?php foreach ($branches as $branch): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td class="fw-semibold"><?= esc($branch['name']) ?></td>
                                    <td><?= esc($branch['address']) ?></td>
                                    <td><span class="badge bg-info text-dark"><?= esc($branch['city_code']) ?></span></td>

                                    <?php if ($userRole === 'pusat'): ?>
                                            <td class="text-center">
                                                <?php if (isset($showDeleted) && $showDeleted): ?>
                                                        <!-- jika melihat deleted, tampilkan restore -->
                                                        <a href="/branches/restore/<?= $branch['id'] ?>" class="btn btn-sm btn-success me-1"
                                                           onclick="return confirm('Pulihkan cabang ini?')" title="Restore">♻️</a>
                                                <?php else: ?>
                                                        <a href="/branches/edit/<?= $branch['id'] ?>" class="btn btn-sm btn-warning me-1" title="Edit">✏️</a>
                                                        <a href="/branches/delete/<?= $branch['id'] ?>" class="btn btn-sm btn-danger"
                                                           onclick="return confirm('Hapus cabang ini?')" title="Delete">🗑️</a>
                                                <?php endif; ?>
                                            </td>
                                    <?php endif; ?>
                                </tr>
                        <?php endforeach; ?>
                <?php else: ?>
                        <tr>
                            <td colspan="<?= ($userRole === 'pusat') ? 5 : 4 ?>" class="text-center text-muted py-3">
                                ❗ Tidak ada data ditemukan
                            </td>
                        </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?= $pager->links('branches', 'pagination') ?>
    </div>
</div>

<?= $this->endSection() ?>
