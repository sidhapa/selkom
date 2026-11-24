<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\BranchModel;

class AuthController extends BaseController
{
    public function login()
    {
        return view('auth/login');
    }

    public function processLogin()
    {
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $userModel = new UserModel();
        $branchModel = new BranchModel();

        $user = $userModel->where('email', $email)->first();

        if ($user) {
            if (password_verify($password, $user['password'])) {
                $branch = $branchModel->find($user['branch_id']);

                $sessionData = [
                'id' => $user['id'], // <--- tambahkan ini agar ProfileController bisa ambil session('id')
                'user_id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'role' => $user['role'],
                'branch_id' => $user['branch_id'],
                'branch_name' => $branch['name'] ?? 'Unknown',
                'photo' => $user['photo'] ?? 'profile.png',
                'isLoggedIn' => true,
];
session()->set($sessionData);

                return redirect()->to('/dashboard');
            } else {
                return redirect()->to('/login')->with('error', 'Password salah');
            }
        } else {
            return redirect()->to('/login')->with('error', 'Email tidak ditemukan');
        }
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }
}
