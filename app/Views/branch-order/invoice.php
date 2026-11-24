<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Invoice Pengiriman <?= esc($order['invoice']) ?></title>
<style>
body{font-family: Arial, sans-serif; color:#222; font-size:13px; margin:40px}
.header{display:flex;align-items:center;justify-content:space-between;margin-bottom:20px}
.logo img{width:120px}
.box{border:1px solid #ddd;padding:12px;border-radius:6px;margin-bottom:20px}
.table{width:100%;border-collapse:collapse;margin-top:12px}
.table th,.table td{border:1px solid #ccc;padding:8px;text-align:left}
.table th{background:#f8f8f8}
.signature{margin-top:60px;display:flex;justify-content:space-between}
.sig-box{text-align:center;width:45%}
.print-btn{position:fixed;top:20px;right:20px;z-index:999;}
@media print {
  .print-btn { display:none; }
  body { margin:0; }
}
</style>
</head>
<body onload="window.print()">

<button class="print-btn" onclick="window.print()">🖨️ Cetak</button>

<div class="header">
  <div class="logo"><img src="<?= base_url('uploads/logo/selkom.jpg') ?>" alt="logo"></div>
  <div>
    <h2>Invoice Pengiriman</h2>
    <div>No: <?= esc($order['invoice']) ?></div>
    <div>Tanggal: <?= esc($order['created_at']) ?></div>
  </div>
</div>

<div class="box">
  <strong>Tujuan:</strong> <?= esc($order['branch_name']) ?><br>
  <strong>Dibuat Oleh:</strong> <?= esc($created_by['username'] ?? session()->get('username')) ?>
</div>

<table class="table">
<thead><tr><th>No</th><th>Produk</th><th>Qty</th></tr></thead>
<tbody>
<?php $n = 1; foreach ($details as $d): ?>
  <tr>
    <td><?= $n++ ?></td>
    <td><?= esc($d['product_name']) ?></td>
    <td><?= esc($d['quantity']) ?></td>
  </tr>
<?php endforeach; ?>
</tbody>
</table>

<div class="signature">
  <div class="sig-box">
    <div>Pengirim (Pusat)</div>
    <div style="margin-top:70px"><?= esc(session()->get('username')) ?></div>
  </div>
  <div class="sig-box">
    <div>Penerima</div>
    <div style="margin-top:70px"><?= esc($order['branch_name']) ?></div>
  </div>
</div>

</body>
</html>
