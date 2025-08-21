<?= $this->extend('produk/layout') ?>
<?= $this->section('content') ?>
<main>
    <div class="p-4 pt-20 rounded-lg dark:border-gray-700 min-h-screen">
        <h1 class="text-3xl font-extrabold mb-8 text-gray-900 dark:text-white tracking-tight">📊 Dashboard</h1>

        <!-- Card Section -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            
            <!-- Jumlah Produk -->
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

            <!-- Total Penghasilan -->
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

            <!-- Total Stok -->
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

            <!-- Total Sukses -->
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

            <!-- Total Gagal -->
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

            <!-- Total Pending -->
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
    </div>
</main>
<?= $this->endSection() ?>
