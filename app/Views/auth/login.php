<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Favicon SELKOM -->
    <link rel="icon" href="<?= base_url('uploads/logo/selkom.jpg') ?>" type="image/jpeg">
    <link rel="shortcut icon" href="<?= base_url('uploads/logo/selkom.jpg') ?>" type="image/jpeg">

    <style>
        body {
            background-color: #f0f2f5;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .card-login {
            width: 22rem;
            padding: 2rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border-radius: 12px;
        }

        .password-wrapper {
            position: relative;
        }

        .password-toggle {
            position: absolute;
            top: 50%;
            right: 10px;
            transform: translateY(-50%);
            cursor: pointer;
            width: 24px;
            height: 24px;
            opacity: 0.7;
        }

        .password-toggle:hover {
            opacity: 1;
        }

        .form-control::placeholder {
            color: #aaa;
        }

        h4 {
            font-weight: 600;
            margin-bottom: 1.5rem;
        }
    </style>
</head>

<body>
    <div class="card card-login text-center">
        <h4>Login Selkom</h4>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>

        <form method="post" action="<?= base_url('login') ?>">
            <?= csrf_field() ?>

            <div class="mb-3 text-start">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" id="email" class="form-control" placeholder="Masukkan email" required
                    autofocus>
            </div>

            <div class="mb-3 text-start">
                <label for="password" class="form-label">Password</label>
                <div class="password-wrapper">
                    <input type="password" name="password" id="password" class="form-control"
                        placeholder="Masukkan password" required>
                    <img src="<?= base_url('uploads/icon/hide.png') ?>" alt="Show" id="togglePassword"
                        class="password-toggle">
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100 mt-3">Login</button>
        </form>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');

        togglePassword.addEventListener('click', function () {
            const isPassword = passwordInput.getAttribute('type') === 'password';
            passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
            this.src = isPassword
                ? "<?= base_url('uploads/icon/view.png') ?>"
                : "<?= base_url('uploads/icon/hide.png') ?>";
        });
    </script>
</body>

</html>