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
        'recipient_number', // Tambahkan ini
        'message_text',
        'direction',        // Tambahkan ini
        'conversation_id',  // Tambahkan ini
        'message_timestamp',
        'status'
    ];
    protected $useTimestamps    = false;
}
