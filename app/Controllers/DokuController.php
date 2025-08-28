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
use Doku\Snap\Utils\Signature;

class DokuController extends BaseController
{
    // Tambahkan properti untuk models
    protected $pesananModel;
    protected $detailPesananModel;
    protected $produkModel;

    private $dokuClient;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        // Memuat models yang diperlukan
        $this->pesananModel = new PesananModel();
        $this->detailPesananModel = new DetailPesananModel();
        $this->produkModel = new ProdukModel();

        // Mengambil kunci dari file lokal
        $merchantPrivateKey = file_get_contents(ROOTPATH . 'keys/private.key');
        $merchantPublicKey = file_get_contents(ROOTPATH . 'keys/public.pem');
        
        // Mengambil public key DOKU dari .env (harus satu baris)
        $dokuPublicKey = getenv('doku.dokupublickey');

        // Mengambil client ID dan secret key dari .env
        $clientId = getenv('doku.clientid');
        $secretKey = getenv('doku.secretkey');

        // Instansiasi DokuClient dengan 7 parameter yang benar
        // privateKey, publicKey, dokuPublicKey, clientId, issuer, isProduction, secretKey
        $this->dokuClient = new Snap(
            $merchantPrivateKey, // 1. Private Key Merchant
            $merchantPublicKey,  // 2. Public Key Merchant
            $dokuPublicKey,      // 3. DOKU Public Key (dari .env)
            $clientId,           // 4. Client ID
            '',                  // 5. Issuer (opsional, kosongkan)
            false,               // 6. isProduction (false untuk sandbox)
            $secretKey           // 7. Secret Key
        );
    }

    public function payment()
    {
        $total_bayar = session()->get('total_bayar_pesanan');
        $id_pesanan = session()->get('id_pesanan');
        $trxId = 'INV-' . $id_pesanan . '-' . date('YmdHis');

        if (empty($total_bayar) || empty($id_pesanan)) {
            session()->setFlashdata('error', 'Keranjang belanja kosong atau data tidak valid.');
            return redirect()->to(base_url('pembeli'));
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
            'C', // Closed Amount
            date('Y-m-d\TH:i:sP', strtotime('+1 day'))
        );
        
        try {
            $response = $this->dokuClient->createVa($request);
            
            if (isset($response->virtualAccountData->virtualAccountNo)) {
                 session()->setFlashdata('message', 'Pembayaran VA berhasil dibuat. Nomor VA Anda: ' . $response->virtualAccountData->virtualAccountNo);
            } else {
                 session()->setFlashdata('error', 'Gagal membuat VA. Pesan: ' . ($response->responseMessage ?? 'Unknown Error'));
            }
            
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
        
        if (Signature::verifySignature($request_body, $signature, $this->dokuClient->getDokuPublicKey(), $timestamp)) {
            $order_status = $notification['transaction']['status'] ?? 'UNKNOWN';

            if ($order_status === 'SUCCESS') {
                $id_pesanan = $notification['transaction']['session_id'];
                
                $items = $this->detailPesananModel->where('id_pesanan', $id_pesanan)->where('status', 'Pending')->findAll();
                
                if (!empty($items)) {
                    foreach ($items as $item) {
                        $this->detailPesananModel->update($item['id_detail'], ['status' => 'Sukses']);
                    }
                    
                    $total_bayar_sekarang = $this->detailPesananModel->where('id_pesanan', $id_pesanan)->where('status', 'Sukses')->selectSum('total_harga')->first()['total_harga'] ?? 0;
                    $this->pesananModel->update($id_pesanan, ['total_bayar' => $total_bayar_sekarang]);
                }

                return $this->response->setStatusCode(200);
            }
        }
        
        return $this->response->setStatusCode(400);
    }
}