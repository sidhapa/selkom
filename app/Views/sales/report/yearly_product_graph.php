<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<h4>Grafik Produk Terjual Tahun <?= esc($year) ?></h4>

<form method="get" class="d-flex gap-2 mb-3">
    <select name="year" class="form-control" style="width:auto;">
        <?php for ($y = date('Y'); $y >= date('Y') - 5; $y--): ?>
            <option value="<?= $y ?>" <?= $y == $year ? 'selected' : '' ?>>
                <?= $y ?>
            </option>
        <?php endfor; ?>
    </select>
    <button class="btn btn-primary">Tampilkan</button>
</form>

<?php if (empty($labels)): ?>
    <div class="alert alert-warning">
        Tidak ada penjualan produk pada tahun ini
    </div>
<?php else: ?>
    <canvas id="yearlyProductChart" height="120"></canvas>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const labels = <?= json_encode($labels) ?>;
    const datasets = <?= json_encode($datasets) ?>;

    if (labels.length > 0) {
        new Chart(document.getElementById('yearlyProductChart'), {
            type: 'line',
            data: { labels, datasets },
            options: {
                responsive: true,
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                plugins: {
                    legend: { position: 'bottom' }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0,
                            stepSize: 1
                        },
                        title: {
                            display: true,
                            text: 'Jumlah Terjual'
                        }
                    }
                }
            }
        });
    }
</script>

<?= $this->endSection() ?>
