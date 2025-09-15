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
        // Langsung log setiap request yang masuk ke file ini
        $requestMethod = $this->request->getMethod();
        log_message('critical', '[Webhook] Metode ' . $requestMethod . ' diterima di WebhookController.');
        $rawBody = $this->request->getBody();
        log_message('critical', '[Webhook] Body Raw: ' . $rawBody);

        // --- Bagian Verifikasi GET (Webhook Verification) ---
        if ($requestMethod === 'GET') {
            log_message('info', '[Webhook] Memproses metode GET (Verifikasi Webhook).');
            $verifyToken = getenv('whatsapp.verify_token'); // Pastikan ini diatur di .env
            $mode = $this->request->getGet('hub.mode');
            $token = $this->request->getGet('hub.verify_token');
            $challenge = $this->request->getGet('hub.challenge');

            if ($mode === 'subscribe' && $token === $verifyToken) {
                log_message('info', '[Webhook] Verifikasi GET Berhasil. Mengembalikan challenge.');
                return $this->response->setStatusCode(200)->setContentType('text/plain')->setBody($challenge);
            } else {
                log_message('error', '[Webhook] Verifikasi GET Gagal. Token tidak cocok atau mode salah. Token diterima: ' . $token . ', Mode diterima: ' . $mode);
                return $this->response->setStatusCode(403, 'Forbidden - Webhook verification failed');
            }
        }

        // --- Penanganan POST (Pesan Masuk, Status, dll.) ---
        if ($requestMethod === 'POST') {
            log_message('info', '[Webhook] Memproses metode POST.');
            $body = $this->request->getJSON(true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                log_message('error', '[Webhook] Gagal mem-parsing JSON dari body: ' . json_last_error_msg());
                return $this->response->setStatusCode(400, 'Bad Request - Invalid JSON');
            }
            
            // Log payload JSON yang sudah di-parse
            log_message('info', '[Webhook] Payload JSON di-parse: ' . json_encode($body));

            // Periksa struktur payload dari Meta
            if (isset($body['entry'][0]['changes'][0]['value'])) {
                $value = $body['entry'][0]['changes'][0]['value'];

                // Cek apakah ini adalah notifikasi pesan masuk
                if (isset($value['messages'][0]) && $value['messages'][0]['type'] === 'text') {
                    $messageData = $value['messages'][0];
                    $clientNumber = $messageData['from'];
                    log_message('info', '[Webhook] Pesan teks dari ' . $clientNumber . ' akan diproses dan disimpan.');

                    try {
                        $messageModel = new WhatsappMessageModel();
                        
                        // 1. Simpan pesan masuk ke database
                        $messageModel->save([
                            'message_id'        => $messageData['id'],
                            'sender_number'     => $clientNumber,
                            'message_text'      => $messageData['text']['body'],
                            'message_timestamp' => $messageData['timestamp'],
                            'direction'         => 'in',
                            'conversation_id'   => $clientNumber // Menggunakan clientNumber sebagai conversation_id sementara
                        ]);
                        
                        // 2. Logika untuk menghitung waktu respons (jika ada pesan keluar sebelumnya)
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
                            log_message('info', '[Webhook] Waktu respons dicatat untuk ' . $clientNumber . '.');
                        }
                        
                        // 3. Update status percakapan
                        $convoData = [
                            'last_message_timestamp' => $messageData['timestamp'], 
                            'last_message_direction' => 'in'
                        ];
                        if ($conversation) {
                            $conversationModel->update($conversation['id'], $convoData);
                            log_message('info', '[Webhook] Percakapan untuk ' . $clientNumber . ' diperbarui.');
                        } else {
                            $conversationModel->insert(['client_number' => $clientNumber] + $convoData);
                            log_message('info', '[Webhook] Percakapan baru untuk ' . $clientNumber . ' dibuat.');
                        }

                        log_message('info', '[Webhook] Pesan dari ' . $clientNumber . ' berhasil diproses dan disimpan ke DB.');

                    } catch (\Exception $e) {
                        // Jika terjadi error pada database, catat di log
                        log_message('error', '[Webhook DB Error] Error saat menyimpan pesan: ' . $e->getMessage());
                        return $this->response->setStatusCode(500, 'Internal Server Error - Database issue');
                    }
                } 
                // else if (isset($value['statuses'][0])) {
                //     // Ini adalah notifikasi status (terkirim, terbaca), tidak perlu disimpan sebagai pesan masuk
                //     log_message('info', '[Webhook] Menerima notifikasi status pesan, tidak diproses sebagai pesan masuk.');
                // } 
                else {
                    log_message('warning', '[Webhook] Menerima payload POST dengan "value", tetapi bukan pesan teks yang valid atau notifikasi status yang dikenali.');
                }
            } else {
                log_message('warning', '[Webhook] Menerima payload POST, tetapi struktur "entry[0].changes[0].value" tidak ditemukan. Payload tidak diproses.');
            }
            
            // Beri respons 200 OK untuk mengonfirmasi penerimaan webhook
            return $this->response->setStatusCode(200);
        }

        // Jika bukan GET atau POST (misalnya PUT, DELETE, dll.)
        log_message('warning', '[Webhook] Menerima metode request yang tidak didukung: ' . $requestMethod . '.');
        return $this->response->setStatusCode(405, 'Method Not Allowed');
    }
}