<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\StockInitialModel;
use App\Models\BranchModel;
use App\Models\StockCardModel;
use App\Models\StockModel;

class StockInitial extends BaseController
{
    protected $stockInitialModel;
    protected $branchModel;
    protected $stockCardModel;
    protected $stockModel;
    protected $db;

    public function __construct()
    {
        $this->stockInitialModel = new StockInitialModel();
        $this->branchModel = new BranchModel();
        $this->stockCardModel = new StockCardModel();
        $this->stockModel = new StockModel();
        $this->db = \Config\Database::connect();
    }

    /* =======================
     * INDEX (LIST CABANG)
     * ======================= */
    public function index()
    {
        $role = session('role');
        $branchId = session('branch_id');

        // Role cabang langsung ke detail sendiri
        if ($role === 'cabang') {
            return redirect()->to('/stockinitial/detail/' . $branchId);
        }

        $search = $this->request->getGet('search');
        $sort = $this->request->getGet('sort');

        $query = $this->branchModel;

        if ($search) {
            $query->groupStart()
                ->like('name', $search)
                ->orLike('address', $search)
                ->groupEnd();
        }

        $query->orderBy('name', $sort === 'name_desc' ? 'DESC' : 'ASC');

        return view('stock_initial/index', [
            'branches' => $query->paginate(10, 'branches'),
            'pager' => $this->branchModel->pager,
            'search' => $search,
            'sort' => $sort,
        ]);
    }

    /* =======================
     * CREATE (PUSAT)
     * ======================= */
    public function create()
    {
        if (session('role') !== 'pusat') {
            throw new \CodeIgniter\Exceptions\PageNotFoundException();
        }

        return view('stock_initial/create', [
            'branches' => $this->branchModel->findAll(),
            // ⬇️ PAKAI METHOD AMAN (SUDAH ADA)
            'products' => $this->stockModel->getAvailableProducts(),
        ]);
    }

    /* =======================
     * DETAIL CABANG
     * ======================= */
    public function detail($branchId)
    {
        $userRole = session('role');
        $userBranchId = session('branch_id');

        // Cabang hanya boleh lihat miliknya
        if ($userRole === 'cabang' && $branchId != $userBranchId) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException();
        }

        $search = $this->request->getGet('search');
        $sort = $this->request->getGet('sort');
        $date = $this->request->getGet('date');

        $builder = $this->stockInitialModel
            ->select('
        stock_initial.*,
        product_items.name AS product_name,
        categories.name AS category_name
    ')
            ->join('product_items', 'product_items.id = stock_initial.product_item_id')
            ->join('categories', 'categories.id = product_items.category_id', 'left')
            ->where('stock_initial.branch_id', $branchId);


        /* ==========================
         * SEARCH
         * ========================== */
        if ($search) {
            $builder->like('product_items.name', $search);
        }

        /* ==========================
         * FILTER DATE (PUSAT SAJA)
         * ========================== */
        if ($date && $userRole === 'pusat') {
            $builder->where('DATE(stock_initial.created_at)', $date);
        }

        /* ==========================
         * SORTING
         * ========================== */
        switch ($sort) {
            case 'name_desc':
                $builder->orderBy('product_items.name', 'DESC');
                break;

            case 'date_new':
                $builder->orderBy('stock_initial.created_at', 'DESC');
                break;

            case 'date_old':
                $builder->orderBy('stock_initial.created_at', 'ASC');
                break;

            default:
                // name_asc / kosong
                $builder->orderBy('product_items.name', 'ASC');
        }

        $stockInitial = $builder->findAll();
        $branch = $this->branchModel->find($branchId);

        if (!$branch) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException();
        }

        return view('stock_initial/detail', [
            'branch' => $branch,
            'stockInitial' => $stockInitial,
            'search' => $search,
            'sort' => $sort,
            'date' => $date,
            'userRole' => $userRole,
        ]);
    }

    /* =======================
     * STORE
     * ======================= */
    public function store()
    {
        if (session('role') !== 'pusat') {
            return redirect()->back()->with('error', 'Akses ditolak');
        }

        $branchId = $this->request->getPost('branch_id');
        $productIds = (array) $this->request->getPost('product_item_id');
        $quantities = (array) $this->request->getPost('quantity');

        $this->db->transBegin();

        try {
            /* =====================
             * VALIDASI STOK PUSAT
             * ===================== */
            foreach ($productIds as $i => $productItemId) {
                $qty = (int) $quantities[$i];
                $available = $this->stockModel->getStockByProductItem($productItemId);

                if ($qty <= 0) {
                    throw new \Exception('Jumlah tidak valid');
                }

                if ($qty > $available) {
                    throw new \Exception('Stok pusat tidak mencukupi');
                }
            }

            /* =====================
             * SIMPAN & POTONG STOK
             * ===================== */
            foreach ($productIds as $i => $productItemId) {
                $qty = (int) $quantities[$i];

                $existing = $this->stockInitialModel
                    ->where('branch_id', $branchId)
                    ->where('product_item_id', $productItemId)
                    ->first();

                if ($existing) {
                    $this->stockInitialModel->update($existing['id'], [
                        'quantity' => $existing['quantity'] + $qty
                    ]);
                } else {
                    $this->stockInitialModel->insert([
                        'branch_id' => $branchId,
                        'product_item_id' => $productItemId,
                        'quantity' => $qty
                    ]);
                }

                // potong stok pusat
                $this->stockModel->reduceStock($productItemId, $qty);

                // stock card
                $this->stockCardModel->insert([
                    'product_item_id' => $productItemId,
                    'branch_id' => $branchId,
                    'date' => date('Y-m-d'),
                    'type' => 'in',
                    'quantity' => $qty,
                    'description' => 'Stok Awal Cabang'
                ]);
            }

            $this->db->transCommit();
            return redirect()->to('/stockinitial')->with('success', 'Stok awal berhasil disimpan');

        } catch (\Throwable $e) {
            $this->db->transRollback();
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }
}
