<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="card shadow-sm border-0 mt-3">
    <div class="card-body">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">✏️ Edit Supplier</h4>
            <a href="/suppliers" class="btn btn-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>

        <form action="/suppliers/update/<?= $supplier['id'] ?>" method="post">

            <div class="mb-3">
                <label class="form-label">Nama Supplier</label>
                <input type="text" name="name" class="form-control" value="<?= $supplier['name'] ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Telepon</label>
                <input type="text" name="phone" class="form-control" value="<?= $supplier['phone'] ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">Alamat</label>
                <textarea name="address" class="form-control" rows="3"><?= $supplier['address'] ?></textarea>
            </div>

            <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-save"></i> Update
                </button>
            </div>

        </form>

    </div>
</div>

<?= $this->endSection() ?>