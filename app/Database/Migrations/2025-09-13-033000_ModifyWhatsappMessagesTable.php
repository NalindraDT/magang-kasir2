<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ModifyWhatsappMessagesTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('whatsapp_messages', [
            'direction' => [
                'type' => 'ENUM("in", "out")',
                'after' => 'message_text',
                'null' => false,
            ],
            'conversation_id' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'after' => 'id',
                'null' => true,
            ],
             'recipient_number' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'after'      => 'sender_number',
                'null'       => true,
            ],
        ]);

        // Mengubah kolom status yang sudah ada
        $this->forge->modifyColumn('whatsapp_messages', [
            'status' => [
                'type' => "ENUM('sent', 'delivered', 'read', 'replied', 'failed')",
                'null' => true,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('whatsapp_messages', ['direction', 'conversation_id', 'recipient_number']);
        
        // Mengembalikan kolom status ke definisi sebelumnya
        $this->forge->modifyColumn('whatsapp_messages', [
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
            ],
        ]);
    }
}