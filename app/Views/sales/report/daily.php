<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container mt-4">

    <h4>Laporan Penjualan Harian</h4>

    <form method="get" class="d-flex gap-2 mb-3">

        <input type="date" name="date" value="<?= esc($date) ?>" class="form-control" style="width:auto;">

        <select name="sort" class="form-select" style="width:auto;">
            <option value="newest" <?= $sort == 'newest' ? 'selected' : '' ?>>Terbaru</option>
            <option value="oldest" <?= $sort == 'oldest' ? 'selected' : '' ?>>Terlama</option>
            <option value="highest" <?= $sort == 'highest' ? 'selected' : '' ?>>Total Tertinggi</option>
            <option value="lowest" <?= $sort == 'lowest' ? 'selected' : '' ?>>Total Terendah</option>

            <?php if (session('role') === 'pusat'): ?>
                <option value="branch_az" <?= $sort == 'branch_az' ? 'selected' : '' ?>>Cabang A → Z</option>
                <option value="branch_za" <?= $sort == 'branch_za' ? 'selected' : '' ?>>Cabang Z → A</option>
            <?php endif; ?>
        </select>

        <button class="btn btn-primary">Filter</button>

        <a href="<?= base_url('sales/report/daily') ?>" class="btn btn-secondary">Reset</a>

        <a href="<?= base_url('sales/report/daily/products-graph?date=' . $date) ?>"
   class="btn btn-success">
   Grafik
</a>

    </form>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Tanggal</th>

                <?php if (session('role') === 'pusat'): ?>
                    <th>Cabang</th>
                <?php endif; ?>

                <th>Total Penjualan</th>
                <th>Aksi</th>
            </tr>
        </thead>

        <tbody>
            <?php if (!empty($report)): ?>
                <?php foreach ($report as $r): ?>
                    <tr>
                        <td><?= esc($r['date']) ?></td>

                        <?php if (session('role') === 'pusat'): ?>
                            <td><?= esc($r['branch_name']) ?></td>
                        <?php endif; ?>

                        <td>Rp <?= number_format($r['total'], 0, ',', '.') ?></td>

                       <td class="d-flex gap-1">

    <!-- DETAIL TRANSAKSI -->
    <a href="<?= base_url('sales/report/daily/detail?date='
                            . $date . '&branch_id=' . $r['branch_id']) ?>" class="btn btn-sm btn-info">
                            Detail
                        </a>    
                        <!-- CETAK -->
                        <a href="<?= base_url('sales/report/daily/pdf?date='
                            . $date . '&branch_id=' . $r['branch_id']) ?>" target="_blank" class="btn btn-sm btn-danger">
                            Cetak
                        </a>
                    
                    </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="<?= session('role') === 'pusat' ? 4 : 3 ?>" class="text-center">
                        Tidak ada data
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

</div>

<?= $this->endSection() ?>