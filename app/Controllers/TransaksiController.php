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
    public function export()
    {
        // 1. Ambil semua data detail pesanan dan gabungkan dengan data pesanan untuk mendapatkan tanggal
        $transactions = $this->detailPesananModel
            ->select('detail_pesanan.*, pesanan.tanggal')
            ->join('pesanan', 'pesanan.id_pesanan = detail_pesanan.id_pesanan', 'left')
            ->orderBy('pesanan.tanggal', 'DESC')
            ->findAll();

        // 2. Siapkan nama file dan header untuk file CSV
        $fileName = 'riwayat_transaksi_' . date('Y-m-d') . '.csv';
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename={$fileName}");
        header("Content-Type: application/csv; charset=UTF-8");

        // 3. Buat file pointer untuk output
        $file = fopen('php://output', 'w');

        // Tambahkan BOM untuk dukungan UTF-8 di Excel
        fputs($file, "\xEF\xBB\xBF");

        // 4. Tulis header kolom di file CSV menggunakan pemisah titik koma (;)
        $header = ['ID Pesanan', 'Tanggal Transaksi', 'Nama Produk', 'Kuantitas', 'Harga Satuan', 'Total Harga', 'Status'];
        fputcsv($file, $header, ';');

        // 5. Tulis setiap baris data ke dalam file CSV
        foreach ($transactions as $item) {
            $rowData = [
                $item['id_pesanan'],
                date('d-m-Y H:i', strtotime($item['tanggal'])),
                $item['nama_produk'],
                $item['kuantitas'],
                'Rp ' . number_format($item['harga_satuan'], 0, ',', '.'),
                'Rp ' . number_format($item['total_harga'], 0, ',', '.'),
                $item['status']
            ];
            fputcsv($file, $rowData, ';');
        }

        // 6. Tutup file pointer
        fclose($file);

        // Hentikan eksekusi skrip
        exit();
    }
}
