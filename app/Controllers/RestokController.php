<?php

namespace App\Controllers;

use App\Models\ProdukModel;
use App\Models\RestokerModel;
use App\Models\RestokProdukModel;

class RestokController extends BaseController
{
    protected $restokerModel;
    protected $restokProdukModel;
    protected $produkModel;
    protected $db;

    public function __construct()
    {
        $this->restokerModel = new RestokerModel();
        $this->restokProdukModel = new RestokProdukModel();
        $this->produkModel = new ProdukModel();
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        // Data untuk "Pemesanan Restok"
        $data['riwayat_restok'] = $this->restokProdukModel
            ->select('restok_produk.*, produk.nama_produk, restokers.nama_restoker')
            ->join('produk', 'produk.id_produk = restok_produk.id_produk', 'left')
            ->join('restokers', 'restokers.id_restoker = produk.id_restoker', 'left')
            ->orderBy('tanggal_pesan', 'DESC')
            ->findAll();

        // Data untuk dropdown
        $data['restokers'] = $this->restokerModel->orderBy('nama_restoker', 'ASC')->findAll();

        return view('restok/index', $data);
    }
    public function riwayat()
    {
        // 1. Ambil semua data seperti biasa
        $semua_restok = $this->restokProdukModel
            ->select('restok_produk.*, produk.nama_produk, restokers.nama_restoker')
            ->join('produk', 'produk.id_produk = restok_produk.id_produk', 'left')
            ->join('restokers', 'restokers.id_restoker = produk.id_restoker', 'left')
            ->orderBy('tanggal_pesan', 'DESC')
            ->findAll();

        // 2. Siapkan array kosong untuk data yang akan dikelompokkan
        $grouped_data = [];

        // 3. Proses dan kelompokkan data
        foreach ($semua_restok as $item) {
            $nama_produk = $item['nama_produk'];

            // Jika produk ini belum ada di array, inisialisasi
            if (!isset($grouped_data[$nama_produk])) {
                $grouped_data[$nama_produk] = [
                    'summary' => [
                        'total_pesan' => 0,
                        'total_diterima' => 0,
                        'total_retur' => 0,
                    ],
                    'details' => []
                ];
            }

            // Akumulasi data untuk ringkasan (summary)
            $grouped_data[$nama_produk]['summary']['total_pesan'] += (int)$item['jumlah_pesan'];
            $grouped_data[$nama_produk]['summary']['total_diterima'] += (int)($item['jumlah_diterima'] ?? 0);
            $grouped_data[$nama_produk]['summary']['total_retur'] += (int)($item['jumlah_retur'] ?? 0);

            // Tambahkan item transaksi asli ke dalam 'details'
            $grouped_data[$nama_produk]['details'][] = $item;
        }

        $data['grouped_restok'] = $grouped_data;

        return view('restok/riwayat', $data);
    }

    // --- FUNGSI UNTUK MANAJEMEN RESTOK ---

    public function getProdukByRestoker($id_restoker)
    {
        if ($this->request->isAJAX()) {
            $produk = $this->produkModel->where('id_restoker', $id_restoker)->findAll();
            return $this->response->setJSON($produk);
        }
        return $this->response->setStatusCode(403);
    }

    public function create()
    {
        $rules = [
            'id_restoker' => 'required|integer',
            'id_produk' => 'required|integer',
            'jumlah_pesan' => 'required|integer|greater_than[0]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Data tidak valid. Pastikan semua field terisi dengan benar.');
        }

        $data = [
            'id_produk'     => $this->request->getPost('id_produk'),
            'jumlah_pesan'  => $this->request->getPost('jumlah_pesan'),
            'tanggal_pesan' => date('Y-m-d H:i:s'),
            'status'        => 'Dipesan',
            'jumlah_diterima' => 0,
        ];

        if ($this->restokProdukModel->save($data)) {
            return redirect()->to(base_url('admin/restok'))->with('message', 'Pesanan restok berhasil dibuat.');
        }

        return redirect()->back()->withInput()->with('error', 'Gagal menyimpan data ke database.');
    }

    public function update($id_restok)
    {
        $restok = $this->restokProdukModel->find($id_restok);
        if (!$restok) {
            return redirect()->to(base_url('admin/restok'))->with('error', 'Data restok tidak ditemukan.');
        }

        // Ambil jumlah yang sudah diterima sebelumnya
        $jumlahDiterimaSebelumnya = (int)($restok['jumlah_diterima'] ?? 0);

        // Aturan validasi baru
        $rules = [
            'jumlah_diterima' => [
                'rules'  => 'required|integer|greater_than_equal_to[' . $jumlahDiterimaSebelumnya . ']|less_than_equal_to[' . $restok['jumlah_pesan'] . ']',
                'errors' => [
                    'greater_than_equal_to' => 'Jumlah diterima tidak boleh kurang dari jumlah yang sudah dikonfirmasi sebelumnya (' . $jumlahDiterimaSebelumnya . ').',
                    'less_than_equal_to' => 'Jumlah diterima tidak boleh melebihi jumlah yang dipesan (' . $restok['jumlah_pesan'] . ').'
                ],
            ],
        ];

        if (!$this->validate($rules)) {
            // Ambil pesan error spesifik dari validator
            $error = $this->validator->getErrors()['jumlah_diterima'] ?? 'Input tidak valid.';
            return redirect()->back()->withInput()->with('error', $error);
        }

        $jumlahDiterimaInput = (int) $this->request->getPost('jumlah_diterima');
        $this->db->transStart();
        $stokUntukDitambah = $jumlahDiterimaInput - $jumlahDiterimaSebelumnya;

        $status_baru = ($jumlahDiterimaInput >= (int)$restok['jumlah_pesan']) ? 'Diterima' : 'Diterima Sebagian';

        $this->restokProdukModel->update($id_restok, [
            'jumlah_diterima'  => $jumlahDiterimaInput,
            'tanggal_diterima' => date('Y-m-d H:i:s'),
            'status'           => $status_baru,
        ]);

        if ($stokUntukDitambah > 0) { // Hanya tambah stok jika ada penambahan
            $this->produkModel->where('id_produk', $restok['id_produk'])->increment('stok', $stokUntukDitambah);
        }

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return redirect()->to(base_url('admin/restok'))->with('error', 'Gagal mengupdate stok produk.');
        }
        return redirect()->to(base_url('admin/restok'))->with('message', 'Stok produk berhasil diperbarui.');
    }

    // --- FUNGSI BARU UNTUK MANAJEMEN SUPPLIER ---

    public function supplier()
    {
        $data['restokers'] = $this->restokerModel->orderBy('nama_restoker', 'ASC')->findAll();
        return view('restok/supplier', $data);
    }
    public function supplierCreate()
    {
        $rules = ['nama_restoker' => 'required|is_unique[restokers.nama_restoker]'];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Nama supplier tidak boleh kosong dan harus unik.');
        }

        $this->restokerModel->save([
            'nama_restoker' => $this->request->getPost('nama_restoker'),
            'kontak'        => $this->request->getPost('kontak'),
            'alamat'        => $this->request->getPost('alamat'),
        ]);

        return redirect()->to(base_url('admin/restok'))->with('message', 'Supplier baru berhasil ditambahkan.');
    }

    public function supplierUpdate($id)
    {
        $rules = ['nama_restoker' => 'required|is_unique[restokers.nama_restoker,id_restoker,' . $id . ']'];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Nama supplier tidak boleh kosong dan harus unik.');
        }

        $this->restokerModel->update($id, [
            'nama_restoker' => $this->request->getPost('nama_restoker'),
            'kontak'        => $this->request->getPost('kontak'),
            'alamat'        => $this->request->getPost('alamat'),
        ]);

        return redirect()->to(base_url('admin/restok'))->with('message', 'Data supplier berhasil diperbarui.');
    }

    public function supplierDelete($id)
    {
        // Cek apakah supplier masih digunakan oleh produk
        $isUsed = $this->produkModel->where('id_restoker', $id)->first();
        if ($isUsed) {
            return redirect()->to(base_url('admin/restok'))->with('error', 'Supplier tidak dapat dihapus karena masih digunakan oleh produk.');
        }

        $this->restokerModel->delete($id);
        return redirect()->to(base_url('admin/restok'))->with('message', 'Supplier berhasil dihapus.');
    }
    public function return($id_restok)
    {
        // Ambil data restok untuk validasi
        $restok = $this->restokProdukModel->find($id_restok);
        if (!$restok) {
            return redirect()->to(base_url('admin/restok'))->with('error', 'Data restok tidak ditemukan.');
        }

        $jumlahReturLama = (int)($restok['jumlah_retur'] ?? 0);
        $sisaUntukDiretur = (int)$restok['jumlah_diterima'] - $jumlahReturLama;

        // 1. Aturan Validasi
        $rules = [
            'jumlah_retur' => [
                'rules'  => 'required|integer|greater_than[0]|less_than_equal_to[' . $sisaUntukDiretur . ']',
                'errors' => [
                    'greater_than'       => 'Jumlah retur harus lebih dari nol.',
                    'less_than_equal_to' => 'Jumlah retur tidak boleh melebihi sisa barang yang dapat diretur (' . $sisaUntukDiretur . ').',
                ],
            ],
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', $this->validator->getErrors()['jumlah_retur']);
        }

        // 2. Ambil data dari form
        $jumlahReturInput = (int) $this->request->getPost('jumlah_retur');

        // 3. Mulai transaksi database
        $this->db->transStart();

        // Kurangi stok di tabel produk
        $this->produkModel->where('id_produk', $restok['id_produk'])
            ->decrement('stok', $jumlahReturInput);

        // Hitung jumlah retur baru dan tentukan status
        $jumlahReturBaru = $jumlahReturLama + $jumlahReturInput;
        $status_baru = ($jumlahReturBaru >= (int)$restok['jumlah_diterima']) ? 'Diretur' : 'Diretur Sebagian';

        // Update tabel restok_produk
        $this->restokProdukModel->update($id_restok, [
            'jumlah_retur' => $jumlahReturBaru,
            'status'       => $status_baru,
        ]);

        // Selesaikan transaksi
        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return redirect()->to(base_url('admin/restok'))->with('error', 'Gagal memproses retur barang.');
        }

        return redirect()->to(base_url('admin/restok'))->with('message', 'Barang berhasil diretur dan stok telah diperbarui.');
    }
    public function deleteRestockHistory($id_restok)
    {
        $restok = $this->restokProdukModel->find($id_restok);

        // Pastikan hanya data dengan status 'Diretur' atau 'Batal' yang bisa dihapus
        if ($restok && ($restok['status'] === 'Diretur' || $restok['status'] === 'Batal')) {
            $this->restokProdukModel->delete($id_restok);
            return redirect()->to(base_url('admin/restok'))->with('message', 'Riwayat restok berhasil dihapus.');
        }

        return redirect()->to(base_url('admin/restok'))->with('error', 'Riwayat ini tidak dapat dihapus.');
    }
    public function exportRiwayat()
    {
        // 1. Ambil semua data restok secara lengkap
        $semua_restok = $this->restokProdukModel
            ->select('restok_produk.*, produk.nama_produk, restokers.nama_restoker')
            ->join('produk', 'produk.id_produk = restok_produk.id_produk', 'left')
            ->join('restokers', 'restokers.id_restoker = produk.id_restoker', 'left')
            ->orderBy('tanggal_pesan', 'DESC') // Diurutkan berdasarkan tanggal pesan terbaru
            ->findAll();

        // 2. Siapkan file CSV untuk diunduh
        $fileName = 'riwayat_restok_lengkap_' . date('Y-m-d') . '.csv';
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename={$fileName}");
        header("Content-Type: application/csv; charset=UTF-8");

        $file = fopen('php://output', 'w');

        // Tambahkan BOM (Byte Order Mark) untuk dukungan UTF-8 di Excel
        fputs($file, "\xEF\xBB\xBF");

        // 3. Tulis header kolom di file CSV menggunakan titik koma (;)
        $header = ['ID Restok', 'Nama Produk', 'Supplier', 'Tanggal Pesan', 'Tanggal Diterima', 'Jumlah Pesan', 'Jumlah Diterima', 'Jumlah Retur', 'Status'];
        fputcsv($file, $header, ';');

        // 4. Tulis setiap baris data transaksi ke CSV
        foreach ($semua_restok as $item) {
            $rowData = [
                $item['id_restok'],
                $item['nama_produk'],
                $item['nama_restoker'],
                date('d-m-Y H:i', strtotime($item['tanggal_pesan'])),
                $item['tanggal_diterima'] ? date('d-m-Y H:i', strtotime($item['tanggal_diterima'])) : '-',
                $item['jumlah_pesan'],
                $item['jumlah_diterima'] ?? 0,
                $item['jumlah_retur'] ?? 0,
                $item['status']
            ];
            fputcsv($file, $rowData, ';');
        }

        // 5. Tutup file pointer
        fclose($file);

        // Hentikan eksekusi skrip
        exit();
    }
}
