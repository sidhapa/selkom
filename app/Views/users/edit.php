<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="card shadow-sm border-0 mt-3">
    <div class="card-body">

        <h4 class="mb-4">✏️ Edit User</h4>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>

        <div class="row g-4">

            <!-- FOTO PROFIL -->
            <div class="col-md-4 text-center">
                <img src="<?= base_url('uploads/users/' . (!empty($user['photo']) ? $user['photo'] : 'default.png')) ?>"
                    class="rounded-circle border border-2 mb-3 object-fit-cover" width="150" height="150"
                    alt="Foto Profil">

                <p class="text-muted small mb-2">
                    JPG / PNG • Max 2MB
                </p>

                <?php if (!empty($user['photo']) && $user['photo'] !== 'default.png'): ?>
                    <form method="post" action="<?= base_url('users/deletePhoto/' . $user['id']) ?>">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-sm btn-outline-danger"
                            onclick="return confirm('Apakah Anda yakin ingin menghapus foto ini?')">
                            <i class="bi bi-trash"></i> Hapus Foto
                        </button>
                    </form>
                <?php endif; ?>
            </div>

            <!-- FORM -->
            <div class="col-md-8">
                <form method="post" action="<?= base_url('users/update/' . $user['id']) ?>"
                    enctype="multipart/form-data">
                    <?= csrf_field() ?>

                    <div class="row g-3">

                        <div class="col-md-6">
                            <label class="form-label">Nama</label>
                            <input type="text" name="name" value="<?= old('name', $user['name']) ?>"
                                class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" value="<?= old('email', $user['email']) ?>"
                                class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Password Baru <span class="text-muted">(Opsional)</span></label>
                            <div class="input-group">
                                <input type="password" name="password" id="password" class="form-control">
                                <span class="input-group-text bg-white">
                                    <a href="javascript:void(0);"
                                        onclick="togglePassword('password', 'toggle-password-icon')">
                                        <img id="toggle-password-icon" src="<?= base_url('uploads/icon/hide.png') ?>"
                                            width="18" alt="Toggle Password">
                                    </a>
                                </span>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Role</label>
                            <select name="role" id="role" class="form-select" required>
                                <option value="">-- Pilih Role --</option>
                                <option value="pusat" <?= old('role', $user['role']) == 'pusat' ? 'selected' : '' ?>>
                                    Pusat
                                </option>
                                <option value="cabang" <?= old('role', $user['role']) == 'cabang' ? 'selected' : '' ?>>
                                    Cabang
                                </option>
                            </select>
                        </div>

                        <div class="col-md-6" id="branch-container">
                            <label class="form-label">Cabang</label>
                            <select name="branch_id" class="form-select">
                                <option value="">-- Pilih Cabang --</option>
                                <?php foreach ($branches as $b): ?>
                                    <option value="<?= $b['id'] ?>" <?= old('branch_id', $user['branch_id']) == $b['id'] ? 'selected' : '' ?>>
                                        <?= esc($b['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Foto Profil</label>
                            <input type="file" name="photo" class="form-control" accept="image/jpeg,image/png">
                        </div>

                    </div>

<div class="d-flex justify-content-end align-items-center gap-2 mt-4 pt-3 border-top">
    <a href="<?= base_url('users') ?>" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>

    <button type="submit" class="btn btn-primary px-4">
        <i class="bi bi-save"></i> Update
    </button>
</div>

                </form>
            </div>
        </div>

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