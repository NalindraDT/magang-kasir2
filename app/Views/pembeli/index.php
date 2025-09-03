<?= $this->extend('pembeli/layout_pembeli') ?>

<?= $this->section('content') ?>
<main class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">

    <div id="notification" class="fixed top-20 right-5 z-50"></div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        <div class="lg:col-span-2">
            <h1 class="text-3xl font-bold mb-8 text-gray-900 dark:text-white">Daftar Produk</h1>
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                <?php if (empty($produks)): ?>
                    <div class="col-span-full text-center text-gray-500 dark:text-gray-400">Tidak ada produk yang tersedia.</div>
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