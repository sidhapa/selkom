<?php

namespace App\Models;

use CodeIgniter\Model;

class StockAllocationDetailModel extends Model
{
    protected $table = 'stock_allocation_details';
    protected $primaryKey = 'id';
    protected $allowedFields = ['allocation_id', 'product_id', 'quantity'];

    // Ambil detail allocation + produk (join)
    public function getDetailsByAllocation($allocationId)
    {
        return $this->select('stock_allocation_details.*, products.name as product_name')
            ->join('products', 'products.id = stock_allocation_details.product_id')
            ->where('allocation_id', $allocationId)
            ->findAll();
    }
    
}
