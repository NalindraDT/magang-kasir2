<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddKategoriToProduk extends Migration
{
    public function up()
    {
        $this->forge->addColumn('produk', [
            'id_kategori' => [
                'type'       => 'INT',
                'constraint' => 5,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'stok'
            ]
        ]);

        // Menambahkan foreign key secara manual setelah kolom dibuat
        $this->db->query('ALTER TABLE produk ADD CONSTRAINT produk_id_kategori_foreign FOREIGN KEY (id_kategori) REFERENCES kategori(id_kategori) ON DELETE SET NULL ON UPDATE SET NULL');
    }

    public function down()
    {
        // Hapus foreign key terlebih dahulu
        $this->forge->dropForeignKey('produk', 'produk_id_kategori_foreign');
        // Kemudian hapus kolomnya
        $this->forge->dropColumn('produk', 'id_kategori');
    }
}