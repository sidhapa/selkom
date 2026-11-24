<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserGroupSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'group_id' => 1, // Pusat
                'user_id' => 1  // User Pusat
            ],
            [
                'group_id' => 2, // Cabang
                'user_id' => 2  // Cabang 1
            ],
            [
                'group_id' => 2, // Cabang
                'user_id' => 3  // Cabang 2
            ],
            [
                'group_id' => 2, // Cabang
                'user_id' => 4  // Cabang 2
            ]
        ];

        $this->db->table('auth_groups_users')->insertBatch($data);
    }
}
