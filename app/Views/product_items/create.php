<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container mt-4">
    <div class="card">
        <div class="card-header">
            <h5>➕ Tambah Produk</h5>
        </div>

        <div class="card-body">
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

            <form method="post" action="<?= site_url('product-items/store') ?>">
                <?= csrf_field() ?>

                <div class="mb-3">
                    <label class="form-label">Nama Produk</label>
                    <input type="text" name="name" value="<?= old('name') ?>" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Kategori</label>
                    <select name="category_id" id="category_id" class="form-select" required>
                        <option value="">-- Pilih Kategori --</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= old('category_id') == $cat['id'] ? 'selected' : '' ?>>
                                <?= esc($cat['name']) ?>
                            </option>
                        <?php endforeach ?>
                    </select>
                </div>

                <button class="btn btn-primary">Simpan</button>
                <a href="<?= site_url('product-items') ?>" class="btn btn-secondary">Kembali</a>
            </form>
        </div>
    </div>
</div>

<!-- jQuery CDN (wajib untuk Select2) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Select2 CSS & JS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function () {
        // Terapkan Select2 ke dropdown kategori
        $('#category_id').select2({
            placeholder: "-- Pilih Kategori --",
            allowClear: true,
            width: '100%'
        });
    });
</script>

<?= $this->endSection() ?>