<?= $this->extend('pembeli/layout_pembeli') ?>

<?= $this->section('content') ?>
<main class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">

    <div id="notification" class="fixed top-20 right-5 z-50"></div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        <div class="lg:col-span-2">
            <h1 class="text-3xl font-bold mb-4 text-gray-900 dark:text-white">Daftar Produk</h1>
            
            <form action="<?= base_url('pembeli') ?>" method="get" id="filter-form" class="mb-8 bg-white dark:bg-gray-800 p-4 rounded-lg shadow-md">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                    <div class="relative md:col-span-1">
                        <label for="search" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Cari Produk</label>
                        <input type="text" name="search" id="search" value="<?= esc($search ?? '') ?>" placeholder="Nama produk..." 
                               class="w-full pl-4 pr-4 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div class="md:col-span-1">
                        <label for="kategori" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Kategori</label>
                        <select name="kategori" id="kategori"
                                class="w-full p-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="all">Semua Kategori</option>
                            <?php foreach($kategori_list as $kat): ?>
                                <option value="<?= $kat['id_kategori'] ?>" <?= ($kategoriId ?? 'all') == $kat['id_kategori'] ? 'selected' : '' ?>>
                                    <?= esc($kat['nama_kategori']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="md:col-span-1">
                        <label for="price_range" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Harga</label>
                        <select name="price_range" id="price_range"
                                class="w-full p-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="all" <?= ($priceRange ?? 'all') === 'all' ? 'selected' : '' ?>>Semua Harga</option>
                            <option value="0-15000" <?= ($priceRange ?? '') === '0-15000' ? 'selected' : '' ?>>Rp 0 - Rp 15.000</option>
                            <option value="15001-50000" <?= ($priceRange ?? '') === '15001-50000' ? 'selected' : '' ?>>Rp 15.001 - Rp 50.000</option>
                            <option value="50001-100000" <?= ($priceRange ?? '') === '50001-100000' ? 'selected' : '' ?>>Rp 50.001 - Rp 100.000</option>
                            <option value="100001-above" <?= ($priceRange ?? '') === '100001-above' ? 'selected' : '' ?>>Diatas Rp 100.000</option>
                        </select>
                    </div>

                    <div class="md:col-span-1 flex items-center gap-2">
                        <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Terapkan</button>
                        <a href="<?= base_url('pembeli') ?>" class="w-full px-4 py-2 bg-gray-600 text-white text-center rounded-lg hover:bg-gray-700">Reset</a>
                    </div>
                </div>
            </form>

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                <?php if (empty($produks)): ?>
                    <div class="col-span-full text-center py-12 text-gray-500 dark:text-gray-400">
                        <p class="text-lg">Produk tidak ditemukan.</p>
                        <a href="<?= base_url('pembeli') ?>" class="text-blue-500 hover:underline mt-2 inline-block">Reset filter</a>
                    </div>
                <?php else: ?>
                    <?php foreach ($produks as $produk): ?>
                        <div class="product-card bg-white dark:bg-gray-800 rounded-lg shadow-md flex flex-col justify-between overflow-hidden cursor-pointer 
            hover:shadow-xl hover:scale-105 hover:bg-gray-50 dark:hover:bg-gray-700 
            transition-all duration-300 ease-in-out"
                            data-id="<?= $produk['id_produk'] ?>">
                            <img src="<?= base_url('uploads/produk/' . $produk['gambar_produk']) ?>" alt="<?= esc($produk['nama_produk']) ?>" class="w-full h-40 object-cover">
                            <div class="p-4">
                                <h5 class="text-lg font-bold tracking-tight text-gray-900 dark:text-white"><?= esc($produk['nama_produk']) ?></h5>
                                <p class="font-normal text-gray-700 dark:text-gray-400 mt-1">Rp <?= number_format($produk['harga'], 0, ',', '.') ?></p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Stok: <?= $produk['stok'] ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="lg:col-span-1">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 sticky top-8" id="keranjang-container">
                <?= view('pembeli/_keranjang', ['keranjang' => $keranjang]) ?>
            </div>
        </div>

    </div>
</main>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const productCards = document.querySelectorAll('.product-card');
        const cartContainer = document.getElementById('keranjang-container');
        const notificationContainer = document.getElementById('notification');

        // Fungsi untuk menampilkan notifikasi
        function showNotification(message, isSuccess = true) {
            const bgColor = isSuccess ? 'bg-green-500' : 'bg-red-500';
            const notification = `
            <div class="p-4 mb-4 text-sm text-white rounded-lg ${bgColor}" role="alert">
                ${message}
            </div>
        `;
            notificationContainer.innerHTML = notification;
            setTimeout(() => {
                notificationContainer.innerHTML = '';
            }, 3000); // Notifikasi hilang setelah 3 detik
        }

        // Event listener untuk setiap kartu produk
        productCards.forEach(card => {
            card.addEventListener('click', function() {
                const productId = this.dataset.id;

                const formData = new FormData();
                formData.append('id_produk', productId);

                fetch('<?= base_url('pembeli/add_to_cart') ?>', {
                        method: 'POST',
                        headers: {
                            "X-Requested-With": "XMLHttpRequest",
                            // Tambahkan header CSRF jika Anda mengaktifkannya
                            // "X-CSRF-TOKEN": "<?= csrf_hash() ?>" 
                        },
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            showNotification(data.message, true);
                            // Update tampilan keranjang dengan HTML baru dari server
                            cartContainer.innerHTML = data.cart_html;
                        } else {
                            showNotification(data.message, false);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showNotification('Terjadi kesalahan. Silakan coba lagi.', false);
                    });
            });
        });

        // Event delegation untuk tombol checkout karena keranjang di-render ulang
        document.body.addEventListener('click', function(event) {
            if (event.target.id === 'checkout-button') {
                fetch('<?= base_url('doku/payment') ?>', {
                        method: 'POST'
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success' && data.paymentUrl) {
                            loadJokulCheckout(data.paymentUrl);
                        } else {
                            alert('Gagal membuat pembayaran: ' + (data.message || 'Silakan coba lagi'));
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Terjadi kesalahan saat memproses pembayaran.');
                    });
            }
        });
    });
</script>
<?= $this->endSection() ?>