<?php

namespace App\Controllers;

use App\Models\ResponseTimeModel;
use App\Models\WhatsappMessageModel;
use App\Models\ConversationModel;

class ResponseReportController extends BaseController
{
    public function index()
    {
        $responseTimeModel = new ResponseTimeModel();

        // Menghitung rata-rata waktu respons operator
        $avgOperator = $responseTimeModel
            ->selectAvg('response_time_seconds', 'avg_response')
            ->where('response_direction', 'operator_to_client')
            ->first();

        // Menghitung rata-rata waktu respons klien
        $avgClient = $responseTimeModel
            ->selectAvg('response_time_seconds', 'avg_response')
            ->where('response_direction', 'client_to_operator')
            ->first();

        $data = [
            'avg_operator_response' => $avgOperator['avg_response'] ?? 0,
            'avg_client_response'   => $avgClient['avg_response'] ?? 0,
            'responses'             => $responseTimeModel->orderBy('created_at', 'DESC')->findAll(20) // Ambil 20 data terakhir
        ];

        return view('whatsapp_analyzer/report', $data);
    }
    public function clearResponseLogs()
    {
        $responseTimeModel = new ResponseTimeModel();

        // Menggunakan truncate untuk menghapus semua data dan mereset tabel
        $responseTimeModel->truncate();

        // Mengirim pesan sukses kembali ke halaman laporan
        return redirect()->to(base_url('admin/whatsapp-report'))
            ->with('message', 'Semua data log waktu respons berhasil dihapus.');
    }
    public function chatLog()
    {
        $messageModel = new WhatsappMessageModel();

        $data = [
            'messages' => $messageModel->orderBy('message_timestamp', 'DESC')->paginate(25), // 25 pesan per halaman
            'pager'    => $messageModel->pager,
        ];

        return view('whatsapp_analyzer/log_chat', $data);
    }
    public function clearChatLogs()
    {
        $messageModel = new WhatsappMessageModel();
        $conversationModel = new ConversationModel();

        // Hapus semua data dari kedua tabel
        $messageModel->truncate();
        $conversationModel->truncate();

        return redirect()->to(base_url('admin/chat-log'))
            ->with('message', 'Semua data log percakapan berhasil dihapus.');
    }
}
