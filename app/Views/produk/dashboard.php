<?= $this->extend('produk/layout') ?>
<?= $this->section('content') ?>
<main>
    <div class="p-4 pt-20 rounded-lg  dark:border-gray-700 min-h-screen">
        <h1 class="text-3xl font-extrabold mb-8 text-gray-900 dark:text-white tracking-tight">📊 Dashboard</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            
            <div class="bg-gradient-to-r from-green-500 to-green-600 text-white p-6 rounded-2xl shadow-lg hover:scale-105 transform transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium opacity-80">JUMLAH PRODUK</p>
                        <h2 class="text-3xl font-bold mt-1"><?= $jumlah_produk ?></h2>
                    </div>
                    <div class="bg-white/20 p-3 rounded-full">
                        📦
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-r from-blue-500 to-indigo-600 text-white p-6 rounded-2xl shadow-lg hover:scale-105 transform transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium opacity-80">TOTAL PENGHASILAN</p>
                        <h2 class="text-3xl font-bold mt-1"><?= $total_penghasilan ?></h2>
                    </div>
                    <div class="bg-white/20 p-3 rounded-full">
                        💰
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-r from-yellow-400 to-orange-500 text-white p-6 rounded-2xl shadow-lg hover:scale-105 transform transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium opacity-80">TOTAL STOK</p>
                        <h2 class="text-3xl font-bold mt-1"><?= $total_stok ?></h2>
                    </div>
                    <div class="bg-white/20 p-3 rounded-full">
                        🏷️
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-r from-emerald-500 to-teal-600 text-white p-6 rounded-2xl shadow-lg hover:scale-105 transform transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium opacity-80">TRANSAKSI SUKSES</p>
                        <h2 class="text-3xl font-bold mt-1"><?= $total_sukses ?></h2>
                    </div>
                    <div class="bg-white/20 p-3 rounded-full">
                        ✅
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-r from-red-500 to-pink-600 text-white p-6 rounded-2xl shadow-lg hover:scale-105 transform transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium opacity-80">TRANSAKSI GAGAL</p>
                        <h2 class="text-3xl font-bold mt-1"><?= $total_refund ?></h2>
                    </div>
                    <div class="bg-white/20 p-3 rounded-full">
                        ❌
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-r from-gray-500 to-gray-700 text-white p-6 rounded-2xl shadow-lg hover:scale-105 transform transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium opacity-80">TRANSAKSI PENDING</p>
                        <h2 class="text-3xl font-bold mt-1"><?= $total_pending ?></h2>
                    </div>
                    <div class="bg-white/20 p-3 rounded-full">
                        ⏳
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-12">
            <div>
                <h2 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white tracking-tight">🆕 5 Produk Terbaru</h2>
                <div class="relative overflow-x-auto shadow-xl sm:rounded-2xl w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                    <table class="w-full text-sm text-left text-gray-600 dark:text-gray-300">
                        <thead class="text-xs text-gray-700 uppercase bg-gradient-to-r from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-800 dark:text-gray-300">
                            <tr>
                                <th scope="col" class="px-6 py-4">No</th>
                                <th scope="col" class="px-6 py-4">Nama Produk</th>
                                <th scope="col" class="px-6 py-4">Harga</th>
                                <th scope="col" class="px-6 py-4">Stok</th>
                            </tr>
                        </thead>
                        <tbody id="latest-products-table-body">
                            <?php if (empty($latest_products)): ?>
                                <tr>
                                    <td colspan="4" class="px-6 py-6 text-center text-gray-500 dark:text-gray-400 italic">
                                        Belum ada produk yang ditambahkan.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php $no = 1; ?>
                                <?php foreach ($latest_products as $produk): ?>
                                    <tr class="border-b even:bg-gray-50 odd:bg-white dark:even:bg-gray-700 dark:odd:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-600 transition">
                                        <td class="px-6 py-4 font-semibold text-gray-900 dark:text-white"><?= $no++ ?></td>
                                        <td class="px-6 py-4"><?= $produk['nama_produk'] ?></td>
                                        <td class="px-6 py-4 font-medium">Rp <?= number_format($produk['harga'], 0, ',', '.') ?></td>
                                        <td class="px-6 py-4">
                                            <?php if ($produk['stok'] > 10): ?>
                                                <span class="px-2 py-1 text-xs font-bold rounded-full bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300">
                                                    <?= $produk['stok'] ?> Tersedia
                                                </span>
                                            <?php elseif ($produk['stok'] > 0): ?>
                                                <span class="px-2 py-1 text-xs font-bold rounded-full bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300">
                                                    <?= $produk['stok'] ?> Terbatas
                                                </span>
                                            <?php else: ?>
                                                <span class="px-2 py-1 text-xs font-bold rounded-full bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300">
                                                    Habis
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div>
                <h2 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white tracking-tight">🕒 5 Transaksi Terbaru</h2>
                <div class="relative overflow-x-auto shadow-xl sm:rounded-2xl w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                    <table class="w-full text-sm text-left text-gray-600 dark:text-gray-300">
                        <thead class="text-xs text-gray-700 uppercase bg-gradient-to-r from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-800 dark:text-gray-300">
                            <tr>
                                <th scope="col" class="px-6 py-4">ID Pesanan</th>
                                <th scope="col" class="px-6 py-4">Nama Produk</th>
                                <th scope="col" class="px-6 py-4">Kuantitas</th>
                                <th scope="col" class="px-6 py-4">Total Harga</th>
                                <th scope="col" class="px-6 py-4">Status</th>
                            </tr>
                        </thead>
                        <tbody id="latest-transactions-table-body">
                            <?php if (empty($latest_transactions)): ?>
                                <tr>
                                    <td colspan="5" class="px-6 py-6 text-center text-gray-500 dark:text-gray-400 italic">
                                        Belum ada transaksi yang dilakukan.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($latest_transactions as $transaction): ?>
                                    <tr class="border-b even:bg-gray-50 odd:bg-white dark:even:bg-gray-700 dark:odd:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-600 transition">
                                        <td class="px-6 py-4 font-semibold text-gray-900 dark:text-white">
                                            <?= $transaction['id_pesanan'] ?>
                                        </td>
                                        <td class="px-6 py-4"><?= $transaction['nama_produk'] ?></td>
                                        <td class="px-6 py-4"><?= $transaction['kuantitas'] ?></td>
                                        <td class="px-6 py-4 font-medium">Rp <?= number_format($transaction['total_harga'], 0, ',', '.') ?></td>
                                        <td class="px-6 py-4">
                                            <?php if (($transaction['status'] ?? '') === 'Sukses'): ?>
                                                <span class="px-2 py-1 rounded-full text-xs font-bold bg-green-600 text-white">Sukses</span>
                                            <?php elseif (($transaction['status'] ?? '') === 'Refund'): ?>
                                                <span class="px-2 py-1 rounded-full text-xs font-bold bg-red-600 text-white">Refund</span>
                                            <?php else: ?>
                                                <span class="px-2 py-1 rounded-full text-xs font-bold bg-gray-500 text-white">Pending</span>
                                            <?php endif; ?>
                                        </td>
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