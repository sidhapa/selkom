<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-body">

            <h4 class="fw-bold mb-4">
                <i class="bi bi-tag"></i> Edit Harga Jual Produk
            </h4>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger">
                    <?= session()->getFlashdata('error') ?>
                </div>
            <?php endif ?>

          <form action="/stock/update-price" method="post">
    <?= csrf_field() ?>

    <input type="hidden" name="product_item_id" value="<?= esc($product_item_id) ?>">

    <div class="mb-3">
        <label>Nama Produk</label>
        <input type="text" class="form-control"
               value="<?= esc($product_name) ?>" readonly>
    </div>

    <div class="mb-3">
        <label>Harga Saat Ini</label>
        <input type="number" class="form-control"
               value="<?= esc($old_price) ?>" readonly>
    </div>

    <div class="mb-3">
        <label>Harga Baru</label>
        <input type="number" name="sell_price"
               class="form-control"
               required min="0" step="100">
    </div>

    <button type="submit" class="btn btn-primary">
        Update Harga
    </button>
</form>


        </div>
    </div>
</div>

<?= $this->endSection() ?>
