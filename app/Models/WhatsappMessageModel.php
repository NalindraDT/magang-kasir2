<?php

namespace App\Models;

use CodeIgniter\Model;

class WhatsappMessageModel extends Model
{
    protected $table            = 'whatsapp_messages';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'message_id',
        'sender_number',
        'message_text',
        'message_timestamp',
        'status'
    ];
    protected $useTimestamps    = true;
}