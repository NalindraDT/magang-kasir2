<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRestokerToProdukTable extends Migration
{
    public function up()
    {
        $fields = [
            'id_restoker' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true, // Dibuat nullable agar tidak error pada data yang sudah ada
                'after'      => 'stok', // Menempatkan kolom ini setelah kolom 'stok'
            ],
        ];

        $this->forge->addColumn('produk', $fields);
        
        // Menambahkan foreign key constraint setelah kolom dibuat
        $this->forge->addForeignKey('id_restoker', 'restokers', 'id_restoker', 'SET NULL', 'SET NULL');
        // Kita gunakan 'SET NULL' agar jika restoker dihapus, produknya tidak ikut terhapus, hanya relasinya saja.
    }

    public function down()
    {
        // Hapus foreign key terlebih dahulu
        $this->forge->dropForeignKey('produk', 'produk_id_restoker_foreign');
        
        // Kemudian hapus kolomnya
        $this->forge->dropColumn('produk', 'id_restoker');
    }
}