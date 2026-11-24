<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="card shadow-sm border-0 mt-3">
    <div class="card-body">

        <h4 class="mb-4">👤 Tambah User</h4>

        <!-- Alert Error -->
        <?php if (session()->getFlashdata('errors')): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach (session()->getFlashdata('errors') as $error): ?>
                        <li><?= esc($error) ?></li>
                    <?php endforeach ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="/users/store" method="post" enctype="multipart/form-data">
            <?= csrf_field() ?>

            <div class="row g-3">

                <div class="col-md-6">
                    <label class="form-label">Nama</label>
                    <input type="text" name="name" class="form-control" value="<?= old('name') ?>"
                        placeholder="Masukkan nama user..." required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="<?= old('email') ?>"
                        placeholder="Masukkan email..." required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Password</label>
                    <div class="input-group">
                        <input type="password" name="password" id="password" class="form-control"
                            placeholder="Masukkan password..." required>
                        <span class="input-group-text bg-white">
                            <a href="javascript:void(0);" onclick="togglePassword('password', 'password-icon')">
                                <img id="password-icon" src="<?= base_url('uploads/icon/hide.png') ?>" width="18"
                                    alt="Toggle Password">
                            </a>
                        </span>
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Role</label>
                    <select name="role" id="role" class="form-select" required>
                        <option value="">-- Pilih Role --</option>
                        <option value="pusat" <?= old('role') == 'pusat' ? 'selected' : '' ?>>Pusat</option>
                        <option value="cabang" <?= old('role') == 'cabang' ? 'selected' : '' ?>>Cabang</option>
                    </select>
                </div>

                <div class="col-md-6" id="branch-container" style="display:none;">
                    <label class="form-label">Cabang</label>
                    <select name="branch_id" class="form-select">
                        <option value="">-- Pilih Cabang --</option>
                        <?php foreach ($branches as $b): ?>
                            <option value="<?= $b['id'] ?>" <?= old('branch_id') == $b['id'] ? 'selected' : '' ?>>
                                <?= esc($b['name']) ?>
                            </option>
                        <?php endforeach ?>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Foto (Opsional)</label>
                    <input type="file" name="photo" class="form-control" accept="image/png,image/jpeg">
                </div>

            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Simpan
                </button>
                <a href="/users" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>

        </form>
    </div>
</div>

<script>
    function togglePassword(fieldId, iconId) {
        const field = document.getElementById(fieldId);
        const icon = document.getElementById(iconId);
        if (field.type === "password") {
            field.type = "text";
            icon.src = "<?= base_url('uploads/icon/view.png') ?>";
        } else {
            field.type = "password";
            icon.src = "<?= base_url('uploads/icon/hide.png') ?>";
        }
    }

    function toggleBranchDropdown() {
        const role = document.getElementById('role').value;
        const container = document.getElementById('branch-container');
        if (role === 'cabang') {
            container.style.display = 'block';
        } else {
            container.style.display = 'none';
            container.querySelector('select').value = '';
        }
    }

    window.addEventListener('DOMContentLoaded', () => {
        toggleBranchDropdown();
        document.getElementById('role').addEventListener('change', toggleBranchDropdown);
    });
</script>

<?= $this->endSection() ?>