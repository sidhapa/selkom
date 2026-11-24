<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddReceivedStatusToBranchOrders extends Migration
{
    public function up()
    {
        // 1. Ubah ENUM status untuk menambahkan nilai 'received'
        $this->db->query("ALTER TABLE `branch_orders` MODIFY `status` ENUM('pending','approved','rejected','received') NOT NULL DEFAULT 'pending'");

        // 2. Tambah kolom received_at
        $this->forge->addColumn('branch_orders', [
            'received_at' => [
                'type'       => 'DATETIME',
                'null'       => true,
                'after'      => 'approved_at',
            ],
        ]);

        // 3. Tambah kolom received_by (user cabang yang konfirmasi)
        $this->forge->addColumn('branch_orders', [
            'received_by' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
                'after'    => 'received_at',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('branch_orders', 'received_at');
        $this->forge->dropColumn('branch_orders', 'received_by');
        $this->db->query("ALTER TABLE `branch_orders` MODIFY `status` ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending'");
    }
}
