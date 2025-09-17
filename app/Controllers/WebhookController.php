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
        if ($this->request->getMethod() === 'GET') {
            log_message('info', '[Webhook] Memproses metode GET (Verifikasi Webhook).');
            $verifyToken = getenv('whatsapp.verify_token');
            $mode = $this->request->getGet('hub.mode');
            $token = $this->request->getGet('hub.verify_token');
            $challenge = $this->request->getGet('hub.challenge');

            if ($mode === 'subscribe' && $token === $verifyToken) {
                log_message('info', '[Webhook] Verifikasi GET Berhasil. Mengembalikan challenge.');
                return $this->response->setStatusCode(200)->setContentType('text/plain')->setBody($challenge);
            } else {
                log_message('error', '[Webhook] Verifikasi GET Gagal.');
                return $this->response->setStatusCode(403, 'Forbidden - Webhook verification failed');
            }
        }

        if ($this->request->getMethod() === 'POST') {
            log_message('info', '[Webhook] Memproses metode POST.');
            $body = $this->request->getJSON(true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                log_message('error', '[Webhook] Gagal mem-parsing JSON dari body: ' . json_last_error_msg());
                return $this->response->setStatusCode(400, 'Bad Request - Invalid JSON');
            }

            log_message('info', '[Webhook] Payload JSON di-parse: ' . json_encode($body));

            if (isset($body['entry'][0]['changes'][0]['value'])) {
                $value = $body['entry'][0]['changes'][0]['value'];

                if (isset($value['messages'][0]) && $value['messages'][0]['type'] === 'text') {
                    $this->handleIncomingMessage($value['messages'][0]);
                }
                else if (isset($value['statuses'][0])) {
                    $this->handleStatusUpdate($value['statuses'][0]);
                } else {
                    log_message('warning', '[Webhook] Menerima payload POST, tetapi bukan pesan teks atau status yang dikenali.');
                }
            } else {
                log_message('warning', '[Webhook] Menerima payload POST dengan struktur tidak valid.');
            }

            return $this->response->setStatusCode(200);
        }

        return $this->response->setStatusCode(405, 'Method Not Allowed');
    }
    
    private function handleIncomingMessage(array $messageData)
    {
        // ... (Fungsi ini tidak diubah sama sekali)
        $clientNumber = $messageData['from'];
        log_message('info', '[Webhook] Pesan teks masuk dari ' . $clientNumber . ' diproses.');

        try {
            $messageModel = new WhatsappMessageModel();
            $conversationModel = new ConversationModel();
            $responseTimeModel = new ResponseTimeModel();

            $messageModel->save([
                'message_id'        => $messageData['id'],
                'sender_number'     => $clientNumber,
                'recipient_number'  => 'OPERATOR',
                'message_text'      => $messageData['text']['body'],
                'message_timestamp' => $messageData['timestamp'],
                'direction'         => 'in',
                'conversation_id'   => $clientNumber
            ]);

            $conversation = $conversationModel->where('client_number', $clientNumber)->first();
            if ($conversation && $conversation['last_message_direction'] === 'out') {
                $responseTime = (int)$messageData['timestamp'] - (int)$conversation['last_message_timestamp'];

                if ($responseTime >= 0) {
                    $responseTimeModel->save([
                        'conversation_id'       => $clientNumber,
                        'response_time_seconds' => $responseTime,
                        'response_direction'    => 'client_to_operator'
                    ]);
                    log_message('info', '[Webhook] Waktu respons KLIEN dicatat untuk ' . $clientNumber . '.');
                }
            }

            $convoData = [
                'client_number'          => $clientNumber,
                'last_message_timestamp' => $messageData['timestamp'],
                'last_message_direction' => 'in'
            ];
            if ($conversation) {
                $conversationModel->update($conversation['id'], $convoData);
            } else {
                $conversationModel->insert($convoData);
            }
        } catch (\Exception $e) {
            log_message('error', '[Webhook DB Error] Error saat menyimpan pesan masuk: ' . $e->getMessage());
        }
    }

    private function handleStatusUpdate(array $statusData)
    {
        if ($statusData['status'] !== 'sent') {
            return;
        }

        $clientNumber = $statusData['recipient_id'];

        try {
            $conversationModel = new ConversationModel();
            $responseTimeModel = new ResponseTimeModel();
            $messageModel = new WhatsappMessageModel(); // Tambahkan ini

            // ==========================================================
            // PENAMBAHAN KODE DIMULAI DI SINI
            // ==========================================================
            // Selalu simpan jejak pesan keluar ke log, apa pun kondisinya.
            $existingMessage = $messageModel->where('message_id', $statusData['id'])->first();
            if (!$existingMessage) {
                $messageModel->save([
                    'message_id'        => $statusData['id'],
                    'sender_number'     => 'OPERATOR',
                    'recipient_number'  => $clientNumber,
                    'message_text'      => 'Message sent by operator.', // Teks generik karena isi asli tidak ada di webhook status
                    'message_timestamp' => $statusData['timestamp'],
                    'direction'         => 'out',
                    'conversation_id'   => $clientNumber,
                    'status'            => 'sent'
                ]);
            }
            // ==========================================================
            // PENAMBAHAN KODE SELESAI
            // ==========================================================
            
            // KONDISI PENTING: Logika di bawah ini tidak diubah dan tetap sama.
            $conversation = $conversationModel->where('client_number', $clientNumber)->first();
            if ($conversation && $conversation['last_message_direction'] === 'in') {
                $responseTime = (int)$statusData['timestamp'] - (int)$conversation['last_message_timestamp'];

                if ($responseTime >= 0) {
                    $responseTimeModel->save([
                        'conversation_id'       => $clientNumber,
                        'response_time_seconds' => $responseTime,
                        'response_direction'    => 'operator_to_client'
                    ]);
                    log_message('info', '[Webhook] Waktu respons OPERATOR dicatat untuk ' . $clientNumber . '.');
                }

                $conversationModel->update($conversation['id'], [
                    'last_message_timestamp' => $statusData['timestamp'],
                    'last_message_direction' => 'out'
                ]);
            } else {
                log_message('info', '[Webhook] Status "sent" untuk ' . $clientNumber . ' diterima, tetapi bukan balasan pertama. Status percakapan tidak diubah.');
            }
        } catch (\Exception $e) {
            log_message('error', '[Webhook DB Error] Error saat memproses status update: ' . $e->getMessage());
        }
    }
}