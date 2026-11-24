<?= $this->extend('layouts/template') ?>
<?= $this->section('content') ?>
<div class="container mt-4">
    <h2>Tambah Alokasi Stok</h2>
    <form action="<?= base_url('alokasi/store') ?>" method="post">
        <?= csrf_field() ?>
        <div class="mb-3">
            <label for="produk_id" class="form-label">Produk</label>
            <select name="produk_id" class="form-control" required>
                <?php foreach ($produk as $p): ?>
                    <option value="<?= $p['id'] ?>"><?= $p['nama_produk'] ?> (Stok: <?= $p['stok'] ?>)</option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="ke_user_id" class="form-label">Cabang Tujuan</label>
            <select name="ke_user_id" class="form-control" required>
                <?php foreach ($users as $u): ?>
                    <option value="<?= $u['id'] ?>"><?= $u['nama'] ?> (<?= $u['email'] ?>)</option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="jumlah" class="form-label">Jumlah</label>
            <input type="number" name="jumlah" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Kirim Alokasi</button>
        <a href="<?= base_url('alokasi') ?>" class="btn btn-secondary">Kembali</a>
    </form>
</div>
<?= $this->endSection() ?>