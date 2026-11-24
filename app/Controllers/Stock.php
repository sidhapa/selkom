<?php

namespace App\Controllers;

use App\Models\StockModel;
use App\Models\ProductItemModel;

class Stock extends BaseController
{
    protected StockModel $stockModel;
    protected ProductItemModel $productItemModel;

    public function __construct()
    {
        $this->stockModel       = new StockModel();
        $this->productItemModel = new ProductItemModel();
    }

    /* =====================================================
       HALAMAN STOK PUSAT (PAGINATION)
    ===================================================== */
    public function index()
    {
        $search  = $this->request->getGet('search');
        $sort    = $this->request->getGet('sort');
        $perPage = 10;

        $stocks = $this->stockModel
            ->getStockIndex($search, $sort)
            ->paginate($perPage, 'stocks');

        return view('stock/index', [
            'title'  => 'Kartu Stok Pusat',
            'stocks' => $stocks,
            'pager'  => $this->stockModel->pager,
            'search' => $search,
            'sort'   => $sort,
        ]);
    }

    /* =====================================================
       FORM EDIT HARGA JUAL
    ===================================================== */
    public function editPrice(int $productItemId)
    {
        $stock = $this->stockModel
            ->where('product_item_id', $productItemId)
            ->first();

        if (!$stock) {
            return redirect()->to('/stock')
                ->with('error', 'Produk tidak ditemukan.');
        }

        $product = $this->productItemModel->find($productItemId);

        return view('stock/edit_price', [
            'product_item_id' => $productItemId,
            'product_name'    => $product['name'] ?? '-',
            'old_price'       => $stock['sell_price'],
        ]);
    }

    /* =====================================================
       UPDATE HARGA JUAL SEMUA BATCH
    ===================================================== */
    public function updatePrice()
    {
        $productItemId = (int) $this->request->getPost('product_item_id');
        $price         = (float) $this->request->getPost('sell_price');

        if ($productItemId <= 0 || $price <= 0) {
            return redirect()->back()->with('error', 'Harga tidak valid.');
        }

        $this->stockModel
            ->where('product_item_id', $productItemId)
            ->set(['sell_price' => $price])
            ->update();

        return redirect()->to('/stock')
            ->with('success', 'Harga jual berhasil diperbarui.');
    }

    /* =====================================================
       TAMBAH STOK MANUAL (BATCH FIFO)
    ===================================================== */
    public function store()
    {
        $productItemId = (int) $this->request->getPost('product_item_id');
        $sellPrice     = (float) $this->request->getPost('sell_price');
        $quantity      = (int) $this->request->getPost('quantity');

        if ($productItemId <= 0 || $sellPrice <= 0 || $quantity <= 0) {
            return redirect()->back()
                ->with('error', 'Input stok tidak valid.');
        }

        $this->stockModel->insert([
            'product_item_id' => $productItemId,
            'quantity'        => $quantity,
            'sell_price'      => $sellPrice,
        ]);

        return redirect()->to('/stock')
            ->with('success', 'Stok berhasil ditambahkan (FIFO).');
    }

    /* =====================================================
       HAPUS SATU BATCH STOK
    ===================================================== */
    public function delete(int $stockId)
    {
        $stock = $this->stockModel->find($stockId);
        if (!$stock) {
            return redirect()->to('/stock')
                ->with('error', 'Batch stok tidak ditemukan.');
        }

        $this->stockModel->delete($stockId);

        return redirect()->to('/stock')
            ->with('success', 'Batch stok berhasil dihapus.');
    }
}
