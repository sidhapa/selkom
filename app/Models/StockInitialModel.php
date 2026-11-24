<?php

namespace App\Models;

use CodeIgniter\Model;

class StockInitialModel extends Model
{
    protected $table = 'stock_initial';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'branch_id',
        'product_item_id',
        'quantity',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;

    // 🔹 Ambil stok cabang
    public function getBranchStock($branchId, $productItemId)
    {
        $row = $this->where([
            'branch_id' => $branchId,
            'product_item_id' => $productItemId,
        ])->first();

        return $row ? (int) $row['quantity'] : 0;
    }

    // 🔹 Kurangi stok cabang
    public function reduceStock($branchId, $productItemId, $qty)
    {
        $row = $this->where([
            'branch_id' => $branchId,
            'product_item_id' => $productItemId,
        ])->first();

        if (!$row) {
            throw new \Exception('Stok cabang tidak ditemukan');
        }

        if ($row['quantity'] < $qty) {
            throw new \Exception('Stok cabang tidak mencukupi');
        }

        $this->update($row['id'], [
            'quantity' => $row['quantity'] - $qty,
        ]);
    }
}
