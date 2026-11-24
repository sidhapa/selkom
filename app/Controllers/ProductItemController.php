<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ProductItemModel;
use App\Models\CategoryModel;
use App\Models\ProductModel;


class ProductItemController extends BaseController
{
    protected $productItemModel;
    protected $categoryModel;
    protected $productModel;

    public function __construct()
    {
        $this->productItemModel = new ProductItemModel();
        $this->categoryModel = new CategoryModel();
        $this->productModel = new ProductModel();
    }

    // =========================
    // LIST PRODUK
    // =========================
    public function index()
    {
        $userRole = session('role');
        $isPusat = ($userRole === 'pusat');

        $search = $this->request->getGet('search');
        $sort = $this->request->getGet('sort');
        $category = $this->request->getGet('category');

        $perPage = 10;
        $page = (int) ($this->request->getGet('page_product_items') ?? 1);

        $query = $this->productItemModel
            ->select('product_items.*, categories.name AS category_name')
            ->join('categories', 'categories.id = product_items.category_id');

        // =====================
        // SEARCH
        // =====================
        if (!empty($search)) {
            $query->groupStart()
                ->like('product_items.name', $search)
                ->orLike('categories.name', $search)
                ->groupEnd();
        }

        // =====================
        // FILTER CATEGORY (WAJIB)
        // =====================
        if (!empty($category)) {
            $query->where('product_items.category_id', $category);
        }

        // =====================
        // SORTING
        // =====================
        switch ($sort) {
            case 'name_az':
                $query->orderBy('product_items.name', 'ASC');
                break;
            case 'name_za':
                $query->orderBy('product_items.name', 'DESC');
                break;
            case 'category_az':
                $query->orderBy('categories.name', 'ASC');
                break;
            case 'category_za':
                $query->orderBy('categories.name', 'DESC');
                break;
            case 'oldest':
                $query->orderBy('product_items.created_at', 'ASC');
                break;
            default:
                $query->orderBy('product_items.created_at', 'DESC'); // latest
                break;
        }

        // =====================
        // PAGINATION
        // =====================
        $products = $query->paginate($perPage, 'product_items', $page);
        $pager = $this->productItemModel->pager;

        return view('product_items/index', [
            'products' => $products,
            'pager' => $pager,
            'categories' => $this->categoryModel->findAll(),
            'search' => $search,
            'sort' => $sort,
            'category' => $category, // penting untuk selected
            'isPusat' => $isPusat,
        ]);
    }


    // =========================
    // FORM TAMBAH PRODUK
    // =========================
    public function create()
    {
        return view('product_items/create', [
            'title' => 'Tambah Produk',
            'categories' => $this->categoryModel->findAll(),
        ]);
    }

    // =========================
    // SIMPAN PRODUK BARU
    // =========================
    public function store()
    {
        $rules = [
            'name' => 'required|min_length[3]',
            'category_id' => 'required|is_not_unique[categories.id]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $name = trim($this->request->getPost('name'));
        $categoryId = $this->request->getPost('category_id');

        if ($this->productItemModel->isDuplicate($name, $categoryId)) {
            return redirect()->back()->withInput()
                ->with('error', 'Produk dengan kategori tersebut sudah ada');
        }

        $this->productItemModel->insert([
            'name' => $name,
            'category_id' => $categoryId,
        ]);

        return redirect()->to('/product-items')
            ->with('success', 'Produk berhasil ditambahkan');
    }

    // =========================
    // FORM EDIT PRODUK
    // =========================
    public function edit($id)
    {
        $product = $this->productItemModel->find($id);
        if (!$product) {
            return redirect()->to('/product-items')
                ->with('error', 'Produk tidak ditemukan');
        }

        // Cek apakah produk ini sudah dipakai di pembelian
        $isUsed = $this->isProductItemUsed((int) $id);

        return view('product_items/edit', [
            'title'      => 'Edit Produk',
            'product'    => $product,
            'categories' => $this->categoryModel->findAll(),
            'isUsed'     => $isUsed,
        ]);
    }

    // =========================
    // UPDATE PRODUK
    // =========================
    public function update($id)
    {
        $product = $this->productItemModel->find($id);
        if (!$product) {
            return redirect()->to('/product-items')
                ->with('error', 'Produk tidak ditemukan');
        }

        $rules = [
            'name' => 'required|min_length[3]',
            'category_id' => 'required|is_not_unique[categories.id]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $name = trim($this->request->getPost('name'));
        $categoryId = $this->request->getPost('category_id');

        if ($this->productItemModel->isDuplicate($name, $categoryId, $id)) {
            return redirect()->back()->withInput()
                ->with('error', 'Produk dengan kategori tersebut sudah ada');
        }

        $this->productItemModel->update($id, [
            'name' => $name,
            'category_id' => $categoryId,
        ]);

        return redirect()->to('/product-items')
            ->with('success', 'Produk berhasil diperbarui');
    }

    // =========================
    // CEK APAKAH PRODUK SUDAH DIPAKAI
    // =========================
    private function isProductItemUsed(int $id): bool
    {
        $usedInPurchase = $this->productModel
            ->where('product_item_id', $id)
            ->where('deleted_at', null)
            ->countAllResults();

        return $usedInPurchase > 0;
    }

    // =========================
    // DELETE PRODUK
    // =========================
    public function delete($id)
    {
        // 1. Pastikan product item ada
        $productItem = $this->productItemModel->find($id);
        if (!$productItem) {
            return redirect()->to('/product-items')
                ->with('error', 'Produk tidak ditemukan.');
        }

        // 2. CEK apakah sudah dipakai di tabel products (soft delete aware)
        if ($this->isProductItemUsed($id)) {
            // Simpan ID agar view bisa highlight tombol Edit
            return redirect()->to('/product-items')
                ->with(
                    'error_cannot_delete',
                    [
                        'id'   => $id,
                        'name' => $productItem['name'],
                    ]
                );
        }

        // 3. AMAN → hapus
        $this->productItemModel->delete($id);

        return redirect()->to('/product-items')
            ->with('success', 'Produk "' . $productItem['name'] . '" berhasil dihapus.');
    }

}
