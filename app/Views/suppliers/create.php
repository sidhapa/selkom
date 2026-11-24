<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="card shadow-sm border-0 mt-3">
    <div class="card-body">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">🏢 Tambah Supplier</h4>
            <a href="/suppliers" class="btn btn-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>

        <form action="/suppliers/store" method="post">

            <div class="mb-3">
                <label class="form-label">Nama Supplier</label>
                <input type="text"
                       name="name"
                       class="form-control"
                       placeholder="Masukkan nama supplier..."
                       required>
            </div>

            <div class="mb-3">
                <label class="form-label">Telepon</label>
                <input type="text"
                       name="phone"
                       class="form-control"
                       placeholder="Contoh: 08123456789">
            </div>

            <div class="mb-3">
                <label class="form-label">Alamat</label>
                <textarea name="address"
                          class="form-control"
                          rows="3"
                          placeholder="Masukkan alamat lengkap supplier..."></textarea>
            </div>

            <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-save"></i> Simpan
                </button>
            </div>

        </form>

    </div>
</div>

<?= $this->endSection() ?>
