<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Laporan Penjualan Harian</title>

    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        .title { text-align: center; font-size: 16px; font-weight: bold; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid black; padding: 6px; }
        th { background: #f0f0f0; }
        .right { text-align: right; }
    </style>
</head>

<body>

    <div class="title">
        LAPORAN PENJUALAN HARIAN<br>
        <?= esc($date) ?>
    </div>

    <div class="info">
        <strong>Cabang:</strong> <?= $isPusat ? 'Pusat' : esc($branch['name']) ?><br>

        <strong>User yang Masuk Laporan:</strong><br>

        <?php if (empty($users)): ?>
            - Tidak ada user<br>
        <?php else: ?>
            <?php foreach ($users as $u): ?>
                - <?= esc($u['name']) ?><br>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Produk</th>
                <th>Kategori</th>
                <th>Qty</th>
                <th>Total Harga</th>
            </tr>
        </thead>

        <tbody>
            <?php $no = 1; ?>
            <?php foreach ($products as $p): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= esc($p['product_name']) ?></td>
                    <td><?= esc($p['category_name']) ?></td>
                    <td class="right"><?= number_format($p['qty']) ?></td>
                    <td class="right"><?= number_format($p['total']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>

        <tfoot>
            <tr>
                <th colspan="4" class="right">Total Penjualan</th>
                <th class="right"><?= number_format($totalHarian) ?></th>
            </tr>
        </tfoot>
    </table>

</body>
</html>
