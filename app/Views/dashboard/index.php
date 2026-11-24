<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php
$hour = date('H');
$greeting = match (true) {
    $hour >= 5 && $hour < 12 => 'Selamat Pagi',
    $hour >= 12 && $hour < 15 => 'Selamat Siang',
    $hour >= 15 && $hour < 18 => 'Selamat Sore',
    default => 'Selamat Malam',
};

$isPusat = isset($userRole) && $userRole === 'pusat';
?>

<div class="container-fluid">

    <!-- HEADER DASHBOARD -->
    <div class="row mb-5">
        <div class="col-12 text-center">
            <h2 class="fw-bold mb-1">
                <?= $greeting ?>, <?= esc($userName) ?>
            </h2>
            <h6 class="text-muted mb-2">
                <?= esc($branchName) ?>
            </h6>
            <span id="clock" class="text-muted small"></span>
        </div>
    </div>
<!-- SUMMARY -->
<div class="row mb-4">

<?php if ($isPusat): ?>
    <!-- ===== PUSAT (4 CARD) ===== -->
    <div class="col-md-3">
        <div class="card shadow-sm text-center">
            <div class="card-body">
                <h6 class="text-muted">Total Produk</h6>
                <h3 class="fw-bold"><?= number_format($totalProduk) ?></h3>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow-sm text-center">
            <div class="card-body">
                <h6 class="text-muted">Total Stok</h6>
                <h3 class="fw-bold"><?= number_format($totalStok) ?></h3>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow-sm text-center">
            <div class="card-body">
                <h6 class="text-muted">Total Cabang</h6>
                <h3 class="fw-bold"><?= number_format($totalCabang) ?></h3>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow-sm text-center">
            <div class="card-body">
                <h6 class="text-muted">Total Supplier</h6>
                <h3 class="fw-bold"><?= number_format($totalSupplier) ?></h3>
            </div>
        </div>
    </div>

<?php else: ?>
    <!-- ===== CABANG (3 CARD, CENTER & RAPI) ===== -->
    <div class="col-md-4">
        <div class="card shadow-sm text-center">
            <div class="card-body">
                <h6 class="text-muted">Total Produk</h6>
                <h3 class="fw-bold"><?= number_format($totalProduk) ?></h3>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card shadow-sm text-center">
            <div class="card-body">
                <h6 class="text-muted">Total Stok</h6>
                <h3 class="fw-bold"><?= number_format($totalStok) ?></h3>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card shadow-sm text-center">
            <div class="card-body">
                <h6 class="text-muted">Cabang</h6>
                <h3 class="fw-bold">Aktif</h3>
            </div>
        </div>
    </div>
<?php endif; ?>

</div>

    <!-- GRAFIK -->
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header fw-bold text-center">
                    Penjualan Harian
                    <br>
                    <small class="text-muted">
                        <?= $isPusat ? 'Seluruh Cabang' : 'Cabang Ini' ?>
                    </small>
                </div>
                <div class="card-body">
                    <canvas id="dailyChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header fw-bold text-center">
                    Penjualan Bulanan   
                    <br>
                    <small class="text-muted">
                        <?= $isPusat ? 'Seluruh Cabang' : 'Cabang Ini' ?>
                    </small>
                </div>
                <div class="card-body">
                    <canvas id="monthlyChart"></canvas>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- CHART JS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    /* CLOCK */
    setInterval(() => {
        document.getElementById('clock').innerText =
            new Date().toLocaleString('id-ID', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
    }, 1000);

    /* DATA */
    const dailyLabels = <?= json_encode(array_column($penjualanHarian, 'tanggal')) ?>;
    const dailyData = <?= json_encode(array_column($penjualanHarian, 'total')) ?>;
    const monthlyLabels = <?= json_encode(array_column($penjualanBulanan, 'bulan')) ?>;
    const monthlyData = <?= json_encode(array_column($penjualanBulanan, 'total')) ?>;

    /* DAILY CHART */
    new Chart(document.getElementById('dailyChart'), {
        type: 'line',
        data: {
            labels: dailyLabels,
            datasets: [{
                label: 'Total Penjualan',
                data: dailyData,
                borderWidth: 2,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            scales: { y: { beginAtZero: true } }
        }
    });

    /* MONTHLY CHART */
    new Chart(document.getElementById('monthlyChart'), {
        type: 'line',
        data: {
            labels: monthlyLabels,
            datasets: [{
                label: 'Total Penjualan',
                data: monthlyData,
                borderWidth: 2,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            scales: { y: { beginAtZero: true } }
        }
    });
</script>

<?= $this->endSection() ?>