<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\BranchModel;

class UserController extends BaseController
{
    protected $userModel;
    protected $branchModel;
    protected $uploadPath;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->branchModel = new BranchModel();
        $this->uploadPath = FCPATH . 'uploads/users/';

        if (!is_dir($this->uploadPath)) {
            mkdir($this->uploadPath, 0755, true);
        }
    }

    // Daftar user dengan sorting
    public function index()
    {
        $search = $this->request->getGet('search');
        $createdDate = $this->request->getGet('created_date');
        $sort = $this->request->getGet('sort'); // Mengambil nilai sorting
        $perPage = 10;

        $builder = $this->userModel
            ->select('users.*, branches.name AS branch_name')
            ->join('branches', 'branches.id = users.branch_id', 'left');

        if ($search) {
            $builder->groupStart()
                ->like('users.name', $search)
                ->orLike('users.email', $search)
                ->orLike('users.role', $search)
                ->groupEnd();
        }

        if ($createdDate) {
            $builder->where('DATE(users.created_at)', $createdDate);
        }

        // Sorting
        switch ($sort) {
            case 'name_asc':
                $builder->orderBy('users.name', 'ASC');
                break;
            case 'name_desc':
                $builder->orderBy('users.name', 'DESC');
                break;
            case 'email_asc':
                $builder->orderBy('users.email', 'ASC');
                break;
            case 'email_desc':
                $builder->orderBy('users.email', 'DESC');
                break;
            case 'role_asc':
                $builder->orderBy('users.role', 'ASC');
                break;
            case 'role_desc':
                $builder->orderBy('users.role', 'DESC');
                break;
            default:
                $builder->orderBy('users.name', 'ASC');
                break;
        }

        $users = $builder->paginate($perPage, 'users');

        return view('users/index', [
            'title' => 'Manajemen User',
            'users' => $users,
            'pager' => $this->userModel->pager,
            'search' => $search,
            'createdDate' => $createdDate,
            'perPage' => $perPage,
            'currentPage' => $this->request->getVar('page_users') ?? 1,
            'sort' => $sort
        ]);
    }

    // Form tambah user
    public function create()
    {
        return view('users/create', [
            'title' => 'Tambah User',
            'branches' => $this->branchModel->findAll()
        ]);
    }

    // Simpan user baru
    public function store()
    {
        $role = $this->request->getPost('role');

        $rules = [
            'name' => 'required',
            'email' => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[6]',
            'role' => 'required',
            'photo' => 'is_image[photo]|max_size[photo,2048]'
        ];

        if ($role === 'cabang') {
            $rules['branch_id'] = 'required';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        $file = $this->request->getFile('photo');
        $photo = 'default.png';
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $photo = $file->getRandomName();
            $file->move($this->uploadPath, $photo);
        }

        $password = $this->request->getPost('password');

        $this->userModel->save([
            'name' => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'password_display' => $password, // << simpan password asli untuk index
            'role' => $role,
            'branch_id' => $role === 'cabang' ? $this->request->getPost('branch_id') : null,
            'photo' => $photo
        ]);

        return redirect()->to('/users')->with('success', 'User berhasil ditambahkan.');
    }

    // Update user
    public function update($id)
    {
        $user = $this->userModel->find($id);
        if (!$user)
            return redirect()->to('/users')->with('error', 'User tidak ditemukan.');

        $role = $this->request->getPost('role');

        $emailRule = ($this->request->getPost('email') != $user['email'])
            ? 'required|valid_email|is_unique[users.email]'
            : 'required|valid_email';

        $rules = [
            'name' => 'required',
            'email' => $emailRule,
            'role' => 'required',
            'photo' => 'is_image[photo]|max_size[photo,2048]'
        ];

        if (!empty($this->request->getPost('password'))) {
            $rules['password'] = 'min_length[6]';
        }

        if ($role === 'cabang') {
            $rules['branch_id'] = 'required';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        $data = [
            'name' => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
            'role' => $role,
            'branch_id' => $role === 'cabang' ? $this->request->getPost('branch_id') : null
        ];

        $file = $this->request->getFile('photo');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move($this->uploadPath, $newName);

            if (!empty($user['photo']) && $user['photo'] !== 'default.png' && file_exists($this->uploadPath . $user['photo'])) {
                unlink($this->uploadPath . $user['photo']);
            }

            $data['photo'] = $newName;
        }

        if (!empty($this->request->getPost('password'))) {
            $password = $this->request->getPost('password');
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
            $data['password_display'] = $password; // << update password asli juga
        }

        $this->userModel->update($id, $data);

        return redirect()->to('/users')->with('success', 'User berhasil diperbarui.');
    }

    // Form edit user
    public function edit($id)
    {
        $user = $this->userModel->find($id);
        if (!$user)
            return redirect()->to('/users')->with('error', 'User tidak ditemukan.');

        return view('users/edit', [
            'title' => 'Edit User',
            'user' => $user,
            'branches' => $this->branchModel->findAll()
        ]);
    }

    // Hapus foto user
    public function deletePhoto($id)
    {
        $user = $this->userModel->find($id);
        if (!$user)
            return redirect()->back()->with('error', 'User tidak ditemukan.');

        if (!empty($user['photo']) && $user['photo'] !== 'default.png') {
            $photoPath = $this->uploadPath . $user['photo'];
            if (file_exists($photoPath))
                unlink($photoPath);
        }

        $this->userModel->update($id, ['photo' => 'default.png']);

        return redirect()->to('/users/edit/' . $id)->with('success', 'Foto profil berhasil dihapus.');
    }

    // Hapus user (soft delete - tidak bisa login lagi, riwayat penjualan tetap ada)
    public function delete($id)
    {
        $user = $this->userModel->find($id);
        if (!$user) {
            return redirect()->to('/users')->with('error', 'User tidak ditemukan.');
        }

        // Jangan hapus diri sendiri
        if ($id == session()->get('user_id')) {
            return redirect()->to('/users')->with('error', 'Anda tidak dapat menghapus akun diri sendiri.');
        }

        // Hapus foto jika bukan default
        if (!empty($user['photo']) && $user['photo'] !== 'default.png' && file_exists($this->uploadPath . $user['photo'])) {
            unlink($this->uploadPath . $user['photo']);
        }

        // Ubah email agar tidak conflict saat ada user baru dengan email sama
        // Format: deleted_<timestamp>_<email_lama>
        $newEmail = 'deleted_' . time() . '_' . $user['email'];

        // Update email dulu (agar slot email bisa dipakai ulang oleh orang baru)
        $this->userModel->db->table('users')->where('id', $id)->update([
            'email' => $newEmail,
        ]);

        // Soft delete (deleted_at diisi, tidak bisa login, tapi riwayat penjualan tetap terbaca)
        $this->userModel->delete($id);

        return redirect()->to('/users')
            ->with('success', 'User "' . esc($user['name']) . '" berhasil dihapus. Riwayat transaksinya tetap tersimpan.');
    }
}
