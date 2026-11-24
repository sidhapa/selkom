<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductModel extends Model
{
    protected $table            = 'products';
    protected $primaryKey       = 'id';
    protected $useSoftDeletes   = true;
    protected $useTimestamps    = true;
    protected $deletedField     = 'deleted_at';

    protected $allowedFields = [
        'product_item_id',
        'supplier_id',
        'price',
        'quantity',
    ];

    /* =====================================================
     * CEK DUPLIKAT SUPPLIER
     * ===================================================== */
    public function existsSameSupplier(int $productItemId, int $supplierId, ?int $ignoreId = null): bool
    {
        $builder = $this->where([
            'product_item_id' => $productItemId,
            'supplier_id'     => $supplierId,
            'deleted_at'      => null,
        ]);

        if ($ignoreId) {
            $builder->where('id !=', $ignoreId);
        }

        return $builder->countAllResults() > 0;
    }

    /* =====================================================
     * CREATE PRODUCT
     * ===================================================== */
    public function createProduct(array $data): int
    {
        $this->db->transBegin();

        try {
            if (!$this->insert($data)) {
                throw new \RuntimeException('Insert product gagal');
            }

            $productId = $this->insertID();

            // Sinkronisasi stock
            model('StockModel')->syncStock($data['product_item_id']);

            $this->db->transCommit();
            return $productId;

        } catch (\Throwable $e) {
            $this->db->transRollback();
            log_message('error', 'CREATE PRODUCT ERROR: ' . $e->getMessage());
            return 0;
        }
    }

    /* =====================================================
     * UPDATE PRODUCT
     * ===================================================== */
public function updateProduct(int $id, array $data): bool
{
    // Ambil data lama
    $oldProduct = $this->find($id);
    if (!$oldProduct) {
        return false;
    }

    $updated = $this->update($id, $data);

    if ($updated) {
        $stockModel = model('StockModel');

        // Sync stock product_item LAMA
        $stockModel->syncStock($oldProduct['product_item_id']);

        // Sync stock product_item BARU (jika berubah)
        if ($oldProduct['product_item_id'] !== $data['product_item_id']) {
            $stockModel->syncStock($data['product_item_id']);
        }
    }

    return (bool) $updated;
}
    

    /* =====================================================
     * DELETE PRODUCT
     * ===================================================== */
    public function deleteProduct(int $id): bool
    {
        $product = $this->find($id);
        if (!$product) {
            return false;
        }

        $deleted = $this->delete($id);

        if ($deleted) {
            model('StockModel')->syncStock($product['product_item_id']);
        }

        return (bool) $deleted;
    }

    /* =====================================================
     * GET PRODUCTS WITH RELATIONS + PAGINATION
     * ===================================================== */
    public function getProductsWithRelationsPaginated(?string $search = null, ?string $sort = null, int $perPage = 10)
    {
        $this->select("
            products.id,
            product_items.name AS product_name,
            categories.name AS category_name,
            suppliers.name AS supplier_name,
            products.quantity,
            products.price
        ")
        ->join('product_items', 'product_items.id = products.product_item_id')
        ->join('categories', 'categories.id = product_items.category_id', 'left')
        ->join('suppliers', 'suppliers.id = products.supplier_id', 'left');

        if ($search) {
            $this->groupStart()
                ->like('product_items.name', $search)
                ->orLike('categories.name', $search)
                ->orLike('suppliers.name', $search)
                ->groupEnd();
        }

        $sortMap = [
            'qty_desc'   => ['products.quantity', 'DESC'],
            'qty_asc'    => ['products.quantity', 'ASC'],
            'price_desc' => ['products.price', 'DESC'],
            'price_asc'  => ['products.price', 'ASC'],
        ];

        [$field, $dir] = $sortMap[$sort] ?? ['product_items.name', 'ASC'];
        $this->orderBy($field, $dir);

        // PAGINATE menggunakan Model, ini aman
        return $this->paginate($perPage, 'products');
    }
}
