<?php

namespace App\Models;

use CodeIgniter\Model;

class StockAllocationModel extends Model
{
    protected $table = 'stock_allocations';
    protected $primaryKey = 'id';
    protected $allowedFields = ['invoice', 'branch_id', 'date', 'created_at'];
    public $useTimestamps = false; // karena kita hanya pakai 'created_at' manual

    // Ambil allocation + branch (join)
    public function getAllocations($search = null)
    {
        $builder = $this->select('stock_allocations.*, branches.name as branch_name')
            ->join('branches', 'branches.id = stock_allocations.branch_id');

        if (!empty($search)) {
            $builder->groupStart()
                ->like('stock_allocations.invoice', $search)
                ->orLike('branches.name', $search)
                ->orLike('stock_allocations.date', $search)
                ->groupEnd();
        }

        return $builder;
    }
}
