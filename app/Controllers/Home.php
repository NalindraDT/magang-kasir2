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
        // Cek apakah pengguna sudah login
        if (!session()->get('user_logged_in')) {
            return redirect()->to(base_url('auth/login'));
        }

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
        
        return view('produk/dashboard', $data);
    }
    
    public function pembeli()
    {
        // Rute ini akan kita kembangkan nanti
        return "Ini adalah halaman pembeli.";
    }
}
