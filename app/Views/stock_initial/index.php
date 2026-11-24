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
        margin-bottom: 16px;
    }

    .filter-box {
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        padding: 16px;
        border-radius: 10px;
        margin-bottom: 18px;
    }
</style>

<div class="content-box">

    <h2 class="section-title">🏬 Stok Awal Cabang</h2>

    <!-- Filter -->
    <form method="get" action="/stockinitial" class="filter-box row g-3 align-items-end">

        <div class="col-md-6">
            <label class="form-label fw-semibold">🔍 Cari Cabang</label>
            <input
                type="text"
                name="search"
                value="<?= esc($search ?? '') ?>"
                class="form-control"
                placeholder="Nama cabang atau alamat">
        </div>

        <div class="col-md-4">
            <label class="form-label fw-semibold">🎯 Urutkan</label>
            <select name="sort" class="form-select">
                <option value="">Default (A–Z)</option>
                <option value="name_asc" <?= ($sort ?? '') === 'name_asc' ? 'selected' : '' ?>>A → Z</option>
                <option value="name_desc" <?= ($sort ?? '') === 'name_desc' ? 'selected' : '' ?>>Z → A</option>
            </select>
        </div>

        <div class="col-md-2 d-grid">
            <button class="btn btn-primary fw-semibold">Terapkan</button>
        </div>
    </form>

    <?php if ($userRole === 'pusat'): ?>
        <a href="/stockinitial/create" class="btn btn-success mb-3">
            + Input Stok Awal
        </a>
    <?php endif; ?>

    <!-- Table Cabang -->
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th width="60">No</th>
                    <th>Nama Cabang</th>
                    <th>Alamat</th>
                    <?php if ($userRole === 'pusat'): ?>
                        <th width="120" class="text-center">Aksi</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($branches)): ?>
                    <?php
                        $page    = $pager->getCurrentPage('branches');
                        $perPage = $pager->getPerPage('branches');
                        $no      = 1 + ($page - 1) * $perPage;
                    ?>
                    <?php foreach ($branches as $branch): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td class="fw-semibold"><?= esc($branch['name']) ?></td>
                            <td><?= esc($branch['address']) ?></td>
                            <?php if ($userRole === 'pusat'): ?>
                                <td class="text-center">
                                    <a href="/stockinitial/detail/<?= $branch['id'] ?>"
                                       class="btn btn-sm btn-primary">
                                        Detail
                                    </a>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach ?>
                <?php else: ?>
                    <tr>
                        <td colspan="<?= $userRole === 'pusat' ? 4 : 3 ?>" class="text-center text-muted py-3">
                            Belum ada data cabang
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-3">
        <?= $pager->links('branches', 'pagination') ?>
    </div>

</div>

<?= $this->endSection() ?>
