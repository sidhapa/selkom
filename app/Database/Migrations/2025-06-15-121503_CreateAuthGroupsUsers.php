<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAuthGroupsUsers extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'group_id' => ['type' => 'INT', 'unsigned' => true],
            'user_id' => ['type' => 'INT', 'unsigned' => true],
        ]);
        $this->forge->addForeignKey('group_id', 'auth_groups', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('auth_groups_users');
    }

    public function down()
    {
        $this->forge->dropTable('auth_groups_users');
    }
}
