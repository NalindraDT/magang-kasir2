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
        // Tangani Verifikasi Webhook (hanya untuk metode GET)
        if ($this->request->getMethod() === 'get') {
            $verifyToken = getenv('whatsapp.verify_token');
            
            $mode = $this->request->getGet('hub.mode');
            $token = $this->request->getGet('hub.verify_token');
            $challenge = $this->request->getGet('hub.challenge');

            if ($mode === 'subscribe' && $token === $verifyToken) {
                // Bersihkan semua output buffer yang mungkin aktif
                while (ob_get_level() > 0) {
                    ob_end_clean();
                }
                
                // Kirim header HTTP secara manual untuk memastikan
                header('HTTP/1.1 200 OK');
                header('Content-Type: text/plain');
                
                // Cetak challenge dan hentikan eksekusi
                echo $challenge;
                exit();
            } else {
                $this->response->setStatusCode(403)->send();
                exit();
            }
        }

        // Tangani Pesan Masuk (hanya untuk metode POST)
        if ($this->request->getMethod() === 'post') {
            $body = $this->request->getJSON(true);
            log_message('info', 'Pesan masuk diterima: ' . json_encode($body));

            if (isset($body['entry'][0]['changes'][0]['value']['messages'][0])) {
                $messageData = $body['entry'][0]['changes'][0]['value']['messages'][0];
                
                $messageModel = new WhatsappMessageModel();
                $messageModel->save([
                    'message_id'        => $messageData['id'],
                    'sender_number'     => $messageData['from'],
                    'message_text'      => $messageData['text']['body'] ?? 'Pesan bukan teks',
                    'message_timestamp' => $messageData['timestamp'],
                    'direction'         => 'in',
                    'conversation_id'   => $messageData['from']
                ]);

                $conversationModel = new ConversationModel();
                $conversation = $conversationModel->where('client_number', $messageData['from'])->first();

                if ($conversation && $conversation['last_message_direction'] === 'out') {
                    $responseTime = (int)$messageData['timestamp'] - (int)$conversation['last_message_timestamp'];

                    $responseTimeModel = new ResponseTimeModel();
                    $responseTimeModel->save([
                        'conversation_id'       => $messageData['from'],
                        'response_time_seconds' => $responseTime,
                        'response_direction'    => 'client_to_operator'
                    ]);
                }

                $convoData = [
                    'client_number'            => $messageData['from'],
                    'last_message_timestamp'   => $messageData['timestamp'],
                    'last_message_direction'   => 'in'
                ];

                if ($conversation) {
                    $conversationModel->update($conversation['id'], $convoData);
                } else {
                    $conversationModel->insert($convoData);
                }
            }

            $this->response->setStatusCode(200)->send();
            exit();
        }
    }
}