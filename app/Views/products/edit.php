<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container mt-4">

    <div class="card shadow-sm">

        <!-- CARD HEADER (SAMADENGAN CREATE) -->
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h2 class="mb-0">✏️ Edit Produk (Pembelian)</h2>
            <a href="/products" class="btn btn-secondary btn-sm">
                ← Kembali
            </a>
        </div>

        <div class="card-body">

            <!-- FLASH ERROR -->
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger">
                    <?= esc(session()->getFlashdata('error')) ?>
                </div>
            <?php endif; ?>

            <!-- FLASH SUCCESS -->
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success">
                    <?= esc(session()->getFlashdata('success')) ?>
                </div>
            <?php endif; ?>

            <form action="/products/update/<?= $product['id'] ?>" method="post">
                <?= csrf_field() ?>

                <!-- PRODUCT ITEM -->
                <div class="mb-3">
                    <label class="form-label">📦 Produk</label>
                    <select name="product_item_id" class="form-select" required>
                        <option value="">-- Pilih Produk --</option>

                        <?php foreach ($productItems as $item): ?>
                            <?php
                                $selected = old(
                                    'product_item_id',
                                    $product['product_item_id']
                                ) == $item['id'] ? 'selected' : '';
                            ?>
                            <option value="<?= $item['id'] ?>" <?= $selected ?>>
                                <?= esc($item['name']) ?>
                                (<?= esc($item['category_name'] ?? '-') ?>)
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
                            <?php
                                $selected = old(
                                    'supplier_id',
                                    $product['supplier_id']
                                ) == $sup['id'] ? 'selected' : '';
                            ?>
                            <option value="<?= $sup['id'] ?>" <?= $selected ?>>
                                <?= esc($sup['name']) ?>
                            </option>
                        <?php endforeach; ?>

                    </select>
                </div>

                <!-- QTY -->
                <div class="mb-3">
                    <label class="form-label">📦 Jumlah</label>
                    <input type="number"
                           name="quantity"
                           class="form-control"
                           min="1"
                           value="<?= old('quantity', $product['quantity']) ?>"
                           required>
                </div>

                <!-- PRICE -->
                <div class="mb-3">
                    <label class="form-label">💰 Harga Beli (Rp)</label>
                    <input type="number"
                           name="price"
                           class="form-control"
                           min="0"
                           step="0.01"
                           value="<?= old('price', $product['price']) ?>"
                           required>
                </div>

                <button type="submit" class="btn btn-warning">
                    🔄 Perbarui Produk
                </button>

            </form>

        </div>
    </div>

</div>

<?= $this->endSection() ?>
