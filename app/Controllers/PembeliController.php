<?php

namespace App\Controllers;

use App\Models\ProdukModel;
use App\Models\PesananModel;
use App\Models\DetailPesananModel;
use App\Models\KategoriModel; // TAMBAHKAN INI

class PembeliController extends BaseController
{
    protected $produkModel;
    protected $pesananModel;
    protected $detailPesananModel;
    protected $kategoriModel; // TAMBAHKAN INI
    protected $db;

    public function __construct()
    {
        $this->produkModel = new ProdukModel();
        $this->pesananModel = new PesananModel();
        $this->detailPesananModel = new DetailPesananModel();
        $this->kategoriModel = new KategoriModel(); // TAMBAHKAN INI
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        // Ambil input dari URL
        $search = $this->request->getGet('search');
        $priceRange = $this->request->getGet('price_range');
        $kategoriId = $this->request->getGet('kategori'); // TAMBAHKAN INI

        // Mulai query builder
        $produkBuilder = $this->produkModel;

        // Terapkan filter pencarian jika ada
        if ($search) {
            $produkBuilder = $produkBuilder->like('nama_produk', $search);
        }

        // TAMBAHKAN BLOK FILTER KATEGORI
        if ($kategoriId && $kategoriId !== 'all') {
            $produkBuilder->where('id_kategori', $kategoriId);
        }

        // Terapkan filter rentang harga dari dropdown
        if ($priceRange && $priceRange !== 'all') {
            if (strpos($priceRange, '-above') > 0) {
                $minPrice = (int) str_replace('-above', '', $priceRange);
                $produkBuilder->where('harga >=', $minPrice);
            } else {
                list($minPrice, $maxPrice) = explode('-', $priceRange);
                $produkBuilder->where('harga >=', (int) $minPrice);
                $produkBuilder->where('harga <=', (int) $maxPrice);
            }
        }

        // Eksekusi query
        $data['produks'] = $produkBuilder->findAll();

        // Teruskan nilai filter ke view
        $data['search'] = $search;
        $data['priceRange'] = $priceRange;
        $data['kategoriId'] = $kategoriId; // TAMBAHKAN INI

        // Ambil daftar kategori untuk dropdown filter
        $data['kategori_list'] = $this->kategoriModel->findAll(); // TAMBAHKAN INI

        // Ambil data keranjang seperti biasa
        $id_pesanan = session()->get('id_pesanan');
        $data['keranjang'] = [];
        if ($id_pesanan) {
            $data['keranjang'] = $this->detailPesananModel
                ->where('id_pesanan', $id_pesanan)
                ->where('status !=', 'Refund')
                ->findAll();
        }
        return view('pembeli/index', $data);
    }

    public function addToCart()
    {
        // Hanya izinkan request AJAX
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403, 'Forbidden');
        }

        $id_produk = $this->request->getPost('id_produk');
        $produk = $this->produkModel->find($id_produk);

        if (!$produk || $produk['stok'] < 1) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Stok produk tidak mencukupi.']);
        }

        // Dapatkan atau buat pesanan baru
        $id_pesanan = session()->get('id_pesanan');
        if (!$id_pesanan) {
            $this->pesananModel->save(['total_bayar' => 0]);
            $id_pesanan = $this->pesananModel->insertID();
            session()->set('id_pesanan', $id_pesanan);
        }

        // Cek apakah item sudah ada di keranjang
        $item_keranjang = $this->detailPesananModel
            ->where('id_pesanan', $id_pesanan)
            ->where('id_produk', $id_produk)
            ->where('status !=', 'Refund')
            ->first();

        $this->db->transStart();

        if ($item_keranjang) {
            // Jika sudah ada, tambah kuantitasnya
            $kuantitas_baru = $item_keranjang['kuantitas'] + 1;
            $this->detailPesananModel->update($item_keranjang['id_detail'], [
                'kuantitas' => $kuantitas_baru,
                'total_harga' => $kuantitas_baru * $produk['harga']
            ]);
        } else {
            // Jika belum ada, buat item baru
            $this->detailPesananModel->save([
                'id_pesanan' => $id_pesanan,
                'id_produk' => $id_produk,
                'nama_produk' => $produk['nama_produk'],
                'kuantitas' => 1,
                'harga_satuan' => $produk['harga'],
                'total_harga' => $produk['harga'],
                'status' => 'Pending'
            ]);
        }

        // Kurangi stok produk
        $this->produkModel->update($id_produk, ['stok' => $produk['stok'] - 1]);

        // Update total bayar
        $total_bayar = $this->detailPesananModel->where('id_pesanan', $id_pesanan)->where('status !=', 'Refund')->selectSum('total_harga')->first()['total_harga'] ?? 0;
        $this->pesananModel->update($id_pesanan, ['total_bayar' => $total_bayar]);

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal menambahkan ke keranjang.']);
        }

        // Kirim kembali HTML keranjang yang sudah di-render
        $data['keranjang'] = $this->detailPesananModel
            ->where('id_pesanan', $id_pesanan)
            ->where('status !=', 'Refund')
            ->findAll();

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Produk ditambahkan ke keranjang!',
            'cart_html' => view('pembeli/_keranjang', $data)
        ]);
    }

    // Metode lain di bawah sini (removeFromCart, cetakNota, dll.) tetap sama
    // ...
    public function removeFromCart($id_detail)
    {
        $item = $this->detailPesananModel->find($id_detail);
        if (!$item) {
            session()->setFlashdata('error', 'Item keranjang tidak ditemukan.');
            return redirect()->to(base_url('pembeli'));
        }

        // Perbarui status item menjadi "Refund"
        $this->detailPesananModel->update($id_detail, ['status' => 'Refund']);

        // Kembalikan stok produk
        $produk = $this->produkModel->find($item['id_produk']);
        if ($produk) {
            $new_stok = $produk['stok'] + $item['kuantitas'];
            $this->produkModel->update($produk['id_produk'], ['stok' => $new_stok]);
        }

        // Perbarui total bayar di tabel pesanan setelah item di-refund
        $id_pesanan = $item['id_pesanan'];
        $total_bayar_sekarang = $this->detailPesananModel->where('id_pesanan', $id_pesanan)->where('status !=', 'Refund')->selectSum('total_harga')->first()['total_harga'] ?? 0;
        $this->pesananModel->update($id_pesanan, ['total_bayar' => $total_bayar_sekarang]);

        // Simpan total_bayar yang baru ke session
        session()->set('total_bayar_pesanan', $total_bayar_sekarang);

        session()->setFlashdata('message', 'Produk berhasil di refund');
        return redirect()->to(base_url('pembeli'));
    }

    public function cetakNota()
    {
        $id_pesanan = session()->get('id_pesanan');
        if (!$id_pesanan) {
            session()->setFlashdata('error', 'Keranjang belanja kosong atau transaksi sudah selesai.');
            return redirect()->to(base_url('pembeli'));
        }

        $data['pesanan'] = $this->pesananModel->find($id_pesanan);
        $data['detail_pesanan'] = $this->detailPesananModel
            ->where('id_pesanan', $id_pesanan)
            ->where('status !=', 'Refund')
            ->findAll();

        // Perbaikan: Hanya perbarui status item yang masih "Pending"
        $this->detailPesananModel->where('id_pesanan', $id_pesanan)->where('status', 'Pending')->set(['status' => 'Sukses'])->update();

        // Kosongkan session id_pesanan setelah dicetak
        session()->remove('id_pesanan');
        session()->remove('total_bayar_pesanan');

        return view('pembeli/nota', $data);
    }

    public function updateCart($id_detail)
    {
        $newKuantitas = $this->request->getGet('kuantitas');

        // Jika kuantitas kurang dari 1, hapus item dari keranjang
        if ($newKuantitas < 1) {
            return $this->removeFromCart($id_detail);
        }

        // Ambil data item keranjang yang lama
        $itemLama = $this->detailPesananModel->find($id_detail);

        if (!$itemLama) {
            session()->setFlashdata('error', 'Item keranjang tidak ditemukan.');
            return redirect()->to(base_url('pembeli'));
        }

        // Ambil data produk terkait
        $produk = $this->produkModel->find($itemLama['id_produk']);

        if (!$produk) {
            session()->setFlashdata('error', 'Produk tidak ditemukan.');
            return redirect()->to(base_url('pembeli'));
        }

        // Hitung selisih kuantitas
        $selisihKuantitas = $newKuantitas - $itemLama['kuantitas'];
        $stokProdukSaatIni = $produk['stok'];

        if (($stokProdukSaatIni - $selisihKuantitas) < 0) {
            session()->setFlashdata('error', 'Stok tidak mencukupi untuk update.');
            return redirect()->to(base_url('pembeli'));
        }

        // Perbarui data di tabel detail_pesanan
        $data_update = [
            'kuantitas' => $newKuantitas,
            'total_harga' => $newKuantitas * $itemLama['harga_satuan']
        ];
        $this->detailPesananModel->update($id_detail, $data_update);

        // Perbarui stok di tabel produk
        $this->produkModel->update($produk['id_produk'], ['stok' => ($stokProdukSaatIni - $selisihKuantitas)]);

        // Perbarui total bayar di tabel pesanan setelah item di-update
        $id_pesanan = $itemLama['id_pesanan'];
        $total_bayar_sekarang = $this->detailPesananModel->where('id_pesanan', $id_pesanan)->where('status !=', 'Refund')->selectSum('total_harga')->first()['total_harga'] ?? 0;
        $this->pesananModel->update($id_pesanan, ['total_bayar' => $total_bayar_sekarang]);

        // Simpan total_bayar yang baru ke session
        session()->set('total_bayar_pesanan', $total_bayar_sekarang);

        session()->setFlashdata('message', 'Keranjang berhasil diupdate!');
        return redirect()->to(base_url('pembeli'));
    }
    public function status()
    {
        $id_pesanan = session()->getFlashdata('last_order_id');
        $invoiceNumber = session()->getFlashdata('last_invoice_number');
        // Ambil invoice number dari sesi untuk ditampilkan
        $data['invoiceNumber'] = session()->get('current_invoice');

        // Hapus sesi setelah digunakan
        session()->remove('id_pesanan');
        session()->remove('current_invoice');

        // Tampilkan view konfirmasi
        return view('pembeli/status', $data);
    }
    public function check_status($invoiceNumber = null)
    {
        // Pastikan hanya request AJAX yang bisa mengakses ini
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        if (!$invoiceNumber) {
            return $this->response->setJSON(['status' => 'Error', 'message' => 'Invoice tidak valid.']);
        }

        $parts = explode('-', $invoiceNumber);
        if (count($parts) < 2 || !is_numeric($parts[1])) {
            return $this->response->setJSON(['status' => 'Error', 'message' => 'Format invoice salah.']);
        }
        $id_pesanan = $parts[1];

        $order = $this->detailPesananModel->where('id_pesanan', $id_pesanan)->first();

        if ($order) {
            return $this->response->setJSON(['status' => $order['status']]);
        }

        return $this->response->setJSON(['status' => 'Tidak Ditemukan']);
    }
}
