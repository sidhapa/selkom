<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container mt-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4><i class="bi bi-cart-plus me-2"></i>Buat Permintaan Stok ke Pusat</h4>
        <a href="<?= base_url('branch-order') ?>" class="btn btn-secondary btn-sm">
            <i class="bi bi-arrow-left-circle"></i> Kembali
        </a>
    </div>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <div class="card shadow-sm rounded-3">
        <div class="card-body">

            <form action="<?= base_url('branch-order/store') ?>" method="post">
                <?= csrf_field() ?>

                <div class="table-responsive">
                    <table class="table table-bordered align-middle" id="productTable">
                        <thead class="table-light text-center">
                            <tr>
                                <th width="55%">Produk</th>
                                <th width="25%">Jumlah</th>
                                <th width="10%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <select name="product_item_id[]" class="form-select product-select form-select-lg" required>
                                        <option value="" disabled selected>-- Pilih Produk --</option>
                                        <?php foreach ($products as $p): ?>
                                            <option value="<?= $p['product_item_id'] ?>">
                                                <?= esc($p['product_name']) ?> (<?= esc($p['category_name']) ?> | Stok pusat: <?= $p['available_quantity'] ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td>
                                    <input type="number" name="quantity[]" class="form-control form-control-lg" min="1" required>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)">
                                        <i class="bi bi-trash">-</i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Tombol Tambah -->
                <div class="mb-3">
                    <button type="button" class="btn btn-success btn-sm" onclick="addRow()">
                        <i class="bi bi-plus-circle me-1"></i> Tambah Produk
                    </button>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-primary btn-lg" onclick="return confirm('Kirim permintaan stok ke pusat?')">
                        <i class="bi bi-check-circle me-1"></i> Kirim Permintaan
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>

<!-- jQuery & Select2 -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function () {
        initSelect2();

        // Set event onchange untuk semua select
        $(document).on('change', '.product-select', function() {
            updateDropdowns();
        });
    });

    // Inisialisasi Select2
    function initSelect2() {
        $('.product-select').select2({
            placeholder: "✨ Pilih Produk",
            width: '100%'
        });
    }

    // Tambah row baru
    function addRow() {
        const rowHtml = `
<tr>
    <td>
        <select name="product_item_id[]" class="form-select product-select form-select-lg" required>
            <option value="" disabled selected>-- Pilih Produk --</option>
            <?php foreach ($products as $p): ?>
                <option value="<?= $p['product_item_id'] ?>">
                    <?= esc($p['product_name']) ?> (<?= esc($p['category_name']) ?> | Stok pusat: <?= $p['available_quantity'] ?>)
                </option>
            <?php endforeach; ?>
        </select>
    </td>
    <td>
        <input type="number" name="quantity[]" class="form-control form-control-lg" min="1" required>
    </td>
    <td class="text-center">
        <button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)">
            <i class="bi bi-trash"></i>
        </button>
    </td>
</tr>`;
        const tbody = document.querySelector('#productTable tbody');
        tbody.insertAdjacentHTML('beforeend', rowHtml);

        // Inisialisasi Select2 baru
        initSelect2();
        updateDropdowns();
    }

    // Hapus row
    function removeRow(btn) {
        const tbody = document.querySelector('#productTable tbody');
        const rows = tbody.querySelectorAll('tr');

        if (rows.length > 1) {
            btn.closest('tr').remove();
        } else {
            // Jika hanya 1 row, kosongkan data
            const row = rows[0];
            $(row).find('.product-select').val(null).trigger('change');
            row.querySelector('input[name="quantity[]"]').value = '';
        }

        updateDropdowns();
    }

    // Fungsi untuk memfilter opsi yang sudah dipilih
    function updateDropdowns() {
        const selected = [];
        $('.product-select').each(function() {
            const val = $(this).val();
            if (val) selected.push(val);
        });

        $('.product-select').each(function() {
            const current = $(this).val();
            $(this).find('option').each(function() {
                if ($(this).val() !== current && selected.includes($(this).val())) {
                    $(this).prop('disabled', true);
                } else {
                    $(this).prop('disabled', false);
                }
            });
        });

        // Refresh Select2
        $('.product-select').select2();
    }
</script>

<?= $this->endSection() ?>
