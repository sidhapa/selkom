<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateStocks extends Migration
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
            'quantity' => [
                'type' => 'INT',
                'default' => 0,
            ],
            'sell_price' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0,
            ],
            'created_at DATETIME null',
            'updated_at DATETIME null',
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey(
            'product_item_id',
            'product_items',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->forge->addUniqueKey('product_item_id', 'uniq_stock_product_item');

        $this->forge->createTable('stocks');
    }

    public function down()
    {
        $this->forge->dropTable('stocks');
    }
}
