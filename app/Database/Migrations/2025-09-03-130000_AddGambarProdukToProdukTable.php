<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddGambarProdukToProdukTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('produk', [
            'gambar_produk' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'stok',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('produk', 'gambar_produk');
    }
}