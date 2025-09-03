<?= $this->extend('produk/layout') ?>

<?= $this->section('content') ?>
<main class="p-4 pt-20 sm:ml-8">
    <div class="p-6 rounded-lg dark:border-gray-700 h-auto">
        <h1 class="text-2xl font-bold mb-6 text-gray-900 dark:text-white">
            <?= isset($produk) ? 'Edit Produk' : 'Tambah Produk' ?>
        </h1>
        <?php if (session()->getFlashdata('errors')): ?>
            <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-100 dark:bg-gray-800 dark:text-red-400" role="alert">
                <ul class="list-disc list-inside">
                    <?php foreach (session()->getFlashdata('errors') as $error): ?>
                        <li><?= esc($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        <div class="border border-gray-300 dark:border-gray-600 rounded-lg p-6 bg-white dark:bg-gray-800 shadow-md">
            <form action="<?= isset($produk) ? base_url('admin/produk/update') : base_url('admin/produk/simpan') ?>" method="post" enctype="multipart/form-data">
                <input type="hidden" name="id_produk" value="<?= isset($produk) ? $produk['id_produk'] : '' ?>">

                <div class="grid gap-4 mb-4 grid-cols-2">
                    <div class="col-span-2">
                        <label for="nama-produk" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            Nama Produk
                        </label>
                        <input type="text" name="nama_produk" id="nama-produk"
                            value="<?= isset($produk) ? $produk['nama_produk'] : '' ?>"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600
                                   focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500
                                   dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                            placeholder="Masukkan nama produk" required>
                    </div>

                    <div class="col-span-2 sm:col-span-1">
                        <label for="harga" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Harga</label>
                        <input type="number" name="harga" id="harga" min="10"
                            value="<?= isset($produk) ? $produk['harga'] : '' ?>"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600
                                   focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500
                                   dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                            placeholder="Rp" required>
                    </div>

                    <div class="col-span-2 sm:col-span-1">
                        <label for="stok" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Stok</label>
                        <input type="number" name="stok" id="stok" min="0"
                            value="<?= isset($produk) ? $produk['stok'] : '' ?>"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600
                                   focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500
                                   dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                            placeholder="Jumlah stok" required>
                    </div>

                    <div class="col-span-2">
                        <label for="id_restoker" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Restoker / Supplier</label>
                        <select name="id_restoker" id="id_restoker" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required>
                            <option value="">-- Pilih Restoker --</option>
                            <?php foreach ($restokers as $restoker): ?>
                                <option value="<?= $restoker['id_restoker'] ?>"
                                    <?= (isset($produk) && $produk['id_restoker'] == $restoker['id_restoker']) ? 'selected' : '' ?>>
                                    <?= esc($restoker['nama_restoker']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-span-2">
                        <label for="gambar_produk" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Gambar Produk</label>
                        <input type="file" name="gambar_produk" id="gambar_produk" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white">
                        <?php if (isset($produk) && $produk['gambar_produk']): ?>
                            <img src="<?= base_url('uploads/produk/' . $produk['gambar_produk']) ?>" alt="<?= $produk['nama_produk'] ?>" class="mt-2 w-32 h-32 object-cover rounded-lg">
                        <?php endif; ?>
                    </div>

                </div>

                <button type="submit"
                    class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none
                           focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center
                           dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                    Simpan Produk
                </button>
                <a href="<?= base_url('admin/produk') ?>"
                    class="text-gray-900 bg-white border border-gray-300 hover:bg-gray-100 focus:ring-4 focus:outline-none
                           focus:ring-gray-200 font-medium rounded-lg text-sm px-5 py-2.5 text-center
                           dark:bg-gray-700 dark:text-white dark:border-gray-600 dark:hover:bg-gray-600
                           dark:hover:border-gray-600 dark:focus:ring-gray-700 ml-2">
                    Batal
                </a>
            </form>
        </div>
    </div>
</main>
<?= $this->endSection() ?>