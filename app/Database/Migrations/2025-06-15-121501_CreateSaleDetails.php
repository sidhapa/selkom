<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSaleDetails extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'unsigned' => true,
                'auto_increment' => true,
            ],

            'sale_id' => [
                'type' => 'INT',
                'unsigned' => true,
            ],

            // INI KUNCI UTAMA TRANSAKSI
            'product_item_id' => [
                'type' => 'INT',
                'unsigned' => true,
            ],

            'quantity' => [
                'type' => 'INT',
                'unsigned' => true,
            ],

            // harga jual saat transaksi
            'price' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
            ],

            'subtotal' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
            ],

            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('sale_id');
        $this->forge->addKey('product_item_id');

        // FK ke sales
        $this->forge->addForeignKey(
            'sale_id',
            'sales',
            'id',
            'CASCADE',
            'CASCADE'
        );

        // FK ke product_items
        $this->forge->addForeignKey(
            'product_item_id',
            'product_items',
            'id',
            'RESTRICT',
            'RESTRICT'
        );

        $this->forge->createTable('sale_details');
    }

    public function down()
    {
        $this->forge->dropTable('sale_details');
    }
}
