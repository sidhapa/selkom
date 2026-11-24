<?php

namespace App\Controllers;

use App\Models\ProductModel;
use App\Models\ProductItemModel;
use App\Models\SupplierModel;

class ProductController extends BaseController
{
    protected ProductModel $productModel;
    protected ProductItemModel $productItemModel;
    protected SupplierModel $supplierModel;

    public function __construct()
    {
        $this->productModel     = new ProductModel();
        $this->productItemModel = new ProductItemModel();
        $this->supplierModel    = new SupplierModel();
    }

    /* =====================================================
     * LIST PRODUK
     * ===================================================== */
    public function index()
    {
        $search = $this->request->getGet('search');
        $sort   = $this->request->getGet('sort');

        // Ambil data paginated
        $products = $this->productModel
            ->getProductsWithRelationsPaginated($search, $sort, 10);

        return view('products/index', [
            'title'    => 'Data Pembelian',
            'products' => $products,
            'pager'    => $this->productModel->pager,
            'search'   => $search,
            'sort'     => $sort,
        ]);
    }

    /* =====================================================
     * FORM CREATE
     * ===================================================== */
    public function create()
    {
        // Ambil semua produk beserta nama kategori
        $productItems = $this->productItemModel
            ->select('product_items.id, product_items.name, categories.name as category_name')
            ->join('categories', 'categories.id = product_items.category_id', 'left')
            ->findAll();

        $suppliers = $this->supplierModel->findAll();

        return view('products/create', [
            'title' => 'Tambah Pembelian',
            'productItems' => $productItems,
            'suppliers' => $suppliers
        ]);
    }

    /* =====================================================
     * SIMPAN PRODUK
     * ===================================================== */
    public function store()
    {
        if (!$this->validate($this->rules(true))) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $data = $this->getPayload(true);

        if ($this->productModel->existsSameSupplier(
            $data['product_item_id'],
            $data['supplier_id']
        )) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Produk dengan supplier ini sudah ada.');
        }

        if (!$this->productModel->createProduct($data)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menyimpan produk.');
        }

        return redirect()->to('/products')
            ->with('success', 'Produk berhasil dibuat.');
    }

    /* =====================================================
     * FORM EDIT
     * ===================================================== */
    public function edit(int $id)
    {
        $product = $this->productModel->find($id);
        if (!$product) {
            return redirect()->to('/products')
                ->with('error', 'Produk tidak ditemukan.');
        }

        return view('products/edit', [
            'title'        => 'Edit Pembelian',
            'product'      => $product,
            'productItems' => $this->productItemModel->getWithCategory(),
            'suppliers'    => $this->supplierModel->findAll(),
        ]);
    }

    /* =====================================================
     * UPDATE PRODUK
     * ===================================================== */
    public function update(int $id)
    {
        if (!$this->productModel->find($id)) {
            return redirect()->to('/products')
                ->with('error', 'Produk tidak ditemukan.');
        }

        if (!$this->validate($this->rules(false))) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $data = $this->getPayload(false);

        if ($this->productModel->existsSameSupplier(
            $data['product_item_id'],
            $data['supplier_id'],
            $id
        )) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Produk dengan supplier ini sudah ada.');
        }

        $this->productModel->updateProduct($id, $data);

        return redirect()->to('/products')
            ->with('success', 'Produk berhasil diperbarui.');
    }

    /* =====================================================
     * DELETE PRODUK
     * ===================================================== */
    public function delete(int $id)
    {
        if (!$this->productModel->find($id)) {
            return redirect()->to('/products')
                ->with('error', 'Produk tidak ditemukan.');
        }

        $this->productModel->deleteProduct($id);

        return redirect()->to('/products')
            ->with('success', 'Produk berhasil dihapus.');
    }

    /* =====================================================
     * VALIDATION RULES
     * ===================================================== */
    private function rules(bool $isCreate): array
    {
        $rules = [
            'product_item_id' => 'required|integer',
            'supplier_id' => 'required|integer',
            'price' => 'required|decimal|greater_than[0]',
        ];

        if ($isCreate) {
            $rules['quantity'] = 'required|integer|greater_than_equal_to[0]';
        }

        return $rules;
    }


    /* =====================================================
     * DATA PAYLOAD
     * ===================================================== */
 private function getPayload(bool $isCreate): array
{
    $data = [
        'product_item_id' => (int) $this->request->getPost('product_item_id'),
        'supplier_id'     => (int) $this->request->getPost('supplier_id'),
        'price'           => (float) $this->request->getPost('price'),
    ];

    if ($isCreate) {
        // CREATE: quantity WAJIB
        $data['quantity'] = (int) $this->request->getPost('quantity');
    } else {
        // EDIT: ambil quantity jika ada, kalau tidak pakai yang lama
        $qty = $this->request->getPost('quantity');
        if ($qty !== null && $qty !== '') {
            $data['quantity'] = (int) $qty;
        }
    }

    return $data;
}


}
