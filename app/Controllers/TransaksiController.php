<?php

namespace App\Controllers;

use App\Models\PesananModel;
use App\Models\DetailPesananModel;
use App\Models\ProdukModel;

class TransaksiController extends BaseController
{
    protected $pesananModel;
    protected $detailPesananModel;
    protected $produkModel;

    public function __construct()
    {
        $this->pesananModel = new PesananModel();
        $this->detailPesananModel = new DetailPesananModel();
        $this->produkModel = new ProdukModel();
    }

    public function index()
    {
        $data['transaksis'] = $this->detailPesananModel->findAll();
        return view('transaksi/index', $data);
    }
    
    // public function refund($id_pesanan)
    // {
    //     $items = $this->detailPesananModel->where('id_pesanan', $id_pesanan)->findAll();

    //     if (empty($items)) {
    //         session()->setFlashdata('error', 'Transaksi tidak ditemukan.');
    //         return redirect()->to(base_url('transaksi'));
    //     }

    //     foreach ($items as $item) {
    //         // Perbarui status menjadi "Refund"
    //         $this->detailPesananModel->update($item['id_detail'], ['status' => 'Refund']);
            
    //         // Kembalikan stok produk
    //         $produk = $this->produkModel->find($item['id_produk']);
    //         if ($produk) {
    //             $new_stok = $produk['stok'] + $item['kuantitas'];
    //             $this->produkModel->update($produk['id_produk'], ['stok' => $new_stok]);
    //         }
    //     }
        
    //     session()->setFlashdata('message', 'Transaksi berhasil di-refund!');
    //     return redirect()->to(base_url('transaksi'));
    // }
    
    public function hapus($id_detail)
    {
        $this->detailPesananModel->where('id_detail', $id_detail)->delete();
        // $this->pesananModel->delete($id_pesanan);

        session()->setFlashdata('message', 'Transaksi berhasil dihapus!');
        return redirect()->to(base_url('admin/transaksi'));
    }
}
