<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Invoice <?= esc($sale['invoice']) ?> - CV SELKOM GROUP</title>
    <style>
        body {
            font-family: Arial;
            margin: 20px;
            font-size: 12px;
            color: #222;
            background: #fff;
        }

        .kop-container {
            display: flex;
            align-items: center;
            padding-bottom: 12px;
            margin-bottom: 22px;
            border-bottom: 2px solid #444;
        }

        .kop-logo img {
            width: 92px;
            height: auto;
            margin-right: 18px;
            display: block;
        }

        .kop-info {
            flex-grow: 1;
            text-align: center;
        }

        .kop-info h1 {
            margin: 0;
            font-size: 24px;
            letter-spacing: 0.6px;
        }

        .kop-info p {
            margin: 3px 0;
            font-size: 13px;
            color: #444;
        }

        .info-box {
            margin-top: 12px;
            margin-bottom: 18px;
            padding: 12px;
            border: 1px solid #cfcfcf;
            border-radius: 8px;
            background: #f7f7f7;
        }

        .info-box h3 {
            margin: 0 0 8px 0;
            font-size: 14px;
            text-transform: uppercase;
        }

        .info-row {
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
            align-items: flex-start;
        }

        .info-item {
            min-width: 220px;
            flex: 1 1 260px;
        }

        .info-item p {
            margin: 6px 0;
            font-size: 13px;
        }

        .muted {
            color: #555;
            font-size: 13px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
            font-size: 14px;
        }

        thead th {
            background: #f0f0f0;
            padding: 10px;
            text-align: left;
            border: 1px solid #cfcfcf;
        }

        tbody td {
            padding: 10px;
            border: 1px solid #cfcfcf;
        }

        tbody tr:nth-child(even) {
            background: #fff;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .total-box {
            margin-top: 18px;
            width: 38%;
            float: right;
            border: 1px solid #cfcfcf;
            border-radius: 8px;
            background: #f7f7f7;
        }

        .total-box td {
            padding: 10px 12px;
        }

        .label {
            color: #333;
        }

        .value {
            text-align: right;
            font-weight: 700;
        }

        .signature {
            clear: both;
            margin-top: 80px;
        }

        .signature h4 {
            margin: 0 0 10px 0;
            font-size: 14px;
            font-weight: 700;
            color: #333;
        }

        .ttd-box {
            width: 280px;
            text-align: center;
            margin-top: 12px;
        }

        .ttd-line {
            border-bottom: 2px solid #000;
            height: 46px;
            margin-bottom: 6px;
        }

        .ttd-name {
            margin: 0;
            font-weight: 700;
        }

        .footer {
            margin-top: 60px;
            text-align: center;
            font-size: 12px;
            padding-top: 10px;
            border-top: 1px solid #cfcfcf;
            color: #555;
        }

        @media print {
            @page {
                margin: 8mm;
            }

            body {
                margin: 8mm;
            }

            .kop-logo img {
                width: 85px;
            }
        }
    </style>
</head>

<body>

    <!-- KOP SURAT -->
    <div class="kop-container">
        <div class="kop-logo">
            <img src="<?= $logoSrc ?>" alt="Logo CV Selkom">
        </div>
        <div class="kop-info">
            <h1>CV SELKOM GROUP</h1>
            <p>Jl. Suka Karya, Pekanbaru, Riau</p>
        </div>
    </div>

    <!-- INFO INVOICE -->
    <div class="info-box">
        <h3>Informasi Invoice</h3>
        <div class="info-row">
            <div class="info-item">
                <p class="muted"><strong>No. Invoice:</strong></p>
                <p><?= esc($sale['invoice']) ?></p>
            </div>
            <div class="info-item">
                <p class="muted"><strong>Tanggal & Waktu:</strong></p>
                <p><?= date('d/m/Y', strtotime($sale['date'])) ?> <?= date('H:i', strtotime($sale['created_at'] ?? $sale['date'])) ?> WIB</p>
            </div>
            <div class="info-item">
                <p class="muted"><strong>Cabang:</strong></p>
                <p><?= esc($sale['branch_name']) ?></p>
            </div>
        </div>
    </div>

    <!-- TABEL PRODUK -->
    <table>
        <thead>
            <tr>
                <th class="text-center" style="width:6%;">No</th>
                <th>Deskripsi Produk</th>
                <th class="text-center" style="width:12%;">Kuantitas</th>
                <th class="text-right" style="width:18%;">Harga Satuan</th>
                <th class="text-right" style="width:18%;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1;
            foreach ($details as $d): ?>
                <tr>
                    <td class="text-center"><?= $no++ ?></td>
                    <td><?= esc($d['product_name']) ?></td>
                    <td class="text-center"><?= esc($d['quantity']) ?></td>
                    <td class="text-right">Rp <?= number_format($d['price'], 0, ',', '.') ?></td>
                    <td class="text-right">Rp <?= number_format($d['subtotal'], 0, ',', '.') ?></td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>

    <!-- TOTAL PEMBAYARAN -->
    <div class="total-box">
        <table>
            <tr>
                <td class="label">Subtotal</td>
                <td class="value">Rp <?= number_format($sale['total'], 0, ',', '.') ?></td>
            </tr>
            <?php if (!empty($sale['ppn'])): ?>
                <tr>
                    <td class="label">PPN (<?= esc($sale['ppn']) ?>%)</td>
                    <td class="value">Rp <?= number_format($sale['total'] * $sale['ppn'] / 100, 0, ',', '.') ?></td>
                </tr>
            <?php endif; ?>
            <tr>
                <td class="label">Total Akhir</td>
                <td class="value">Rp <?= number_format($sale['grand_total'] ?? $sale['total'], 0, ',', '.') ?></td>
            </tr>
        </table>
    </div>

    <!-- TANDA TANGAN -->
    <div class="signature">
        <h4>Penanggung Jawab</h4>
        <div class="ttd-box">
            <div class="ttd-line"></div>
            <p><?= esc($sale['kasir_name']) ?></p>
        </div>
    </div>

    <div class="footer">
        Terima kasih atas kepercayaan Anda kepada CV SELKOM GROUP
    </div>

</body>

</html>