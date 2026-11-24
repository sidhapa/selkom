<?= $this->extend('layouts/template') ?>
<?= $this->section('content') ?>
<div class="container mt-4">
    <h2>Data Alokasi</h2>
    <?php if (in_groups('pusat')): ?>
        <a href="<?= base_url('alokasi/create') ?>" class="btn btn-primary mb-3">Tambah Alokasi</a>
    <?php endif; ?>
    <form method="get" class="mb-3">
        <div class="input-group">
            <input type="text" name="q" class="form-control" placeholder="Cari produk atau cabang..." value="<?= esc($search) ?>">
            <button class="btn btn-outline-secondary" type="submit">Cari</button>
        </div>
    </form>
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success">
            <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Produk</th>
                <th>Jumlah</th>
                <th>Ke Cabang</th>
                <th>Status</th>
                <th>Dibuat</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($alokasi as $index => $a): ?>
                <tr>
                    <td><?= $index + 1 + ($pager->getCurrentPage() - 1) * $pager->getPerPage() ?></td>
                    <td><?= esc($a['nama_produk']) ?></td>
                    <td><?= esc($a['jumlah']) ?></td>
                    <td><?= esc($a['ke_user']) ?></td>
                    <td><?= esc(ucfirst($a['status'])) ?></td>
                    <td><?= date('d M Y H:i', strtotime($a['created_at'])) ?></td>
                    <td>
                    <?php if ($a['ke_user_id'] == user_id() && $a['status'] === 'dikirim'): ?>
                            <a href="<?= base_url('alokasi/updateStatus/' . $a['id'] . '/diterima') ?>" class="btn btn-success btn-sm">Terima</a>
                        <?php else: ?>
                            <span class="text-muted">-</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?= $pager->links() ?>
</div>
<?= $this->endSection() ?>