<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="card shadow-sm border-0 mt-3">
    <div class="card-body">
        <h4 class="mb-3">✏️ Edit Kategori</h4>

        <!-- Alert Error -->
        <?php if (session()->getFlashdata('errors')): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach (session()->getFlashdata('errors') as $error): ?>
                        <li><?= esc($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="/categories/update/<?= $category['id'] ?>" method="post">
            <?= csrf_field() ?>

            <div class="mb-3">
                <label class="form-label">Nama Kategori</label>
                <input type="text" name="name" class="form-control" value="<?= esc(old('name', $category['name'])) ?>"
                    required>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-warning text-white">
                    <i class="bi bi-pencil-square"></i> Update
                </button>
                <a href="/categories" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>