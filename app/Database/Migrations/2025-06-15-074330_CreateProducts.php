<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProducts extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'unsigned' => true,
                'auto_increment' => true,
            ],

            'product_item_id' => [
                'type' => 'INT',
                'unsigned' => true,
            ],

            'supplier_id' => [
                'type' => 'INT',
                'unsigned' => true,
            ],

            // harga beli / harga dasar
            'price' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
            ],

            // stok pusat
            'quantity' => [
                'type' => 'INT',
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

            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        // Primary Key
        $this->forge->addKey('id', true);

        // Unique Key (product_item_id + supplier_id)
        $this->forge->addUniqueKey(
            ['product_item_id', 'supplier_id'],
            'uniq_product_supplier'
        );

        // FK ke master product
        $this->forge->addForeignKey(
            'product_item_id',
            'product_items',
            'id',
            'CASCADE',
            'RESTRICT'
        );

        // FK ke supplier
        $this->forge->addForeignKey(
            'supplier_id',
            'suppliers',
            'id',
            'CASCADE',
            'RESTRICT'
        );

        $this->forge->createTable('products');
    }

    public function down()
    {
        $this->forge->dropTable('products');
    }
}
