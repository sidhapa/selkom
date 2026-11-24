<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container mt-4">

    <h4>Laporan Penjualan Tahunan</h4>

    <!-- FILTER FORM -->
   <form method="get" class="d-flex gap-2 mb-3">

    <!-- PILIH TAHUN -->
    <select name="year" class="form-select" style="width:auto;">
        <?php foreach ($years as $y): ?>
            <option value="<?= $y['year'] ?>" <?= ($y['year'] == $year) ? 'selected' : '' ?>>
                <?= $y['year'] ?>
            </option>
        <?php endforeach; ?>
    </select>

    <!-- SORTING -->
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

    <!-- FILTER -->
    <button class="btn btn-primary">Filter</button>

    <!-- RESET -->
    <a href="<?= base_url('sales/report/yearly') ?>" class="btn btn-secondary">Reset</a>

    
    <!-- 🔥 TOMBOL GRAFIK -->
    <a href="<?= base_url('sales/report/yearly-product-graph?year=' . $year) ?>" class="btn btn-success">
        Grafik
    </a>

</form>


    <!-- TABEL LAPORAN -->
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Tahun</th>
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
                        <td><?= esc($r['year'] ?? '-') ?></td>

                        <?php if (session('role') === 'pusat'): ?>
                            <td><?= esc($r['branch_name'] ?? '-') ?></td>
                        <?php endif; ?>

                        <td>Rp <?= number_format($r['total'], 0, ',', '.') ?></td>

                        <!-- TOMBOL CETAK PER CABANG / TAHUN -->
                        <td>
                            <a href="<?= base_url('sales/report/yearly/pdf?year='
                                . $r['year'] . '&branch_id=' . ($r['branch_id'] ?? '')) ?>" target="_blank" class="btn btn-sm btn-danger">
                                Cetak
                            </a>
                            <a href="<?= base_url(
    'sales/report/yearly/detail?year='
    . $r['year']
    . '&branch_id=' . $r['branch_id']
) ?>"
   class="btn btn-sm btn-info">
    Detail
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