<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSales extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'unsigned' => true,
                'auto_increment' => true,
            ],

            'invoice' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'unique' => true, // ✅ cukup ini saja
            ],

            'branch_id' => [
                'type' => 'INT',
                'unsigned' => true,
                'null' => true,
            ],

            'user_id' => [
                'type' => 'INT',
                'unsigned' => true,
            ],

            'date' => [
                'type' => 'DATE',
            ],

            'total' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => 0,
            ],

            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],

            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);


        $this->forge->addForeignKey(
            'branch_id',
            'branches',
            'id',
            'SET NULL',
            'RESTRICT'
        );

        $this->forge->createTable('sales');
    }

    public function down()
    {
        $this->forge->dropTable('sales');
    }
}
