<?php

namespace App\Controllers;

use App\Models\UserModel;

class ProfileController extends BaseController
{
    protected $userModel;
    protected $uploadPath;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->uploadPath = FCPATH . 'uploads/users/';

        if (!is_dir($this->uploadPath)) {
            mkdir($this->uploadPath, 0755, true);
        }
    }

    public function index()
    {
        $userId = session('user_id');
        $user = $this->userModel->find($userId);

        if (!$user) {
            return redirect()->to('/dashboard')->with('error', 'User tidak ditemukan.');
        }

        return view('profile/index', [
            'title' => 'Profil Saya',
            'user' => $user,
            'validation' => \Config\Services::validation()
        ]);
    }

    public function update()
    {
        $userId = session('user_id'); // ✅ FIX: konsisten
        $user = $this->userModel->find($userId);

        if (!$user) {
            return redirect()->to('/profile')->with('error', 'User tidak ditemukan.');
        }

        /* =========================
         * HAPUS FOTO (BUTTON)
         * ========================= */
        if ($this->request->getPost('delete_photo')) {

            if (!empty($user['photo']) && $user['photo'] !== 'default.png') {
                $oldPath = $this->uploadPath . $user['photo'];
                if (is_file($oldPath)) {
                    unlink($oldPath);
                }
            }

            $this->userModel->update($userId, ['photo' => 'default.png']);
            session()->set('photo', 'default.png');

            return redirect()->to('/profile')->with('success', 'Foto profil berhasil dihapus.');
        }

        /* =========================
         * UPDATE DATA PROFIL
         * ========================= */
        $dataUpdate = [
            'name' => $this->request->getPost('name')
        ];

        // ✅ FIX PASSWORD + DISPLAY
        $password = $this->request->getPost('password');
        if (!empty($password)) {
            $dataUpdate['password'] = password_hash($password, PASSWORD_DEFAULT);
            $dataUpdate['password_display'] = $password; // ⬅️ FIX UTAMA
        }

        /* =========================
         * UPLOAD FOTO BARU
         * ========================= */
        $photo = $this->request->getFile('photo');
        if ($photo && $photo->isValid() && !$photo->hasMoved()) {

            $validExt = ['jpg', 'jpeg', 'png'];
            if (!in_array($photo->getExtension(), $validExt)) {
                return redirect()->to('/profile')
                    ->withInput()
                    ->with('error', 'Format foto harus JPG atau PNG.');
            }

            if ($photo->getSize() > 2 * 1024 * 1024) {
                return redirect()->to('/profile')
                    ->withInput()
                    ->with('error', 'Ukuran foto maksimal 2MB.');
            }

            $newName = 'user_' . $userId . '_' . time() . '.' . $photo->getExtension();
            $photo->move($this->uploadPath, $newName);

            // hapus foto lama
            if (!empty($user['photo']) && $user['photo'] !== 'default.png') {
                $oldPath = $this->uploadPath . $user['photo'];
                if (is_file($oldPath)) {
                    unlink($oldPath);
                }
            }

            $dataUpdate['photo'] = $newName;
            session()->set('photo', $newName);
        }

        $this->userModel->update($userId, $dataUpdate);

        return redirect()->to('/profile')->with('success', 'Profil berhasil diperbarui.');
    }
}
