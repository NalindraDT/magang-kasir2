<?= $this->extend('pembeli/layout_pembeli') ?>

<?= $this->section('content') ?>
<main class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <h1 class="text-3xl font-bold mb-8 text-gray-900 dark:text-white">Daftar Produk</h1>

    <?php if (session()->getFlashdata('message')): ?>
        <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400" role="alert">
            <?= session()->getFlashdata('message') ?>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <form id="form-beli-multiple" action="<?= base_url('pembeli/beli') ?>" method="post" onsubmit="return konfirmasiPembelian(event)">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-12">
            <?php if (empty($produks)): ?>
                <div class="col-span-full text-center text-gray-500 dark:text-gray-400">Tidak ada produk yang tersedia.</div>
            <?php else: ?>
                <?php foreach ($produks as $produk): ?>
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 flex flex-col justify-between" data-nama-produk="<?= $produk['nama_produk'] ?>">
                        <div>
                            <h5 class="text-xl font-bold tracking-tight text-gray-900 dark:text-white mb-2"><?= $produk['nama_produk'] ?></h5>
                            <p class="font-normal text-gray-700 dark:text-gray-400">Harga: Rp <?= number_format($produk['harga'], 0, ',', '.') ?></p>
                            <p class="font-normal text-gray-700 dark:text-gray-400">Stok: <?= $produk['stok'] ?></p>
                        </div>
                        <div class="mt-4 flex items-center space-x-2">
                            <input type="number" name="kuantitas[<?= $produk['id_produk'] ?>]" value="0" min="0" max="<?= $produk['stok'] ?>" class="kuantitas-input w-20 px-3 py-2 text-center text-gray-900 border border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <button type="submit" class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
            Beli Semua Produk Dipilih
        </button>
    </form>

    <hr class="my-8 border-gray-300 dark:border-gray-700">

    <h2 class="text-3xl font-bold mb-8 text-gray-900 dark:text-white">Keranjang Belanja</h2>

    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3">ID Pesanan</th>
                    <th scope="col" class="px-6 py-3">Nama Produk</th>
                    <th scope="col" class="px-6 py-3">Kuantitas</th>
                    <th scope="col" class="px-6 py-3">Harga Satuan</th>
                    <th scope="col" class="px-6 py-3">Total Harga</th>
                    <th scope="col" class="px-6 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($keranjang)): ?>
                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">Keranjang belanja kosong.</td>
                    </tr>
                <?php else: ?>
                    <?php
                    $current_id_pesanan = null;
                    ?>
                    <?php foreach ($keranjang as $item): ?>
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                            <?php if ($item['id_pesanan'] !== $current_id_pesanan): ?>
                                <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white" rowspan="<?= count(array_filter($keranjang, fn($i) => $i['id_pesanan'] === $item['id_pesanan'])) ?>"><?= $item['id_pesanan'] ?></td>
                                <?php $current_id_pesanan = $item['id_pesanan']; ?>
                            <?php endif; ?>
                            <td class="px-6 py-4"><?= $item['nama_produk'] ?></td>
                            <td class="px-6 py-4">
                                <form id="update-form-<?= $item['id_detail'] ?>" action="<?= base_url('pembeli/updateCart/' . $item['id_detail']) ?>" method="post">
                                    <input type="number" name="kuantitas" value="<?= $item['kuantitas'] ?>" min="1" class="kuantitas-input-cart w-20 px-3 py-2 text-center text-gray-900 border border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white" onchange="this.form.submit()">
                                </form>
                            </td>
                            <td class="px-6 py-4">Rp <?= number_format($item['harga_satuan'], 0, ',', '.') ?></td>
                            <td class="px-6 py-4">Rp <?= number_format($item['total_harga'], 0, ',', '.') ?></td>
                            <td class="px-6 py-4">
                                <a href="<?= base_url('pembeli/removeFromCart/' . $item['id_detail']) ?>" class="font-medium text-red-600 dark:text-red-500 hover:underline" onclick="return confirm('Apakah Anda yakin ingin Refund item ini?')">Refund</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php
                    $grandTotal = array_sum(array_column($keranjang, 'total_harga'));
                    ?>
                    <tr class="bg-gray-100 dark:bg-gray-700 font-bold">
                        <td colspan="4" class="px-6 py-4 text-right text-gray-900 dark:text-white">Jumlah Bayar</td>
                        <td colspan="2" class="px-6 py-4 text-gray-900 dark:text-white">
                            Rp <?= number_format($grandTotal, 0, ',', '.') ?>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Tombol Cetak Nota -->
    <?php if (!empty($keranjang)): ?>
        <div class="mt-6 text-right">
            <button onclick="cetakNota()" class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">
                Cetak Nota
            </button>
        </div>
    <?php endif; ?>
</main>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    function konfirmasiPembelian(event) {
        let totalKuantitas = 0;
        const inputs = document.querySelectorAll('.kuantitas-input');

        inputs.forEach(input => {
            if (parseInt(input.value) > 0) {
                totalKuantitas += parseInt(input.value);
            }
        });

        if (totalKuantitas === 0) {
            alert('Pilih setidaknya satu produk untuk dibeli.');
            event.preventDefault();
            return false;
        }

        if (confirm(`Apakah Anda ingin membeli total ${totalKuantitas} produk?`)) {
            return true;
        } else {
            event.preventDefault();
            return false;
        }
    }

    function cetakNota() {
        const urlNota = `<?= base_url('pembeli/cetakNota') ?>`;
        window.open(urlNota, '_blank', 'noopener=yes');
    }
</script>
<?= $this->endSection() ?>