<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="content-box">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="fw-bold"><i class="bi bi-plus-circle"></i> Tambah Stok Produk</h3>
        <a href="/stock" class="btn btn-danger btn-sm"><i class="bi bi-arrow-left"></i> Kembali</a>
    </div>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif ?>

    <form action="/stock/store" method="post">
        <?= csrf_field() ?>

        <div class="mb-3">
            <label class="form-label">Pilih Produk</label>
           <select name="product_id" class="form-select" required>
    <option value="">-- Pilih Produk --</option>
    <?php foreach ($products as $product): ?>
                    <option value="<?= esc($product['product_item_id']) ?>">
                        <?= esc($product['product_name'] . ' | Kategori: ' . $product['category_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Jumlah</label>
            <input type="number" name="quantity" class="form-control" required min="1"
                placeholder="Masukkan jumlah stok">
        </div>

        <div class="mb-3">
            <label class="form-label">Harga Jual</label>
            <input type="number" name="sell_price" class="form-control" required min="0"
                placeholder="Masukkan harga jual">
        </div>

        <div class="text-end">
            <button type="submit" class="btn btn-primary"><i class="bi bi-check2-circle"></i> Tambah Stok</button>
        </div>
    </form>
</div>

<?= $this->endSection() ?>