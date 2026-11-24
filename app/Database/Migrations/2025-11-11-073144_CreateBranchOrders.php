<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBranchOrders extends Migration
{
    public function up()
    {
        /*
        |--------------------------------------------------------------------------
        | branch_orders (HEADER REQUEST)
        |--------------------------------------------------------------------------
        */
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'request_code' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'unique' => true,
            ],
            'branch_id' => [
                'type' => 'INT',
                'unsigned' => true,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['pending', 'approved', 'rejected'],
                'default' => 'pending',
            ],
            'note' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'requested_at' => [
                'type' => 'DATETIME',
            ],
            'approved_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'approved_by' => [
                'type' => 'INT',
                'unsigned' => true,
                'null' => true,
            ],
            'created_by' => [
                'type' => 'INT',
                'unsigned' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('branch_id', 'branches', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('approved_by', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('branch_orders', true);

        /*
        |--------------------------------------------------------------------------
        | branch_order_details (DETAIL REQUEST)
        |--------------------------------------------------------------------------
        */
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'branch_order_id' => [
                'type' => 'INT',
                'unsigned' => true,
            ],
            'product_item_id' => [
                'type' => 'INT',
                'unsigned' => true,
            ],

            'quantity' => [
                'type' => 'INT',
                'unsigned' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('branch_order_id', 'branch_orders', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('product_item_id','product_items','id','CASCADE','CASCADE');
        $this->forge->createTable('branch_order_details', true);
    }

    public function down()
    {
        $this->forge->dropTable('branch_order_details', true);
        $this->forge->dropTable('branch_orders', true);
    }
}
