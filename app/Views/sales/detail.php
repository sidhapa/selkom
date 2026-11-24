<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container mt-4">
    <h4>🧾 Detail Penjualan</h4>

    <!-- HEADER -->
    <div class="card mb-3">
        <div class="card-body">
            <table class="table table-borderless mb-0">
                <tr>
                    <th width="150">Invoice</th>
                    <td>: <?= esc($sale['invoice']) ?></td>
                </tr>
                <tr>
                    <th>Tanggal & Waktu</th>
                    <td>: <?= date('d/m/Y', strtotime($sale['date'])) ?> pukul <strong><?= date('H:i:s', strtotime($sale['created_at'] ?? $sale['date'])) ?> WIB</strong></td>
                </tr>
                <tr>
                    <th>Cabang</th>
                    <td>: <?= esc($sale['branch_name'] ?? 'Pusat') ?></td>
                </tr>
                <tr>
                    <th>Total</th>
                    <td>: <strong>Rp <?= number_format($sale['total'], 0, ',', '.') ?></strong></td>
                </tr>
            </table>
        </div>
    </div>

    <!-- DETAIL PRODUK -->
    <div class="card">
        <div class="card-body">
            <table class="table table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th width="50">No</th>
                        <th>Produk</th>
                        <th width="120">Harga</th>
                        <th width="80">Jumlah</th>
                        <th width="150">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($details)): ?>
                        <tr>
                            <td colspan="5" class="text-center">
                                Tidak ada detail penjualan
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($details as $i => $item): ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td><?= esc($item['product_name']) ?></td>
                                <td>
                                    Rp <?= number_format($item['price'], 0, ',', '.') ?>
                                </td>
                                <td><?= (int) $item['quantity'] ?></td>
                                <td>
                                    Rp <?= number_format($item['subtotal'], 0, ',', '.') ?>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    <?php endif; ?>
                </tbody>
                <tfoot>
                    <tr class="table-secondary">
                        <th colspan="4" class="text-end">TOTAL</th>
                        <th>
                            Rp <?= number_format($sale['total'], 0, ',', '.') ?>
                        </th>
                    </tr>
                </tfoot>
            </table>

            <a href="<?= site_url('sales') ?>" class="btn btn-secondary mt-3">
                ← Kembali
            </a>
        </div>
    </div>
</div>

<?= $this->endSection() ?>