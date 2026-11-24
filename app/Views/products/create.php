<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container mt-4">

    <div class="card shadow-sm">

        <!-- HEADER PINDAH KE DALAM BOX (TANPA UBAH STYLE LAIN) -->
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h2 class="mb-0">📦 Tambah Produk (Pembelian)</h2>
            <a href="/products" class="btn btn-secondary btn-sm">
                ← Kembali
            </a>
        </div>

        <div class="card-body">

            <!-- ERROR VALIDATION -->
            <?php if (session()->getFlashdata('errors')): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach (session('errors') as $error): ?>
                            <li><?= esc($error) ?></li>
                        <?php endforeach ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="/products/store" method="post">
                <?= csrf_field() ?>

                <!-- PRODUCT ITEM -->
                <div class="mb-3">
                    <label class="form-label">📦 Produk</label>
                    <select name="product_item_id" id="product-item" class="form-select" required>
                        <option value=""></option>
                        <?php foreach ($productItems as $item): ?>
                            <option value="<?= $item['id'] ?>" <?= old('product_item_id') == $item['id'] ? 'selected' : '' ?>>
                                <?= esc($item['name']) ?> (<?= esc($item['category_name'] ?? '-') ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <small class="text-muted">
                        Produk & kategori berasal dari Master Product
                    </small>
                </div>

                <!-- SUPPLIER -->
                <div class="mb-3">
                    <label class="form-label">🚚 Supplier</label>
                    <select name="supplier_id" class="form-select" required>
                        <option value="">-- Pilih Supplier --</option>
                        <?php foreach ($suppliers as $sup): ?>
                            <option value="<?= $sup['id'] ?>" <?= old('supplier_id') == $sup['id'] ? 'selected' : '' ?>>
                                <?= esc($sup['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- QTY -->
                <div class="mb-3">
                    <label class="form-label">📦 Jumlah</label>
                    <input type="number" name="quantity" class="form-control" min="1" value="<?= old('quantity') ?>"
                        required>
                </div>

                <!-- PRICE -->
                <div class="mb-3">
                    <label class="form-label">💰 Harga Beli (Rp)</label>
                    <input type="number" name="price" class="form-control" min="0" step="0.01"
                        value="<?= old('price') ?>" required>
                </div>

                <button type="submit" class="btn btn-success">
                    💾 Simpan Produk
                </button>

            </form>

        </div>
    </div>

</div>

<!-- jQuery & Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function () {
        $('#product-item').select2({
            placeholder: "✨ Pilih Produk",
            allowClear: true,
            width: '100%'
        });
    });
</script>

<?= $this->endSection() ?>