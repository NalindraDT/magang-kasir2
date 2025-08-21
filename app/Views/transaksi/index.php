<?= $this->extend('produk/layout') ?>

<?= $this->section('content') ?>
<main class="p-4 sm:ml-64">
    <div class="p-4 rounded-lg min-h-screen">
        <h1 class="text-2xl font-bold mb-6 text-gray-900 dark:text-white">Riwayat Transaksi</h1>

        <!-- Tabel Transaksi -->
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg mt-6 w-full max-w-4xl mx-auto">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">ID Pesanan</th>
                        <th scope="col" class="px-6 py-3">Nama Produk</th>
                        <th scope="col" class="px-6 py-3">Kuantitas</th>
                        <th scope="col" class="px-6 py-3">Harga Satuan</th>
                        <th scope="col" class="px-6 py-3">Total Harga</th>
                        <th scope="col" class="px-6 py-3">Status</th>
                        <th scope="col" class="px-6 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        if (empty($transaksis)):
                    ?>
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                            <td colspan="7" class="px-6 py-4 text-center text-gray-500">Tidak ada riwayat transaksi.</td>
                        </tr>
                    <?php else:
                        $grouped_transaksis = [];
                        foreach ($transaksis as $transaksi) {
                            $id_pesanan = $transaksi['id_pesanan'] ?? null;
                            if ($id_pesanan) {
                                if (!isset($grouped_transaksis[$id_pesanan])) {
                                    $grouped_transaksis[$id_pesanan] = [];
                                }
                                $grouped_transaksis[$id_pesanan][] = $transaksi;
                            }
                        }
                    ?>
                        <?php foreach ($grouped_transaksis as $id_pesanan => $items): ?>
                            <?php $total_pesanan = 0; ?>
                            <?php foreach ($items as $index => $item): ?>
                                <?php if (isset($item['status']) && $item['status'] == 'Sukses'): ?>
                                    <?php $total_pesanan += $item['total_harga']; ?>
                                <?php endif; ?>
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                    <?php if ($index === 0): ?>
                                        <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white" rowspan="<?= count($items) + 1 ?>"><?= $id_pesanan ?></td>
                                    <?php endif; ?>
                                    <td class="px-6 py-4"><?= $item['nama_produk'] ?></td>
                                    <td class="px-6 py-4"><?= $item['kuantitas'] ?></td>
                                    <td class="px-6 py-4">Rp <?= number_format($item['harga_satuan'], 0, ',', '.') ?></td>
                                    <td class="px-6 py-4">Rp <?= number_format($item['total_harga'], 0, ',', '.') ?></td>
                                    <td class="px-6 py-4">
                                        <?php if (isset($item['status']) && $item['status'] == 'Sukses'): ?>
                                            <span class="text-green-500 font-semibold">Sukses</span>
                                        <?php elseif (isset($item['status']) && $item['status'] == 'Refund'): ?>
                                            <span class="text-red-500 font-semibold">Refund</span>
                                        <?php else: ?>
                                            <span class="text-gray-500 font-semibold">Pending</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4">
                                        <?php if (isset($item['status']) && $item['status'] == 'Sukses'): ?>
                                            <a href="<?= base_url('transaksi/hapus/' . $item['id_detail']) ?>" ...
                                                class="font-medium text-red-600 dark:text-red-500 hover:underline" onclick="return confirm('Apakah Anda yakin ingin menghapus transaksi ini?')">
                                                <svg class="w-4 h-4 text-red-800 dark:text-red-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                                    <path fill-rule="evenodd" d="M8.586 2.586A2 2 0 0 1 10 2h4a2 2 0 0 1 2 2v2h3a1 1 0 1 1 0 2h-1v12a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V8H4a1 1 0 0 1 0-2h3V4a2 2 0 0 1 1.586-1.914ZM8 6h8V4h-2a2 2 0 1 1-4 0H8v2Zm2 5a1 1 0 0 1 1 1v5a1 1 0 1 1-2 0v-5a1 1 0 0 1 1-1Zm4 0a1 1 0 0 1 1 1v5a1 1 0 1 1-2 0v-5a1 1 0 0 1 1-1Z" clip-rule="evenodd" />
                                                </svg>
                                            </a>
                                        <?php elseif (isset($item['status']) && $item['status'] == 'Refund'): ?>
                                            <a href="<?= base_url('transaksi/hapus/' . $item['id_detail']) ?>" class="font-medium text-red-600 dark:text-red-500 hover:underline" onclick="return confirm('Apakah Anda yakin ingin menghapus transaksi ini?')">
                                                <svg class="w-4 h-4 text-red-800 dark:text-red-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                                    <path fill-rule="evenodd" d="M8.586 2.586A2 2 0 0 1 10 2h4a2 2 0 0 1 2 2v2h3a1 1 0 1 1 0 2h-1v12a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V8H4a1 1 0 0 1 0-2h3V4a2 2 0 0 1 1.586-1.914ZM8 6h8V4h-2a2 2 0 1 1-4 0H8v2Zm2 5a1 1 0 0 1 1 1v5a1 1 0 1 1-2 0v-5a1 1 0 0 1 1-1Zm4 0a1 1 0 0 1 1 1v5a1 1 0 1 1-2 0v-5a1 1 0 0 1 1-1Z" clip-rule="evenodd"/>
                                                </svg>
                                            </a>
                                        <?php else: ?>
                                            <span class="text-gray-500 font-medium">
                                                <svg class="w-4 h-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                                    <path fill-rule="evenodd" d="M8.586 2.586A2 2 0 0 1 10 2h4a2 2 0 0 1 2 2v2h3a1 1 0 1 1 0 2h-1v12a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V8H4a1 1 0 0 1 0-2h3V4a2 2 0 0 1 1.586-1.914ZM8 6h8V4h-2a2 2 0 1 1-4 0H8v2Zm2 5a1 1 0 0 1 1 1v5a1 1 0 1 1-2 0v-5a1 1 0 0 1 1-1Zm4 0a1 1 0 0 1 1 1v5a1 1 0 1 1-2 0v-5a1 1 0 0 1 1-1Z" clip-rule="evenodd"/>
                                                </svg>
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <tr class="bg-gray-200 dark:bg-gray-700 font-bold text-gray-900 dark:text-white">
                                <td colspan="4" class="px-6 py-2 text-right">Jumlah Bayar</td>
                                <td colspan="2" class="px-6 py-2">Rp <?= number_format($total_pesanan, 0, ',', '.') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>
<?= $this->endSection() ?>
