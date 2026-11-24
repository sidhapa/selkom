<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<style>
    .content-box {
        background: #fff;
        border-radius: 12px;
        padding: 25px;
        box-shadow: 0 3px 10px rgba(0,0,0,0.06);
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
</style>

<div class="content-box">
    <h2>🏢 Data Supplier</h2>

    <!-- Alert -->
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

    <!-- Filter & Search -->
    <form method="get" action="/suppliers" class="filter-box row g-3 mb-3">

        <!-- Search -->
        <div class="col-md-5">
            <input type="text" name="search" value="<?= esc($search) ?>"
                class="form-control"
                placeholder="🔍 Cari nama, telepon atau alamat...">
        </div>

        <!-- Sorting -->
        <div class="col-md-4">
            <select name="sort" class="form-select" onchange="this.form.submit()">
                <option value="">✨ Urutkan Supplier Berdasarkan</option>
                <option value="name_asc" <?= $sort == 'name_asc' ? 'selected' : '' ?>>📛 Nama — A → Z</option>
                <option value="name_desc" <?= $sort == 'name_desc' ? 'selected' : '' ?>>📛 Nama — Z → A</option>
                <option value="address_asc" <?= $sort == 'address_asc' ? 'selected' : '' ?>>📍 Alamat — A → Z</option>
                <option value="address_desc" <?= $sort == 'address_desc' ? 'selected' : '' ?>>📍 Alamat — Z → A</option>
            </select>
        </div>

        <!-- Buttons -->
        <div class="col-md-3 d-flex gap-2">
            <button class="btn btn-primary fw-semibold flex-fill shadow-sm">
                🔍 Terapkan
            </button>
            <a href="/suppliers" class="btn btn-outline-secondary fw-semibold flex-fill">
                ✖ Reset
            </a>
        </div>
    </form>

    <!-- Button Tambah Supplier -->
    <a href="/suppliers/create" class="btn btn-success mb-3">+ Tambah Supplier Baru</a>

    <!-- Table -->
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th style="width: 60px;">No</th>
                    <th>Nama Supplier</th>
                    <th>Telepon</th>
                    <th>Alamat</th>
                    <th style="width: 140px;" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($suppliers)): ?>
                    <?php 
                        $currentPage = $pager->getCurrentPage('suppliers');
                        $perPage = $pager->getPerPage('suppliers');
                        $no = 1 + ($perPage * ($currentPage - 1));
                    ?>
                    <?php foreach ($suppliers as $s): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><strong><?= esc($s['name']) ?></strong></td>
                            <td><?= esc($s['phone']) ?></td>
                            <td><?= esc($s['address']) ?></td>
                            <td class="text-center">
                                <a href="/suppliers/edit/<?= $s['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                                <a href="/suppliers/delete/<?= $s['id'] ?>" class="btn btn-danger btn-sm"
                                    onclick="return confirm('Hapus supplier ini?')">Hapus</a>
                            </td>
                        </tr>
                    <?php endforeach ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted py-3">
                            ❗ Tidak ada data supplier
                        </td>
                    </tr>
                <?php endif ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?= $pager->links('suppliers', 'pagination') ?>

</div>

<?= $this->endSection() ?>
