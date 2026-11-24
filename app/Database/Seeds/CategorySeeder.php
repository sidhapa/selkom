<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $data = [
            ['name' => 'Voucher Tri'],
            ['name' => 'SP Tri'],
            ['name' => 'Voucher Axis'],
            ['name' => 'SP Axis'],
            ['name' => 'Voucher Smartfren'],
            ['name' => 'SP Smartfren'],
            ['name' => 'SP XL'],
            ['name' => 'Voucher XL'],
            ['name' => 'SP Telkomsel'],
            ['name' => 'Voucher Telkomsel'],
            ['name' => 'SP Indosat'],
            ['name' => 'Voucher Indosat'],
            ['name' => 'Kepala Charger'],
            ['name' => 'USB'],
            ['name' => 'HANDSFRE'],
        ];

        $this->db->table('categories')->insertBatch($data);
    }
}
