<?php

namespace App\Models;

use CodeIgniter\Model;

class SaleModel extends Model
{
    protected $table = 'sales';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;
    protected $returnType = 'array';

    protected $allowedFields = [
        'invoice',
        'branch_id',
        'user_id',
        'date',
        'total',
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    /* =====================================================
     * 1. TOTAL PENJUALAN HARI INI (DASHBOARD CARD)
     * ===================================================== */
    public function getSalesPerDay($branchId = null)
    {
        $builder = $this->selectSum('total', 'total')
            ->where('date', date('Y-m-d'));

        if ($branchId !== null) {
            $builder->where('branch_id', $branchId);
        }

        return $builder->first(); // ['total' => 123456]
    }

    /* =====================================================
     * 2. TOTAL PENJUALAN BULAN INI (DASHBOARD CARD)
     * ===================================================== */
    public function getSalesPerMonth($branchId = null)
    {
        $builder = $this->selectSum('total', 'total')
            ->where('MONTH(date)', date('m'))
            ->where('YEAR(date)', date('Y'));

        if ($branchId !== null) {
            $builder->where('branch_id', $branchId);
        }

        return $builder->first(); // ['total' => 123456]
    }

    /* =====================================================
     * 3. DATA GRAFIK HARIAN (7 HARI TERAKHIR)
     *    → INI YANG MEMBUAT GRAFIK MUNCUL
     * ===================================================== */
    public function getSalesChartDaily($branchId = null)
    {
        $builder = $this->db->table($this->table)
            ->select('date, SUM(total) AS total')
            ->where('date >=', date('Y-m-d', strtotime('-6 days')))
            ->groupBy('date')
            ->orderBy('date', 'ASC');

        if ($branchId !== null) {
            $builder->where('branch_id', $branchId);
        }

        return $builder->get()->getResultArray();
        /*
            OUTPUT:
            [
                ['date' => '2025-01-15', 'total' => 120000],
                ['date' => '2025-01-16', 'total' => 300000],
            ]
        */
    }

    /* =====================================================
     * 4. LIST PENJUALAN + CABANG (INDEX SALES)
     * ===================================================== */
    public function getSalesWithBranch()
    {
        return $this->select('sales.*, branches.name AS branch_name')
            ->join('branches', 'branches.id = sales.branch_id', 'left')
            ->orderBy('sales.created_at', 'DESC')
            ->findAll();
    }

    /* =====================================================
     * 5. DETAIL HEADER PENJUALAN (INVOICE)
     * ===================================================== */
    public function getSaleWithUserAndBranch($id)
    {
        return $this->select('
                sales.*,
                users.name AS user_name,
                branches.name AS branch_name
            ')
            ->join('users', 'users.id = sales.user_id', 'left')
            ->join('branches', 'branches.id = sales.branch_id', 'left')
            ->where('sales.id', $id)
            ->first();
    }
    public function getSalesChartMonthly($branchId = null)
    {
        $builder = $this->db->table($this->table)
            ->select('DATE_FORMAT(date, "%Y-%m") AS month, SUM(total) AS total')
            ->groupBy('month')
            ->orderBy('month', 'ASC');

        if ($branchId !== null) {
            $builder->where('branch_id', $branchId);
        }

        return $builder->get()->getResultArray();
    }

}
