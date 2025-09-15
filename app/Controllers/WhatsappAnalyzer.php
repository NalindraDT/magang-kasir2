<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use DateTime;
use App\Models\WhatsappMessageModel;
use App\Models\ConversationModel;
use App\Models\ResponseTimeModel;

class WhatsappAnalyzer extends BaseController
{
    public function index()
    {
        // Menampilkan halaman form untuk upload file
        return view('whatsapp_analyzer/index');
    }

    public function kirimPesan()
    {
        $nomorTujuan = $this->request->getPost('nomor_tujuan');
        $token = getenv('whatsapp.token');
        $phoneId = getenv('whatsapp.phone_number_id'); // Mengambil phone_number_id dari .env

        // Tambahkan logging untuk debugging
        log_message('info', '[WhatsappAnalyzer] Menerima permintaan kirimPesan.');
        log_message('info', '[WhatsappAnalyzer] nomor_tujuan dari POST: ' . ($nomorTujuan ?? 'NULL'));
        log_message('info', '[WhatsappAnalyzer] phone_number_id dari .env: ' . ($phoneId ?? 'NULL'));

        if (empty($nomorTujuan) || empty($token) || empty($phoneId)) {
            $errorMessage = '';
            if (empty($nomorTujuan)) $errorMessage .= 'Nomor tujuan belum diatur. ';
            if (empty($token)) $errorMessage .= 'Token WhatsApp belum diatur. ';
            if (empty($phoneId)) $errorMessage .= 'Phone ID WhatsApp belum diatur. ';
            log_message('error', '[WhatsappAnalyzer] ' . $errorMessage);
            return redirect()->back()->with('error', trim($errorMessage));
        }

        $url = "https://graph.facebook.com/v19.0/{$phoneId}/messages";
        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $nomorTujuan,
            'type' => 'template',
            'template' => [
                'name' => 'hello_world', // Pastikan nama template ini ada di akun WhatsApp Business Anda
                'language' => [
                    'code' => 'en_US'
                ],
                'components' => [] // Wajib ada, meskipun kosong
            ]
        ];

        $client = \Config\Services::curlrequest();

        try {
            $response = $client->post($url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Content-Type' => 'application/json'
                ],
                'json' => $payload
            ]);

            $body = json_decode($response->getBody(), true);
            log_message('info', '[WhatsappAnalyzer] Respons API WhatsApp: ' . json_encode($body));

            if ($response->getStatusCode() === 200 && isset($body['messages'][0]['id'])) {
                $outgoingTimestamp = time();

                // Inisialisasi model
                $messageModel = new WhatsappMessageModel();
                $conversationModel = new ConversationModel();
                $responseTimeModel = new ResponseTimeModel();

                // --- LOGIKA PENCATATAN PESAN KELUAR DAN RESPONS OPERATOR ---

                // 1. Simpan pesan keluar ke database (whatsapp_messages)
                $messageModel->save([
                    'message_id'        => $body['messages'][0]['id'],
                    'sender_number'     => $phoneId,            // Sender adalah nomor WA Business Anda
                    'recipient_number'  => $nomorTujuan,       // Penerima adalah nomor klien
                    'message_text'      => 'Template: Hello World', // Isi template yang Anda kirim
                    'message_timestamp' => $outgoingTimestamp,
                    'direction'         => 'out',              // Ini adalah pesan KELUAR
                    'conversation_id'   => $nomorTujuan,       // Nomor tujuan sebagai conversation ID
                    'status'            => 'sent'
                ]);
                log_message('info', '[WhatsappAnalyzer] Pesan keluar berhasil disimpan ke whatsapp_messages untuk ' . $nomorTujuan . '. ID Pesan: ' . $body['messages'][0]['id']);

                // 2. Cek percakapan sebelumnya untuk menghitung waktu respons operator
                $conversation = $conversationModel->where('client_number', $nomorTujuan)->first();

                if ($conversation && $conversation['last_message_direction'] === 'in') {
                    $responseTime = $outgoingTimestamp - (int)$conversation['last_message_timestamp'];

                    $responseTimeModel->save([
                        'conversation_id'       => $nomorTujuan,
                        'response_time_seconds' => $responseTime,
                        'response_direction'    => 'operator_to_client' // Arah respons operator ke klien
                    ]);
                    log_message('info', '[WhatsappAnalyzer] Waktu respons operator dicatat untuk ' . $nomorTujuan . ': ' . $responseTime . ' detik.');
                } else {
                    log_message('info', '[WhatsappAnalyzer] Tidak ada pesan masuk klien sebelumnya untuk menghitung waktu respons operator untuk ' . $nomorTujuan . '.');
                }

                // 3. Update atau buat data percakapan baru (conversation)
                $convoData = [
                    'client_number'          => $nomorTujuan,
                    'last_message_timestamp' => $outgoingTimestamp,
                    'last_message_direction' => 'out' // Pesan terakhir adalah keluar
                ];

                if ($conversation) {
                    $conversationModel->update($conversation['id'], $convoData);
                    log_message('info', '[WhatsappAnalyzer] Percakapan untuk ' . $nomorTujuan . ' diperbarui dengan pesan keluar.');
                } else {
                    $conversationModel->insert($convoData);
                    log_message('info', '[WhatsappAnalyzer] Percakapan baru untuk ' . $nomorTujuan . ' dibuat karena operator mengirim pesan pertama.');
                }

                return redirect()->back()->with('message', 'Pesan tes berhasil dikirim dan dicatat!');
            } else {
                $errorMessage = $body['error']['message'] ?? 'Terjadi kesalahan yang tidak diketahui dari API WhatsApp.';
                log_message('error', '[WhatsappAnalyzer] Gagal mengirim pesan melalui API WhatsApp: ' . $errorMessage);
                return redirect()->back()->with('error', 'Gagal mengirim pesan: ' . $errorMessage);
            }
        } catch (\Exception $e) {
            log_message('error', '[WhatsappAnalyzer] Exception saat mengirim pesan: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Exception: ' . $e->getMessage());
        }
    }

    // Metode proses(), buatRingkasan(), dan formatDetikKeHms() tetap sama
    public function proses()
    {
        // 1. Validasi File Upload
        $validationRule = [
            'chatfile' => [
                'label' => 'Chat File',
                'rules' => 'uploaded[chatfile]|ext_in[chatfile,txt]',
            ],
            'kontakfile' => [
                'label' => 'Kontak CSV',
                'rules' => 'uploaded[kontakfile]|ext_in[kontakfile,csv]',
            ],
        ];

        if (!$this->validate($validationRule)) {
            return redirect()->back()->withInput()->with('error', $this->validator->getErrors()['chatfile'] ?? $this->validator->getErrors()['kontakfile']);
        }

        $chatFile = $this->request->getFile('chatfile');
        $kontakFile = $this->request->getFile('kontakfile');
        $namaAnda = $this->request->getPost('nama_anda');

        // 2. Baca File Kontak (kontak.csv)
        $kontakData = [];
        if ($kontakFile->isValid() && !$kontakFile->hasMoved()) {
            $file = fopen($kontakFile->getTempName(), 'r');
            // Lewati header
            fgetcsv($file);
            while (($line = fgetcsv($file)) !== FALSE) {
                // Asumsi format: Nama,NomorHP
                if (isset($line[0]) && isset($line[1])) {
                    $kontakData[trim($line[0])] = trim($line[1]);
                }
            }
            fclose($file);
        }

        // 3. Proses File Chat (.txt)
        $hasilAnalisis = [];
        $waktuPesanTerakhir = [];

        $fileHandle = fopen($chatFile->getTempName(), "r");
        if ($fileHandle) {
            while (($baris = fgets($fileHandle)) !== false) {
                // Pola Regex untuk format: DD/MM/YY HH.MM - Nama: Pesan
                if (preg_match('/(\d{2}\/\d{2}\/\d{2,4}) (\d{2}\.\d{2}) - (.*?): (.*)/', $baris, $cocok)) {
                    $tanggalStr = $cocok[1];
                    $waktuStr = str_replace('.', ':', $cocok[2]);
                    $pengirim = trim($cocok[3]);

                    $waktuPenuh = DateTime::createFromFormat('d/m/y H:i', "$tanggalStr $waktuStr");
                    if (!$waktuPenuh) {
                        $waktuPenuh = DateTime::createFromFormat('d/m/Y H:i', "$tanggalStr $waktuStr");
                    }
                    if (!$waktuPenuh) continue;

                    // Jika pengirim BUKAN Anda
                    if ($pengirim != $namaAnda) {
                        // Jika ada pesan dari Anda sebelumnya, hitung respons pelanggan
                        if (isset($waktuPesanTerakhir[$namaAnda])) {
                            $selisihDetik = $waktuPenuh->getTimestamp() - $waktuPesanTerakhir[$namaAnda]->getTimestamp();
                            $hasilAnalisis[] = [
                                'Waktu Kejadian' => $waktuPenuh,
                                'Tipe Respons' => "$pengirim -> $namaAnda",
                                'Nama' => $pengirim,
                                'Waktu Respons (Detik)' => $selisihDetik,
                            ];
                            unset($waktuPesanTerakhir[$namaAnda]);
                        }
                        $waktuPesanTerakhir[$pengirim] = $waktuPenuh;
                    }
                    // Jika pengirim adalah ANDA
                    else {
                        // Cek setiap pelanggan yang menunggu balasan
                        foreach ($waktuPesanTerakhir as $namaPelanggan => $waktuPesan) {
                            if ($namaPelanggan != $namaAnda) {
                                $selisihDetik = $waktuPenuh->getTimestamp() - $waktuPesan->getTimestamp();
                                $hasilAnalisis[] = [
                                    'Waktu Kejadian' => $waktuPenuh,
                                    'Tipe Respons' => "$namaAnda -> $namaPelanggan",
                                    'Nama' => $namaPelanggan,
                                    'Waktu Respons (Detik)' => $selisihDetik,
                                ];
                            }
                        }
                        // Kosongkan semua antrian pelanggan & set waktu terakhir Anda
                        $waktuPesanTerakhir = [$namaAnda => $waktuPenuh];
                    }
                }
            }
            fclose($fileHandle);
        }

        // Urutkan hasil berdasarkan Waktu Kejadian
        usort($hasilAnalisis, function ($a, $b) {
            return $a['Waktu Kejadian'] <=> $b['Waktu Kejadian'];
        });

        $data['rincian'] = $hasilAnalisis;
        $data['ringkasan'] = $this->buatRingkasan($hasilAnalisis, $kontakData);

        return view('whatsapp_analyzer/hasil', $data);
    }

    private function buatRingkasan($rincian, $kontakData)
    {
        if (empty($rincian)) return [];

        $ringkasan = [];
        foreach ($rincian as $item) {
            $tipe = $item['Tipe Respons'];
            if (!isset($ringkasan[$tipe])) {
                $ringkasan[$tipe] = ['total_detik' => 0, 'jumlah' => 0, 'nama' => $item['Nama']];
            }
            $ringkasan[$tipe]['total_detik'] += $item['Waktu Respons (Detik)'];
            $ringkasan[$tipe]['jumlah']++;
        }

        $hasilAkhir = [];
        foreach ($ringkasan as $tipe => $data) {
            $rataRataDetik = $data['total_detik'] / $data['jumlah'];
            $nomorHp = $kontakData[$data['nama']] ?? '-';

            $hasilAkhir[] = [
                'Tipe Respons' => $tipe,
                'NomorHP' => $nomorHp,
                'Rata-rata Respons (Detik)' => round($rataRataDetik),
                'Rata-rata Respons (JJ:MM:DD)' => $this->formatDetikKeHms($rataRataDetik),
            ];
        }
        return $hasilAkhir;
    }

    private function formatDetikKeHms($totalDetik)
    {
        $jam = floor($totalDetik / 3600);
        $sisaDetik = $totalDetik % 3600;
        $menit = floor($sisaDetik / 60);
        $detik = $sisaDetik % 60;
        return sprintf('%02d:%02d:%02d', $jam, $menit, $detik);
    }
}