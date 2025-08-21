<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Models\ProdukModel;

class ProdukController extends ResourceController
{
    protected $modelName = ProdukModel::class;
    protected $format    = 'json';

    public function index()
    {
        $produks = $this->model->findAll();
        return $this->respond($produks);
    }

    public function create()
    {
        // Aturan validasi
        $rules = [
            'nama_produk' => 'required|string|max_length[255]|is_unique[produk.nama_produk]',
            'harga' => 'required|numeric|greater_than_equal_to[0]',
            'stok' => 'required|integer|greater_than_equal_to[0]',
        ];

        // Validasi input dari request
        if (!$this->validate($rules)) {
            return $this->fail($this->validator->getErrors());
        }

        // Mengambil data dari request body menggunakan getVar()
        $data = $this->request->getVar();
        
        // Simpan data ke database
        $this->model->insert($data);
        $produkBaru = $this->model->find($this->model->insertID);

        return $this->respondCreated([
            'message' => 'Produk berhasil ditambahkan!',
            'data' => $produkBaru
        ]);
    }

    public function show($id = null)
    {
        $produk = $this->model->find($id);
        if (!$produk) {
            return $this->failNotFound('Produk tidak ditemukan.');
        }
        return $this->respond([
            'message' => 'Detail produk berhasil diambil.',
            'data' => $produk
        ]);
    }

    public function update($id = null)
    {
        $produk = $this->model->find($id);
        if (!$produk) {
            return $this->failNotFound('Produk tidak ditemukan.');
        }

        $rules = [
            'nama_produk' => 'required|string|max_length[255]|is_unique[produk.nama_produk,id_produk,' . $id . ']',
            'harga' => 'required|numeric|greater_than_equal_to[0]',
            'stok' => 'required|integer|greater_than_equal_to[0]',
        ];

        if (!$this->validate($rules)) {
            return $this->fail($this->validator->getErrors());
        }
        
        $data = $this->request->getVar();
        $this->model->update($id, $data);
        $produkUpdate = $this->model->find($id);

        return $this->respondUpdated([
            'message' => 'Produk berhasil diupdate!',
            'data' => $produkUpdate
        ]);
    }

    public function delete($id = null)
    {
        $produk = $this->model->find($id);
        if (!$produk) {
            return $this->failNotFound('Produk tidak ditemukan.');
        }
        $this->model->delete($id);
        return $this->respondDeleted([
            'message' => 'Produk berhasil dihapus!'
        ]);
    }
}
