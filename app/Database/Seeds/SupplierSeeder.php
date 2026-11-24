<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SupplierSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'id' => 1,
                'name' => 'vegerofficialshop',
                'phone' => null,
                'address' => 'Jakarta',
                'created_at' => null,
                'updated_at' => '2025-12-01 09:05:14',
                'deleted_at' => null,
            ],
            [
                'id' => 2,
                'name' => 'GadgetKaca',
                'phone' => null,
                'address' => 'Jakarta Pusat',
                'created_at' => null,
                'updated_at' => '2025-12-01 09:06:22',
                'deleted_at' => '2025-12-01 09:06:22',
            ],
            [
                'id' => 3,
                'name' => 'STATION accessories',
                'phone' => null,
                'address' => 'Semarang',
                'created_at' => '2025-11-29 12:25:01',
                'updated_at' => '2025-12-01 09:05:48',
                'deleted_at' => null,
            ],
            [
                'id' => 4,
                'name' => 'Olike Authorized Official Shop',
                'phone' => null,
                'address' => 'Jakarta',
                'created_at' => '2025-12-15 07:07:15',
                'updated_at' => '2025-12-15 07:07:15',
                'deleted_at' => null,
            ],
            [
                'id' => 5,
                'name' => 'DAP Official Shop',
                'phone' => null,
                'address' => 'Jakarta',
                'created_at' => '2025-12-15 07:07:15',
                'updated_at' => '2025-12-15 07:07:15',
                'deleted_at' => null,
            ],
            [
                'id' => 6,
                'name' => 'VIVAN Official Shop',
                'phone' => null,
                'address' => 'Jakarta',
                'created_at' => '2025-12-15 07:07:15',
                'updated_at' => '2025-12-15 07:07:15',
                'deleted_at' => null,
            ],
            [
                'id' => 7,
                'name' => 'DIGI PONSEL',
                'phone' => '08123456789',
                'address' => 'Jl. Riau No.42, Padang Terubuk, Kec. Senapelan, Kota Pekanbaru, Riau 28155',
                'created_at' => '2025-12-15 07:07:15',
                'updated_at' => '2025-12-15 07:07:15',
                'deleted_at' => null,
            ],
            [
                'id' => 8,
                'name' => 'Broadcast Cell',
                'phone' => '089666066609',
                'address' => 'Jl. Hangtuah No.24, Sumahilang, Kec. Pekanbaru Kota, Kota Pekanbaru, Riau 28111',
                'created_at' => '2025-12-15 07:07:15',
                'updated_at' => '2025-12-15 07:07:15',
                'deleted_at' => null,
            ],
            [
                'id' => 9,
                'name' => 'Gatotkaca',
                'phone' => 'null',
                'address' => 'Semarang',
                'created_at' => '2025-12-15 07:07:15',
                'updated_at' => '2025-12-15 07:07:15',
                'deleted_at' => null,
            ],
        ];

        $this->db->table('suppliers')->insertBatch($data);
    }
}
