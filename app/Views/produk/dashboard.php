<?= $this->extend('produk/layout') ?>
<?= $this->section('content') ?>
<main>
    <div class="p-4 pt-20 rounded-lg dark:border-gray-700 min-h-screen">
        <h1 class="text-2xl font-bold mb-6 text-gray-900 dark:text-white">Dashboard</h1>
        <!-- Card Section -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md border-t-4 border-green-500">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">JUMLAH PRODUK</p>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mt-1"><?= $jumlah_produk ?></h2>
            </div>
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md border-t-4 border-blue-500">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">TOTAL PENGHASILAN</p>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mt-1"><?= $total_penghasilan ?></h2>
            </div>
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md border-t-4 border-yellow-500">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">TOTAL STOK</p>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mt-1"><?= $total_stok ?></h2>
            </div>
        </div>
<?= $this->endSection() ?>
