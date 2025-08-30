<?= $this->extend('pembeli/layout_pembeli') ?>

<?= $this->section('content') ?>
<main class="max-w-4xl mx-auto py-16 px-4 sm:px-6 lg:px-8">
    <div class="bg-white dark:bg-gray-800 shadow-xl rounded-lg p-8 text-center">
        
        <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-blue-100 dark:bg-blue-900">
            <svg class="h-12 w-12 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
            </svg>
        </div>
        <h1 class="mt-6 text-2xl font-extrabold text-gray-900 dark:text-white">Terima Kasih!</h1>
        <p class="mt-2 text-gray-600 dark:text-gray-400">
            Pembayaran Anda untuk invoice <br>
            <strong class="font-mono"><?= esc($invoiceNumber ?? 'sedang diproses') ?></strong>
            <br> sedang kami verifikasi. Status akan diperbarui secara otomatis.
        </p>

        <div class="mt-8">
            <a href="<?= site_url('/pembeli') ?>" class="text-white bg-blue-700 hover:bg-blue-800 font-medium rounded-lg text-sm px-5 py-2.5">
                Kembali ke Halaman Utama
            </a>
        </div>

    </div>
</main>
<?= $this->endSection() ?>