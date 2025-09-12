<?php

namespace App\Models;

use CodeIgniter\Model;

class ProdukModel extends Model
{
    protected $table = 'produk';
    protected $primaryKey = 'id_produk';
    protected $useAutoIncrement = true;
    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'nama_produk',
        'harga',
        'stok',
        'id_restoker',
        'gambar_produk',
        'id_kategori',
    ];
    protected $useTimestamps = false; 
}