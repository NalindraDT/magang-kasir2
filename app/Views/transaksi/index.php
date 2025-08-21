<?= $this->extend('produk/layout') ?>


<?= $this->section('content') ?>
<main class="p-4 pt-20 sm:ml-8">
    <div class="p-4 rounded-lg min-h-screen">
        <h1 class="text-3xl font-extrabold mb-8 text-gray-900 dark:text-white ml-8 tracking-tight">
            📜 Riwayat Transaksi
        </h1>

        <!-- Tabel Transaksi -->
        <div class="relative overflow-x-auto shadow-xl sm:rounded-2xl mt-6 w-full max-w-6xl mx-auto ml-8 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
            <table class="w-full text-sm text-left text-gray-600 dark:text-gray-300">
                <thead class="text-xs text-gray-700 uppercase bg-gradient-to-r from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-800 dark:text-gray-300">
                    <tr>
                        <th scope="col" class="px-6 py-4">ID Pesanan</th>
                        <th scope="col" class="px-6 py-4">Nama Produk</th>
                        <th scope="col" class="px-6 py-4">Kuantitas</th>
                        <th scope="col" class="px-6 py-4">Harga Satuan</th>
                        <th scope="col" class="px-6 py-4">Total Harga</th>
                        <th scope="col" class="px-6 py-4">Status</th>
                        <th scope="col" class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($transaksis)): ?>
                        <tr>
                            <td colspan="7" class="px-6 py-6 text-center text-gray-500 dark:text-gray-400 italic">
                                Belum ada riwayat transaksi 📂
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php
                        $grouped_transaksis = [];
                        foreach ($transaksis as $transaksi) {
                            $id_pesanan = $transaksi['id_pesanan'] ?? null;
                            if ($id_pesanan) {
                                $grouped_transaksis[$id_pesanan][] = $transaksi;
                            }
                        }
                        ?>
                        <?php foreach ($grouped_transaksis as $id_pesanan => $items): ?>
                            <?php $total_pesanan = 0; ?>
                            <?php foreach ($items as $index => $item): ?>
                                <?php if (($item['status'] ?? '') === 'Sukses'): ?>
                                    <?php $total_pesanan += $item['total_harga']; ?>
                                <?php endif; ?>
                                <tr class="border-b hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                    <?php if ($index === 0): ?>
                                        <td class="px-6 py-4 font-semibold text-gray-900 dark:text-white align-top" rowspan="<?= count($items) + 1 ?>">
                                            <span class="px-3 py-1 text-sm rounded-lg bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">
                                                <?= $id_pesanan ?>
                                            </span>
                                        </td>
                                    <?php endif; ?>
                                    <td class="px-6 py-4"><?= $item['nama_produk'] ?></td>
                                    <td class="px-6 py-4"><?= $item['kuantitas'] ?></td>
                                    <td class="px-6 py-4">Rp <?= number_format($item['harga_satuan'], 0, ',', '.') ?></td>
                                    <td class="px-6 py-4 font-medium">Rp <?= number_format($item['total_harga'], 0, ',', '.') ?></td>
                                    <td class="px-6 py-4">
                                        <?php if (($item['status'] ?? '') === 'Sukses'): ?>
                                            <span class="px-2 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300">Sukses</span>
                                        <?php elseif (($item['status'] ?? '') === 'Refund'): ?>
                                            <span class="px-2 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300">Refund</span>
                                        <?php else: ?>
                                            <span class="px-2 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300">Pending</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <?php if (in_array(($item['status'] ?? ''), ['Sukses', 'Refund'])): ?>
                                            <a href="<?= base_url('admin/transaksi/hapus/' . $item['id_detail']) ?>"
                                                class="inline-flex items-center justify-center p-2 rounded-full bg-red-100 hover:bg-red-200 dark:bg-red-800 dark:hover:bg-red-700 text-red-600 dark:text-red-300 transition"
                                                onclick="return confirm('Apakah Anda yakin ingin menghapus transaksi ini?')">
                                                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                                    <path fill-rule="evenodd" d="M8.586 2.586A2 2 0 0110 2h4a2 2 0 012 2v2h3a1 1 0 010 2h-1v12a2 2 0 01-2 2H7a2 2 0 01-2-2V8H4a1 1 0 010-2h3V4a2 2 0 011.586-1.414zM8 6h8V4h-2a2 2 0 11-4 0H8v2zm2 5a1 1 0 011 1v5a1 1 0 11-2 0v-5a1 1 0 011-1zm4 0a1 1 0 011 1v5a1 1 0 11-2 0v-5a1 1 0 011-1z" clip-rule="evenodd" />
                                                </svg>
                                            </a>
                                        <?php else: ?>
                                            <span class="inline-flex items-center justify-center p-2 rounded-full bg-gray-200 dark:bg-gray-600 text-gray-500 dark:text-gray-300 cursor-not-allowed">
                                                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                                    <path fill-rule="evenodd" d="M8.586 2.586A2 2 0 0110 2h4a2 2 0 012 2v2h3a1 1 0 010 2h-1v12a2 2 0 01-2 2H7a2 2 0 01-2-2V8H4a1 1 0 010-2h3V4a2 2 0 011.586-1.414zM8 6h8V4h-2a2 2 0 11-4 0H8v2zm2 5a1 1 0 011 1v5a1 1 0 11-2 0v-5a1 1 0 011-1zm4 0a1 1 0 011 1v5a1 1 0 11-2 0v-5a1 1 0 011-1z" clip-rule="evenodd" />
                                                </svg>
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <tr class="bg-gradient-to-r from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-800 font-bold text-gray-900 dark:text-white">
                                <td colspan="4" class="px-6 py-3 text-right">💰 Jumlah Bayar</td>
                                <td colspan="3" class="px-6 py-3">Rp <?= number_format($total_pesanan, 0, ',', '.') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>
<?= $this->endSection() ?>