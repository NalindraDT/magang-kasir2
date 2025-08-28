<?php

namespace App\Controllers;

use App\Models\ProdukModel;
use App\Models\PesananModel;
use App\Models\DetailPesananModel;

class Home extends BaseController
{
    protected $produkModel;
    protected $pesananModel;
    protected $detailPesananModel;

    public function __construct()
    {
        $this->produkModel = new ProdukModel();
        $this->pesananModel = new PesananModel();
        $this->detailPesananModel = new DetailPesananModel();
    }
    
    // Metode index akan mengarahkan ke halaman landing
    public function index()
    {
        return view('landing');
    }

    public function admin()
{
    // Ambil semua data produk
    $produks = $this->produkModel->findAll();
    $data['jumlah_produk'] = count($produks);

    // Hitung total stok dari semua produk
    $totalStok = 0;
    foreach ($produks as $produk) {
        $totalStok += $produk['stok'];
    }
    $data['total_stok'] = $totalStok;

    // Hitung total penghasilan dari transaksi yang statusnya "Sukses"
    $transaksisSukses = $this->detailPesananModel->where('status', 'Sukses')->findAll();
    $totalPenghasilan = 0;
    foreach ($transaksisSukses as $transaksi) {
        $totalPenghasilan += $transaksi['total_harga'];
    }
    $data['total_penghasilan'] = 'Rp ' . number_format($totalPenghasilan, 0, ',', '.');
    $data['total_sukses'] = count($transaksisSukses);

    //Hitung transaksi gagal
    $transaksisGagal = $this->detailPesananModel->where('status', 'Refund')->findAll();
    $data['total_refund'] = count($transaksisGagal);
    $transaksisGagal =0;

    //Hitung transaksi pending
    $transaksisPending = $this->detailPesananModel->where('status','Pending')->findAll();
    $data['total_pending'] = count($transaksisPending);
    $transaksisPending =0;

    // Ambil 5 produk terbaru
    $data['latest_products'] = $this->produkModel->orderBy('id_produk', 'DESC')->findAll(5);
    
    // Ambil 3 transaksi terbaru
    $data['latest_transactions'] = $this->detailPesananModel->orderBy('id_detail', 'DESC')->findAll(5);

    return view('produk/dashboard', $data);
}
    
    public function pembeli()
    {
        // Rute ini akan kita kembangkan nanti
        return "Ini adalah halaman pembeli.";
    }
}