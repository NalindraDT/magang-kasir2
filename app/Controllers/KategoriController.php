<?php

namespace App\Controllers;

use App\Models\KategoriModel;
use App\Models\ProdukModel;

class KategoriController extends BaseController
{
    protected $kategoriModel;
    protected $produkModel;

    public function __construct()
    {
        $this->kategoriModel = new KategoriModel();
        $this->produkModel = new ProdukModel();
    }

    // Menampilkan halaman utama manajemen kategori
    public function index()
    {
        $data['kategori'] = $this->kategoriModel->findAll();
        return view('kategori/index', $data);
    }

    // Menyimpan kategori baru
    public function create()
    {
        $rules = ['nama_kategori' => 'required|is_unique[kategori.nama_kategori]'];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Nama kategori tidak boleh kosong dan harus unik.');
        }

        $this->kategoriModel->save([
            'nama_kategori' => $this->request->getPost('nama_kategori'),
        ]);

        return redirect()->to(base_url('admin/kategori'))->with('message', 'Kategori baru berhasil ditambahkan.');
    }

    // Mengupdate kategori
    public function update($id)
    {
        $rules = ['nama_kategori' => 'required|is_unique[kategori.nama_kategori,id_kategori,' . $id . ']'];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Nama kategori tidak boleh kosong dan harus unik.');
        }

        $this->kategoriModel->update($id, [
            'nama_kategori' => $this->request->getPost('nama_kategori'),
        ]);

        return redirect()->to(base_url('admin/kategori'))->with('message', 'Kategori berhasil diperbarui.');
    }

    // Menghapus kategori
    public function delete($id)
    {
        // Cek apakah kategori masih digunakan oleh produk
        $isUsed = $this->produkModel->where('id_kategori', $id)->first();
        if ($isUsed) {
            return redirect()->to(base_url('admin/kategori'))->with('error', 'Kategori tidak dapat dihapus karena masih digunakan oleh produk.');
        }

        $this->kategoriModel->delete($id);
        return redirect()->to(base_url('admin/kategori'))->with('message', 'Kategori berhasil dihapus.');
    }
}