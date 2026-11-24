<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="container mt-4">

    <h4>Laporan Penjualan Bulanan</h4>

    <!-- FILTER FORM -->
    <form method="get" class="d-flex gap-2 mb-3 align-items-center">

        <!-- PILIH BULAN -->
        <select name="month" class="form-select" style="width:auto;">
            <?php
            $months = [
                1 => "Januari",
                2 => "Februari",
                3 => "Maret",
                4 => "April",
                5 => "Mei",
                6 => "Juni",
                7 => "Juli",
                8 => "Agustus",
                9 => "September",
                10 => "Oktober",
                11 => "November",
                12 => "Desember"
            ];
            foreach ($months as $num => $name): ?>
                <option value="<?= $num ?>" <?= ($num == (int) $selectedMonth) ? 'selected' : '' ?>>
                    <?= $name ?>
                </option>
            <?php endforeach; ?>
        </select>

        <!-- PILIH TAHUN -->
        <select name="year" class="form-select" style="width:auto;">
            <?php foreach ($years as $y): ?>
                <option value="<?= $y ?>" <?= ($y == (int) $selectedYear) ? 'selected' : '' ?>>
                    <?= $y ?>
                </option>
            <?php endforeach; ?>
        </select>

        <!-- SORT -->
        <select name="sort" class="form-select" style="width:auto;">
            <option value="highest" <?= $sort == 'highest' ? 'selected' : '' ?>>Total Tertinggi</option>
            <option value="lowest" <?= $sort == 'lowest' ? 'selected' : '' ?>>Total Terendah</option>

            <?php if (session('role') === 'pusat'): ?>
                <option value="branch_az" <?= $sort == 'branch_az' ? 'selected' : '' ?>>Cabang A → Z</option>
                <option value="branch_za" <?= $sort == 'branch_za' ? 'selected' : '' ?>>Cabang Z → A</option>
            <?php endif; ?>
        </select>

        <button class="btn btn-primary">Filter</button>

        <a href="<?= base_url('sales/report/monthly') ?>" class="btn btn-secondary">Reset</a>

        <!-- 🔥 TOMBOL GRAFIK PRODUK (SEJAJAR FILTER) -->
        <a href="<?= base_url(
            'sales/report/monthly-product-graph?month='
            . $selectedMonth . '&year=' . $selectedYear
        ) ?>" class="btn btn-success">
            Grafik
        </a>

    </form>

    <!-- TABEL LAPORAN -->
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Bulan</th>
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
                        <td><?= $months[(int) $selectedMonth] . ' ' . $selectedYear ?></td>

                        <?php if (session('role') === 'pusat'): ?>
                            <td><?= esc($r['branch_name'] ?? '-') ?></td>
                        <?php endif; ?>

                        <td>Rp <?= number_format($r['total'], 0, ',', '.') ?></td>

                        <td>
                            <a href="<?= base_url(
                                'sales/report/monthly/pdf?month='
                                . $selectedMonth . '&year=' . $selectedYear
                                . '&branch_id=' . $r['branch_id']
                            ) ?>" target="_blank" class="btn btn-sm btn-danger">
                                Cetak
                            </a>
                            <a href="<?= base_url(
                                'sales/report/monthly/detail?month='
                                . $selectedYear . '-' . str_pad($selectedMonth, 2, '0', STR_PAD_LEFT)
                                . '&branch_id=' . $r['branch_id']
                            ) ?>" class="btn btn-sm btn-info">
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