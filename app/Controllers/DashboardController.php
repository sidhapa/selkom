<?php

namespace App\Controllers;

use App\Models\BranchModel;
use App\Models\SupplierModel;
use App\Models\SaleModel;
use App\Models\StockModel;

class DashboardController extends BaseController
{
    protected $branchModel;
    protected $supplierModel;
    protected $saleModel;
    protected $stockModel;

    public function __construct()
    {
        $this->branchModel = new BranchModel();
        $this->supplierModel = new SupplierModel();
        $this->saleModel = new SaleModel();
        $this->stockModel = new StockModel();
    }

    public function index()
    {
        /*
        |--------------------------------------------------------------------------
        | USER & ROLE
        |--------------------------------------------------------------------------
        */
        $userRole = session('role'); // pusat / cabang

        if (function_exists('user') && user()) {
            $userName = user()->name ?? user()->username ?? 'User';
        } else {
            $userName = session('name') ?? 'User';
        }

        /*
        |--------------------------------------------------------------------------
        | CABANG
        |--------------------------------------------------------------------------
        */
        $branchName = 'Pusat';
        $branchId = session('branch_id');

        if ($branchId) {
            $branch = $this->branchModel->find($branchId);
            if ($branch) {
                $branchName = $branch['name'];
            }
        }

        /*
        |--------------------------------------------------------------------------
        | TOTAL PRODUK (UNIK DARI STOCK)
        |--------------------------------------------------------------------------
        */
        $totalProduk = $this->stockModel
            ->select('product_items.id')
            ->join('product_items', 'product_items.id = stocks.product_item_id')
            ->groupBy('product_items.id')
            ->countAllResults();

        /*
        |--------------------------------------------------------------------------
        | TOTAL STOK
        |--------------------------------------------------------------------------
        */
        $stok = $this->stockModel
            ->selectSum('quantity')
            ->first();

        $totalStok = $stok['quantity'] ?? 0;

        /*
        |--------------------------------------------------------------------------
        | TOTAL CABANG & SUPPLIER
        |--------------------------------------------------------------------------
        */
        $totalCabang = $this->branchModel->countAll();
        $totalSupplier = ($userRole === 'pusat')
            ? $this->supplierModel->countAll()
            : 0;
        /*
        |--------------------------------------------------------------------------
        | PENJUALAN HARIAN (1 BULAN PENUH - TANPA LOMPAT TANGGAL)
        |--------------------------------------------------------------------------
        */
        $year = date('Y');
        $month = date('m');

        // Jumlah hari real (28 / 29 / 30 / 31)
        $totalDays = cal_days_in_month(CAL_GREGORIAN, $month, $year);

        // Ambil data penjualan dari DB
        $salesQuery = $this->saleModel
            ->select("DATE(created_at) AS tanggal, SUM(total) AS total")
            ->where("DATE_FORMAT(created_at, '%Y-%m')", "$year-$month");

        if ($userRole !== 'pusat') {
            $salesQuery->where('branch_id', $branchId);
        }

        $salesRaw = $salesQuery
            ->groupBy('tanggal')
            ->orderBy('tanggal', 'ASC')
            ->findAll();

        // Mapping tanggal => total
        $salesMap = [];
        foreach ($salesRaw as $row) {
            $salesMap[$row['tanggal']] = (float) $row['total'];
        }

        // Isi semua tanggal dalam bulan
        $penjualanHarian = [];
        for ($day = 1; $day <= $totalDays; $day++) {
            $date = sprintf('%s-%s-%02d', $year, $month, $day);

            $penjualanHarian[] = [
                'tanggal' => $date,
                'total' => $salesMap[$date] ?? 0
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | PENJUALAN BULANAN (12 BULAN TERAKHIR)
        |--------------------------------------------------------------------------
        */
        $penjualanBulanan = $this->saleModel
            ->select("DATE_FORMAT(created_at, '%Y-%m') AS bulan, SUM(total) AS total");

        if ($userRole !== 'pusat') {
            $penjualanBulanan->where('branch_id', $branchId);
        }

        $penjualanBulanan = $penjualanBulanan
            ->where('created_at >=', date('Y-m-01', strtotime('-11 months')))
            ->groupBy('bulan')
            ->orderBy('bulan', 'ASC')
            ->findAll();

        /*
        |--------------------------------------------------------------------------
        | VIEW
        |--------------------------------------------------------------------------
        */
        return view('dashboard/index', [
            'userRole' => $userRole,
            'userName' => $userName,
            'branchName' => $branchName,
            'totalProduk' => $totalProduk,
            'totalCabang' => $totalCabang,
            'totalSupplier' => $totalSupplier,
            'totalStok' => $totalStok,
            'penjualanHarian' => $penjualanHarian,
            'penjualanBulanan' => $penjualanBulanan,
        ]);
    }
}
