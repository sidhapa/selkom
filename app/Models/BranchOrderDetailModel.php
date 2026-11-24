<?php

namespace App\Models;

use CodeIgniter\Model;

class BranchOrderDetailModel extends Model
{
    protected $table = 'branch_order_details';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'branch_order_id',
        'product_item_id', // diubah dari product_id
        'quantity',
    ];

    protected $useTimestamps = false;

    public function getByOrder($orderId)
    {
        return $this->select('
                branch_order_details.*,
                pi.name AS product_name,
                c.name AS category_name
            ')
            ->join('product_items pi', 'pi.id = branch_order_details.product_item_id', 'left') // diubah
            ->join('categories c', 'c.id = pi.category_id', 'left')
            ->where('branch_order_details.branch_order_id', $orderId)
            ->findAll();
    }
}
