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

    // Metode baru untuk cetak nota dari halaman admin
    public function cetak($id_pesanan)
    {
        if (empty($id_pesanan)) {
            return redirect()->back()->with('error', 'ID Pesanan tidak valid.');
        }

        $data['pesanan'] = $this->pesananModel->find($id_pesanan);
        $data['detail_pesanan'] = $this->detailPesananModel
            ->where('id_pesanan', $id_pesanan)
            ->where('status !=', 'Refund')
            ->findAll();

        if (empty($data['pesanan']) || empty($data['detail_pesanan'])) {
            return redirect()->back()->with('error', 'Data transaksi tidak ditemukan.');
        }

        return view('pembeli/nota', $data);
    }

    public function hapus($id_detail)
    {
        $this->detailPesananModel->where('id_detail', $id_detail)->delete();
        $page = $this->request->getGet('page') ?? 1;

        session()->setFlashdata('message', 'Transaksi berhasil dihapus!');
        return redirect()->to(base_url('admin/transaksi?page=' . $page));
    }
}
