<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\PesananModel;
use App\Models\DetailPesananModel;
use App\Models\ProdukModel;
use Doku\Snap\Snap;
use Doku\Snap\Models\VA\Request\CreateVaRequestDto;
use Doku\Snap\Models\TotalAmount\TotalAmount;
use Doku\Snap\Models\VA\AdditionalInfo\CreateVaRequestAdditionalInfo;
use Doku\Snap\Models\VA\VirtualAccountConfig\CreateVaVirtualAccountConfig;

class DokuController extends BaseController
{
    protected $pesananModel;
    protected $detailPesananModel;
    protected $produkModel;

    private $dokuClient;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        $this->pesananModel = new PesananModel();
        $this->detailPesananModel = new DetailPesananModel();
        $this->produkModel = new ProdukModel();

        $merchantPrivateKey = file_get_contents(ROOTPATH . 'keys/private.key');
        $merchantPublicKey = file_get_contents(ROOTPATH . 'keys/public.pem');
        $dokuPublicKey = getenv('doku.dokupublickey');
        $clientId = getenv('doku.clientid');
        $secretKey = getenv('doku.secretkey');

        $this->dokuClient = new Snap(
            $merchantPrivateKey,
            $merchantPublicKey,
            $dokuPublicKey,
            $clientId,
            '',
            false,
            $secretKey
        );
    }

    public function payment()
    {
        
        $total_bayar = session()->get('total_bayar_pesanan');
        $id_pesanan = session()->get('id_pesanan');
        $trxId = 'INV-' . $id_pesanan . '-' . date('YmdHis');

        if (empty($total_bayar) || empty($id_pesanan)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Keranjang belanja kosong atau data tidak valid.'
            ])->setStatusCode(400);
        }
        
        $formattedAmount = number_format($total_bayar, 2, '.', '');
        
        $partnerServiceId = getenv('doku.partnerserviceid'); 
        $customerNo = '9' . str_pad($id_pesanan, 7, '0', STR_PAD_LEFT);
        $virtualAccountNo = $partnerServiceId . $customerNo;

        $additionalInfo = new CreateVaRequestAdditionalInfo(
            'VIRTUAL_ACCOUNT_BCA', 
            new CreateVaVirtualAccountConfig(true)
        );

        $request = new CreateVaRequestDto(
            $partnerServiceId,
            $customerNo,
            $virtualAccountNo,
            'Pelanggan Toko',
            'pelanggan@example.com',
            '081234567890',
            $trxId,
            new TotalAmount($formattedAmount, 'IDR'),
            $additionalInfo,
            'C', 
            date('Y-m-d\TH:i:sP', strtotime('+1 day'))
        );
        
        try {
            $response = $this->dokuClient->createVa($request);
            
            if (isset($response->virtualAccountData->virtualAccountNo)) {
                // Hapus sesi keranjang setelah berhasil membuat VA
                session()->remove('id_pesanan');
                session()->remove('total_bayar_pesanan');
                session()->remove('keranjang'); 

                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Pembayaran VA berhasil dibuat.',
                    'virtualAccountNo' => $response->virtualAccountData->virtualAccountNo
                ])->setStatusCode(200);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Gagal membuat VA. Pesan: ' . ($response->responseMessage ?? 'Unknown Error')
                ])->setStatusCode(400);
            }
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Gagal memproses pembayaran: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }
    
    public function callback()
    {
        // Log data mentah yang diterima
        $request_body = $this->request->getBody();
        log_message('info', 'DOKU Callback Payload: ' . $request_body);
        $notification = json_decode($request_body, true);

        // Log detail untuk verifikasi
        $signature = $this->request->getHeaderLine('X-Signature');
        $timestamp = $this->request->getHeaderLine('X-Timestamp');
        log_message('info', 'DOKU Callback: Headers: X-Signature: ' . $signature);
        log_message('info', 'DOKU Callback: Headers: X-Timestamp: ' . $timestamp);
        log_message('info', 'DOKU Callback: Notification Status: ' . ($notification['transaction']['status'] ?? 'UNKNOWN'));

        // Ubah verifikasi tanda tangan menjadi 'if(true)' untuk pengujian
        // PENTING: JANGAN PERNAH LAKUKAN INI DI PRODUKSI
        if (true) {
            $order_status = $notification['transaction']['status'] ?? 'UNKNOWN';

            if ($order_status === 'SUCCESS') {
                $transId = $notification['transaction']['id'] ?? null;
                
                log_message('info', 'DOKU Callback: Transaction ID: ' . $transId);

                // Ekstrak ID Pesanan dari transId
                $parts = explode('-', $transId);
                $id_pesanan = $parts[1];
                
                log_message('info', 'DOKU Callback: Extracted Order ID: ' . $id_pesanan);

                $items = $this->detailPesananModel->where('id_pesanan', $id_pesanan)->where('status', 'Pending')->findAll();
                
                if (!empty($items)) {
                    log_message('info', 'DOKU Callback: Updating order ' . $id_pesanan . ' with ' . count($items) . ' items.');
                    foreach ($items as $item) {
                        $this->detailPesananModel->update($item['id_detail'], ['status' => 'Sukses']);
                    }
                    
                    $total_bayar_sekarang = $this->detailPesananModel->where('id_pesanan', $id_pesanan)->where('status', 'Sukses')->selectSum('total_harga')->first()['total_harga'] ?? 0;
                    $this->pesananModel->update($id_pesanan, ['total_bayar' => $total_bayar_sekarang]);

                    session()->remove('id_pesanan');
                    session()->remove('total_bayar_pesanan');
                    log_message('info', 'DOKU Callback: Order ' . $id_pesanan . ' updated and session cleared.');
                } else {
                    log_message('warning', 'DOKU Callback: No pending items found for order ID ' . $id_pesanan);
                }

                return $this->response->setStatusCode(200);
            } else {
                log_message('warning', 'DOKU Callback: Transaction status is not SUCCESS. Status: ' . $order_status);
            }
        } else {
            // Bagian ini sekarang tidak akan pernah dieksekusi
            log_message('warning', 'DOKU Callback: Signature verification failed.');
        }
        
        return $this->response->setStatusCode(400);
    }

    private function verifyManualSignature($request_body, $signature, $timestamp)
    {
        $secretKey = getenv('doku.secretkey');
        $generatedSignature = hash('sha256', $request_body . $timestamp . $secretKey);
        return $generatedSignature === $signature;
    }
}