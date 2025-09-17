<?= $this->extend('produk/layout') ?>

<?= $this->section('content') ?>
<main class="p-4 pt-20 sm:ml-16">
    <div class="p-4 rounded-lg w-full">
        <h1 class="text-3xl font-extrabold mb-8 text-gray-900 dark:text-white tracking-tight">Laporan Penjualan</h1>

        <div class="mb-8 p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
            <form action="<?= base_url('admin/laporan') ?>" method="get">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                    <div>
                        <label for="start_date" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tanggal Mulai</label>
                        <input type="date" name="start_date" id="start_date" value="<?= esc($startDate) ?>" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                    </div>
                    <div>
                        <label for="end_date" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tanggal Selesai</label>
                        <input type="date" name="end_date" id="end_date" value="<?= esc($endDate) ?>" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                    </div>
                    <button type="submit" class="text-white bg-blue-600 hover:bg-blue-700 font-semibold rounded-lg text-sm px-5 py-2.5 h-10">Tampilkan Laporan</button>
                </div>
            </form>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <div class="bg-gradient-to-r from-green-500 to-green-600 text-white p-6 rounded-2xl shadow-lg">
                <p class="text-sm font-medium opacity-80">TOTAL PENDAPATAN</p>
                <h2 class="text-3xl font-bold mt-1">Rp <?= number_format($totalPendapatan, 0, ',', '.') ?></h2>
            </div>
            <div class="bg-gradient-to-r from-blue-500 to-indigo-600 text-white p-6 rounded-2xl shadow-lg">
                <p class="text-sm font-medium opacity-80">PRODUK TERJUAL</p>
                <h2 class="text-3xl font-bold mt-1"><?= $totalProdukTerjual ?></h2>
            </div>
            <div class="bg-gradient-to-r from-yellow-400 to-orange-500 text-white p-6 rounded-2xl shadow-lg">
                <p class="text-sm font-medium opacity-80">JUMLAH TRANSAKSI</p>
                <h2 class="text-3xl font-bold mt-1"><?= $jumlahTransaksi ?></h2>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div>
                <h2 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white">Produk Terlaris</h2>
                <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-300">
                            <tr>
                                <th scope="col" class="px-6 py-3">No</th>
                                <th scope="col" class="px-6 py-3">Nama Produk</th>
                                <th scope="col" class="px-6 py-3">Total Terjual</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($produkTerlaris)): ?>
                                <tr>
                                    <td colspan="3" class="text-center py-4">Tidak ada data.</td>
                                </tr>
                            <?php else: ?>
                                <?php $no = 1;
                                foreach ($produkTerlaris as $nama => $jumlah): ?>
                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                        <td class="px-6 py-4"><?= $no++ ?></td>
                                        <td class="px-6 py-4 font-medium text-gray-900 dark:text-white"><?= esc($nama) ?></td>
                                        <td class="px-6 py-4"><?= $jumlah ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div>
                <h2 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white">Rincian Transaksi</h2>
                <div class="relative overflow-x-auto shadow-md sm:rounded-lg max-h-96">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-300 sticky top-0">
                            <tr>
                                <th scope="col" class="px-6 py-3">Waktu</th>
                                <th scope="col" class="px-6 py-3">Produk</th>
                                <th scope="col" class="px-6 py-3">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($transaksi)): ?>
                                <tr>
                                    <td colspan="3" class="text-center py-4">Tidak ada transaksi pada periode ini.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($transaksi as $item): ?>
                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                        <td class="px-6 py-4">
                                            <?= (new DateTime($item['tanggal'], new DateTimeZone('UTC')))->setTimezone(new DateTimeZone('Asia/Jakarta'))->format('d M Y, H:i') ?>
                                        </td>
                                        <td class="px-6 py-4 font-medium text-gray-900 dark:text-white"><?= esc($item['nama_produk']) ?> (x<?= $item['kuantitas'] ?>)</td>
                                        <td class="px-6 py-4">Rp <?= number_format($item['total_harga'], 0, ',', '.') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>
<?= $this->endSection() ?>