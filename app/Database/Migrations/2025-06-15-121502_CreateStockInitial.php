<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateStockInitial extends Migration
{
    public function up()
{
    $this->forge->addField([
        'id'         => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
        'product_item_id' => ['type' => 'INT', 'unsigned' => true],
        'branch_id'  => ['type' => 'INT', 'unsigned' => true],
        'quantity'   => ['type' => 'INT'],
        'created_at' => ['type' => 'DATETIME', 'null' => true],
        'updated_at' => ['type' => 'DATETIME', 'null' => true],
    ]);
    $this->forge->addKey('id', true);
        $this->forge->addForeignKey('product_item_id', 'product_items', 'id', 'CASCADE', 'CASCADE');
    $this->forge->addForeignKey('branch_id', 'branches', 'id', 'CASCADE', 'CASCADE');
    $this->forge->createTable('stock_initial');
}


    public function down()
    {
        $this->forge->dropTable('stock_initial');
    }
}
