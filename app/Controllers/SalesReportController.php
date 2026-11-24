<?php

namespace App\Controllers;

use App\Models\SaleModel;
use App\Models\SaleDetailModel;
use App\Models\BranchModel;
use App\Models\UserModel;
use Dompdf\Dompdf;

class SalesReportController extends BaseController
{
    protected $saleModel;
    protected $saleDetailModel;
    protected $branchModel;
    protected $userModel;

    public function __construct()
    {
        $this->saleModel = new SaleModel();
        $this->saleDetailModel = new SaleDetailModel();
        $this->branchModel = new BranchModel();
        $this->userModel = new UserModel();
    }

    /* =====================================================
     * DAILY REPORT
     * ===================================================== */
    public function daily()
    {
        $date = $this->request->getGet('date') ?? date('Y-m-d');
        $sort = $this->request->getGet('sort') ?? 'newest';
        $role = session('role');
        $branchId = session('branch_id');

        // ✅ FLAG PUSAT
        $isPusat = ($role === 'pusat');

        // Builder utama
        $builder = $this->saleModel
            ->select("
            sales.date,
            sales.branch_id,
            branches.name AS branch_name,
            SUM(sales.total) AS total
        ")
            ->join('branches', 'branches.id = sales.branch_id')
            ->where('DATE(sales.date)', $date)
            ->groupBy('sales.date, sales.branch_id');

        // ✅ JIKA CABANG → batasi data
        if (!$isPusat) {
            $builder->where('sales.branch_id', $branchId);
        }

        // Sorting
        switch ($sort) {
            case 'oldest':
                $builder->orderBy('sales.date', 'ASC');
                break;
            case 'highest':
                $builder->orderBy('total', 'DESC');
                break;
            case 'lowest':
                $builder->orderBy('total', 'ASC');
                break;
            case 'branch_az':
                $builder->orderBy('branch_name', 'ASC');
                break;
            case 'branch_za':
                $builder->orderBy('branch_name', 'DESC');
                break;
            default:
                $builder->orderBy('sales.date', 'DESC');
        }

        // ✅ DATA CABANG (untuk view / pdf)
        $branch = null;
        if (!$isPusat) {
            $branch = $this->branchModel->find($branchId);
        }

        return view('sales/report/daily', [
            'report' => $builder->findAll(),
            'date' => $date,
            'sort' => $sort,
            'isPusat' => $isPusat, // 🔥 FIX ERROR
            'branch' => $branch,  // 🔥 FIX ERROR
        ]);
    }
    public function monthly()
    {
        $month = (int) ($this->request->getGet('month') ?? date('m'));
        $year = (int) ($this->request->getGet('year') ?? date('Y'));
        $sort = $this->request->getGet('sort') ?? 'highest';
        $role = session('role');
        $branchId = session('branch_id');

        // ✅ FIX AMBIL TAHUN (AMAN)
        $years = $this->saleModel
            ->select('YEAR(date) AS year')
            ->groupBy('YEAR(date)')
            ->orderBy('YEAR(date)', 'DESC')
            ->get()
            ->getResultArray();

        $years = array_column($years, 'year');

        // QUERY UTAMA
        $builder = $this->saleModel
            ->select("
            sales.branch_id,
            branches.name AS branch_name,
            SUM(sales.total) AS total
        ")
            ->join('branches', 'branches.id = sales.branch_id')
            ->where('MONTH(sales.date)', $month)
            ->where('YEAR(sales.date)', $year)
            ->groupBy('sales.branch_id');

        if ($role === 'cabang') {
            $builder->where('sales.branch_id', $branchId);
        }

        switch ($sort) {
            case 'lowest':
                $builder->orderBy('total', 'ASC');
                break;
            case 'branch_az':
                $builder->orderBy('branch_name', 'ASC');
                break;
            case 'branch_za':
                $builder->orderBy('branch_name', 'DESC');
                break;
            default:
                $builder->orderBy('total', 'DESC');
        }

        return view('sales/report/monthly', [
            'report' => $builder->findAll(),
            'selectedMonth' => $month,
            'selectedYear' => $year,
            'sort' => $sort,
            'years' => $years,
        ]);
    }

    /* =====================================================
     * YEARLY REPORT (FINAL – CLEAN)
     * ===================================================== */
    public function yearly()
    {
        $year = $this->request->getGet('year') ?? date('Y');
        $sort = $this->request->getGet('sort') ?? 'newest';

        $role = session('role');
        $branchId = session('branch_id');

        // ===============================
        // LIST TAHUN (UNTUK FILTER)
        // ===============================
        $years = $this->saleModel
            ->select('YEAR(date) AS year')
            ->groupBy('YEAR(date)')
            ->orderBy('YEAR(date)', 'DESC')
            ->findAll();

        // ===============================
        // BASE QUERY
        // ===============================
        $builder = $this->saleModel
            ->select('
            YEAR(sales.date) AS year,
            sales.branch_id,
            branches.name AS branch_name,
            SUM(sales.total) AS total
        ')
            ->join('branches', 'branches.id = sales.branch_id', 'left')
            ->groupBy('YEAR(sales.date), sales.branch_id');

        // ===============================
        // ROLE CABANG
        // ===============================
        if ($role === 'cabang') {
            $builder->where('sales.branch_id', $branchId);
        }

        // ===============================
        // FILTER TAHUN
        // ===============================
        if (!empty($year)) {
            $builder->where('YEAR(sales.date)', $year);
        }

        // ===============================
        // SORTING
        // ===============================
        switch ($sort) {
            case 'oldest':
                $builder->orderBy('year', 'ASC');
                break;

            case 'highest':
                $builder->orderBy('total', 'DESC');
                break;

            case 'lowest':
                $builder->orderBy('total', 'ASC');
                break;

            case 'branch_az':
                $builder->orderBy('branch_name', 'ASC');
                break;

            case 'branch_za':
                $builder->orderBy('branch_name', 'DESC');
                break;

            default: // newest
                $builder->orderBy('year', 'DESC');
        }

        // ===============================
        // RESULT
        // ===============================
        $report = $builder->findAll();

        return view('sales/report/yearly', [
            'report' => $report,
            'years' => $years,
            'year' => $year,
            'sort' => $sort,
        ]);
    }

    /* =====================================================
     * PRINT DAILY PDF (FINAL)
     * ===================================================== */
    public function printDaily()
    {
        $date = $this->request->getGet('date') ?? date('Y-m-d');
        $role = session('role');
        $branchId = session('branch_id');

        // ======================
        // ROLE CHECK
        // ======================
        $isPusat = ($role === 'pusat');

        // ======================
        // DATA CABANG
        // ======================
        $branch = null;
        if (!$isPusat) {
            $branch = $this->branchModel->find($branchId);
            if (!$branch) {
                throw new \Exception('Cabang tidak ditemukan');
            }
        }

        // ======================
        // DATA USER
        // ======================
        $usersBuilder = $this->userModel
            ->select('users.name')
            ->join('sales', 'sales.user_id = users.id')
            ->where('DATE(sales.date)', $date);

        if (!$isPusat) {
            $usersBuilder->where('sales.branch_id', $branchId);
        }

        $users = $usersBuilder
            ->groupBy('users.id')
            ->findAll();

        // ======================
        // DATA PRODUK
        // ======================
        $productsBuilder = $this->saleDetailModel
            ->select("
            product_items.name AS product_name,
            categories.name AS category_name,
            SUM(sale_details.quantity) AS qty,
            SUM(sale_details.quantity * sale_details.price) AS total
        ")
            ->join('sales', 'sales.id = sale_details.sale_id')
            ->join('product_items', 'product_items.id = sale_details.product_item_id')
            ->join('categories', 'categories.id = product_items.category_id', 'left')
            ->where('DATE(sales.date)', $date)
            ->groupBy('product_items.id');

        if (!$isPusat) {
            $productsBuilder->where('sales.branch_id', $branchId);
        }

        $products = $productsBuilder->findAll();

        // ======================
        // TOTAL
        // ======================
        $totalHarian = array_sum(array_column($products, 'total'));

        // ======================
        // PDF
        // ======================
        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml(view('sales/report/pdf_daily', [
            'date' => $date,
            'isPusat' => $isPusat,   // 🔥 FIX ERROR
            'branch' => $branch,
            'users' => $users,
            'products' => $products,
            'totalHarian' => $totalHarian,
        ]));

        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = $isPusat
            ? "laporan-harian-pusat-$date.pdf"
            : "laporan-harian-{$branch['name']}-$date.pdf";

        $dompdf->stream($filename, ['Attachment' => false]);
    }

    public function dailyDetail()
    {
        $date = $this->request->getGet('date');
        $branchId = $this->request->getGet('branch_id');

        if (!$date) {
            return redirect()->back()->with('error', 'Tanggal tidak valid');
        }

        $role = session('role');

        // 🔐 Jika CABANG → paksa branch_id dari session
        if ($role === 'cabang') {
            $branchId = session('branch_id');
        }

        // ======================
        // AMBIL DATA TRANSAKSI
        // ======================
        $sales = $this->saleModel
            ->select([
                'sales.id',
                'sales.invoice',
                'sales.date',
                'sales.created_at',
                'sales.total',
                'users.name AS cashier',
                'branches.name AS branch'
            ])
            ->join('users', 'users.id = sales.user_id')
            ->join('branches', 'branches.id = sales.branch_id')
            ->where('DATE(sales.date)', $date)
            ->where('sales.branch_id', $branchId)
            ->orderBy('sales.id', 'DESC')
            ->findAll();

        if (empty($sales)) {
            return view('sales/report/daily_detail', [
                'date' => $date,
                'branch' => '-',
                'sales' => []
            ]);
        }

        // ======================
        // DETAIL PRODUK
        // ======================
        $saleIds = array_column($sales, 'id');

        $details = $this->saleDetailModel
            ->select([
                'sale_details.sale_id',
                'product_items.name AS product',
                'sale_details.quantity',
                'sale_details.price',
                '(sale_details.quantity * sale_details.price) AS subtotal'
            ])
            ->join('product_items', 'product_items.id = sale_details.product_item_id')
            ->whereIn('sale_details.sale_id', $saleIds)
            ->findAll();

        // GROUP DETAIL PER SALE
        $detailMap = [];
        foreach ($details as $d) {
            $detailMap[$d['sale_id']][] = $d;
        }

        return view('sales/report/daily_detail', [
            'date' => $date,
            'branch' => $sales[0]['branch'] ?? '-',
            'sales' => $sales,
            'details' => $detailMap
        ]);
    }


    public function dailyProductGraph()
    {
        $date = $this->request->getGet('date') ?? date('Y-m-d');
        $role = session('role');
        $branchId = session('branch_id');

        $builder = $this->saleDetailModel
            ->select([
                'product_items.name AS product',
                'branches.name AS branch',
                'SUM(sale_details.quantity) AS qty'
            ])
            ->join('sales', 'sales.id = sale_details.sale_id')
            ->join('product_items', 'product_items.id = sale_details.product_item_id')
            ->join('branches', 'branches.id = sales.branch_id')
            ->where('DATE(sales.date)', $date);

        // 🔐 BATASI CABANG JIKA ROLE = CABANG
        if ($role === 'cabang') {
            $builder->where('sales.branch_id', $branchId);
        }

        $rows = $builder
            ->groupBy('product_items.id, sales.branch_id')
            ->orderBy('product_items.name')
            ->findAll();

        if (empty($rows)) {
            return view('sales/report/daily_product_graph', [
                'date' => $date,
                'labels' => [],
                'datasets' => []
            ]);
        }

        // ======================
        // FORMAT DATA (SAMA)
        // ======================
        $products = [];
        $branches = [];
        $map = [];

        foreach ($rows as $r) {
            $products[$r['product']] = true;
            $branches[$r['branch']] = true;
            $map[$r['branch']][$r['product']] = (int) $r['qty'];
        }

        // ======================
        // DATASET PER CABANG
        // ======================
        $colors = ['red', 'blue', 'green', 'orange', 'purple', 'brown'];
        $datasets = [];
        $i = 0;

        foreach (array_keys($branches) as $branch) {
            $datasets[] = [
                'label' => $branch,
                'borderColor' => $colors[$i++ % count($colors)],
                'fill' => false,
                'data' => array_map(
                    fn($p) => $map[$branch][$p] ?? 0,
                    array_keys($products)
                )
            ];
        }

        return view('sales/report/daily_product_graph', [
            'date' => $date,
            'labels' => array_keys($products),
            'datasets' => $datasets
        ]);
    }


    public function printMonthly()
    {
        $month = (int) ($this->request->getGet('month') ?? date('m'));
        $year = (int) ($this->request->getGet('year') ?? date('Y'));
        $branchId = $this->request->getGet('branch_id');

        $role = session('role');

        // ===============================
        // CABANG
        // ===============================
        if ($role === 'pusat') {
            $branch = $this->branchModel->find($branchId);
            if (!$branch) {
                throw new \Exception('Cabang tidak ditemukan');
            }
        } else {
            $branchId = session('branch_id');
            $branch = $this->branchModel->find($branchId);
        }

        $branchName = $branch['name'];

        // ===============================
        // USERS CABANG
        // ===============================
        $users = $this->userModel
            ->where('branch_id', $branchId)
            ->where('role', 'cabang')
            ->findAll();

        $userNames = array_column($users, 'name');

        // ===============================
        // SALES HEADER (BULANAN)
        // ===============================
        $sales = $this->saleModel
            ->where('branch_id', $branchId)
            ->where('MONTH(date)', $month)
            ->where('YEAR(date)', $year)
            ->orderBy('date', 'ASC')
            ->findAll();

        // ===============================
        // DETAIL PER SALE
        // ===============================
        $totalAmount = 0;

        foreach ($sales as &$sale) {

            $details = $this->saleDetailModel
                ->select('
                product_items.name AS product_name,
                categories.name AS category_name,
                sale_details.quantity AS qty
            ')
                ->select('sale_details.quantity * sale_details.price AS total', false)
                ->join('product_items', 'product_items.id = sale_details.product_item_id')
                ->join('categories', 'categories.id = product_items.category_id', 'left')
                ->where('sale_details.sale_id', $sale['id'])
                ->findAll();

            foreach ($details as $d) {
                $totalAmount += (float) $d['total'];
            }

            $sale['details'] = $details;
        }

        // ===============================
        // RENDER PDF
        // ===============================
        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml(view('sales/report/pdf_monthly', [
            'month' => $month,
            'year' => $year,
            'branchName' => $branchName,
            'users' => $userNames,
            'sales' => $sales,
            'totalAmount' => $totalAmount,
        ]));

        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $dompdf->stream(
            "laporan-penjualan-bulanan-{$branchName}-{$month}-{$year}.pdf",
            ['Attachment' => false]
        );
    }
    public function monthlyDetail()
    {
        $month = $this->request->getGet('month'); // YYYY-MM
        $branchId = $this->request->getGet('branch_id');

        if (!$month || !preg_match('/^\d{4}-\d{2}$/', $month)) {
            return redirect()->back()->with('error', 'Format bulan tidak valid');
        }

        [$year, $monthOnly] = explode('-', $month);

        $role = session('role');

        if ($role === 'cabang') {
            $branchId = session('branch_id');
        }

        // ======================
        // NAMA CABANG
        // ======================
        $branchName = '-';
        if ($branchId) {
            $branch = $this->branchModel->find($branchId);
            $branchName = $branch['name'] ?? '-';
        }

        // ======================
        // LAPORAN BULANAN GABUNG
        // ======================
        $sales = $this->saleDetailModel
            ->select([
                'product_items.name AS product',
                'sale_details.price',
                'SUM(sale_details.quantity) AS quantity',
                'SUM(sale_details.quantity * sale_details.price) AS subtotal'
            ])
            ->join('sales', 'sales.id = sale_details.sale_id')
            ->join('product_items', 'product_items.id = sale_details.product_item_id')
            ->where('MONTH(sales.date)', (int) $monthOnly)
            ->where('YEAR(sales.date)', (int) $year)
            ->where('sales.branch_id', $branchId)
            ->groupBy('sale_details.product_item_id, sale_details.price')
            ->orderBy('product_items.name', 'ASC')
            ->findAll();

        return view('sales/report/monthly_detail', [
            'month' => $month,
            'branch' => $branchName,
            'sales' => $sales
        ]);
    }


    public function monthlyProductGraph()
    {
        // ======================
        // AMANKAN FORMAT BULAN
        // ======================
        $month = $this->request->getGet('month');

        if (!$month || !preg_match('/^\d{4}-\d{2}$/', $month)) {
            $month = date('Y-m');
        }

        [$year, $monthOnly] = explode('-', $month);

        $role = session('role');
        $branchId = session('branch_id');

        // ======================
        // QUERY (IDENTIK DAILY)
        // ======================
        $builder = $this->saleDetailModel
            ->select([
                'product_items.name AS product',
                'branches.name AS branch',
                'SUM(sale_details.quantity) AS qty'
            ])
            ->join('sales', 'sales.id = sale_details.sale_id')
            ->join('product_items', 'product_items.id = sale_details.sale_id')
            ->join('branches', 'branches.id = sales.branch_id')
            ->where('MONTH(sales.date)', (int) $monthOnly)
            ->where('YEAR(sales.date)', (int) $year);

        // 🔐 BATASI DATA JIKA ROLE CABANG
        if ($role === 'cabang') {
            $builder->where('sales.branch_id', $branchId);
        }

        $rows = $builder
            ->groupBy('product_items.id, sales.branch_id')
            ->orderBy('product_items.name')
            ->findAll();

        if (empty($rows)) {
            return view('sales/report/monthly_product_graph', [
                'month' => $month,
                'labels' => [],
                'datasets' => []
            ]);
        }

        // ======================
        // FORMAT DATA (SAMA PERSIS DAILY)
        // ======================
        $products = [];
        $branches = [];
        $map = [];

        foreach ($rows as $r) {
            $products[$r['product']] = true;
            $branches[$r['branch']] = true;
            $map[$r['branch']][$r['product']] = (int) $r['qty'];
        }

        // ======================
        // DATASET PER CABANG
        // ======================
        $colors = ['red', 'blue', 'green', 'orange', 'purple', 'brown'];
        $datasets = [];
        $i = 0;

        foreach (array_keys($branches) as $branch) {
            $datasets[] = [
                'label' => $branch,
                'borderColor' => $colors[$i++ % count($colors)],
                'fill' => false,
                'data' => array_map(
                    fn($product) => $map[$branch][$product] ?? 0,
                    array_keys($products)
                )
            ];
        }

        return view('sales/report/monthly_product_graph', [
            'month' => $month,
            'labels' => array_keys($products),
            'datasets' => $datasets
        ]);
    }


    /* =====================================================
     * PRINT YEARLY PDF (FINAL)
     * ===================================================== */
    public function printYearly()
    {
        $year = (int) ($this->request->getGet('year') ?? date('Y'));
        $role = session('role');
        $branchId = $this->request->getGet('branch_id') ?? session('branch_id');

        // ===============================
        // CABANG
        // ===============================
        if ($role === 'pusat') {
            $branch = $this->branchModel->find($branchId);
            if (!$branch) {
                throw new \Exception('Cabang tidak ditemukan');
            }
            $branchName = $branch['name'];
        } else {
            $branchId = session('branch_id');
            $branch = $this->branchModel->find($branchId);
            $branchName = $branch['name'];
        }

        // ===============================
        // USERS YANG MELAKUKAN TRANSAKSI
        // ===============================
        $users = $this->userModel
            ->select('users.name')
            ->join('sales', 'sales.user_id = users.id')
            ->where('YEAR(sales.date)', $year)
            ->where('sales.branch_id', $branchId)
            ->groupBy('users.id')
            ->findColumn('name');

        // ===============================
        // SALES HEADER
        // ===============================
        $sales = $this->saleModel
            ->where('branch_id', $branchId)
            ->where('YEAR(date)', $year)
            ->orderBy('date', 'ASC')
            ->findAll();

        // ===============================
        // DETAIL PER SALE
        // ===============================
        $totalAmount = 0;

        foreach ($sales as &$sale) {

            $details = $this->saleDetailModel
                ->select([
                    'product_items.name AS product_name',
                    'categories.name AS category_name',
                    'sale_details.quantity AS qty',
                    '(sale_details.quantity * sale_details.price) AS total'
                ])
                ->join('product_items', 'product_items.id = sale_details.product_item_id')
                ->join('categories', 'categories.id = product_items.category_id', 'left')
                ->where('sale_details.sale_id', $sale['id'])
                ->findAll();

            foreach ($details as $d) {
                $totalAmount += $d['total'];
            }

            $sale['details'] = $details;
        }

        // ===============================
        // RENDER PDF
        // ===============================
        $dompdf = new Dompdf();
        $dompdf->loadHtml(view('sales/report/pdf_yearly', [
            'year' => $year,
            'branchName' => $branchName,
            'users' => $users,
            'sales' => $sales,
            'totalAmount' => $totalAmount,
        ]));

        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = "laporan-penjualan-tahunan-{$branchName}-{$year}.pdf";

        $dompdf->stream($filename, ['Attachment' => false]);
    }

    public function yearlyDetail()
    {
        $year = $this->request->getGet('year'); // YYYY
        $branchId = $this->request->getGet('branch_id');

        if (!$year || !preg_match('/^\d{4}$/', $year)) {
            return redirect()->back()->with('error', 'Format tahun tidak valid');
        }

        $role = session('role');

        if ($role === 'cabang') {
            $branchId = session('branch_id');
        }

        // ======================
        // NAMA CABANG
        // ======================
        $branchName = '-';
        if ($branchId) {
            $branch = $this->branchModel->find($branchId);
            $branchName = $branch['name'] ?? '-';
        }

        // ======================
        // LAPORAN TAHUNAN (GABUNG)
        // ======================
        $sales = $this->saleDetailModel
            ->select([
                'product_items.name AS product',
                'sale_details.price',
                'SUM(sale_details.quantity) AS quantity',
                'SUM(sale_details.quantity * sale_details.price) AS subtotal'
            ])
            ->join('sales', 'sales.id = sale_details.sale_id')
            ->join('product_items', 'product_items.id = sale_details.product_item_id')
            ->where('YEAR(sales.date)', (int) $year)
            ->where('sales.branch_id', $branchId)
            ->groupBy('sale_details.product_item_id, sale_details.price')
            ->orderBy('product_items.name', 'ASC')
            ->findAll();

        return view('sales/report/yearly_detail', [
            'year' => $year,
            'branch' => $branchName,
            'sales' => $sales
        ]);
    }

    public function yearlyProductGraph()
    {
        // ======================
        // AMANKAN TAHUN
        // ======================
        $year = $this->request->getGet('year');

        if (!$year || !preg_match('/^\d{4}$/', $year)) {
            $year = date('Y');
        }

        $role = session('role');
        $branchId = session('branch_id');

        // ======================
        // QUERY (IDENTIK MONTHLY)
        // ======================
        $builder = $this->saleDetailModel
            ->select([
                'product_items.name AS product',
                'branches.name AS branch',
                'SUM(sale_details.quantity) AS qty'
            ])
            ->join('sales', 'sales.id = sale_details.sale_id')
            ->join('product_items', 'product_items.id = sale_details.product_item_id')
            ->join('branches', 'branches.id = sales.branch_id')
            ->where('YEAR(sales.date)', (int) $year);

        // 🔐 BATASI DATA JIKA CABANG
        if ($role === 'cabang') {
            $builder->where('sales.branch_id', $branchId);
        }

        $rows = $builder
            ->groupBy('product_items.id, sales.branch_id')
            ->orderBy('product_items.name')
            ->findAll();

        if (empty($rows)) {
            return view('sales/report/yearly_product_graph', [
                'year' => $year,
                'labels' => [],
                'datasets' => []
            ]);
        }

        // ======================
        // FORMAT DATA
        // ======================
        $products = [];
        $branches = [];
        $map = [];

        foreach ($rows as $r) {
            $products[$r['product']] = true;
            $branches[$r['branch']] = true;
            $map[$r['branch']][$r['product']] = (int) $r['qty'];
        }

        // ======================
        // DATASET PER CABANG
        // ======================
        $colors = ['red', 'blue', 'green', 'orange', 'purple', 'brown'];
        $datasets = [];
        $i = 0;

        foreach (array_keys($branches) as $branch) {
            $datasets[] = [
                'label' => $branch,
                'borderColor' => $colors[$i++ % count($colors)],
                'fill' => false,
                'data' => array_map(
                    fn($product) => $map[$branch][$product] ?? 0,
                    array_keys($products)
                )
            ];
        }

        return view('sales/report/yearly_product_graph', [
            'year' => $year,
            'labels' => array_keys($products),
            'datasets' => $datasets
        ]);
    }


}
