<?php

// app/Database/Seeds/UserSeeder.php
namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $data = [
             [
        'name'       => 'Admin Pusat',
        'email'      => 'admin@gmail.com',
        'password'   => password_hash('password', PASSWORD_DEFAULT),
        'password_display' => 'password',
        'role'       => 'pusat', // bisa kelola semua cabang
        'branch_id'  => null, 
        'photo'      => null,
    ],
    [
        'name'       => 'Ikhlas',
        'email'      => 'ikhlas@gmail.com',
        'password'   => password_hash('password', PASSWORD_DEFAULT),
        'password_display' => 'password',
        'role'       => 'cabang', // hanya bisa melakukan penjualan di cabang Padang
        'branch_id'  => 2, 
        'photo'      => null,
    ],
    [
        'name'       => 'Dapa',
        'email'      => 'daffa@gmail.com',
        'password'   => password_hash('password', PASSWORD_DEFAULT),
        'password_display' => 'password',
        'role'       => 'cabang', // hanya bisa melakukan penjualan di cabang Jakarta
        'branch_id'  => 3, 
        'photo'      => null,
    ],
    [
        'name' => 'Asep',
        'email' => 'asep@gmail.com',
        'password' => password_hash('password', PASSWORD_DEFAULT),
        'password_display' => 'password',
        'role' => 'cabang', // hanya bisa melakukan penjualan di cabang Jakarta
        'branch_id' => 1,
        'photo' => null,
            ],
        ];

        $this->db->table('users')->insertBatch($data);
    }
}
