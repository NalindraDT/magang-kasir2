<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use DateTime;
use DateTimeZone;

class DokuController extends BaseController
{
    private $clientId;
    private $secretKey;
    private $isProduction = false;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        
        $this->clientId = getenv('doku.clientid');
        $this->secretKey = getenv('doku.secretkey');
    }

    /**
     * Metode utama untuk memulai pembayaran dan mendapatkan URL checkout DOKU.
     */
    public function payment()
    {
        $id_pesanan = session()->get('id_pesanan');
        $items_in_cart = model('DetailPesananModel')
            ->where('id_pesanan', $id_pesanan)
            ->where('status !=', 'Refund')
            ->findAll();

        if (empty($items_in_cart) || empty($id_pesanan)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Keranjang kosong.'])->setStatusCode(400);
        }
        
        $invoiceNumber = 'INV-' . $id_pesanan . '-' . substr(str_replace('.', '', uniqid('', true)), -6);

        $line_items = [];
        $calculated_amount = 0;
        foreach ($items_in_cart as $item) {
            $item_price = (int)$item['harga_satuan'];
            $item_quantity = (int)$item['kuantitas'];
            
            $line_items[] = [
                'name'     => $item['nama_produk'],
                'price'    => $item_price,
                'quantity' => $item_quantity
            ];
            $calculated_amount += $item_price * $item_quantity;
        }

        $requestBody = [
            'order' => [
                'invoice_number' => $invoiceNumber,
                'amount'         => $calculated_amount,
                'line_items'     => $line_items,
                'currency'       => 'IDR',
                'callback_url'   => base_url('pembeli'),
                'auto_redirect'  => true,
            ],
            'payment' => [
                'payment_due_date' => 60,
                'payment_method_types' => [
                    "VIRTUAL_ACCOUNT_BCA",
                    "VIRTUAL_ACCOUNT_BANK_MANDIRI",
                    "VIRTUAL_ACCOUNT_BRI",
                    "VIRTUAL_ACCOUNT_BNI",
                    "VIRTUAL_ACCOUNT_BANK_PERMATA",
                    "VIRTUAL_ACCOUNT_BANK_CIMB",
                    "EMONEY_SHOPEEPAY",
                    "EMONEY_OVO",
                    "EMONEY_DANA",
                    "CREDIT_CARD"
                ]
            ],
            'customer' => [
                'id'    => 'CUST-' . ($id_pesanan ?? 'UNKNOWN'),
                'name'  => 'Pelanggan Toko',
                'email' => 'pelanggan@example.com',
                'phone' => '6281234567890'
            ],
            'shipping_address' => [
                'first_name' => 'Pelanggan',
                'last_name' => 'Toko',
                'address' => 'Jalan Merdeka No. 1',
                'city' => 'Jakarta',
                'postal_code' => '12345',
                'phone' => '081234567890',
                'country_code' => 'IDN'
            ]
        ];
        
        $requestId = 'req-' . uniqid();
        $timestamp = $this->getTimestamp();
        $endpoint = '/checkout/v1/payment';
        
        $signature = $this->createSignature('POST', $endpoint, $requestBody, $timestamp, $requestId);

        $client = \Config\Services::curlrequest();
        try {
            $response = $client->post($this->getBaseUrl() . $endpoint, [
                'headers' => [
                    'Client-Id'         => $this->clientId,
                    'Request-Id'        => $requestId,
                    'Request-Timestamp' => $timestamp,
                    'Signature'         => $signature,
                    'Content-Type'      => 'application/json'
                ],
                'body' => json_encode($requestBody),
                'http_errors' => false
            ]);

            $responseBody = json_decode($response->getBody());

            if ($response->getStatusCode() === 200 && isset($responseBody->response->payment->url)) {
                session()->remove('id_pesanan');
                session()->remove('total_bayar_pesanan');
                session()->remove('keranjang');

                return $this->response->setJSON([
                    'status' => 'success',
                    'paymentUrl' => $responseBody->response->payment->url
                ]);
            } else {
                log_message('error', 'DOKU Error: ' . $response->getBody());
                $errorMessage = 'Terjadi kesalahan pada DOKU.';
                if (isset($responseBody->error_messages) && is_array($responseBody->error_messages) && !empty($responseBody->error_messages)) {
                    $errorMessage = implode(', ', $responseBody->error_messages);
                }
                return $this->response->setJSON(['status' => 'error', 'message' => $errorMessage])->setStatusCode(400);
            }

        } catch (\Exception $e) {
            log_message('error', 'DOKU Exception: ' . $e->getMessage());
            return $this->response->setJSON(['status' => 'error', 'message' => $e->getMessage()])->setStatusCode(500);
        }
    }

    /**
     * Endpoint untuk menerima notifikasi dari DOKU
     */
    public function callback()
    {
        try {
            $clientId = $this->request->getHeaderLine('Client-Id');
            $requestId = $this->request->getHeaderLine('Request-Id');
            $requestTimestamp = $this->request->getHeaderLine('Request-Timestamp');
            $signature = $this->request->getHeaderLine('Signature');
            $requestBody = $this->request->getBody();

            log_message('info', 'DOKU Notification Received: ' . $requestBody);
            log_message('info', 'DOKU Signature Header: ' . $signature);

            // Ganti endpoint target untuk validasi signature callback
            $endpointTarget = '/doku/callback'; 
            if (!$this->verifySignature($signature, $clientId, $requestId, $requestTimestamp, $requestBody, $endpointTarget)) {
                log_message('error', 'DOKU Callback Signature Mismatch.');
                return $this->response->setStatusCode(401, 'Unauthorized');
            }

            $data = json_decode($requestBody, true);
            $transactionStatus = $data['transaction']['status'] ?? 'UNKNOWN';
            $invoiceNumber = $data['order']['invoice_number'] ?? null;

            if (!$invoiceNumber) {
                throw new \Exception('Invoice number not found in DOKU notification.');
            }

            $parts = explode('-', $invoiceNumber);
            if (count($parts) < 2 || !is_numeric($parts[1])) {
                throw new \Exception('Invalid invoice number format: ' . $invoiceNumber);
            }
            $id_pesanan = $parts[1];

            $detailPesananModel = model('DetailPesananModel');
            
            if ($transactionStatus === 'SUCCESS') {
                $detailPesananModel
                    ->where('id_pesanan', $id_pesanan)
                    ->set(['status' => 'Sukses'])
                    ->update();
                log_message('info', 'Order #' . $id_pesanan . ' updated to SUCCESS.');

            } else {
                $detailPesananModel
                    ->where('id_pesanan', $id_pesanan)
                    ->set(['status' => 'Gagal'])
                    ->update();
                log_message('warning', 'Order #' . $id_pesanan . ' has status: ' . $transactionStatus);
            }

            return $this->response->setStatusCode(200, 'OK');

        } catch (\Exception $e) {
            log_message('error', 'DOKU Callback Exception: ' . $e->getMessage());
            return $this->response->setStatusCode(500, 'Internal Server Error');
        }
    }

    private function createSignature(string $httpMethod, string $endpointUrl, array $requestBody, string $timestamp, string $requestId): string
    {
        $digest = base64_encode(hash('sha256', json_encode($requestBody), true));
        $stringToSign = "Client-Id:" . $this->clientId . "\n"
                      . "Request-Id:" . $requestId . "\n"
                      . "Request-Timestamp:" . $timestamp . "\n"
                      . "Request-Target:" . $endpointUrl . "\n"
                      . "Digest:" . $digest;
        $signature = base64_encode(hash_hmac('sha256', $stringToSign, $this->secretKey, true));
        return "HMACSHA256=" . $signature;
    }

    private function verifySignature($signatureFromHeader, $clientId, $requestId, $timestamp, $requestBody, $endpointTarget): bool
    {
        if (empty($signatureFromHeader) || $clientId !== $this->clientId) {
            return false;
        }

        $digest = base64_encode(hash('sha256', $requestBody, true));
        
        $stringToSign = "Client-Id:" . $clientId . "\n"
                      . "Request-Id:" . $requestId . "\n"
                      . "Request-Timestamp:" . $timestamp . "\n"
                      . "Request-Target:" . $endpointTarget . "\n"
                      . "Digest:" . $digest;
        
        $generatedSignature = base64_encode(hash_hmac('sha256', $stringToSign, $this->secretKey, true));
        $expectedSignature = "HMACSHA256=" . $generatedSignature;
        
        log_message('debug', 'Expected Signature for Callback: ' . $expectedSignature);
        
        return hash_equals($expectedSignature, $signatureFromHeader);
    }

    private function getTimestamp(): string
    {
        return (new DateTime('now', new DateTimeZone('UTC')))->format('Y-m-d\TH:i:s\Z');
    }

    private function getBaseUrl(): string
    {
        return $this->isProduction ? 'https://api.doku.com' : 'https://api-sandbox.doku.com';
    }
}