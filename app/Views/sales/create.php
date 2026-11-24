<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container mt-4">
    <h4>🧾 Penjualan</h4>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <form action="<?= site_url('sales/store') ?>" method="post" id="salesForm">
        <?= csrf_field() ?>

        <table class="table table-bordered" id="itemsTable">
            <thead>
                <tr>
                    <th>Produk</th>
                    <th width="120">Harga</th>
                    <th width="80">Stok</th>
                    <th width="80">Jumlah</th>
                    <th width="120">Subtotal</th>
                    <th width="40">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <select name="items[0][product_item_id]" class="form-control product-select" required>
                            <option value="">-- Pilih Produk --</option>
                            <?php foreach ($products as $p): 
                                $price = isset($p['sell_price']) ? $p['sell_price'] : ($p['price'] ?? 0);
                                $stock = $p['quantity'] ?? 0;
                                $productName = $p['product_name'] ?? $p['name'];
                                $categoryName = $p['category_name'] ?? '';
                            ?>
                                <option value="<?= $p['product_item_id'] ?? $p['id'] ?>" 
                                    data-price="<?= $price ?>" data-stock="<?= $stock ?>">
                                    <?= esc($productName) ?> (<?= esc($categoryName) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td><input type="number" name="items[0][price]" class="form-control price" readonly></td>
                    <td><input type="number" class="form-control stock" readonly></td>
                    <td><input type="number" name="items[0][quantity]" class="form-control qty" min="1" required></td>
                    <td><input type="number" class="form-control subtotal" readonly></td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm remove-row">✕</button>
                    </td>
                </tr>
            </tbody>
        </table>

        <button type="button" class="btn btn-secondary btn-sm mb-3" id="addRow">+ Tambah Produk</button>

        <hr>

        <div class="text-end">
            <h5>Total: Rp <span id="grandTotal">0</span></h5>
        </div>

        <button type="submit" class="btn btn-primary mt-3">Simpan Penjualan</button>
    </form>
</div>

<!-- jQuery & Select2 -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function () {

    // Inisialisasi Select2
    function initSelect2(element) {
        element.select2({
            placeholder: "✨ Pilih Produk",
            width: '100%'
        });
    }

    initSelect2($('.product-select'));

    // Hitung subtotal & grand total
    function recalc() {
        let total = 0;
        $('.subtotal').each(function() {
            total += Number($(this).val() || 0);
        });
        $('#grandTotal').text(total.toLocaleString('id-ID'));
    }

    // Update semua dropdown untuk mencegah produk duplikat
    function updateDropdowns() {
        const selected = [];
        $('.product-select').each(function() {
            const val = $(this).val();
            if (val) selected.push(val);
        });

        $('.product-select').each(function() {
            const currentVal = $(this).val();
            $(this).find('option').each(function() {
                if ($(this).val() !== currentVal && selected.includes($(this).val())) {
                    $(this).prop('disabled', true);
                } else {
                    $(this).prop('disabled', false);
                }
            });
        });

        // refresh Select2
        $('.product-select').select2({
            placeholder: "✨ Pilih Produk",
            width: '100%'
        });
    }

    // Saat pilih produk
    $(document).on('change', '.product-select', function() {
        const row = $(this).closest('tr');
        const option = $(this).find('option:selected');

        row.find('.price').val(option.data('price') || 0);
        row.find('.stock').val(option.data('stock') || 0);
        row.find('.qty').val('');
        row.find('.subtotal').val('');
        recalc();
        updateDropdowns();
    });

    // Input qty
    $(document).on('input', '.qty', function() {
        const row = $(this).closest('tr');
        const qty = Number($(this).val());
        const price = Number(row.find('.price').val());
        const stock = Number(row.find('.stock').val());

        if (qty > stock) $(this).val(stock);
        row.find('.subtotal').val($(this).val() * price);
        recalc();
    });

    // Tambah row baru
    $('#addRow').click(function () {
        const tbody = $('#itemsTable tbody');
        const index = tbody.children().length;

        const newRow = $('<tr></tr>');

        // Buat HTML baru untuk row
        let selectHtml = '<select name="items['+index+'][product_item_id]" class="form-control product-select" required>';
        selectHtml += '<option value="">-- Pilih Produk --</option>';
        <?php foreach ($products as $p): 
            $price = isset($p['sell_price']) ? $p['sell_price'] : ($p['price'] ?? 0);
            $stock = $p['quantity'] ?? 0;
            $productName = $p['product_name'] ?? $p['name'];
            $categoryName = $p['category_name'] ?? '';
        ?>
        selectHtml += '<option value="<?= $p['product_item_id'] ?? $p['id'] ?>" data-price="<?= $price ?>" data-stock="<?= $stock ?>"><?= esc($productName) ?> (<?= esc($categoryName) ?>)</option>';
        <?php endforeach; ?>
        selectHtml += '</select>';

        const rowHtml = `
            <td>${selectHtml}</td>
            <td><input type="number" name="items[${index}][price]" class="form-control price" readonly></td>
            <td><input type="number" class="form-control stock" readonly></td>
            <td><input type="number" name="items[${index}][quantity]" class="form-control qty" min="1" required></td>
            <td><input type="number" class="form-control subtotal" readonly></td>
            <td><button type="button" class="btn btn-danger btn-sm remove-row">✕</button></td>
        `;
        newRow.html(rowHtml);

        tbody.append(newRow);

        initSelect2(newRow.find('.product-select'));
        updateDropdowns();
    });

    // Hapus row
    $(document).on('click', '.remove-row', function () {
        const tbody = $('#itemsTable tbody');
        const rows = tbody.find('tr');

        if (rows.length > 1) {
            $(this).closest('tr').remove();
        } else {
            const row = rows.first();
            row.find('.product-select').val(null).trigger('change');
            row.find('input').val('');
        }

        recalc();
        updateDropdowns();
    });
});
</script>

<?= $this->endSection() ?>
