<?= $this->extend('produk/layout') ?>

<?= $this->section('content') ?>
<main class="p-4 pt-20 sm:ml-16">
    <div class="p-4 rounded-lg w-full">

        <!-- Pesan Status Global -->
        <?php if (session()->getFlashdata('message')): ?>
            <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-100 dark:bg-gray-800 dark:text-green-400" role="alert">
                <?= session()->getFlashdata('message') ?>
            </div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-100 dark:bg-gray-800 dark:text-red-400" role="alert">
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>

        <!-- Kontrol Tab -->
        <div class="mb-4 border-b border-gray-200 dark:border-gray-700">
            <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="myTab" data-tabs-toggle="#myTabContent" role="tablist">
                <li class="mr-2" role="presentation">
                    <button class="inline-block p-4 border-b-2 rounded-t-lg" id="restok-tab" data-tabs-target="#restok" type="button" role="tab" aria-controls="restok" aria-selected="true">Manajemen Restok</button>
                </li>
                <li class="mr-2" role="presentation">
                    <button class="inline-block p-4 border-b-2 rounded-t-lg" id="supplier-tab" data-tabs-target="#supplier" type="button" role="tab" aria-controls="supplier" aria-selected="false">Manajemen Supplier</button>
                </li>
            </ul>
        </div>

        <!-- Konten Tab -->
        <div id="myTabContent">
            <!-- Tab 1: Manajemen Restok -->
            <div class="hidden p-4 rounded-lg bg-gray-50 dark:bg-gray-800" id="restok" role="tabpanel" aria-labelledby="restok-tab">
                <h2 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white">Riwayat Restok</h2>
                <button type="button" data-modal-target="tambah-restok-modal" data-modal-toggle="tambah-restok-modal" class="inline-flex items-center gap-2 text-white bg-blue-600 hover:bg-blue-700 font-semibold rounded-lg text-sm px-5 py-2.5 mb-4">
                    ➕ Tambah Pesanan Restok
                </button>
                <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-300">
                            <tr>
                                <th scope="col" class="px-6 py-3">Supplier</th>
                                <th scope="col" class="px-6 py-3">Produk</th>
                                <th scope="col" class="px-6 py-3">Dipesan</th>
                                <th scope="col" class="px-6 py-3">Diterima</th>
                                <th scope="col" class="px-6 py-3">Diretur</th>
                                <th scope="col" class="px-6 py-3">Tgl Pesan</th>
                                <th scope="col" class="px-6 py-3">Tgl Diterima</th>
                                <th scope="col" class="px-6 py-3">Status</th>
                                <th scope="col" class="px-6 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($riwayat_restok)): ?>
                                <tr>
                                    <td colspan="8" class="text-center py-4 dark:text-gray-400">Belum ada riwayat restok.</td>
                                </tr>
                                <?php else: ?><?php foreach ($riwayat_restok as $item): ?>
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                    <td class="px-6 py-4 font-medium text-gray-900 dark:text-white"><?= esc($item['nama_restoker']) ?></td>
                                    <td class="px-6 py-4"><?= esc($item['nama_produk']) ?></td>
                                    <td class="px-6 py-4"><?= $item['jumlah_pesan'] ?></td>
                                    <td class="px-6 py-4"><?= $item['jumlah_diterima'] ?? 0 ?></td>
                                    <td class="px-6 py-4"><?= $item['jumlah_retur'] ?? 0 ?></td>
                                    <td class="px-6 py-4"><?= date('d M Y', strtotime($item['tanggal_pesan'])) ?></td>
                                    <td class="px-6 py-4"><?= $item['tanggal_diterima'] ? date('d M Y', strtotime($item['tanggal_diterima'])) : '---' ?></td>
                                    <td class="px-6 py-4">
                                        <?php
                                                    $statusClass = 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300';
                                                    $statusText = esc($item['status']);
                                                    if ($item['status'] === 'Dipesan') {
                                                        $statusClass = 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300';
                                                    } elseif ($item['status'] === 'Diterima Sebagian') {
                                                        $statusClass = 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300';
                                                        $statusText = "Sebagian (" . esc($item['jumlah_diterima']) . "/" . esc($item['jumlah_pesan']) . ")";
                                                    } elseif ($item['status'] === 'Diterima') {
                                                        $statusClass = 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300';
                                                    } elseif ($item['status'] === 'Batal') {
                                                        $statusClass = 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300';
                                                    } elseif ($item['status'] === 'Diretur') {
                                                        $statusClass = 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300';
                                                    }
                                        ?>
                                        <span class="px-2 py-1 text-xs font-bold rounded-full <?= $statusClass ?>"><?= $statusText ?></span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <!-- LOGIKA IF/ELSEIF/ELSE YANG DIPERBAIKI -->
                                        <?php if ($item['status'] === 'Dipesan' || $item['status'] === 'Diterima Sebagian'): ?>
                                            <button type="button" data-modal-target="konfirmasi-modal" data-modal-toggle="konfirmasi-modal" data-id-restok="<?= $item['id_restok'] ?>" data-nama-produk="<?= esc($item['nama_produk']) ?>" data-jumlah-pesan="<?= esc($item['jumlah_pesan']) ?>" data-jumlah-diterima="<?= esc($item['jumlah_diterima'] ?? 0) ?>" class="font-medium text-blue-600 dark:text-blue-500 hover:underline btn-konfirmasi">Konfirmasi</button>
                                        <?php elseif ($item['status'] === 'Diterima'): ?>
                                            <button type="button" data-modal-target="return-modal" data-modal-toggle="return-modal" data-id-restok="<?= $item['id_restok'] ?>" data-nama-produk="<?= esc($item['nama_produk']) ?>" data-jumlah-diterima="<?= esc($item['jumlah_diterima'] ?? 0) ?>" class="font-medium text-red-600 dark:text-red-500 hover:underline btn-return">Return</button>
                                        <?php else: ?>
                                            <span>---</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?><?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tab 2: Manajemen Supplier -->
            <div class="hidden p-4 rounded-lg bg-gray-50 dark:bg-gray-800" id="supplier" role="tabpanel" aria-labelledby="supplier-tab">
                <h2 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white">Daftar Supplier</h2>
                <button type="button" data-modal-target="tambah-supplier-modal" data-modal-toggle="tambah-supplier-modal" class="inline-flex items-center gap-2 text-white bg-blue-600 hover:bg-blue-700 font-semibold rounded-lg text-sm px-5 py-2.5 mb-4">
                    ➕ Tambah Supplier
                </button>
                <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-300">
                            <tr>
                                <th scope="col" class="px-6 py-3">Nama Supplier</th>
                                <th scope="col" class="px-6 py-3">Kontak</th>
                                <th scope="col" class="px-6 py-3">Alamat</th>
                                <th scope="col" class="px-6 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($restokers)): ?>
                                <tr>
                                    <td colspan="4" class="text-center py-4 dark:text-gray-400">Belum ada data supplier.</td>
                                </tr>
                                <?php else: ?><?php foreach ($restokers as $supplier): ?>
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                    <td class="px-6 py-4 font-medium text-gray-900 dark:text-white"><?= esc($supplier['nama_restoker']) ?></td>
                                    <td class="px-6 py-4"><?= esc($supplier['kontak']) ?></td>
                                    <td class="px-6 py-4"><?= esc($supplier['alamat']) ?></td>
                                    <td class="px-6 py-4 text-center">
                                        <button type="button" data-modal-target="edit-supplier-modal" data-modal-toggle="edit-supplier-modal" class="font-medium text-blue-600 dark:text-blue-500 hover:underline btn-edit-supplier"
                                            data-id="<?= $supplier['id_restoker'] ?>" data-nama="<?= esc($supplier['nama_restoker']) ?>"
                                            data-kontak="<?= esc($supplier['kontak']) ?>" data-alamat="<?= esc($supplier['alamat']) ?>">Edit</button>
                                        <a href="<?= base_url('admin/restok/supplier/delete/' . $supplier['id_restoker']) ?>" class="font-medium text-red-600 dark:text-red-500 hover:underline ml-4" onclick="return confirm('Yakin ingin menghapus supplier ini?')">Hapus</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?><?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Modal Tambah Restok -->
<div id="tambah-restok-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-md max-h-full">
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Buat Pesanan Restok Baru</h3>
                <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-toggle="tambah-restok-modal">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                    </svg><span class="sr-only">Close modal</span>
                </button>
            </div>
            <form action="<?= base_url('admin/restok/create') ?>" method="POST" class="p-4 md:p-5">
                <div class="grid gap-4 mb-4 grid-cols-2">
                    <div class="col-span-2">
                        <label for="restoker" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Pilih Restoker</label>
                        <select id="restoker" name="id_restoker" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white" required>
                            <option value="">-- Pilih Restoker --</option>
                            <?php if (!empty($restokers)): ?><?php foreach ($restokers as $restoker): ?>
                            <option value="<?= $restoker['id_restoker'] ?>"><?= esc($restoker['nama_restoker']) ?></option>
                            <?php endforeach; ?><?php endif; ?>
                        </select>
                    </div>
                    <div class="col-span-2">
                        <label for="produk" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Produk</label>
                        <select id="produk" name="id_produk" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white" disabled required>
                            <option value="">-- Pilih Restoker Dulu --</option>
                        </select>
                    </div>
                    <div class="col-span-2">
                        <label for="jumlah_pesan" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Jumlah Pesan</label>
                        <input type="number" name="jumlah_pesan" id="jumlah_pesan" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white" placeholder="0" required>
                    </div>
                </div>
                <button type="submit" class="text-white inline-flex items-center bg-blue-700 hover:bg-blue-800 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                    <svg class="me-1 -ms-1 w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"></path>
                    </svg>
                    Tambah Pesanan
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi -->
<div id="konfirmasi-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-md max-h-full">
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Konfirmasi Penerimaan Barang</h3>
                <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-toggle="konfirmasi-modal">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                    </svg><span class="sr-only">Close modal</span>
                </button>
            </div>
            <form id="form-konfirmasi" method="POST" class="p-4 md:p-5">
                <p class="mb-4 text-sm text-gray-500 dark:text-gray-400">Anda akan mengonfirmasi penerimaan untuk: <br><strong id="konfirmasi-nama-produk" class="text-lg text-gray-900 dark:text-white"></strong></p>
                <div class="mb-4">
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Jumlah Dipesan</label>
                    <input type="text" id="konfirmasi-jumlah-pesan" class="bg-gray-200 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-gray-400" readonly>
                </div>
                <div class="mb-4">
                    <label for="jumlah_diterima" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Total Jumlah Diterima</label>
                    <input type="number" name="jumlah_diterima" id="jumlah_diterima" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white" required>
                </div>
                <button type="submit" class="text-white inline-flex items-center bg-green-600 hover:bg-green-700 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-green-500 dark:hover:bg-green-600 dark:focus:ring-green-800">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    Konfirmasi & Perbarui Stok
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Modal retur -->
<div id="return-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-md max-h-full">
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Proses Return Barang</h3>
                <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-toggle="return-modal">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                    </svg><span class="sr-only">Close modal</span>
                </button>
            </div>
            <form id="form-return" method="POST" class="p-4 md:p-5">
                <p class="mb-4 text-sm text-gray-500 dark:text-gray-400">Anda akan memproses retur untuk: <br><strong id="return-nama-produk" class="text-lg text-gray-900 dark:text-white"></strong></p>
                <div class="mb-4">
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Jumlah Diterima</label>
                    <input type="text" id="return-jumlah-diterima" class="bg-gray-200 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-gray-400" readonly>
                </div>
                <div class="mb-4">
                    <label for="jumlah_retur" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Jumlah yang Diretur</label>
                    <input type="number" name="jumlah_retur" id="jumlah_retur" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500" required>
                </div>
                <button type="submit" class="text-white inline-flex items-center bg-red-600 hover:bg-red-700 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-red-500 dark:hover:bg-red-600 dark:focus:ring-red-800">
                    Proses Return
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Modal Tambah Supplier -->
<div id="tambah-supplier-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-md max-h-full">
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Tambah Supplier Baru</h3>
                <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-toggle="tambah-supplier-modal">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                    </svg><span class="sr-only">Close modal</span>
                </button>
            </div>
            <form action="<?= base_url('admin/restok/supplier/create') ?>" method="POST" class="p-4 md:p-5">
                <div class="grid gap-4 mb-4">
                    <div>
                        <label for="nama_restoker" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama Supplier</label>
                        <input type="text" name="nama_restoker" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white" required>
                    </div>
                    <div>
                        <label for="kontak" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Kontak (Telp/Email)</label>
                        <input type="text" name="kontak" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white">
                    </div>
                    <div>
                        <label for="alamat" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Alamat</label>
                        <textarea name="alamat" rows="3" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white"></textarea>
                    </div>
                </div>
                <button type="submit" class="text-white inline-flex items-center bg-blue-700 hover:bg-blue-800 font-medium rounded-lg text-sm px-5 py-2.5">
                    <svg class="me-1 -ms-1 w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"></path>
                    </svg>
                    Tambah Supplier
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Supplier -->
<div id="edit-supplier-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-md max-h-full">
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Edit Data Supplier</h3>
                <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-toggle="edit-supplier-modal">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                    </svg><span class="sr-only">Close modal</span>
                </button>
            </div>
            <form id="form-edit-supplier" method="POST" class="p-4 md:p-5">
                <div class="grid gap-4 mb-4">
                    <div>
                        <label for="edit_nama_restoker" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama Supplier</label>
                        <input type="text" name="nama_restoker" id="edit_nama_restoker" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white" required>
                    </div>
                    <div>
                        <label for="edit_kontak" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Kontak (Telp/Email)</label>
                        <input type="text" name="kontak" id="edit_kontak" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white">
                    </div>
                    <div>
                        <label for="edit_alamat" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Alamat</label>
                        <textarea name="alamat" id="edit_alamat" rows="3" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white"></textarea>
                    </div>
                </div>
                <button type="submit" class="text-white inline-flex items-center bg-blue-700 hover:bg-blue-800 font-medium rounded-lg text-sm px-5 py-2.5">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z"></path>
                        <path fill-rule="evenodd" d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" clip-rule="evenodd"></path>
                    </svg>
                    Simpan Perubahan
                </button>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- Skrip untuk Modal Tambah Restok (Dropdown Dinamis) ---
        const restokerSelect = document.getElementById('restoker');
        const produkSelect = document.getElementById('produk');

        if (restokerSelect) {
            restokerSelect.addEventListener('change', function() {
                const restokerId = this.value;
                produkSelect.innerHTML = '<option selected>Memuat...</option>';
                produkSelect.disabled = true;

                if (restokerId) {
                    fetch(`<?= base_url('admin/restok/get-produk/') ?>/${restokerId}`, {
                            headers: {
                                "X-Requested-With": "XMLHttpRequest"
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            produkSelect.innerHTML = '<option value="">-- Pilih Produk --</option>';
                            if (data.length > 0) {
                                data.forEach(produk => {
                                    const option = document.createElement('option');
                                    option.value = produk.id_produk;
                                    option.textContent = produk.nama_produk;
                                    produkSelect.appendChild(option);
                                });
                                produkSelect.disabled = false;
                            } else {
                                produkSelect.innerHTML = '<option value="">-- Tidak ada produk untuk supplier ini --</option>';
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            produkSelect.innerHTML = '<option value="">-- Gagal memuat produk --</option>';
                        });
                } else {
                    produkSelect.innerHTML = '<option value="">-- Pilih Restoker Dulu --</option>';
                }
            });
        }

        // --- Skrip untuk Modal Konfirmasi Penerimaan ---
        const konfirmasiButtons = document.querySelectorAll('.btn-konfirmasi');
        const formKonfirmasi = document.getElementById('form-konfirmasi');
        const namaProdukKonfirmasi = document.getElementById('konfirmasi-nama-produk');
        const jumlahPesanKonfirmasi = document.getElementById('konfirmasi-jumlah-pesan');
        const jumlahDiterimaInput = document.getElementById('jumlah_diterima');

        konfirmasiButtons.forEach(button => {
            button.addEventListener('click', function() {
                const idRestok = this.dataset.idRestok;
                const namaProduk = this.dataset.namaProduk;
                const jumlahPesan = this.dataset.jumlahPesan;
                const jumlahDiterima = this.dataset.jumlahDiterima;

                formKonfirmasi.action = `<?= base_url('admin/restok/update/') ?>/${idRestok}`;
                namaProdukKonfirmasi.textContent = namaProduk;
                jumlahPesanKonfirmasi.value = jumlahPesan;
                jumlahDiterimaInput.value = jumlahDiterima;
            });
        });

        // --- Skrip untuk Modal Edit Supplier ---
        const editSupplierButtons = document.querySelectorAll('.btn-edit-supplier');
        const formEditSupplier = document.getElementById('form-edit-supplier');
        const editNama = document.getElementById('edit_nama_restoker');
        const editKontak = document.getElementById('edit_kontak');
        const editAlamat = document.getElementById('edit_alamat');

        editSupplierButtons.forEach(button => {
            button.addEventListener('click', function() {
                const id = this.dataset.id;
                const nama = this.dataset.nama;
                const kontak = this.dataset.kontak;
                const alamat = this.dataset.alamat;

                formEditSupplier.action = `<?= base_url('admin/restok/supplier/update/') ?>/${id}`;
                editNama.value = nama;
                editKontak.value = kontak;
                editAlamat.value = alamat;
            });
        });
        // --- Skrip BARU untuk Modal Return ---
        const returnButtons = document.querySelectorAll('.btn-return');
        const formReturn = document.getElementById('form-return');
        const namaProdukReturn = document.getElementById('return-nama-produk');
        const jumlahDiterimaReturn = document.getElementById('return-jumlah-diterima');

        returnButtons.forEach(button => {
            button.addEventListener('click', function() {
                const idRestok = this.dataset.idRestok;
                const namaProduk = this.dataset.namaProduk;
                const jumlahDiterima = this.dataset.jumlahDiterima;

                formReturn.action = `<?= base_url('admin/restok/return/') ?>/${idRestok}`;
                namaProdukReturn.textContent = namaProduk;
                jumlahDiterimaReturn.value = jumlahDiterima;
                document.getElementById('jumlah_retur').value = ''; // Kosongkan input
            });
        });
    });
</script>
<?= $this->endSection() ?>