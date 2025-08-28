<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class DokuController extends BaseController
{
    // Hapus type-hint agar tidak error, karena kita akan memuat kelas secara manual
    private $dokuClient;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        // Muat file kelas DOKU secara manual
        require_once ROOTPATH . 'vendor/doku/doku-php-library/src/Snap.php';
        require_once ROOTPATH . 'vendor/doku/doku-php-library/src/Snap/VA/PaymentRequest.php';
        require_once ROOTPATH . 'vendor/doku/doku-php-library/src/Snap/Utils/Signature.php';

        $merchantPrivateKey = file_get_contents(ROOTPATH . 'keys/private.key');
        $dokuPublicKey = file_get_contents(ROOTPATH . 'keys/public.pem');

        // Gunakan namespace lengkap
        $this->dokuClient = new \Doku\Snap(
            getenv('doku.clientid'),
            getenv('doku.secretkey'),
            $dokuPublicKey,
            $merchantPrivateKey
        );
        $this->dokuClient->isProduction(false);
    }

    public function payment()
    {
        $total_bayar = session()->get('total_bayar_pesanan');
        $id_pesanan = session()->get('id_pesanan');

        if (empty($total_bayar) || empty($id_pesanan)) {
            session()->setFlashdata('error', 'Keranjang belanja kosong atau data tidak valid.');
            return redirect()->to(base_url('pembeli'));
        }

        $request = new \Doku\Snap\VA\PaymentRequest();
        $request->setInvoiceNumber('INV-' . $id_pesanan . '-' . date('YmdHis'));
        $request->setAmount($total_bayar);
        $request->setCurrency('IDR');
        $request->setCustomerEmail('pelanggan@example.com');
        $request->setCustomerName('Pelanggan Toko');
        $request->setPaymentChannel('VA');

        try {
            $response = $this->dokuClient->createVa($request);
            
            session()->setFlashdata('message', 'Pembayaran VA berhasil dibuat: ' . $response['va_number']);
            return redirect()->to(base_url('pembeli'));
            
        } catch (\Exception $e) {
            session()->setFlashdata('error', 'Gagal memproses pembayaran: ' . $e->getMessage());
            return redirect()->to(base_url('pembeli'));
        }
    }
    
    public function callback()
    {
        $request_body = $this->request->getBody();
        $notification = json_decode($request_body, true);

        $signature = $this->request->getHeaderLine('X-Signature');
        $timestamp = $this->request->getHeaderLine('X-Timestamp');
        
        if (\Doku\Snap\Utils\Signature::verifySignature($request_body, $signature, getenv('doku.dokupublickey'), $timestamp)) {
            $order_status = $notification['transaction']['status'] ?? 'UNKNOWN';

            if ($order_status === 'SUCCESS') {
                $id_pesanan = $notification['transaction']['session_id'];
                
                return $this->response->setStatusCode(200);
            }
        }
        
        return $this->response->setStatusCode(400);
    }
}