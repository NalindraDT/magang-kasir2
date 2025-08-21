<?php

namespace App\Controllers;

use App\Models\ProdukModel;

class ProdukTampilan extends BaseController
{
    protected $produkModel;

    public function __construct()
    {
        $this->produkModel = new ProdukModel();
    }

    public function index()
    {
        // Ambil semua data produk dari model
        $data['produks'] = $this->produkModel->findAll();
        
        // Kirim data ke view untuk ditampilkan
        return view('produk/produk', $data);
    }

    public function tambah()
    {
        // Metode ini yang akan merender halaman form tambah produk
        return view('produk/produk_view_form');
    }

    public function simpan()
    {
        // Ambil data dari form
        $data = [
            'nama_produk' => $this->request->getPost('nama_produk'),
            'harga' => $this->request->getPost('harga'),
            'stok' => $this->request->getPost('stok'),
        ];
        
        // Validasi input
        $rules = [
            'nama_produk' => 'required|string|max_length[255]|is_unique[produk.nama_produk]',
            'harga' => 'required|numeric|greater_than_equal_to[0]',
            'stok' => 'required|integer|greater_than_equal_to[0]',
        ];

        if (!$this->validate($rules)) {
            session()->setFlashdata('errors', $this->validator->getErrors());
            return redirect()->back()->withInput();
        }

        // Simpan data ke database
        $this->produkModel->save($data);

        // Redirect kembali ke halaman produk dengan pesan sukses
        session()->setFlashdata('message', 'Produk berhasil ditambahkan!');
        return redirect()->to(base_url('produk'));
    }

    public function edit($id = null)
    {
        // Ambil data produk berdasarkan ID
        $data['produk'] = $this->produkModel->find($id);
        if (empty($data['produk'])) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Produk tidak ditemukan.');
        }

        return view('produk/produk_view_form', $data);
    }

    public function update()
    {
        $id = $this->request->getPost('id_produk');
        $data = [
            'nama_produk' => $this->request->getPost('nama_produk'),
            'harga' => $this->request->getPost('harga'),
            'stok' => $this->request->getPost('stok'),
        ];
        
        // Validasi unik nama produk, abaikan produk dengan ID yang sedang diedit
        $rules = [
            'nama_produk' => "required|string|max_length[255]|is_unique[produk.nama_produk,id_produk,{$id}]",
            'harga' => 'required|numeric|greater_than_equal_to[0]',
            'stok' => 'required|integer|greater_than_equal_to[0]',
        ];

        if (!$this->validate($rules)) {
            session()->setFlashdata('errors', $this->validator->getErrors());
            return redirect()->back()->withInput();
        }

        $this->produkModel->update($id, $data);

        session()->setFlashdata('message', 'Produk berhasil diupdate!');
        return redirect()->to(base_url('produk'));
    }

    public function hapus($id = null)
    {
        // Hapus produk dari database
        $this->produkModel->delete($id);

        // Redirect kembali ke halaman produk dengan pesan sukses
        session()->setFlashdata('message', 'Produk berhasil dihapus!');
        return redirect()->to(base_url('produk'));
    }
}
