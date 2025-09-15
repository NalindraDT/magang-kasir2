<?php

namespace App\Models;

use CodeIgniter\Model;

class ResponseTimeModel extends Model
{
    protected $table            = 'response_times';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'conversation_id',
        'response_time_seconds',
        'response_direction',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = '';
}