<?php

namespace App\Controllers;

use App\Models\SupplierModel;
use App\Models\ProductModel;

class SupplierController extends BaseController
{
    protected $supplierModel;
    protected $productModel;

    public function __construct()
    {
        $this->supplierModel = new SupplierModel();
        $this->productModel  = new ProductModel();
    }

    public function index()
    {
        $search = $this->request->getGet('search');
        $sort = $this->request->getGet('sort'); // name_asc, name_desc, address_asc, address_desc
        $perPage = 5;

        $builder = $this->supplierModel;

        // 🔍 Filter Pencarian
        if (!empty($search)) {
            $builder = $builder->groupStart()
                ->like('name', $search)
                ->orLike('phone', $search)
                ->orLike('address', $search)
                ->groupEnd();
        }

        // 🧭 Sorting
        switch ($sort) {
            case 'name_asc':
                $builder = $builder->orderBy('name', 'ASC');
                break;
            case 'name_desc':
                $builder = $builder->orderBy('name', 'DESC');
                break;
            case 'address_asc':
                $builder = $builder->orderBy('address', 'ASC');
                break;
            case 'address_desc':
                $builder = $builder->orderBy('address', 'DESC');
                break;
            default:
                // Default: paling baru
                $builder = $builder->orderBy('created_at', 'DESC');
        }

        $suppliers = $builder->paginate($perPage, 'suppliers');
        $pager = $builder->pager;

        // Kirim data ke view
        return view('suppliers/index', [
            'suppliers' => $suppliers,
            'pager' => $pager,
            'search' => $search,
            'sort' => $sort,
            'perPage' => $perPage,
        ]);
    }

    public function create()
    {
        return view('suppliers/create');
    }

    public function store()
    {
        $this->supplierModel->save([
            'name' => $this->request->getPost('name'),
            'phone' => $this->request->getPost('phone'),
            'address' => $this->request->getPost('address'),
        ]);

        return redirect()->to('/suppliers')->with('success', 'Supplier berhasil ditambahkan.');
    }

    public function edit($id)
    {
        return view('suppliers/edit', [
            'supplier' => $this->supplierModel->find($id),
        ]);
    }

    public function update($id)
    {
        $this->supplierModel->update($id, [
            'name' => $this->request->getPost('name'),
            'phone' => $this->request->getPost('phone'),
            'address' => $this->request->getPost('address'),
        ]);

        return redirect()->to('/suppliers')->with('success', 'Supplier berhasil diperbarui.');
    }

    public function delete($id)
    {
        $supplier = $this->supplierModel->find($id);
        if (!$supplier) {
            return redirect()->to('/suppliers')->with('error', 'Supplier tidak ditemukan.');
        }

        // Cek apakah supplier masih dipakai di tabel products (pembelian)
        $usedCount = $this->productModel
            ->where('supplier_id', $id)
            ->where('deleted_at', null)
            ->countAllResults();

        if ($usedCount > 0) {
            return redirect()->to('/suppliers')
                ->with('error', 'Supplier <strong>"' . esc($supplier['name']) . '"</strong> tidak dapat dihapus karena masih terdapat <strong>' . $usedCount . ' data pembelian</strong> yang menggunakan supplier ini.');
        }

        $this->supplierModel->delete($id);
        return redirect()->to('/suppliers')
            ->with('success', 'Supplier "' . $supplier['name'] . '" berhasil dihapus.');
    }
}
