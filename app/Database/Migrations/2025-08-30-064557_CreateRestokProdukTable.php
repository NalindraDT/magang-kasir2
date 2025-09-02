<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRestokProdukTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_restok' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'id_produk' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'jumlah_pesan' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'jumlah_diterima' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
            'tanggal_pesan' => [
                'type' => 'DATETIME',
            ],
            'tanggal_diterima' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['Dipesan', 'Diterima', 'Batal'],
                'default'    => 'Dipesan',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id_restok', true);
        $this->forge->addForeignKey('id_produk', 'produk', 'id_produk', 'CASCADE', 'CASCADE');
        $this->forge->createTable('restok_produk');
    }

    public function down()
    {
        $this->forge->dropTable('restok_produk');
    }
}