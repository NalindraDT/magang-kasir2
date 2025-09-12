<?php

namespace App\Controllers;

use App\Models\DetailPesananModel;
use App\Models\PesananModel;

class LaporanController extends BaseController
{
    protected $detailPesananModel;
    protected $pesananModel;

    public function __construct()
    {
        $this->detailPesananModel = new DetailPesananModel();
        $this->pesananModel = new PesananModel();
    }

    public function index()
    {
        // Ambil input tanggal dari form, jika tidak ada, gunakan tanggal hari ini
        $startDate = $this->request->getGet('start_date') ?? date('Y-m-d');
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-d');

        // Pastikan tanggal akhir mencakup keseluruhan hari
        $endDateTime = $endDate . ' 23:59:59';

        // 1. Ambil data transaksi yang sukses dalam rentang tanggal yang dipilih
        $db = \Config\Database::connect();
        $builder = $db->table('detail_pesanan');
        $builder->select('detail_pesanan.*, pesanan.tanggal');
        $builder->join('pesanan', 'pesanan.id_pesanan = detail_pesanan.id_pesanan');
        $builder->where('detail_pesanan.status', 'Sukses');
        $builder->where('pesanan.tanggal >=', $startDate);
        $builder->where('pesanan.tanggal <=', $endDateTime);
        $transaksi = $builder->get()->getResultArray();
        
        // 2. Olah data untuk ringkasan
        $totalPendapatan = 0;
        $totalProdukTerjual = 0;
        $produkTerlaris = [];

        foreach ($transaksi as $item) {
            $totalPendapatan += $item['total_harga'];
            $totalProdukTerjual += $item['kuantitas'];

            // Mengelompokkan produk terlaris
            $namaProduk = $item['nama_produk'];
            if (!isset($produkTerlaris[$namaProduk])) {
                $produkTerlaris[$namaProduk] = 0;
            }
            $produkTerlaris[$namaProduk] += $item['kuantitas'];
        }
        
        // Mengurutkan produk terlaris dari yang paling banyak terjual
        arsort($produkTerlaris);

        // 3. Siapkan data untuk dikirim ke view
        $data = [
            'transaksi' => $transaksi,
            'totalPendapatan' => $totalPendapatan,
            'totalProdukTerjual' => $totalProdukTerjual,
            'jumlahTransaksi' => count(array_unique(array_column($transaksi, 'id_pesanan'))), // Menghitung transaksi unik
            'produkTerlaris' => $produkTerlaris,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ];

        return view('laporan/index', $data);
    }
}