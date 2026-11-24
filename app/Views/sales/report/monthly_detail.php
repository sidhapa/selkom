<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container mt-4">

    <h4>
        Rekap Penjualan Bulanan <br>
        <small class="text-muted">
            <?= esc($month) ?> — <?= esc($branch) ?>
        </small>
    </h4>

    <a href="<?= base_url('sales/report/monthly?month=' . $month) ?>" class="btn btn-secondary mb-3">
        ← Kembali
    </a>

    <?php if (empty($sales)): ?>
        <div class="alert alert-warning">
            Tidak ada transaksi pada bulan ini
        </div>
    <?php else: ?>

        <div class="card shadow-sm">
            <div class="card-body p-0">
                <table class="table table-bordered mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Produk</th>
                            <th class="text-center">Qty</th>
                            <th class="text-end">Harga</th>
                            <th class="text-end">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $grandTotal = 0;
                        foreach ($sales as $s):
                            $grandTotal += $s['subtotal'];
                            ?>
                            <tr>
                                <td><?= esc($s['product']) ?></td>
                                <td class="text-center"><?= $s['quantity'] ?></td>
                                <td class="text-end">
                                    Rp <?= number_format($s['price'], 0, ',', '.') ?>
                                </td>
                                <td class="text-end">
                                    Rp <?= number_format($s['subtotal'], 0, ',', '.') ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <th colspan="3" class="text-end">TOTAL</th>
                            <th class="text-end">
                                Rp <?= number_format($grandTotal, 0, ',', '.') ?>
                            </th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

    <?php endif; ?>

</div>

<?= $this->endSection() ?>