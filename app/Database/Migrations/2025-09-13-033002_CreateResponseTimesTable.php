<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateResponseTimesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'conversation_id' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'response_time_seconds' => [
                'type' => 'INT',
            ],
            'response_direction' => [
                'type' => 'ENUM("operator_to_client", "client_to_operator")',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('response_times');
    }

    public function down()
    {
        $this->forge->dropTable('response_times');
    }
}