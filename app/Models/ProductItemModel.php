<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductItemModel extends Model
{
    protected $table = 'product_items';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useAutoIncrement = true;
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'name',
        'category_id',
    ];

    // =========================
    // GET PAGINATED LIST
    // =========================
    public function getPaginated($perPage = 10, $keyword = null, $categoryId = null, $sort = 'latest')
    {
        $builder = $this->select('
                product_items.id,
                product_items.name,
                product_items.category_id,
                categories.name AS category_name,
                product_items.created_at
            ')
            ->join('categories', 'categories.id = product_items.category_id');

        if ($keyword) {
            $builder->groupStart()
                ->like('product_items.name', $keyword)
                ->orLike('categories.name', $keyword)
                ->groupEnd();
        }

        if ($categoryId) {
            $builder->where('product_items.category_id', $categoryId);
        }

        $sortWhitelist = [
            'name' => 'product_items.name ASC',
            'oldest' => 'product_items.created_at ASC',
            'latest' => 'product_items.created_at DESC',
        ];

        $builder->orderBy($sortWhitelist[$sort] ?? $sortWhitelist['latest']);

        return $builder->paginate($perPage);
    }


    public function getWithCategory()
    {
        return $this->select('
            product_items.id,
            product_items.name,
            categories.name AS category_name
        ')
            ->join('categories', 'categories.id = product_items.category_id', 'left')
            ->orderBy('product_items.name', 'ASC')
            ->findAll();
    }


    // =========================
    // GET DETAIL
    // =========================
    public function getDetail($id)
    {
        return $this->select('
                product_items.*,
                categories.name AS category_name
            ')
            ->join('categories', 'categories.id = product_items.category_id')
            ->where('product_items.id', $id)
            ->first();
    }

    // =========================
    // GET DROPDOWN
    // =========================
    public function getDropdown()
    {
        $rows = $this->select('id, name')->orderBy('name', 'ASC')->findAll();
        $dropdown = [];
        foreach ($rows as $row) {
            $dropdown[$row['id']] = $row['name'];
        }
        return $dropdown;
    }

    // =========================
    // VALIDATE DUPLICATE
    // =========================
    public function isDuplicate($name, $categoryId, $ignoreId = null)
    {
        $builder = $this->where([
            'name' => $name,
            'category_id' => $categoryId
        ]);

        if ($ignoreId) {
            $builder->where('id !=', $ignoreId);
        }

        return $builder->countAllResults() > 0;
    }
}
