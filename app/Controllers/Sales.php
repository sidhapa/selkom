<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\SaleModel;
use App\Models\SaleDetailModel;
use App\Models\BranchModel;
use App\Models\StockInitialModel;
use App\Models\StockCardModel;
use Dompdf\Dompdf;

class Sales extends BaseController
{
    protected $saleModel;
    protected $saleDetailModel;
    protected $stockInitialModel;
    protected $branchModel;
    protected $stockCardModel;
    protected $db;

    public function __construct()
    {
        $this->saleModel = new SaleModel();
        $this->saleDetailModel = new SaleDetailModel();
        $this->stockInitialModel = new StockInitialModel();
        $this->stockCardModel = new StockCardModel();
        $this->branchModel = new BranchModel(); // 🔥 WAJIB
        $this->db = \Config\Database::connect();
    }
    // =========================
// INDEX
// =========================
    public function index()
    {
        $request = service('request');
        $role = session('role');
        $branchId = session('branch_id');
        $isPusat = ($role === 'pusat');

        // =========================
        // INPUT FILTER
        // =========================
        $date = $request->getGet('date');
        $month = $request->getGet('month');
        $year = $request->getGet('year');
        $keyword = trim($request->getGet('keyword'));
        $branch = $request->getGet('branch_id');
        $sort = $request->getGet('sort') ?? 'newest';

        // FLAG FILTER
        $hasFilter = $date || $month || $year || $keyword || $branch;

        // =========================
        // BUILDER
        // =========================
        $builder = $this->saleModel
            ->select('sales.*, branches.name AS branch_name')
            ->join('branches', 'branches.id = sales.branch_id', 'left');

        // =========================
        // ROLE CABANG
        // =========================
        if (!$isPusat) {
            $builder->where('sales.branch_id', $branchId);
        }

        // =========================
        // FILTER WAKTU (SATU PINTU)
        // =========================
        if ($date) {
            $builder->where('DATE(sales.date)', $date);
        } elseif ($month) {
            $builder->where('DATE_FORMAT(sales.date,"%Y-%m")', $month);
        } elseif ($year) {
            $builder->where('YEAR(sales.date)', $year);
        } elseif (!$hasFilter) {
            // DEFAULT: hari ini
            $today = date('Y-m-d');
            $builder->where('DATE(sales.date)', $today);
            $date = $today;
        }

        // =========================
        // SEARCH GLOBAL (FIXED)
        // =========================
        if ($keyword) {
            $builder->groupStart()
                ->like('sales.invoice', $keyword)
                ->orLike('sales.total', $keyword)
                ->orLike('sales.date', $keyword)
                ->orLike('sales.id', $keyword);

            if ($isPusat) {
                $builder->orLike('branches.name', $keyword);
            }

            $builder->groupEnd();
        }

        // =========================
        // FILTER CABANG (PUSAT)
        // =========================
        if ($isPusat && $branch) {
            $builder->where('sales.branch_id', $branch);
        }

        // =========================
        // SORTING
        // =========================
        switch ($sort) {
            case 'oldest':
                $builder->orderBy('sales.date', 'ASC');
                break;
            case 'highest':
                $builder->orderBy('sales.total', 'DESC');
                break;
            case 'lowest':
                $builder->orderBy('sales.total', 'ASC');
                break;
            case 'invoice_az':
                $builder->orderBy('sales.invoice', 'ASC');
                break;
            case 'invoice_za':
                $builder->orderBy('sales.invoice', 'DESC');
                break;
            default:
                $builder->orderBy('sales.date', 'DESC');
        }

        // =========================
        // PAGINATION
        // =========================
        $sales = $builder->paginate(10, 'sales');

        return view('sales/index', [
            'sales' => $sales,
            'pager' => $this->saleModel->pager,
            'date' => $date,
            'month' => $month,
            'year' => $year,
            'keyword' => $keyword,
            'sort' => $sort,
            'branch' => $branch,
            'isPusat' => $isPusat,
            'branches' => $isPusat ? $this->branchModel->findAll() : [],
        ]);
    }



    // =========================
    // CREATE (STOK CABANG)
    // =========================
    public function create()
    {
        // ❌ PUSAT DILARANG JUAL
        if (session('role') === 'pusat') {
            return redirect()->to('/sales')
                ->with('error', 'Pusat tidak dapat melakukan transaksi penjualan');
        }

        $branchId = session('branch_id');

        // Ambil produk dari stok cabang + harga dari stok pusat
        $products = model('StockInitialModel')
            ->select('
            stock_initial.product_item_id,
            stock_initial.quantity,
            product_items.name AS product_name,
            categories.name AS category_name,
            stocks.sell_price AS price
        ')
            ->join('product_items', 'product_items.id = stock_initial.product_item_id')
            ->join('categories', 'categories.id = product_items.category_id', 'left')
            ->join('stocks', 'stocks.product_item_id = stock_initial.product_item_id', 'left')
            ->where('stock_initial.branch_id', $branchId)
            ->where('stock_initial.quantity >', 0)
            ->findAll();

        return view('sales/create', compact('products'));
    }

    // =========================
// STORE (AMAN & TRANSAKSI)
// =========================
    public function store()
    {
        // ❌ Cegah pusat submit
        if (session('role') === 'pusat') {
            return redirect()->to('/sales')
                ->with('error', 'Pusat tidak dapat melakukan transaksi penjualan');
        }

        $this->db->transBegin();

        try {
            $branchId = session('branch_id');
            $userId = session('user_id');
            $items = $this->request->getPost('items');

            if (!$items || !is_array($items)) {
                throw new \Exception('Item penjualan kosong');
            }

            // HEADER
            $saleId = $this->saleModel->insert([
                'invoice' => 'INV-' . time(),
                'branch_id' => $branchId,
                'user_id' => $userId,
                'date' => date('Y-m-d'),
                'total' => 0,
            ], true);

            $grandTotal = 0;

            foreach ($items as $item) {
                $productItemId = (int) $item['product_item_id'];
                $qty = (int) $item['quantity'];
                $price = (int) $item['price'];

                if ($qty <= 0) {
                    throw new \Exception('Qty tidak valid');
                }

                // 🔒 VALIDASI STOK CABANG
                $stock = $this->stockInitialModel
                    ->where('branch_id', $branchId)
                    ->where('product_item_id', $productItemId)
                    ->first();

                if (!$stock || $qty > $stock['quantity']) {
                    throw new \Exception('Stok cabang tidak mencukupi');
                }

                // DETAIL
                $this->saleDetailModel->insert([
                    'sale_id' => $saleId,
                    'product_item_id' => $productItemId,
                    'quantity' => $qty,
                    'price' => $price,
                    'subtotal' => $qty * $price,
                ]);

                // POTONG STOK CABANG
                $this->stockInitialModel->update($stock['id'], [
                    'quantity' => $stock['quantity'] - $qty,
                ]);

                // STOCK CARD (OUT)
                $this->stockCardModel->insert([
                    'product_item_id' => $productItemId,
                    'branch_id' => $branchId,
                    'date' => date('Y-m-d'),
                    'type' => 'out',
                    'quantity' => $qty,
                    'description' => 'Penjualan',
                ]);

                $grandTotal += $qty * $price;
            }

            $this->saleModel->update($saleId, ['total' => $grandTotal]);

            $this->db->transCommit();
            return redirect()->to('/sales')->with('success', 'Penjualan berhasil');

        } catch (\Throwable $e) {
            $this->db->transRollback();
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }
    // =========================
    // DETAIL
    // =========================
    public function detail($id)
    {
        $sale = $this->saleModel->getSaleWithUserAndBranch($id);
        if (!$sale) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException();
        }

        $details = $this->saleDetailModel
            ->select('sale_details.*, product_items.name AS product_name')
            ->join('product_items', 'product_items.id = sale_details.product_item_id')
            ->where('sale_id', $id)
            ->findAll();

        return view('sales/detail', compact('sale', 'details'));
    }

    // =========================
// PDF INVOICE
// =========================
    public function pdf_invoice($id)
    {
        // 🔹 Ambil header penjualan + kasir + cabang
        $sale = $this->saleModel
            ->select('sales.*, users.name AS kasir_name, branches.name AS branch_name')
            ->join('users', 'users.id = sales.user_id', 'left')
            ->join('branches', 'branches.id = sales.branch_id', 'left')
            ->where('sales.id', $id)
            ->first();

        if (!$sale) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Data penjualan tidak ditemukan');
        }

        // 🔹 Ambil detail penjualan
        $details = $this->saleDetailModel
            ->select('sale_details.*, product_items.name AS product_name')
            ->join('product_items', 'product_items.id = sale_details.product_item_id')
            ->where('sale_details.sale_id', $id)
            ->findAll();

        // 🔹 Logo (optional, aman kalau tidak ada)
        $logoPath = FCPATH . 'uploads/logo/selkom.jpg';
        $logoSrc = '';

        if (file_exists($logoPath)) {
            $logoData = file_get_contents($logoPath);
            $logoSrc = 'data:image/jpeg;base64,' . base64_encode($logoData);
        }

        // 🔹 Render HTML invoice
        $html = view('sales/pdf_invoice', [
            'sale' => $sale,
            'details' => $details,
            'logoSrc' => $logoSrc
        ]);

        // 🔹 Generate PDF
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // 🔹 Tampilkan di browser
        $dompdf->stream(
            "Invoice-{$sale['invoice']}.pdf",
            ["Attachment" => false]
        );
    }

}
