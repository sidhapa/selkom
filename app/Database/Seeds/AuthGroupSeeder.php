<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AuthGroupSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'name' => 'pusat',
                'description' => 'Akses Pusat'
            ],
            [
                'name' => 'cabang',
                'description' => 'Akses Cabang'
            ]
        ];

        $this->db->table('auth_groups')->insertBatch($data);
    }
}
