<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateStockCards extends Migration
{
    public function up()
{
    $this->forge->addField([
        'id'         => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
        'product_item_id' => ['type' => 'INT', 'unsigned' => true],
        'branch_id'  => ['type' => 'INT', 'unsigned' => true],
        'date'       => ['type' => 'DATE'],
        'type'       => ['type' => 'ENUM', 'constraint' => ['in', 'out']],
        'quantity'   => ['type' => 'INT'],
        'description'=> ['type' => 'VARCHAR', 'constraint' => 255],
        'created_at' => ['type' => 'DATETIME', 'null' => true],
        'updated_at' => ['type' => 'DATETIME', 'null' => true],

    ]);
    $this->forge->addKey('id', true);
        $this->forge->addForeignKey('product_item_id', 'product_items', 'id', 'CASCADE', 'CASCADE');
    $this->forge->addForeignKey('branch_id', 'branches', 'id', 'CASCADE', 'CASCADE');
    $this->forge->createTable('stock_cards');
}


    public function down()
    {
        $this->forge->dropTable('stock_cards');
    }
}
