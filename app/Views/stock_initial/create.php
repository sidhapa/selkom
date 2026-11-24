<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container mt-4">

<div class="card shadow-sm rounded-3">
    
    <!-- HEADER CARD -->
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">📦 Tambah Stok Awal Cabang</h5>
        <a href="/stockinitial" class="btn btn-secondary btn-sm">← Kembali</a>
    </div>

    <div class="card-body">

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <div class="card shadow-sm rounded-3">
        <div class="card-body">

            <form action="/stockinitial/store" method="post" onsubmit="return validateForm()">
                <?= csrf_field() ?>

                <!-- CABANG -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">🏬 Pilih Cabang</label>
                    <select name="branch_id" id="branch_id" class="form-select" required>
                        <option value="">-- Pilih Cabang --</option>
                        <?php foreach ($branches as $b): ?>
                            <option value="<?= $b['id'] ?>"><?= esc($b['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- PRODUK -->
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
                                    <select name="product_item_id[]" class="form-select product-select form-select-lg"
                                        required>
                                        <option value="" disabled selected>-- Pilih Produk --</option>
                                        <?php foreach ($products as $p): ?>
                                            <option value="<?= $p['product_item_id'] ?>">
                                                [<?= esc($p['category_name']) ?>] <?= esc($p['name']) ?> (Stok: <?= $p['stock'] ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td>
                                    <input type="number" name="quantity[]" class="form-control form-control-lg" min="1"
                                        required>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)">
                                        ×
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Tombol Tambah -->
                <div class="mb-3">
                    <button type="button" class="btn btn-success btn-sm" onclick="addRow()">
                        + Tambah Produk
                    </button>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-primary btn-lg">
                        💾 Simpan Stok Awal
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
        updateDropdowns();

        // Event change semua select
        $(document).on('change', '.product-select', function () {
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
                    <?= esc($p['name']) ?> (Stok: <?= $p['stock'] ?>)
                </option>
            <?php endforeach; ?>
        </select>
    </td>
    <td>
        <input type="number" name="quantity[]" class="form-control form-control-lg" min="1" required>
    </td>
    <td class="text-center">
        <button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)">×</button>
    </td>
</tr>`;
        $('#productTable tbody').append(rowHtml);
        initSelect2();
        updateDropdowns();
    }

    // Hapus row
    function removeRow(btn) {
        const tbody = $('#productTable tbody');
        const rows = tbody.find('tr');

        if (rows.length > 1) {
            $(btn).closest('tr').remove();
        } else {
            // Kosongkan row pertama jika cuma 1
            const row = rows.eq(0);
            row.find('.product-select').val(null).trigger('change');
            row.find('input[name="quantity[]"]').val('');
        }

        updateDropdowns();
    }

    // Update dropdown agar tidak ada duplikat
    function updateDropdowns() {
        const selected = [];
        $('.product-select').each(function () {
            const val = $(this).val();
            if (val) selected.push(val);
        });

        $('.product-select').each(function () {
            const current = $(this).val();
            $(this).find('option').each(function () {
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

    // Validasi form sebelum submit
    function validateForm() {
        if (!$('#branch_id').val()) {
            alert('Pilih cabang terlebih dahulu');
            return false;
        }

        let valid = true;
        $('.qty-input, input[name="quantity[]"]').each(function () {
            const val = parseInt($(this).val());
            if (!val || val <= 0) {
                alert('Jumlah tidak boleh kosong atau nol');
                valid = false;
                return false;
            }
        });
        return valid;
    }
</script>

<?= $this->endSection() ?>