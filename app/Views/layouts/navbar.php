<header class="navbar navbar-expand-lg navbar-custom sticky-top py-2">
    <div class="container-fluid container-xl">

        <!-- Logo -->
        <a class="navbar-brand d-flex align-items-center gap-2" href="/dashboard">
            <img src="<?= base_url('uploads/logo/selkom.jpg') ?>" alt="Logo"
                style="width:36px; height:36px; border-radius:50%; object-fit:cover; border:2px solid rgba(255,255,255,0.8); box-shadow: 0 2px 5px rgba(0,0,0,0.2);">
            <div class="d-flex flex-column leading-none">
                <span class="text-white fw-bold fs-5 tracking-wide">SELKOM</span>
                <span class="text-white-50 small" style="font-size: 0.65rem; margin-top: -3px;"><?= strtoupper(session('role') ?? 'GUEST') ?></span>
            </div>
        </a>

        <!-- Burger Toggle (Mobile) -->
        <button class="navbar-toggler border-0 p-2 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu"
            aria-controls="navbar-menu" aria-expanded="false" aria-label="Toggle navigation">
            <i class="bi bi-list text-white fs-2"></i>
        </button>

        <div class="collapse navbar-collapse" id="navbar-menu">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 mt-2 mt-lg-0">

                <!-- Dashboard -->
                <li class="nav-item">
                    <a class="nav-link nav-pill" href="/dashboard">
                        <i class="bi bi-grid-1x2 me-1"></i> Dashboard
                    </a>
                </li>

                <?php if (session('role') === 'pusat'): ?>
                    <!-- Data Master -->
                    <li class="nav-item dropdown">
                        <a class="nav-link nav-pill dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <i class="bi bi-database me-1"></i> Master
                        </a>
                        <ul class="dropdown-menu shadow-sm">
                            <li><a class="dropdown-item py-2" href="/users"><i class="bi bi-people me-2 text-primary"></i> Data User</a></li>
                            <li><a class="dropdown-item py-2" href="/branches"><i class="bi bi-buildings me-2 text-primary"></i> Data Cabang</a></li>
                        </ul>
                    </li>
                <?php endif; ?>

                <!-- Resources (Both Roles) -->
                <li class="nav-item dropdown">
                    <a class="nav-link nav-pill dropdown-toggle" href="#" data-bs-toggle="dropdown">
                        <i class="bi bi-box-seam me-1"></i> Produk
                    </a>
                    <ul class="dropdown-menu shadow-sm">
                        <li><a class="dropdown-item py-2" href="/categories"><i class="bi bi-tags me-2 text-info"></i> Kategori Produk</a></li>
                        <?php if (session('role') === 'pusat'): ?>
                            <li><a class="dropdown-item py-2" href="/suppliers"><i class="bi bi-truck me-2 text-info"></i> Supplier</a></li>
                        <?php endif; ?>
                        <li><a class="dropdown-item py-2" href="/product-items"><i class="bi bi-boxes me-2 text-info"></i> Daftar Produk</a></li>
                    </ul>
                </li>

                <?php if (session('role') === 'pusat'): ?>
                    <li class="nav-item">
                        <a class="nav-link nav-pill" href="/products">
                            <i class="bi bi-cart-plus me-1"></i> Pembelian
                        </a>
                    </li>
                <?php endif; ?>

                <!-- Stok -->
                <li class="nav-item">
                    <a class="nav-link nav-pill" href="/stock">
                        <i class="bi bi-stack me-1"></i> Stok
                    </a>
                </li>

                <!-- Penjualan -->
                <li class="nav-item dropdown">
                    <a class="nav-link nav-pill dropdown-toggle" href="#" data-bs-toggle="dropdown">
                        <i class="bi bi-receipt me-1"></i> Penjualan
                    </a>
                    <ul class="dropdown-menu shadow-sm">
                        <li><a class="dropdown-item py-2" href="/sales"><i class="bi bi-cash-coin me-2 text-success"></i> Transaksi Penjualan</a></li>
                        <li><hr class="dropdown-divider my-1"></li>
                        <li><a class="dropdown-item py-2" href="/sales/report/daily"><i class="bi bi-calendar-event me-2 text-secondary"></i> Laporan Harian</a></li>
                        <li><a class="dropdown-item py-2" href="/sales/report/monthly"><i class="bi bi-calendar-month me-2 text-secondary"></i> Laporan Bulanan</a></li>
                        <li><a class="dropdown-item py-2" href="/sales/report/yearly"><i class="bi bi-calendar3 me-2 text-secondary"></i> Laporan Tahunan</a></li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a class="nav-link nav-pill" href="/stockinitial">
                        <i class="bi bi-journal-text me-1"></i> Kartu Stok
                    </a>
                </li>

                <!-- Permintaan Stok -->
                <li class="nav-item">
                    <a class="nav-link nav-pill" href="/branch-order">
                        <i class="bi bi-arrow-left-right me-1"></i> 
                        <?= session('role') === 'pusat' ? 'Review Permintaan Cabang' : 'Permintaan Stok' ?>
                    </a>
                </li>

            </ul>

            <!-- Profile Menu -->
            <?php
            $userModel = new \App\Models\UserModel();
            $userId = session()->get('user_id');
            $user = $userModel->find($userId);
            $photo = $user['photo'] ?? 'default.png';
            if (!file_exists(FCPATH . 'uploads/users/' . $photo))
                $photo = 'default.png';
            $photoUrl = base_url('uploads/users/' . $photo);
            ?>

            <ul class="navbar-nav ms-auto border-top border-lg-0 pt-2 pt-lg-0">
                <li class="nav-item dropdown">
                    <a class="nav-link nav-pill d-flex align-items-center dropdown-toggle px-2" href="#" role="button"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="<?= $photoUrl ?>" alt="Profile" class="profile-photo me-2">
                        <div class="d-flex flex-column text-start">
                            <span class="text-white fw-semibold small"><?= esc($user['name'] ?? 'User') ?></span>
                            <span class="text-white-50" style="font-size:0.7rem; margin-top:-2px;"><?= esc($user['email'] ?? '') ?></span>
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                        <li>
                            <a class="dropdown-item py-2" href="/profile">
                                <i class="bi bi-person-gear me-2 text-primary"></i> Edit Profil
                            </a>
                        </li>
                        <li><hr class="dropdown-divider my-1"></li>
                        <li>
                            <a class="dropdown-item py-2 text-danger" href="/logout" onclick="return confirm('Keluar dari aplikasi?')">
                                <i class="bi bi-box-arrow-right me-2"></i> Logout
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</header>

<style>
    .navbar-custom {
        background: linear-gradient(135deg, #1d60af 0%, #206bc4 100%);
        box-shadow: 0 4px 12px rgba(32, 107, 196, 0.15);
        z-index: 1030;
    }

    .navbar-custom .nav-link.nav-pill {
        color: rgba(255, 255, 255, 0.9);
        border-radius: 8px;
        padding: 7px 12px;
        font-size: 0.9rem;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .navbar-custom .nav-link.nav-pill:hover,
    .navbar-custom .nav-link.nav-pill.active {
        background: rgba(255, 255, 255, 0.18);
        color: #ffffff;
        transform: translateY(-1px);
    }

    .dropdown-menu {
        border: 1px solid rgba(0, 0, 0, 0.08);
        border-radius: 10px;
        padding: 6px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
    }

    .dropdown-item {
        border-radius: 6px;
        font-size: 0.88rem;
        font-weight: 500;
        transition: background 0.15s ease;
    }

    .dropdown-item:hover {
        background-color: #f1f5f9;
    }

    .profile-photo {
        width: 34px;
        height: 34px;
        object-fit: cover;
        border-radius: 50%;
        border: 2px solid rgba(255, 255, 255, 0.85);
    }

    /* Penyesuaian saat dibuka di Mobile / Tablet */
    @media (max-width: 991.98px) {
        .navbar-collapse {
            background: #17549c;
            padding: 14px 16px;
            border-radius: 12px;
            margin-top: 10px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.25);
        }

        .navbar-custom .nav-link.nav-pill {
            margin: 3px 0;
            padding: 9px 14px;
            width: 100%;
        }

        .dropdown-menu {
            background-color: rgba(255, 255, 255, 0.97);
            margin: 4px 0 10px 0;
        }
    }
</style>

