<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="card shadow-sm border-0">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="fw-bold mb-0">👥 Data Users</h4>
            <a href="/users/create" class="btn btn-primary btn-sm">➕ Tambah User</a>
        </div>

        <!-- Alert -->
        <?php if (session('success')): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?= session('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <?php if (session('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <?= session('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Search & Sorting -->
        <form class="input-group mb-3" method="get" action="/users">
            <input type="text" name="search" class="form-control" placeholder="🔍 Cari nama, email atau role..."
                value="<?= esc($search) ?>">
            <button class="btn btn-outline-secondary" type="submit">Cari</button>
            <?php if ($search): ?>
                <a href="/users" class="btn btn-outline-danger">Reset</a>
            <?php endif; ?>
        </form>

        <!-- Sorting Dropdown -->
        <form method="get" action="/users" class="d-flex mb-3">
            <select name="sort" class="form-select w-auto" onchange="this.form.submit()">
                <option value="">✨ Sort By</option>
                <option value="name_asc" <?= $sort == 'name_asc' ? 'selected' : '' ?>>📛 Nama — A to Z</option>
                <option value="name_desc" <?= $sort == 'name_desc' ? 'selected' : '' ?>>📛 Nama — Z to A</option>
                <option value="email_asc" <?= $sort == 'email_asc' ? 'selected' : '' ?>>📧 Email — A to Z</option>
                <option value="email_desc" <?= $sort == 'email_desc' ? 'selected' : '' ?>>📧 Email — Z to A</option>
                <option value="role_asc" <?= $sort == 'role_asc' ? 'selected' : '' ?>>🛠️ Role — A to Z</option>
                <option value="role_desc" <?= $sort == 'role_desc' ? 'selected' : '' ?>>🛠️ Role — Z to A</option>
            </select>
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Password</th>
                        <th>Role</th>
                        <th>Lokasi Cabang</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($users)): ?>
                        <?php $no = 1 + ($pager->getCurrentPage('users') - 1) * $pager->getPerPage('users'); ?>
                        <?php foreach ($users as $u): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td class="fw-semibold"><?= esc($u['name']) ?></td>
                                <td><?= esc($u['email']) ?></td>

                                <!-- Password dengan toggle hide/show -->
                                <td>
                                    <div class="d-flex align-items-center">
                                        <input type="password" value="<?= esc($u['password_display'] ?? '') ?>" 
                                               id="pwd-<?= $u['id'] ?>" class="form-control form-control-sm me-1" readonly>
                                        <img src="/uploads/icon/hide.png" class="toggle-pwd" 
                                             data-target="pwd-<?= $u['id'] ?>" style="width:24px; cursor:pointer;" 
                                             title="Show/Hide Password">
                                    </div>
                                </td>

                                <td>
                                    <span class="badge bg-dark"><?= esc(ucfirst($u['role'])) ?></span>
                                </td>

                                <td>
                                    <?= esc($u['branch_name'] ?? '-') ?>
                                </td>

                                <td class="text-center">
                                    <a href="/users/edit/<?= $u['id'] ?>" class="btn btn-sm btn-warning me-1" title="Edit">✏️</a>
                                    <a href="/users/delete/<?= $u['id'] ?>" class="btn btn-sm btn-danger"
                                       onclick="return confirm('Hapus user ini?')" title="Delete">🗑️</a>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-3">❗ Tidak ada data ditemukan</td>
                        </tr>
                    <?php endif ?>
                </tbody>
            </table>
        </div>

        <?= $pager->links('users', 'pagination') ?>
    </div>
</div>

<script>
document.querySelectorAll('.toggle-pwd').forEach(el => {
    el.addEventListener('click', function() {
        const input = document.getElementById(this.dataset.target);
        if (!input) return;
        if (input.type === 'password') {
            input.type = 'text';
            this.src = '/uploads/icon/view.png'; // icon view
        } else {
            input.type = 'password';
            this.src = '/uploads/icon/hide.png'; // icon hide
        }
    });
});
</script>

<?= $this->endSection() ?>
