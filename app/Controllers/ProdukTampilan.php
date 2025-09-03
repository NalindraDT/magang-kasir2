<?php

namespace App\Controllers;

use App\Models\ProdukModel;
use App\Models\RestokerModel;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ProdukTampilan extends BaseController
{
    protected $produkModel;
    protected $restokerModel; // TAMBAHKAN INI

    public function __construct()
    {
        $this->produkModel = new ProdukModel();
        $this->restokerModel = new RestokerModel(); // TAMBAHKAN INI
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
        // Ambil data restoker untuk dropdown
        $data['restokers'] = $this->restokerModel->findAll();
        return view('produk/produk_view_form', $data);
    }

    public function simpan()
    {
        // Validasi input
        $rules = [
            'nama_produk' => 'required|string|max_length[255]|is_unique[produk.nama_produk]',
            'harga' => 'required|numeric|greater_than_equal_to[0]',
            'stok' => 'required|integer|greater_than_equal_to[0]',
            'id_restoker' => 'required|integer',
            'gambar_produk' => [
                'rules' => 'uploaded[gambar_produk]|max_size[gambar_produk,1024]|is_image[gambar_produk]|mime_in[gambar_produk,image/jpg,image/jpeg,image/png]',
                'errors' => [
                    'uploaded' => 'Pilih gambar produk terlebih dahulu.',
                    'max_size' => 'Ukuran gambar terlalu besar.',
                    'is_image' => 'File yang diunggah bukan gambar.',
                    'mime_in' => 'Format gambar harus jpg, jpeg, atau png.'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            session()->setFlashdata('errors', $this->validator->getErrors());
            return redirect()->back()->withInput();
        }

        // Ambil file gambar
        $gambarProduk = $this->request->getFile('gambar_produk');

        // Pindahkan file ke folder public/uploads/produk
        $namaGambar = $gambarProduk->getRandomName();
        $gambarProduk->move('uploads/produk', $namaGambar);

        // Ambil data dari form
        $data = [
            'nama_produk' => $this->request->getPost('nama_produk'),
            'harga' => $this->request->getPost('harga'),
            'stok' => $this->request->getPost('stok'),
            'id_restoker' => $this->request->getPost('id_restoker'),
            'gambar_produk' => $namaGambar, // Simpan nama file gambar
        ];

        // Simpan data ke database
        $this->produkModel->save($data);

        // Redirect kembali ke halaman produk dengan pesan sukses
        session()->setFlashdata('message', 'Produk berhasil ditambahkan!');
        return redirect()->to(base_url('admin/produk'));
    }

    public function edit($id = null)
    {
        // Ambil data produk berdasarkan ID
        $data['produk'] = $this->produkModel->find($id);
        if (empty($data['produk'])) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Produk tidak ditemukan.');
        }

        // Ambil juga data restoker untuk dropdown
        $data['restokers'] = $this->restokerModel->findAll();

        return view('produk/produk_view_form', $data);
    }


    public function update()
    {
        $id = $this->request->getPost('id_produk');

        // Validasi unik nama produk, abaikan produk dengan ID yang sedang diedit
        $rules = [
            'nama_produk' => "required|string|max_length[255]|is_unique[produk.nama_produk,id_produk,{$id}]",
            'harga' => 'required|numeric|greater_than_equal_to[0]',
            'stok' => 'required|integer|greater_than_equal_to[0]',
            'id_restoker' => 'required|integer',
            'gambar_produk' => [
                'rules' => 'max_size[gambar_produk,1024]|is_image[gambar_produk]|mime_in[gambar_produk,image/jpg,image/jpeg,image/png]',
                'errors' => [
                    'max_size' => 'Ukuran gambar terlalu besar.',
                    'is_image' => 'File yang diunggah bukan gambar.',
                    'mime_in' => 'Format gambar harus jpg, jpeg, atau png.'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            session()->setFlashdata('errors', $this->validator->getErrors());
            return redirect()->back()->withInput();
        }

        $data = [
            'nama_produk' => $this->request->getPost('nama_produk'),
            'harga' => $this->request->getPost('harga'),
            'stok' => $this->request->getPost('stok'),
            'id_restoker' => $this->request->getPost('id_restoker'),
        ];

        // Cek apakah ada file gambar yang diunggah
        $gambarProduk = $this->request->getFile('gambar_produk');
        if ($gambarProduk->isValid() && !$gambarProduk->hasMoved()) {
            // Hapus gambar lama jika ada
            $produkLama = $this->produkModel->find($id);
            if ($produkLama['gambar_produk'] && file_exists('uploads/produk/' . $produkLama['gambar_produk'])) {
                unlink('uploads/produk/' . $produkLama['gambar_produk']);
            }

            // Pindahkan file baru
            $namaGambar = $gambarProduk->getRandomName();
            $gambarProduk->move('uploads/produk', $namaGambar);
            $data['gambar_produk'] = $namaGambar;
        }

        $this->produkModel->update($id, $data);

        session()->setFlashdata('message', 'Produk berhasil diupdate!');
        return redirect()->to(base_url('admin/produk'));
    }

    public function hapus($id = null)
    {
        // Ambil data produk untuk mendapatkan nama file gambar
        $produk = $this->produkModel->find($id);

        // Hapus file gambar jika ada
        if ($produk['gambar_produk'] && file_exists('uploads/produk/' . $produk['gambar_produk'])) {
            unlink('uploads/produk/' . $produk['gambar_produk']);
        }

        // Hapus produk dari database
        $this->produkModel->delete($id);

        $page = $this->request->getGet('page') ?? 1;
        // Redirect kembali ke halaman produk dengan pesan sukses
        session()->setFlashdata('message', 'Produk berhasil dihapus!');
        return redirect()->to(base_url('admin/produk?page=' . $page));
    }
    public function import()
    {
        $file = $this->request->getFile('excel_file');

        // Validasi file
        if ($file === null || !$file->isValid() || !in_array($file->getMimeType(), ['application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])) {
            return redirect()->to(base_url('admin/produk'))->with('error', 'File tidak valid atau bukan file Excel. Silakan unggah file .xls atau .xlsx');
        }

        try {
            // Baca file Excel
            $spreadsheet = IOFactory::load($file->getTempName());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            $importedCount = 0;
            $errorCount = 0;
            $errors = [];

            // Loop melalui setiap baris (mulai dari baris ke-2 untuk melewati header)
            foreach ($rows as $key => $row) {
                if ($key == 0) {
                    continue; // Lewati baris header
                }

                // Asumsikan urutan kolom: Nama Produk, Harga, Stok, ID Restoker (Opsional)
                $nama_produk = $row[0] ?? null;
                $harga = $row[1] ?? null;
                $stok = $row[2] ?? null;
                $id_restoker = $row[3] ?? null;

                // Validasi data sederhana
                if (empty($nama_produk) || !is_numeric($harga) || !is_numeric($stok)) {
                    $errorCount++;
                    $errors[] = "Baris " . ($key + 1) . ": Data tidak lengkap atau format salah.";
                    continue;
                }

                // Cek apakah produk sudah ada
                $existingProduct = $this->produkModel->where('nama_produk', $nama_produk)->first();
                if ($existingProduct) {
                    // Jika sudah ada, update data
                    $this->produkModel->update($existingProduct['id_produk'], [
                        'harga' => $harga,
                        'stok' => $stok,
                        'id_restoker' => $id_restoker
                    ]);
                } else {
                    // Jika belum ada, insert data baru
                    $this->produkModel->insert([
                        'nama_produk' => $nama_produk,
                        'harga' => $harga,
                        'stok' => $stok,
                        'id_restoker' => $id_restoker
                    ]);
                }
                $importedCount++;
            }

            $message = "Berhasil mengimpor atau memperbarui {$importedCount} produk.";
            if ($errorCount > 0) {
                session()->setFlashdata('import_errors', $errors);
                $message .= " Gagal memproses {$errorCount} baris.";
                return redirect()->to(base_url('admin/produk'))->with('error', $message);
            }

            return redirect()->to(base_url('admin/produk'))->with('message', $message);
        } catch (\Exception $e) {
            return redirect()->to(base_url('admin/produk'))->with('error', 'Terjadi kesalahan saat memproses file: ' . $e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        // Nama file template
        $filename = 'template_import_produk.xlsx';
        $filepath = WRITEPATH . 'template_import_produk.xlsx';

        // Buat file template jika belum ada
        if (!file_exists($filepath)) {
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setCellValue('A1', 'Nama Produk');
            $sheet->setCellValue('B1', 'Harga');
            $sheet->setCellValue('C1', 'Stok');
            $sheet->setCellValue('D1', 'ID Restoker (Opsional)');

            // Contoh data
            $sheet->setCellValue('A2', 'Contoh Produk 1');
            $sheet->setCellValue('B2', '15000');
            $sheet->setCellValue('C2', '100');
            $sheet->setCellValue('D2', '1');

            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save($filepath);
        }

        return $this->response->download($filepath, null)->setFileName($filename);
    }
}
