<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="card shadow-sm border-0 mt-3">
    <div class="card-body">
        <h4 class="mb-3">✏️ Edit Cabang</h4>

        <?php if (isset($errors) && !empty($errors)): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach ($errors as $err): ?>
                        <li><?= esc($err) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="/branches/update/<?= esc($branch['id']) ?>" method="post">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label class="form-label">Nama Cabang</label>
                <input type="text" name="name" class="form-control" value="<?= esc(old('name', $branch['name'])) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Alamat</label>
                <textarea name="address" class="form-control" rows="3" required><?= esc(old('address', $branch['address'])) ?></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Kode Kota</label>
                <input type="text" name="city_code" class="form-control" value="<?= esc(old('city_code', $branch['city_code'])) ?>" required>
            </div>

            <div class="d-flex gap-2">
                <button class="btn btn-primary">Update</button>
                <a href="/branches" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>
