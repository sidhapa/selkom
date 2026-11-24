<?php

namespace App\Models;

use CodeIgniter\Model;

class StockModel extends Model
{
    protected $table = 'stocks';
    protected $primaryKey = 'id';
    protected $useTimestamps = true;

    protected $allowedFields = [
        'product_item_id',
        'quantity',
        'sell_price',
    ];

    /* =====================================================
     * SYNC STOCK DARI PRODUCT (ALUR LAMA - JANGAN DIUBAH)
     * ===================================================== */
    public function syncStock(int $productItemId): void
    {
        $productModel = model('ProductModel');

        $products = $productModel
            ->where('product_item_id', $productItemId)
            ->where('deleted_at', null)
            ->findAll();

        if (empty($products)) {
            // Jika produk sudah tidak ada → hapus stok
            $this->where('product_item_id', $productItemId)->delete();
            return;
        }

        // HITUNG TOTAL QTY DARI PEMBELIAN (BENAR)
        $totalQty = 0;
        foreach ($products as $product) {
            $totalQty += $product['quantity'];
        }

        $existing = $this->where('product_item_id', $productItemId)->first();

        if ($existing) {
            // ❗ JANGAN SENTUH HARGA JUAL JIKA SUDAH ADA
            $this->update($existing['id'], [
                'quantity' => $totalQty,
            ]);
        } else {
            // ❗ HARGA JUAL AWAL = 0
            $this->insert([
                'product_item_id' => $productItemId,
                'quantity' => $totalQty,
                'sell_price' => 0,
            ]);
        }
    }


    /* =====================================================
     * TOTAL STOCK (UNTUK VALIDASI)
     * ===================================================== */
    public function getTotalStock(int $productItemId): int
    {
        return (int) (
            $this->where('product_item_id', $productItemId)
                ->selectSum('quantity')
                ->get()
                ->getRow()
                ->quantity ?? 0
        );
    }

    /* =====================================================
     * METHOD BARU: ALIAS AMAN (UNTUK STOCK INITIAL)
     * ===================================================== */
    public function getStockByProductItem(int $productItemId): int
    {
        return $this->getTotalStock($productItemId);
    }

    /* =====================================================
     * METHOD BARU: REDUCE STOCK FIFO (TIDAK MERUSAK SYNC)
     * ===================================================== */
    public function reduceStock(int $productItemId, int $qty): void
    {
        $total = $this->getTotalStock($productItemId);

        if ($qty > $total) {
            throw new \Exception('Stok pusat tidak mencukupi');
        }

        // Karena stok pusat hasil agregasi product,
        // kita cukup update quantity di stocks
        $stock = $this->where('product_item_id', $productItemId)->first();

        if (!$stock) {
            throw new \Exception('Data stok tidak ditemukan');
        }

        $this->update($stock['id'], [
            'quantity' => $stock['quantity'] - $qty
        ]);
    }

    /* =====================================================
     * LISTING STOCK (PAGINATION)
     * ===================================================== */
    public function getStockIndex(?string $search = null, ?string $sort = null): self
    {
        $this->select([
            'pi.id AS product_item_id',
            'pi.name AS product_name',
            'c.name AS category_name',
            'SUM(stocks.quantity) AS total_quantity',
            'MAX(stocks.sell_price) AS sell_price',
        ])
            ->join('product_items pi', 'pi.id = stocks.product_item_id')
            ->join('categories c', 'c.id = pi.category_id', 'left')
            ->groupBy('pi.id, pi.name, c.name');

        if ($search) {
            $this->groupStart()
                ->like('pi.name', $search)
                ->orLike('c.name', $search)
                ->groupEnd();
        }

        switch ($sort) {
            case 'name_desc':
                $this->orderBy('pi.name', 'DESC');
                break;
            case 'qty_high':
                $this->orderBy('total_quantity', 'DESC');
                break;
            case 'qty_low':
                $this->orderBy('total_quantity', 'ASC');
                break;
            case 'price_high':
                $this->orderBy('sell_price', 'DESC');
                break;
            case 'price_low':
                $this->orderBy('sell_price', 'ASC');
                break;
            default:
                $this->orderBy('pi.name', 'ASC');
        }

        return $this;
    }

    /* =====================================================
     * AVAILABLE PRODUCTS UNTUK PENJUALAN
     * ===================================================== */
    public function getAvailableProducts(): array
    {
        return $this->select('
            pi.id AS product_item_id,
            pi.name,
            c.name AS category_name,
            SUM(stocks.quantity) AS stock,
            MAX(stocks.sell_price) AS price
        ')
            ->join('product_items pi', 'pi.id = stocks.product_item_id')
            ->join('categories c', 'c.id = pi.category_id', 'left')
            ->where('stocks.quantity >', 0)
            ->groupBy('pi.id, pi.name, c.name')
            ->orderBy('pi.name', 'ASC')
            ->findAll();
    }

}
