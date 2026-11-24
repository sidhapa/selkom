<?php

namespace App\Models;

use CodeIgniter\Model;

class SaleDetailModel extends Model
{
    protected $table = 'sale_details';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;
    protected $returnType = 'array';

    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'sale_id',
        'product_item_id',
        'quantity',
        'price',
        'subtotal',
    ];

    protected $useTimestamps = false;
    protected $createdField = 'created_at';
    protected $updatedField = null;

    // =========================
    // METHOD BANTU
    // =========================

    /**
     * Ambil detail penjualan berdasarkan sale_id
     */
    public function getBySaleId($saleId)
    {
        return $this->select(
            'sale_details.*,
                 product_items.name AS product_name,
                 categories.name AS category_name'
        )
            ->join('product_items', 'product_items.id = sale_details.product_item_id')
            ->join('categories', 'categories.id = product_items.category_id')
            ->where('sale_details.sale_id', $saleId)
            ->get()
            ->getResultArray();
    }

    /**
     * Hitung total dari detail penjualan
     */
    public function calculateTotal($saleId)
    {
        return $this->selectSum('subtotal')
            ->where('sale_id', $saleId)
            ->get()
            ->getRowArray()['subtotal'] ?? 0;
    }
}
