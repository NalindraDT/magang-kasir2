<?php

namespace App\Controllers;

use App\Models\ResponseTimeModel;

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
}