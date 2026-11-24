<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class BranchSeeder extends Seeder
{
    public function run()
    {
        $data = [
            ['name' => 'Alysaa', 'address' => 'Jln. Suka Karya, Tuah Karya, Tampan, Pekanbaru', 'city_code' => 'ALY'],
            ['name' => 'Inara', 'address' => 'Jl. Suka Karya, Tuah Karya, Tampan, Pekanbaru', 'city_code' => 'INR'],
            ['name' => 'Aurel', 'address' => 'Tuah Karya, Tampan, Pekanbaru', 'city_code' => 'AUR'],
            ['name' => 'Safian', 'address' => 'Jln. Suka Karya, Tarai Bangun, Tambang, Kampar', 'city_code' => 'SAF'],
            ['name' => 'Selkom', 'address' => 'Jl. Terubuk No.41, Marpoyan Damai, Pekanbaru', 'city_code' => 'SLKM'],
        ];

        $this->db->table('branches')->insertBatch($data);
    }
}
