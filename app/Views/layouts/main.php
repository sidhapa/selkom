<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title><?= esc($title ?? 'O-Stock - Sistem Manajemen Stok & Penjualan') ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

    <!-- Favicon -->
    <link rel="icon" href="<?= base_url('uploads/logo/selkom.jpg') ?>" type="image/x-icon">

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Tabler, Bootstrap 5 & Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/tabler@1.0.0-beta20/dist/css/tabler.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --bs-body-font-family: 'Inter', system-ui, -apple-system, sans-serif;
            --primary-color: #206bc4;
            --primary-hover: #17549c;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background-color: #f4f6f9;
            color: #2c3e50;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .page-wrapper {
            flex: 1;
        }

        .main-container {
            padding: 24px 20px;
            max-width: 1400px;
            margin: 0 auto;
            width: 100%;
        }

        /* Hover dropdown hanya di layar desktop */
        @media (min-width: 992px) {
            .nav-item.dropdown:hover > .dropdown-menu {
                display: block;
                margin-top: 0;
                animation: fadeIn 0.15s ease-in-out;
            }
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(4px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Card Styling Modern */
        .card {
            border: 1px solid rgba(0, 0, 0, 0.06);
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.04);
            transition: box-shadow 0.2s ease;
        }

        /* Responsif Table & Scrollbar */
        .table-responsive {
            border-radius: 10px;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .table-responsive::-webkit-scrollbar {
            height: 6px;
            width: 6px;
        }

        .table-responsive::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }

        .table-responsive::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* Form Controls */
        .form-control, .form-select {
            border-radius: 8px;
            border-color: #dce1e7;
            font-size: 0.925rem;
            padding: 8px 12px;
        }

        .form-control:focus, .form-select:focus {
            border-color: #206bc4;
            box-shadow: 0 0 0 0.2rem rgba(32, 107, 196, 0.15);
        }

        .btn {
            border-radius: 8px;
            font-weight: 500;
            padding: 7px 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        /* Optimasi Tampilan Mobile */
        @media (max-width: 768px) {
            .main-container {
                padding: 14px 10px;
            }

            h2, .h2 { font-size: 1.4rem; }
            h3, .h3 { font-size: 1.25rem; }
            h4, .h4 { font-size: 1.15rem; }
            h5, .h5 { font-size: 1rem; }

            .btn {
                padding: 6px 12px;
                font-size: 0.875rem;
            }

            .table th, .table td {
                padding: 8px 10px;
                font-size: 0.85rem;
                white-space: nowrap;
            }

            .alert {
                padding: 10px 14px;
                font-size: 0.875rem;
            }
        }
    </style>
</head>

<body>
    <?= $this->include('layouts/navbar') ?>

    <main class="page-wrapper main-container">
        <?= $this->renderSection('content') ?>
    </main>

    <?= $this->include('layouts/footer') ?>
</body>

</html>
