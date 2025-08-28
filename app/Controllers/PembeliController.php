<?php

namespace App\Controllers;

use App\Models\ProdukModel;
use App\Models\PesananModel;
use App\Models\DetailPesananModel;

class PembeliController extends BaseController
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

    public function index()
    {
        $data['produks'] = $this->produkModel->findAll();
        $data['keranjang'] = $this->detailPesananModel
            ->where('id_pesanan', session()->get('id_pesanan'))
            ->where('status !=', 'Refund')
            ->findAll();
        $data['jumlah_keranjang'] = count($data['keranjang']);
        return view('pembeli/index', $data);
    }

    public function beli()
    {
        $kuantitas_request = $this->request->getPost('kuantitas');
        $item_dipilih = array_filter($kuantitas_request, function($kuantitas) {
            return $kuantitas > 0;
        });

        if (empty($item_dipilih)) {
            session()->setFlashdata('error', 'Pilih setidaknya satu produk untuk dibeli.');
            return redirect()->to(base_url('pembeli'));
        }

        $id_pesanan = session()->get('id_pesanan');
        if (!$id_pesanan) {
            $data_pesanan = ['total_bayar' => 0];
            $this->pesananModel->save($data_pesanan);
            $id_pesanan = $this->pesananModel->insertID();
            session()->set('id_pesanan', $id_pesanan);
        }

        foreach ($item_dipilih as $id_produk => $kuantitas) {
            $produk = $this->produkModel->find($id_produk);

            if ($produk && $produk['stok'] >= $kuantitas) {
                $existing_item = $this->detailPesananModel
                    ->where('id_pesanan', $id_pesanan)
                    ->where('id_produk', $id_produk)
                    ->where('status !=', 'Refund')
                    ->first();
                
                if ($existing_item) {
                    $new_kuantitas = $existing_item['kuantitas'] + $kuantitas;
                    $total_harga_item = $new_kuantitas * $produk['harga'];
                    $this->detailPesananModel->update($existing_item['id_detail'], [
                        'kuantitas' => $new_kuantitas,
                        'total_harga' => $total_harga_item
                    ]);
                } else {
                    $total_harga_item = $kuantitas * $produk['harga'];
                    $data_detail = [
                        'id_pesanan' => $id_pesanan,
                        'id_produk' => $id_produk,
                        'nama_produk' => $produk['nama_produk'],
                        'kuantitas' => $kuantitas,
                        'harga_satuan' => $produk['harga'],
                        'total_harga' => $total_harga_item,
                        'status' => 'Pending'
                    ];
                    $this->detailPesananModel->save($data_detail);
                }
                
                $new_stok = $produk['stok'] - $kuantitas;
                $this->produkModel->update($id_produk, ['stok' => $new_stok]);

            } else {
                session()->setFlashdata('error', 'Pembelian gagal. Stok produk ' . $produk['nama_produk'] . ' tidak mencukupi atau data tidak valid.');
                return redirect()->to(base_url('pembeli'));
            }
        }
        
        $total_bayar_sekarang = $this->detailPesananModel->where('id_pesanan', $id_pesanan)->where('status !=', 'Refund')->selectSum('total_harga')->first()['total_harga'] ?? 0;
        $this->pesananModel->update($id_pesanan, ['total_bayar' => $total_bayar_sekarang]);
        
        // Simpan total_bayar ke session agar bisa diakses di DokuController
        session()->set('total_bayar_pesanan', $total_bayar_sekarang);
        
        session()->setFlashdata('message', 'Pembelian berhasil!');
        return redirect()->to(base_url('pembeli'));
    }

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
        $newKuantitas = $this->request->getPost('kuantitas');

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
}
