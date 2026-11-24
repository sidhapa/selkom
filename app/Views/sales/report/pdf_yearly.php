<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Laporan Penjualan Tahunan <?= esc($year) ?></title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }

        .title {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .info {
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table,
        th,
        td {
            border: 1px solid black;
            padding: 6px;
        }

        th {
            background: #f0f0f0;
        }

        .right {
            text-align: right;
        }
    </style>
</head>

<body>

    <div class="title">
        LAPORAN PENJUALAN TAHUNAN<br>
        Tahun: <?= esc($year) ?>
    </div>

    <div class="info">
        <strong>Cabang:</strong> <?= esc($branchName) ?><br>
        <strong>User yang Masuk Laporan:</strong><br>
        <?php if (empty($users)): ?>
            - Tidak ada user<br>
        <?php else: ?>
            <?php foreach ($users as $u): ?>
                - <?= esc($u) ?><br>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Produk</th>
                <th>Kategori</th>
                <th>Qty</th>
                <th>Total Harga</th>
            </tr>
        </thead>

        <tbody>
            <?php
            $no = 1;
            if (!empty($sales)):
                foreach ($sales as $sale):
                    $details = $sale['details'] ?? [];
                    foreach ($details as $d): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= date('d/m/Y', strtotime($sale['date'])) ?></td>
                            <td><?= esc($d['product_name']) ?></td>
                            <td><?= esc($d['category_name']) ?></td>
                            <td class="right"><?= number_format($d['qty']) ?></td>
                            <td class="right"><?= number_format($d['total']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="text-center">Tidak ada data penjualan</td>
                </tr>
            <?php endif; ?>
        </tbody>

        <tfoot>
            <tr>
                <th colspan="5" class="right">Total Penjualan Tahun <?= esc($year) ?></th>
                <th class="right"><?= number_format($totalAmount) ?></th>
            </tr>
        </tfoot>
    </table>

</body>

</html>