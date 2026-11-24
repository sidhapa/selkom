<?php

namespace App\Models;

use CodeIgniter\Model;

class StockCardModel extends Model
{
    protected $table = 'stock_cards';
    protected $primaryKey = 'id';
    protected $useTimestamps = true;

    protected $allowedFields = [
        'product_item_id',
        'branch_id',
        'date',
        'type',
        'quantity',
        'description',
        'source',
        'ref_id',
    ];

    /* ============================================
     * HITUNG STOK CABANG PER PRODUK
     * ============================================ */
    public function getBranchStock(int $branchId, int $productItemId): int
    {
        $row = $this->select("
                SUM(
                    CASE 
                        WHEN type = 'in' THEN quantity
                        ELSE -quantity
                    END
                ) AS stock
            ")
            ->where('branch_id', $branchId)
            ->where('product_item_id', $productItemId)
            ->get()
            ->getRow();

        return (int) ($row->stock ?? 0);
    }

    /* ============================================
     * PRODUK UNTUK DROPDOWN PENJUALAN CABANG
     * (SUMBER STOK: stock_cards)
     * ============================================ */
    public function getProductsForSale(int $branchId): array
    {
        return $this->db->query("
            SELECT
                pi.id   AS product_item_id,
                pi.name AS product_name,
                c.name  AS category_name,
                s.sell_price,
                SUM(
                    CASE 
                        WHEN sc.type = 'in' THEN sc.quantity
                        ELSE -sc.quantity
                    END
                ) AS stock
            FROM stock_cards sc
            JOIN product_items pi ON pi.id = sc.product_item_id
            JOIN stocks s ON s.product_item_id = pi.id
            LEFT JOIN categories c ON c.id = pi.category_id
            WHERE sc.branch_id = ?
            GROUP BY 
                pi.id, pi.name, c.name, s.sell_price
            HAVING stock > 0
            ORDER BY pi.name ASC
        ", [$branchId])->getResultArray();
    }

    /* ============================================
     * KURANGI STOK CABANG (PENJUALAN)
     * ============================================ */
    public function reduceBranchStock(
        int $branchId,
        int $productItemId,
        int $qty,
        string $description
    ): void {
        $available = $this->getBranchStock($branchId, $productItemId);

        if ($qty > $available) {
            throw new \Exception('Stok cabang tidak mencukupi');
        }

        $this->insert([
            'product_item_id' => $productItemId,
            'branch_id' => $branchId,
            'date' => date('Y-m-d'),
            'type' => 'out',
            'quantity' => $qty,
            'description' => $description,
            'source' => 'sale',
        ]);
    }
}
