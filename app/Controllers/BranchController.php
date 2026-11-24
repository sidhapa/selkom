<?php

namespace App\Controllers;

use App\Models\BranchModel;
use App\Models\UserModel;
use App\Models\StockAllocationModel;
use App\Models\StockAllocationDetailModel;
use App\Models\BranchOrderModel;
use App\Models\BranchOrderDetailModel;
use App\Models\StockInitialModel;

class BranchController extends BaseController
{
    protected $branchModel;
    protected $perPage = 10;

    public function __construct()
    {
        $this->branchModel = new BranchModel();
    }

    // List Cabang + Search + Sort + Pagination + Show Deleted Toggle
    public function index()
    {
        // hanya pusat boleh akses daftar cabang
        if (session()->get('role') !== 'pusat') {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak!');
        }

        $search = $this->request->getGet('search');
        $sort = $this->request->getGet('sort') ?? 'name';
        $order = strtoupper($this->request->getGet('order') ?? 'ASC');
        $showDeleted = $this->request->getGet('show_deleted') ? true : false;
        $perPage = $this->perPage;

        // Siapkan builder, gunakan withDeleted() jika ingin tampilkan yang sudah dihapus
        $builder = $this->branchModel;

        if ($showDeleted) {
            // Model harus punya softDeletes = true
            $builder = $builder->onlyDeleted();
        }

        // Search
        if (!empty($search)) {
            $builder = $builder->groupStart()
                ->like('name', $search)
                ->orLike('address', $search)
                ->orLike('city_code', $search)
                ->groupEnd();
        }

        // Sorting (aman: hanya izinkan kolom tertentu)
        $allowedSort = ['name', 'address', 'city_code'];
        if (in_array($sort, $allowedSort)) {
            $order = ($order === 'DESC') ? 'DESC' : 'ASC';
            $builder = $builder->orderBy($sort, $order);
        } else {
            $builder = $builder->orderBy('name', 'ASC');
        }

        $branches = $builder->paginate($perPage, 'branches');

        $data = [
            'title' => 'Data Cabang',
            'branches' => $branches,
            'pager' => $this->branchModel->pager,
            'search' => $search,
            'sort' => $sort,
            'order' => $order,
            'perPage' => $perPage,
            'currentPage' => $this->request->getVar('page_branches') ?? 1,
            'userRole' => session()->get('role'),
            'showDeleted' => $showDeleted,
        ];

        return view('branches/index', $data);
    }

    public function create()
    {
        if (session()->get('role') !== 'pusat') {
            return redirect()->to('/branches')->with('error', 'Akses ditolak!');
        }

        return view('branches/create', [
            'title' => 'Tambah Cabang'
        ]);
    }

    public function store()
    {
        if (session()->get('role') !== 'pusat') {
            return redirect()->to('/branches')->with('error', 'Akses ditolak!');
        }

        $rules = [
            'name' => 'required|min_length[2]',
            'address' => 'required',
            'city_code' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->branchModel->save([
            'name' => $this->request->getPost('name'),
            'address' => $this->request->getPost('address'),
            'city_code' => $this->request->getPost('city_code')
        ]);

        return redirect()->to('/branches')->with('success', 'Cabang berhasil ditambahkan.');
    }

    public function edit($id = null)
    {
        if (session()->get('role') !== 'pusat') {
            return redirect()->to('/branches')->with('error', 'Akses ditolak!');
        }

        $branch = $this->branchModel->find($id);
        if (!$branch) {
            return redirect()->to('/branches')->with('error', 'Cabang tidak ditemukan.');
        }

        return view('branches/edit', [
            'title' => 'Edit Cabang',
            'branch' => $branch
        ]);
    }

    public function update($id = null)
    {
        if (session()->get('role') !== 'pusat') {
            return redirect()->to('/branches')->with('error', 'Akses ditolak!');
        }

        $branch = $this->branchModel->find($id);
        if (!$branch) {
            return redirect()->to('/branches')->with('error', 'Cabang tidak ditemukan.');
        }

        $rules = [
            'name' => 'required|min_length[2]',
            'address' => 'required',
            'city_code' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->branchModel->update($id, [
            'name' => $this->request->getPost('name'),
            'address' => $this->request->getPost('address'),
            'city_code' => $this->request->getPost('city_code')
        ]);

        return redirect()->to('/branches')->with('success', 'Cabang berhasil diperbarui.');
    }

    // Soft delete (jika model support softDeletes)
    public function delete($id = null)
    {
        if (session()->get('role') !== 'pusat') {
            return redirect()->to('/branches')->with('error', 'Akses ditolak!');
        }

        $branch = $this->branchModel->find($id);
        if (!$branch) {
            return redirect()->to('/branches')->with('error', 'Cabang tidak ditemukan.');
        }

        $db = \Config\Database::connect();

        // =============================================
        // CEK: apakah masih ada USER aktif di cabang ini?
        // =============================================
        $userCount = $db->table('users')
            ->where('branch_id', $id)
            ->where('deleted_at', null)
            ->countAllResults();

        if ($userCount > 0) {
            return redirect()->to('/branches')
                ->with('error',
                    'Cabang <strong>"' . esc($branch['name']) . '"</strong> tidak dapat dihapus karena masih ada <strong>' . $userCount . ' user aktif</strong> yang terdaftar di cabang ini. ' .
                    'Pindahkan atau hapus user tersebut terlebih dahulu melalui menu <a href="/users" class="alert-link">Manajemen User</a>.'
                );
        }

        // =============================================
        // HAPUS DATA CABANG (semua terkait branch_id ini)
        // Penjualan (sales) dan detail penjualan tetap DIBIARKAN sebagai cache riwayat
        // =============================================
        $db->transBegin();

        try {
            // 1. Hapus branch_order_details lewat branch_orders cabang ini
            $orderIds = $db->table('branch_orders')
                ->select('id')
                ->where('branch_id', $id)
                ->get()->getResultArray();

            if (!empty($orderIds)) {
                $ids = array_column($orderIds, 'id');
                $db->table('branch_order_details')->whereIn('branch_order_id', $ids)->delete();
            }

            // 2. Hapus branch_orders
            $db->table('branch_orders')->where('branch_id', $id)->delete();

            // 3. Hapus detail alokasi lewat stock_allocations cabang ini
            $allocIds = $db->table('stock_allocations')
                ->select('id')
                ->where('branch_id', $id)
                ->get()->getResultArray();

            if (!empty($allocIds)) {
                $allocIdArr = array_column($allocIds, 'id');
                $db->table('stock_allocation_details')->whereIn('allocation_id', $allocIdArr)->delete();
            }

            // 4. Hapus stock_allocations
            $db->table('stock_allocations')->where('branch_id', $id)->delete();

            // 5. Hapus stock_initial (stok cabang)
            $db->table('stock_initial')->where('branch_id', $id)->delete();

            // 6. Hapus stock_cards cabang ini
            $db->table('stock_cards')->where('branch_id', $id)->delete();

            // 7. Soft delete cabang (sales tetap ada sebagai cache riwayat)
            $this->branchModel->delete($id);

            $db->transCommit();

        } catch (\Throwable $e) {
            $db->transRollback();
            log_message('error', 'DELETE BRANCH ERROR: ' . $e->getMessage());
            return redirect()->to('/branches')
                ->with('error', 'Terjadi kesalahan saat menghapus cabang. Silakan coba lagi.');
        }

        return redirect()->to('/branches')
            ->with('success',
                'Cabang "' . $branch['name'] . '" berhasil dihapus. Riwayat penjualan tetap tersimpan sebagai arsip.'
            );
    }

    // Restore from soft delete
    public function restore($id = null)
    {
        if (session()->get('role') !== 'pusat') {
            return redirect()->to('/branches')->with('error', 'Akses ditolak!');
        }

        // Pastikan model support withDeleted/onlyDeleted
        // update deleted_at = null untuk restore
        $branch = $this->branchModel->onlyDeleted()->find($id);
        if (!$branch) {
            return redirect()->to('/branches')->with('error', 'Cabang tidak ditemukan atau tidak berada di daftar terhapus.');
        }

        $this->branchModel->update($id, ['deleted_at' => null]);

        return redirect()->to('/branches')->with('success', 'Cabang berhasil dipulihkan.');
    }
}
