<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container mt-4">

    <h4>
        Detail Penjualan Harian <br>
        <small class="text-muted">
            <?= esc($date) ?> — <?= esc($branch) ?>
        </small>
    </h4>

    <a href="<?= base_url('sales/report/daily?date=' . $date) ?>" class="btn btn-secondary mb-3">
        ← Kembali
    </a>

    <?php if (empty($sales)): ?>
        <div class="alert alert-warning">
            Tidak ada transaksi
        </div>
    <?php else: ?>
        <?php foreach ($sales as $sale): ?>
            <div class="card mb-3 shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <strong>Invoice:</strong> <?= esc($sale['invoice']) ?>
                        <span class="badge bg-secondary ms-2"><i class="bi bi-clock"></i> <?= date('H:i:s', strtotime($sale['created_at'] ?? $sale['date'])) ?> WIB</span>
                        <br>
                        <small class="text-muted">Kasir: <?= esc($sale['cashier']) ?></small>
                    </div>
                    <div class="text-end">
                        <small class="text-muted d-block">Total Transaksi</small>
                        <strong class="text-primary fs-6">Rp <?= number_format($sale['total'], 0, ',', '.') ?></strong>
                    </div>
                </div>

                <div class="card-body p-0">
                    <table class="table table-bordered mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Produk</th>
                                <th>Qty</th>
                                <th>Harga</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($details[$sale['id']] ?? [] as $d): ?>
                                <tr>
                                    <td><?= esc($d['product']) ?></td>
                                    <td><?= $d['quantity'] ?></td>
                                    <td>Rp <?= number_format($d['price'], 0, ',', '.') ?></td>
                                    <td>Rp <?= number_format($d['subtotal'], 0, ',', '.') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

</div>

<?= $this->endSection() ?>
