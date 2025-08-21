<?= $this->extend('produk/layout') ?>

<?= $this->section('content') ?>
<main class="p-4 sm:ml-16 flex flex-col items-center">
    <!-- Kontainer utama tanpa garis putus-putus -->
    <div class="p-4 rounded-lg min-h-screen max-w-full w-full">
        <h1 class="text-2xl font-bold mb-6 text-gray-900 dark:text-white">Manajemen Produk</h1>

        <!-- Pesan Status -->
        <?php if (session()->getFlashdata('message')): ?>
            <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400" role="alert">
                <?= session()->getFlashdata('message') ?>
            </div>
        <?php endif; ?>

        <!-- Tombol Tambah Produk -->
        <a href="<?= base_url('produk/tambah') ?>" class="inline-block w-fit text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
            Tambah Produk
        </a>

        <!-- Tabel Produk -->
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg mt-6 w-full">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">NO</th>
                        <th scope="col" class="px-6 py-3">Nama Produk</th>
                        <th scope="col" class="px-6 py-3">Harga</th>
                        <th scope="col" class="px-6 py-3">Stok</th>
                        <th scope="col" class="px-6 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody id="produk-table-body">
                    <?php if (empty($produks)): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">Tidak ada data produk.</td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1; ?>
                        <?php foreach ($produks as $produk): ?>
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white"><?= $no++ ?></td>
                                <td class="px-6 py-4"><?= $produk['nama_produk'] ?></td>
                                <td class="px-6 py-4">Rp <?= number_format($produk['harga'], 0, ',', '.') ?></td>
                                <td class="px-6 py-4"><?= $produk['stok'] ?></td>
                                <td class="px-6 py-4">
                                    <a href="<?= base_url('produk/edit/' . $produk['id_produk']) ?>" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">Edit</a>
                                    <a href="<?= base_url('produk/hapus/' . $produk['id_produk']) ?>" class="font-medium text-red-600 dark:text-red-500 hover:underline ml-2" onclick="return confirm('Apakah Anda yakin ingin menghapus produk ini?')">Hapus</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>
<?= $this->endSection() ?>
