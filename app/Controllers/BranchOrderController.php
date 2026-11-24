<?php

namespace App\Controllers;

use App\Models\BranchOrderModel;
use App\Models\BranchOrderDetailModel;
use App\Models\StockModel;
use App\Models\StockCardModel;
use App\Models\StockInitialModel;

class BranchOrderController extends BaseController
{
    protected $orderModel;
    protected $detailModel;
    protected $stockModel;
    protected $db;
    protected $stockInitialModel;

    public function __construct()
    {
        $this->orderModel        = new BranchOrderModel();
        $this->detailModel       = new BranchOrderDetailModel();
        $this->stockModel        = new StockModel();
        $this->stockInitialModel = new StockInitialModel();
        $this->db                = \Config\Database::connect();
    }

    /* =====================================================
     * INDEX
     * ===================================================== */
    public function index()
    {
        $role     = session('role');
        $branchId = session('branch_id');

        $selectedWeek   = $this->request->getGet('week');
        $selectedStatus = $this->request->getGet('status');
        $selectedSort   = $this->request->getGet('sort') ?? 'desc';

        if (!$selectedWeek) {
            $selectedWeek = date('Y-m-d');
        }

        $monday = date('Y-m-d', strtotime('monday this week', strtotime($selectedWeek)));
        $sunday = date('Y-m-d', strtotime('sunday this week', strtotime($selectedWeek)));

        if ($role === 'pusat') {
            $builder = $this->db->table('branch_orders as bo')
                ->select('bo.*, b.name as branch_name')
                ->join('branches b', 'b.id = bo.branch_id', 'left')
                ->where('DATE(bo.requested_at) >=', $monday)
                ->where('DATE(bo.requested_at) <=', $sunday);

            if ($selectedStatus) {
                $builder->where('bo.status', $selectedStatus);
            }

            $orders = $builder
                ->orderBy('bo.requested_at', strtoupper($selectedSort))
                ->get()->getResultArray();

        } else {
            $builder = $this->orderModel
                ->where('branch_id', $branchId)
                ->where('DATE(requested_at) >=', $monday)
                ->where('DATE(requested_at) <=', $sunday);

            if ($selectedStatus) {
                $builder->where('status', $selectedStatus);
            }

            $orders = $builder
                ->orderBy('requested_at', strtoupper($selectedSort))
                ->findAll();
        }

        $view = $role === 'pusat' ? 'branch-order/admin_index' : 'branch-order/index';

        return view($view, [
            'orders'         => $orders,
            'selectedWeek'   => $selectedWeek,
            'weekStart'      => $monday,
            'weekEnd'        => $sunday,
            'selectedStatus' => $selectedStatus,
            'selectedSort'   => $selectedSort,
        ]);
    }

    /* =====================================================
     * CREATE (CABANG)
     * ===================================================== */
    public function create()
    {
        if (session('role') !== 'cabang') {
            return redirect()->to('/branch-order')->with('error', 'Akses ditolak');
        }

        $products = $this->stockModel
            ->select([
                'product_items.id   AS product_item_id',
                'product_items.name AS product_name',
                'categories.name    AS category_name',
                'SUM(stocks.quantity) AS available_quantity',
            ])
            ->join('product_items', 'product_items.id = stocks.product_item_id')
            ->join('categories', 'categories.id = product_items.category_id', 'left')
            ->groupBy(['product_items.id', 'product_items.name', 'categories.name'])
            ->having('available_quantity >', 0)
            ->orderBy('product_items.name', 'ASC')
            ->findAll();

        if (empty($products)) {
            return redirect()->to('/branch-order')->with('error', 'Stok pusat kosong, tidak ada produk yang bisa diminta.');
        }

        return view('branch-order/create', ['products' => $products]);
    }

    /* =====================================================
     * STORE (CABANG)
     * ===================================================== */
    public function store()
    {
        if (session('role') !== 'cabang') {
            return redirect()->back()->with('error', 'Akses ditolak');
        }

        $branchId     = session('branch_id');
        $userId       = session('user_id');
        $productItems = (array) $this->request->getPost('product_item_id');
        $quantities   = (array) $this->request->getPost('quantity');

        if (empty($productItems)) {
            return redirect()->back()->with('error', 'Pilih minimal satu produk');
        }

        $this->db->transBegin();

        try {
            $orderId = $this->orderModel->insert([
                'request_code' => 'REQ-' . date('YmdHis'),
                'branch_id'    => $branchId,
                'status'       => 'pending',
                'requested_at' => date('Y-m-d H:i:s'),
                'created_by'   => $userId,
            ]);

            foreach ($productItems as $index => $productItemId) {
                $qty = (int) ($quantities[$index] ?? 0);
                if ($productItemId <= 0 || $qty <= 0) continue;

                $available = (int) ($this->stockModel
                    ->where('product_item_id', $productItemId)
                    ->selectSum('quantity')
                    ->first()['quantity'] ?? 0);

                if ($qty > $available) {
                    throw new \Exception('Jumlah melebihi stok pusat untuk salah satu produk');
                }

                $this->detailModel->insert([
                    'branch_order_id' => $orderId,
                    'product_item_id' => $productItemId,
                    'quantity'        => $qty,
                ]);
            }

            $this->db->transCommit();

            return redirect()->to('/branch-order')
                ->with('success', 'Permintaan stok berhasil dikirim ke pusat. Menunggu persetujuan.');

        } catch (\Throwable $e) {
            $this->db->transRollback();
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    /* =====================================================
     * DETAIL
     * ===================================================== */
    public function detail($id)
    {
        $role     = session('role');
        $branchId = session('branch_id');

        $order = $this->orderModel->getById($id);
        if (!$order) {
            return redirect()->to('/branch-order')->with('error', 'Data tidak ditemukan');
        }

        if ($role === 'cabang' && $order['branch_id'] != $branchId) {
            return redirect()->to('/branch-order')->with('error', 'Akses ditolak');
        }

        $details = $this->db->table('branch_order_details as bod')
            ->select('bod.id as detail_id, pi.name as product_name, c.name as category_name,
                      bod.quantity, si.quantity as branch_stock, s.quantity as central_stock')
            ->join('product_items pi', 'pi.id = bod.product_item_id', 'left')
            ->join('categories c', 'c.id = pi.category_id', 'left')
            ->join('stock_initial si', "si.branch_id = {$order['branch_id']} AND si.product_item_id = bod.product_item_id", 'left')
            ->join('stocks s', 's.product_item_id = bod.product_item_id', 'left')
            ->where('bod.branch_order_id', $id)
            ->get()->getResultArray();

        // Produk pusat untuk dropdown tambah (hanya pusat, hanya status pending)
        $allProducts = [];
        if ($role === 'pusat' && $order['status'] === 'pending') {
            $allProducts = $this->stockModel
                ->select([
                    'product_items.id as product_item_id',
                    'product_items.name as product_name',
                    'categories.name as category_name',
                    'SUM(stocks.quantity) as central_stock',
                    'si.quantity as branch_stock'
                ])
                ->join('product_items', 'product_items.id = stocks.product_item_id')
                ->join('categories', 'categories.id = product_items.category_id', 'left')
                ->join('stock_initial si',
                    'si.product_item_id = product_items.id AND si.branch_id = ' . (int) $order['branch_id'],
                    'left'
                )
                ->groupBy('product_items.id, product_items.name, categories.name, si.quantity')
                ->having('central_stock >', 0)
                ->orderBy('product_items.name', 'ASC')
                ->findAll();
        }

        return view('branch-order/detail', [
            'order'       => $order,
            'details'     => $details,
            'allProducts' => $allProducts,
            'role'        => $role,
        ]);
    }

    /* =====================================================
     * APPROVE (PUSAT)
     * Hanya kurangi stok PUSAT, belum tambah stok cabang.
     * Cabang nantinya menekan tombol "Sudah Diterima" untuk menambah stok cabang.
     * Pusat bisa:
     * - Mengubah jumlah item yang diminta
     * - Menghapus/menolak item yang diminta cabang (jika tidak mau dikirim)
     * - Menambahkan item baru yang diinginkan pusat
     * - Memberikan catatan/komentar (opsional)
     * ===================================================== */
    public function approve($id)
    {
        if (session('role') !== 'pusat') {
            return redirect()->back()->with('error', 'Akses ditolak');
        }

        $this->db->transBegin();

        try {
            $order = $this->orderModel->find($id);
            if (!$order || $order['status'] !== 'pending') {
                throw new \Exception('Permintaan tidak valid atau sudah diproses');
            }

            $oldQuantities = $this->request->getPost('quantity') ?? [];
            $newProducts   = $this->request->getPost('new_quantity') ?? [];
            $note          = $this->request->getPost('note') ?? null;

            $approvedItemCount = 0;

            // ---- 1. PROSES DETAIL LAMA ----
            $existingDetails = $this->detailModel->where('branch_order_id', $id)->findAll();
            foreach ($existingDetails as $detail) {
                $detailId      = $detail['id'];
                $productItemId = $detail['product_item_id'];

                // Jika pusat menghapus baris dari tabel atau mengisi qty <= 0, berarti produk ini tidak disetujui / ditolak
                if (!isset($oldQuantities[$detailId]) || (int) $oldQuantities[$detailId] <= 0) {
                    $this->detailModel->delete($detailId);
                    continue;
                }

                $qty = (int) $oldQuantities[$detailId];

                // Validasi ketersediaan stok pusat
                $available = (int) ($this->stockModel
                    ->where('product_item_id', $productItemId)
                    ->selectSum('quantity')->get()->getRow()->quantity ?? 0);

                if ($available < $qty) {
                    throw new \Exception("Stok pusat tidak cukup untuk produk permintaan ID {$productItemId}");
                }

                // Update qty detail jika ada perubahan
                if ((int)$detail['quantity'] !== $qty) {
                    $this->detailModel->update($detailId, ['quantity' => $qty]);
                }

                // Kurangi stok PUSAT
                $this->stockModel->reduceStock($productItemId, $qty);
                $approvedItemCount++;
            }

            // ---- 2. PROSES PRODUK BARU YANG DITAMBAHKAN PUSAT ----
            foreach ($newProducts as $productItemId => $qty) {
                $qty = (int) $qty;
                if ($qty <= 0) continue;

                $available = (int) ($this->stockModel
                    ->where('product_item_id', $productItemId)
                    ->selectSum('quantity')->get()->getRow()->quantity ?? 0);

                if ($available < $qty) {
                    throw new \Exception("Stok pusat tidak cukup untuk produk tambahan (ID: {$productItemId})");
                }

                // Masukkan detail baru
                $this->detailModel->insert([
                    'branch_order_id' => $id,
                    'product_item_id' => $productItemId,
                    'quantity'        => $qty,
                ]);

                // Kurangi stok PUSAT
                $this->stockModel->reduceStock($productItemId, $qty);
                $approvedItemCount++;
            }

            if ($approvedItemCount === 0) {
                throw new \Exception('Tidak ada produk yang disetujui. Minimal harus ada 1 produk atau gunakan tombol Tolak.');
            }

            // ---- 3. UPDATE STATUS → approved ----
            $this->orderModel->update($id, [
                'status'      => 'approved',
                'note'        => !empty($note) ? trim($note) : null,
                'approved_at' => date('Y-m-d H:i:s'),
                'approved_by' => session('user_id'),
            ]);

            $this->db->transCommit();

            return redirect()->to('/branch-order')
                ->with('success', 'Permintaan disetujui dan stok pusat telah dikurangi. Menunggu konfirmasi penerimaan barang dari cabang.');

        } catch (\Throwable $e) {
            $this->db->transRollback();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /* =====================================================
     * REJECT (PUSAT)
     * ===================================================== */
    public function reject($id)
    {
        if (session('role') !== 'pusat') {
            return redirect()->back()->with('error', 'Akses ditolak');
        }

        $order = $this->orderModel->find($id);
        if (!$order || $order['status'] !== 'pending') {
            return redirect()->to('/branch-order')->with('error', 'Permintaan tidak valid atau sudah diproses');
        }

        $note = $this->request->getPost('note');

        $this->orderModel->update($id, [
            'status'      => 'rejected',
            'note'        => !empty($note) ? trim($note) : null,
            'approved_at' => date('Y-m-d H:i:s'),
            'approved_by' => session('user_id'),
        ]);

        return redirect()->to('/branch-order')
            ->with('success', 'Permintaan berhasil ditolak.');
    }

    /* =====================================================
     * RECEIVE (CABANG) — Konfirmasi barang sudah diterima
     * Saat ini stok CABANG bertambah
     * ===================================================== */
    public function receive($id)
    {
        if (session('role') !== 'cabang') {
            return redirect()->back()->with('error', 'Akses ditolak');
        }

        $branchId = session('branch_id');
        $userId   = session('user_id');

        $order = $this->orderModel->find($id);

        if (!$order || $order['status'] !== 'approved') {
            return redirect()->to('/branch-order')
                ->with('error', 'Permintaan tidak valid atau belum disetujui oleh pusat.');
        }

        if ($order['branch_id'] != $branchId) {
            return redirect()->to('/branch-order')->with('error', 'Akses ditolak');
        }

        $this->db->transBegin();

        try {
            $stockCardModel = new StockCardModel();

            // Ambil semua detail order
            $details = $this->detailModel
                ->where('branch_order_id', $id)
                ->findAll();

            foreach ($details as $detail) {
                $productItemId = $detail['product_item_id'];
                $qty           = (int) $detail['quantity'];

                // ✅ Tambah stok CABANG
                $existing = $this->stockInitialModel
                    ->where('branch_id', $branchId)
                    ->where('product_item_id', $productItemId)
                    ->first();

                if ($existing) {
                    $this->stockInitialModel->update($existing['id'], [
                        'quantity' => $existing['quantity'] + $qty,
                    ]);
                } else {
                    $this->stockInitialModel->insert([
                        'branch_id'       => $branchId,
                        'product_item_id' => $productItemId,
                        'quantity'        => $qty,
                    ]);
                }

                // ✅ Catat ke stock_card sebagai IN
                $stockCardModel->insert([
                    'branch_id'       => $branchId,
                    'product_item_id' => $productItemId,
                    'quantity'        => $qty,
                    'type'            => 'in',
                    'date'            => date('Y-m-d'),
                    'description'     => 'Diterima dari pusat - ' . $order['request_code'],
                ]);
            }

            // Update status → received
            $this->orderModel->update($id, [
                'status'      => 'received',
                'received_at' => date('Y-m-d H:i:s'),
                'received_by' => $userId,
            ]);

            $this->db->transCommit();

            return redirect()->to('/branch-order')
                ->with('success', 'Barang berhasil dikonfirmasi diterima. Stok cabang sudah diperbarui.');

        } catch (\Throwable $e) {
            $this->db->transRollback();
            log_message('error', 'RECEIVE ORDER ERROR: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
