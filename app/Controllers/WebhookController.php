<?php

namespace App\Controllers;

use App\Models\ConversationModel;
use App\Models\ResponseTimeModel;
use App\Models\WhatsappMessageModel;
use CodeIgniter\Controller;

class WebhookController extends Controller
{
    public function index()
    {
        // Bagian verifikasi GET untuk Meta
        if ($this->request->getMethod() === 'get') {
            $verifyToken = getenv('whatsapp.verify_token');
            $mode = $this->request->getGet('hub.mode');
            $token = $this->request->getGet('hub.verify_token');
            $challenge = $this->request->getGet('hub.challenge');

            if ($mode === 'subscribe' && $token === $verifyToken) {
                return $this->response->setStatusCode(200)->setContentType('text/plain')->setBody($challenge);
            } else {
                return $this->response->setStatusCode(403);
            }
        }

        // Penanganan POST saat ada pesan masuk
        if ($this->request->getMethod() === 'post') {
            $body = $this->request->getJSON(true);

            // Cek apakah ini notifikasi pesan masuk
            if (isset($body['entry'][0]['changes'][0]['value']['messages'][0])) {
                $messageData = $body['entry'][0]['changes'][0]['value']['messages'][0];
                $clientNumber = $messageData['from'];

                try {
                    $messageModel = new WhatsappMessageModel();
                    
                    // 1. Simpan pesan masuk ke database
                    $messageModel->save([
                        'message_id'        => $messageData['id'],
                        'sender_number'     => $clientNumber,
                        'message_text'      => $messageData['text']['body'] ?? 'Pesan bukan teks',
                        'message_timestamp' => $messageData['timestamp'],
                        'direction'         => 'in',
                        'conversation_id'   => $clientNumber
                    ]);
                    
                    // 2. Logika untuk menghitung waktu respons
                    $conversationModel = new ConversationModel();
                    $conversation = $conversationModel->where('client_number', $clientNumber)->first();

                    if ($conversation && $conversation['last_message_direction'] === 'out') {
                        $responseTime = (int)$messageData['timestamp'] - (int)$conversation['last_message_timestamp'];
                        $responseTimeModel = new ResponseTimeModel();
                        $responseTimeModel->save([
                            'conversation_id'       => $clientNumber,
                            'response_time_seconds' => $responseTime,
                            'response_direction'    => 'client_to_operator'
                        ]);
                    }
                    
                    // 3. Update status percakapan
                    $convoData = ['last_message_timestamp' => $messageData['timestamp'], 'last_message_direction' => 'in'];
                    if ($conversation) {
                        $conversationModel->update($conversation['id'], $convoData);
                    } else {
                        $conversationModel->insert(['client_number' => $clientNumber] + $convoData);
                    }

                } catch (\Exception $e) {
                    log_message('error', '[Webhook DB Error] ' . $e->getMessage());
                    return $this->response->setStatusCode(500);
                }
            }
            
            return $this->response->setStatusCode(200);
        }
    }
}