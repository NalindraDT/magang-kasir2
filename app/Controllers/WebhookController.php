<?php

namespace App\Controllers;

use App\Models\WhatsappMessageModel;
use CodeIgniter\Controller;

/**
 * @property \CodeIgniter\HTTP\IncomingRequest $request
 */
class WebhookController extends Controller
{
    public function index()
    {
        // Tangani Verifikasi Webhook (hanya untuk metode GET)
        if ($this->request->getMethod() === 'get') {
            
            $verifyToken = getenv('whatsapp.verify_token');
            
            $mode      = $this->request->getGet('hub.mode');
            $token     = $this->request->getGet('hub.verify_token');
            $challenge = $this->request->getGet('hub.challenge');

            // Periksa apakah semua parameter ada dan token cocok
            if ($mode === 'subscribe' && $token === $verifyToken) {
                log_message('info', 'WEBHOOK BERHASIL DIVERIFIKASI.');
                
                // Langsung kirim challenge sebagai respons dan hentikan eksekusi
                // Ini adalah cara paling "bersih" untuk verifikasi
                echo $challenge;
                exit();
            } else {
                log_message('error', 'VERIFIKASI GAGAL: Token tidak cocok atau parameter kurang.');
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
                $model = new WhatsappMessageModel();
                $model->save([
                    'message_id'        => $messageData['id'],
                    'sender_number'     => $messageData['from'],
                    'message_text'      => $messageData['text']['body'] ?? 'Pesan bukan teks',
                    'message_timestamp' => $messageData['timestamp'],
                ]);
            }

            // Selalu kirim respons 200 OK ke Meta untuk menandakan pesan sudah diterima
            $this->response->setStatusCode(200)->send();
            exit();
        }
    }
}