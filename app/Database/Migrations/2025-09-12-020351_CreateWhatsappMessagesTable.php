<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateWhatsappMessagesTable extends Migration
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
            'message_id' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'unique'     => true,
            ],
            'sender_number' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
            'message_text' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'message_timestamp' => [
                'type' => 'INT',
                'unsigned' => true,
            ],
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('whatsapp_messages');
    }

    public function down()
    {
        $this->forge->dropTable('whatsapp_messages');
    }
}