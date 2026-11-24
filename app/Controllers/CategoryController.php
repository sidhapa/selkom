<?php

namespace App\Controllers;

use App\Models\CategoryModel;
use App\Models\ProductItemModel;

class CategoryController extends BaseController
{
    protected $categoryModel;
    protected $productItemModel;

    public function __construct()
    {
        $this->categoryModel    = new CategoryModel();
        $this->productItemModel = new ProductItemModel();
    }

    public function index()
    {
        $search = $this->request->getGet('search');
        $sort = $this->request->getGet('sort');
        $perPage = 5;
        $page = (int) ($this->request->getGet('page_categories') ?? 1);

        $query = $this->categoryModel;

        // Pencarian
        if (!empty($search)) {
            $query = $query->groupStart()
                ->like('name', $search)
                ->orLike('created_at', $search)
                ->orLike('updated_at', $search)
                ->groupEnd();
        }

        // Sorting
        if ($sort === 'name_desc') {
            $query = $query->orderBy('name', 'DESC');
        } else {
            $query = $query->orderBy('name', 'ASC'); // default A→Z
        }

        $categories = $query->paginate($perPage, 'categories', $page);
        $pager = $this->categoryModel->pager;

        return view('categories/index', [
            'title' => 'Kategori',
            'categories' => $categories,
            'pager' => $pager,
            'search' => $search,
            'sort' => $sort
        ]);
    }



    public function create()
    {
        return view('categories/create', [
            'title' => 'Tambah Kategori',
        ]);
    }

    public function store()
    {
        $this->categoryModel->save([
            'name' => $this->request->getPost('name'),
        ]);
        return redirect()->to('/categories');
    }

    public function edit($id)
    {
        $data['category'] = $this->categoryModel->find($id);
        return view('categories/edit', $data);
    }

    public function update($id)
    {
        $this->categoryModel->update($id, [
            'name' => $this->request->getPost('name'),
        ]);
        return redirect()->to('/categories');
    }

    public function delete($id)
    {
        $category = $this->categoryModel->find($id);
        if (!$category) {
            return redirect()->to('/categories')->with('error', 'Kategori tidak ditemukan.');
        }

        // Cek apakah masih ada product_item yang menggunakan kategori ini
        $usedCount = $this->productItemModel
            ->where('category_id', $id)
            ->countAllResults();

        if ($usedCount > 0) {
            return redirect()->to('/categories')
                ->with('error', 'Kategori <strong>"' . esc($category['name']) . '"</strong> tidak dapat dihapus karena masih digunakan oleh <strong>' . $usedCount . ' produk</strong>. Hapus atau pindahkan produk tersebut terlebih dahulu.');
        }

        $this->categoryModel->delete($id);
        return redirect()->to('/categories')
            ->with('success', 'Kategori "' . $category['name'] . '" berhasil dihapus.');
    }
}
