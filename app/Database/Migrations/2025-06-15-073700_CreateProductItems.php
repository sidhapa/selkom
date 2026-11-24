<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProductItems extends Migration
{
    public function up()
    {
        // =========================
        // TABLE: product_items
        // =========================
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],

            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
            ],

            'category_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
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

        // Index untuk performa
        $this->forge->addKey('name');
        $this->forge->addKey('category_id');

        // FK AMAN (RESTRICT, bukan CASCADE)
        $this->forge->addForeignKey(
            'category_id',
            'categories',
            'id',
            'RESTRICT',
            'RESTRICT'
        );

        $this->forge->createTable('product_items', true);
    }

    public function down()
    {
        $this->forge->dropTable('product_items', true);
    }
}
