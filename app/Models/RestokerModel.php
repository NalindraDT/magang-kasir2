<?php

namespace App\Models;

use CodeIgniter\Model;

class RestokerModel extends Model
{
    protected $table            = 'restokers';
    protected $primaryKey       = 'id_restoker';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['nama_restoker', 'kontak', 'alamat'];

    // Mengaktifkan timestamp
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
