<h2 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white">Keranjang</h2>

<div class="space-y-4">
    <?php if (empty($keranjang)): ?>
        <p class="text-center text-gray-500 dark:text-gray-400 py-8">Keranjang belanja Anda kosong.</p>
    <?php else: ?>
        <?php $grandTotal = 0; ?>
        <?php foreach ($keranjang as $item): ?>
            <div class="flex items-center justify-between border-b border-gray-200 dark:border-gray-700 pb-3">
                <div class="flex-grow">
                    <p class="font-semibold text-gray-800 dark:text-white"><?= esc($item['nama_produk']) ?></p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Rp <?= number_format($item['harga_satuan'], 0, ',', '.') ?> x <?= $item['kuantitas'] ?></p>
                </div>
                <div class="text-right">
                    <p class="font-bold text-gray-800 dark:text-white">Rp <?= number_format($item['total_harga'], 0, ',', '.') ?></p>
                    <a href="<?= base_url('pembeli/removeFromCart/' . $item['id_detail']) ?>" class="text-xs text-red-500 hover:underline" onclick="return confirm('Apakah Anda yakin ingin Refund item ini?')">Refund</a>
                </div>
            </div>
            <?php $grandTotal += $item['total_harga']; ?>
        <?php endforeach; ?>

        <div class="pt-4 space-y-2">
            <div class="flex justify-between text-lg font-bold">
                <span class="text-gray-800 dark:text-white">Total Bayar</span>
                <span class="text-gray-900 dark:text-white">Rp <?= number_format($grandTotal, 0, ',', '.') ?></span>
            </div>
        </div>

        <div class="mt-6">
            <button type="button" id="checkout-button" class="w-full text-white bg-green-600 hover:bg-green-700 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-3 text-center dark:bg-green-500 dark:hover:bg-green-600 dark:focus:ring-green-800">
                Bayar dengan DOKU
            </button>
        </div>
    <?php endif; ?>
</div>