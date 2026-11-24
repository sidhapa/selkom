<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container mt-4">
    <h3 class="mb-4">Profil Saya</h3>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <div class="card shadow-sm p-4">
        <div class="row">
            <div class="col-md-4 text-center mb-3">
               <img src="<?= base_url('uploads/users/' . (!empty($user['photo']) ? $user['photo'] : 'default.png')) ?>"
                alt="Foto Profil" width="150" height="150" class="rounded-circle border border-2 object-fit-cover"
                id="profileImage">
            
            <p class="mt-2 text-muted small">Format: JPG, PNG | Max: 2MB</p>
            
            <?php if (!empty($user['photo']) && $user['photo'] !== 'default.png'): ?>
                <form method="post" action="<?= base_url('profile/update') ?>">
                    <?= csrf_field() ?>
                    <input type="hidden" name="delete_photo" value="1">
                    <button type="submit" class="btn btn-sm btn-danger mt-2">Hapus Foto</button>
                </form>
            <?php endif; ?>
            </div>

            <div class="col-md-8">
                <form method="post" action="<?= base_url('profile/update') ?>" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label>Nama</label>
                        <input type="text" name="name" value="<?= esc($user['name']) ?>" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>Password Baru (opsional)</label>
                        <div class="input-group">
                            <input type="password" name="password" id="password" class="form-control">
                            <span class="input-group-text">
                                <a href="#" onclick="togglePassword('password'); return false;">
                                    <img id="toggle-password-icon" src="<?= base_url('uploads/icon/hide.png') ?>"
                                        width="20" alt="Toggle Password">
                                </a>
                            </span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label>Foto Profil</label>
                        <input type="file" name="photo" class="form-control" accept="image/jpeg,image/png"
                            onchange="validateSize(this)">
                    </div>

                    <div class="d-grid gap-2 d-md-block">
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        <a href="<?= base_url('dashboard') ?>" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function togglePassword(fieldId) {
        const field = document.getElementById(fieldId);
        const icon = document.getElementById('toggle-password-icon');

        if (field.type === "password") {
            field.type = "text";
            icon.src = "<?= base_url('uploads/icon/view.png') ?>";
        } else {
            field.type = "password";
            icon.src = "<?= base_url('uploads/icon/hide.png') ?>";
        }
    }

    function validateSize(input) {
        const file = input.files[0];
        if (file && file.size > 2 * 1024 * 1024) {
            alert('Ukuran file maksimal 2MB');
            input.value = '';
        }
    }
</script>

<?= $this->endSection() ?>