<?php

namespace App\Controllers;

use App\Models\AlokasiModel;
use App\Models\ProdukModel;
use Myth\Auth\Models\UserModel;

class Alokasi extends BaseController
{
    protected $alokasiModel;
    protected $produkModel;
    protected $userModel;

    public function __construct()
    {
        $this->alokasiModel = new AlokasiModel();
        $this->produkModel = new ProdukModel();
        $this->userModel = new UserModel();
    }

    public function index()
    {
        $search = $this->request->getGet('q');
        $builder = $this->alokasiModel->getWithProdukUser();

        if (!in_groups('pusat')) {
            $builder->where('ke_user_id', user_id());
        }

        if ($search) {
            $builder->like('produk.nama_produk', $search)->orLike('users.nama', $search);
        }

        $data['alokasi'] = $builder->orderBy('alokasi.created_at', 'DESC')->paginate(10);
        $data['pager'] = $this->alokasiModel->pager;
        $data['search'] = $search;

        return view('alokasi/index', $data);
    }

    public function create()
    {
        $data['produk'] = $this->produkModel->findAll();
        $data['users'] = $this->userModel->where('id !=', user_id())->findAll();
        return view('alokasi/create', $data);
    }

    public function store()
    {
        $this->alokasiModel->save([
            'produk_id' => $this->request->getPost('produk_id'),
            'ke_user_id' => $this->request->getPost('ke_user_id'),
            'jumlah' => $this->request->getPost('jumlah'),
            'keterangan' => $this->request->getPost('keterangan'),
            'status' => 'menunggu'
        ]);

        return redirect()->to('/alokasi')->with('success', 'Alokasi berhasil dibuat');
    }

    public function updateStatus($id, $status)
    {
        $alokasi = $this->alokasiModel->find($id);

        if ($alokasi['ke_user_id'] != user_id()) {
            return redirect()->back()->with('error', 'Tidak diizinkan');
        }

        $this->alokasiModel->update($id, ['status' => $status]);
        return redirect()->to('/alokasi')->with('success', 'Status alokasi diperbarui');
    }
}
