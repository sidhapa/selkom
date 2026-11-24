<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php
$role = session('role');

/**
 * Minggu aktif
 * $selectedWeek dikirim dari controller
 */
$prevWeek = date('Y-m-d', strtotime($weekStart . ' -7 days'));
$nextWeek = date('Y-m-d', strtotime($weekStart . ' +7 days'));
?>

<div class="container mt-4">
    <h4 class="mb-3">📦 Daftar Permintaan Stok Cabang</h4>

    <!-- FLASH MESSAGES -->
    <?php if (session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= session('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= session('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- INFO PERIODE -->
    <div class="alert alert-info text-center py-2 mb-3">
        Periode:
        <strong><?= date('d M Y', strtotime($weekStart)) ?></strong>
        s/d
        <strong><?= date('d M Y', strtotime($weekEnd)) ?></strong>
    </div>

    <!-- FILTER -->
    <form method="get" class="row g-2 mb-3 align-items-end">

        <!-- PILIH MINGGU -->
        <div class="col-md-3">
            <label class="form-label small text-muted">Pilih Minggu</label>
            <input type="date" name="week"
                value="<?= esc($selectedWeek) ?>"
                class="form-control">
        </div>

        <!-- STATUS -->
        <div class="col-md-3">
            <label class="form-label small text-muted">Status</label>
            <select name="status" class="form-select">
                <option value="">-- Semua Status --</option>
                <option value="pending" <?= ($selectedStatus ?? '') === 'pending' ? 'selected' : '' ?>>⏳ Menunggu Persetujuan</option>
                <option value="approved" <?= ($selectedStatus ?? '') === 'approved' ? 'selected' : '' ?>>🚚 Disetujui / Dalam Pengiriman</option>
                <option value="received" <?= ($selectedStatus ?? '') === 'received' ? 'selected' : '' ?>>✅ Selesai (Diterima)</option>
                <option value="rejected" <?= ($selectedStatus ?? '') === 'rejected' ? 'selected' : '' ?>>❌ Ditolak</option>
            </select>
        </div>

        <!-- SORT -->
        <div class="col-md-3">
            <label class="form-label small text-muted">Urutkan</label>
            <select name="sort" class="form-select">
                <option value="desc" <?= ($selectedSort ?? 'desc') === 'desc' ? 'selected' : '' ?>>
                    Terbaru
                </option>
                <option value="asc" <?= ($selectedSort ?? '') === 'asc' ? 'selected' : '' ?>>
                    Terlama
                </option>
            </select>
        </div>

        <!-- BUTTON -->
        <div class="col-md-3 d-flex gap-2">
            <button class="btn btn-primary flex-fill">🔍 Filter</button>
            <a href="<?= base_url('branch-order') ?>" class="btn btn-secondary flex-fill">🔄 Reset</a>
        </div>
    </form>

    <!-- NAVIGASI MINGGU -->
    <div class="d-flex justify-content-center gap-2 mb-3">
        <a href="<?= base_url('branch-order?week=' . $prevWeek) ?>"
            class="btn btn-outline-secondary btn-sm">⏪ Minggu Sebelumnya</a>

        <a href="<?= base_url('branch-order') ?>"
            class="btn btn-outline-primary btn-sm">📅 Minggu Ini</a>

        <a href="<?= base_url('branch-order?week=' . $nextWeek) ?>"
            class="btn btn-outline-secondary btn-sm">⏩ Minggu Berikutnya</a>
    </div>

    <!-- TOMBOL CABANG -->
    <?php if ($role === 'cabang'): ?>
        <a href="<?= base_url('branch-order/create') ?>" class="btn btn-primary mb-3">
            ➕ Buat Permintaan Baru
        </a>
    <?php endif; ?>

    <!-- TABLE -->
    <div class="table-responsive shadow-sm rounded" style="max-height:500px; overflow-y:auto;">
        <table class="table table-bordered table-hover align-middle mb-0 bg-white">
            <thead class="table-light text-center sticky-top">
                <tr>
                    <th style="width:50px;">No</th>
                    <th>Kode Permintaan</th>
                    <th>Waktu Permintaan</th>
                    <th>Status</th>
                    <th style="width:180px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($orders)): ?>
                    <?php foreach ($orders as $i => $o): ?>
                        <tr>
                            <td class="text-center"><?= $i + 1 ?></td>
                            <td class="fw-semibold"><?= esc($o['request_code']) ?></td>
                            <td class="text-center"><?= date('d/m/Y H:i', strtotime($o['requested_at'])) ?></td>
                            <td class="text-center">
                                <?php if ($o['status'] === 'approved'): ?>
                                    <span class="badge bg-info text-dark">🚚 Disetujui / Belum Diterima</span>
                                <?php elseif ($o['status'] === 'received'): ?>
                                    <span class="badge bg-success">✅ Selesai (Diterima)</span>
                                <?php elseif ($o['status'] === 'pending'): ?>
                                    <span class="badge bg-warning text-dark">⏳ Menunggu Persetujuan</span>
                                <?php elseif ($o['status'] === 'rejected'): ?>
                                    <span class="badge bg-danger">❌ Ditolak</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <?php if ($role === 'cabang' && $o['status'] === 'approved'): ?>
                                    <a href="<?= base_url('branch-order/detail/' . $o['id']) ?>"
                                        class="btn btn-sm btn-success fw-semibold">
                                        📦 Terima Barang
                                    </a>
                                <?php else: ?>
                                    <a href="<?= base_url('branch-order/detail/' . $o['id']) ?>"
                                        class="btn btn-sm btn-outline-primary">
                                        Lihat Detail
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">
                            <em>Tidak ada permintaan pada periode minggu ini.</em>
                        </td>
                    </tr>
                <?php endif ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>
