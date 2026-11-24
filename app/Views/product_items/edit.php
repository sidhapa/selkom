<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container mt-4">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">✏️ Edit Produk</h5>
            <?php if (!empty($isUsed)): ?>
                <span class="badge bg-warning text-dark">
                    <i class="bi bi-exclamation-triangle-fill"></i> Sudah dipakai dalam pembelian
                </span>
            <?php endif; ?>
        </div>

        <div class="card-body">

            <?php if (!empty($isUsed)): ?>
                <div class="alert alert-info d-flex gap-2 align-items-start">
                    <span style="font-size:1.3rem;">ℹ️</span>
                    <div>
                        <strong>Produk ini sudah tercatat dalam data pembelian.</strong><br>
                        Jika ada kesalahan nama, kamu cukup <strong>perbaiki di sini</strong>.
                        Semua data pembelian & stok akan otomatis menggunakan nama yang baru.
                        <br><small class="text-muted">Produk yang sudah masuk pembelian tidak bisa dihapus, tapi bisa diedit kapan saja.</small>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (session('error')): ?>
                <div class="alert alert-danger"><?= session('error') ?></div>
            <?php endif ?>

            <?php if (session('errors')): ?>
                <div class="alert alert-danger">
                    <ul>
                        <?php foreach (session('errors') as $e): ?>
                            <li><?= esc($e) ?></li>
                        <?php endforeach ?>
                    </ul>
                </div>
            <?php endif ?>

            <form method="post" action="<?= site_url('product-items/update/' . $product['id']) ?>">
                <?= csrf_field() ?>

                <div class="mb-3">
                    <label class="form-label">Nama Produk</label>
                    <input type="text" name="name" value="<?= old('name', $product['name']) ?>" class="form-control"
                        required placeholder="Masukkan nama produk yang benar">
                    <?php if (!empty($isUsed)): ?>
                        <div class="form-text text-muted">
                            💡 Perbaiki nama di sini jika sebelumnya salah input.
                        </div>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label class="form-label">Kategori</label>
                    <select name="category_id" id="category_id" class="form-select" required>
                        <option value="">-- Pilih Kategori --</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= old('category_id', $product['category_id']) == $cat['id'] ? 'selected' : '' ?>>
                                <?= esc($cat['name']) ?>
                            </option>
                        <?php endforeach ?>
                    </select>
                </div>

                <button class="btn btn-primary">
                    <i class="bi bi-check-circle"></i> Simpan Perubahan
                </button>
                <a href="<?= site_url('product-items') ?>" class="btn btn-secondary">Kembali</a>
            </form>
        </div>
    </div>
</div>

<!-- jQuery CDN -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Select2 CSS & JS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function () {
        // Aktifkan Select2 pada dropdown kategori
        $('#category_id').select2({
            placeholder: "-- Pilih Kategori --",
            allowClear: true,
            width: '100%'
        });
    });
</script>

<?= $this->endSection() ?>