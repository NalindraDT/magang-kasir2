<?php

namespace App\Models;

use CodeIgniter\Model;

class RestokProdukModel extends Model
{
    protected $table            = 'restok_produk';
    protected $primaryKey       = 'id_restok';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'id_produk',
        'jumlah_pesan',
        'jumlah_diterima',
        'tanggal_pesan',
        'tanggal_diterima',
        'status',
        'jumlah_retur',
    ];

    // Mengaktifkan timestamp
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
