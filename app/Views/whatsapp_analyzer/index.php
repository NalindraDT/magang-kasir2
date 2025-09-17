<?= $this->extend('produk/layout') ?>

<?= $this->section('content') ?>
<main class="p-4 pt-20 sm:ml-16">
    <div class="p-4 rounded-lg w-full">
        <h1 class="text-3xl font-extrabold mb-8 text-gray-900 dark:text-white tracking-tight">
            WhatsApp Tools
        </h1>

        <?php if (session()->getFlashdata('message')): ?>
            <div class="p-4 mb-6 text-sm font-medium text-green-800 rounded-lg bg-green-100 dark:bg-gray-800 dark:text-green-400">
                ✅ <?= session()->getFlashdata('message') ?>
            </div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="p-4 mb-6 text-sm font-medium text-red-800 rounded-lg bg-red-100 dark:bg-gray-800 dark:text-red-400">
                ❌ <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div class="border border-gray-300 dark:border-gray-600 rounded-lg p-6 bg-white dark:bg-gray-800 shadow-md">
                <h2 class="text-xl font-bold mb-4 text-gray-800 dark:text-white">🚀 Kirim Pesan Tes via API</h2>
                <form action="<?= base_url('admin/whatsapp-analyzer/kirim') ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="mb-4">
                        <label for="nomor_tujuan" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nomor HP Penerima</label>
                        <input type="text" name="nomor_tujuan" id="nomor_tujuan" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Gunakan format 628..." required>
                    </div>
                    <button type="submit" class="text-white bg-green-700 hover:bg-green-800 font-medium rounded-lg text-sm px-5 py-2.5">Kirim Pesan "Hello World"</button>
                </form>
            </div>
            <div class="border border-gray-300 dark:border-gray-600 rounded-lg p-6 bg-white dark:bg-gray-800 shadow-md">
                <h2 class="text-xl font-bold mb-4 text-gray-800 dark:text-white">✍️ Kirim Pesan Template</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                    Mengirim template pesan "pesan_template_1" yang sudah disetujui.
                </p>
                <form action="<?= base_url('admin/whatsapp-analyzer/kirim-kustom') ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="mb-4">
                        <label for="nomor_tujuan_kustom" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nomor HP Penerima</label>
                        <input type="text" name="nomor_tujuan_kustom" id="nomor_tujuan_kustom" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Gunakan format 628..." required>
                    </div>
                    <button type="submit" class="text-white bg-purple-700 hover:bg-purple-800 font-medium rounded-lg text-sm px-5 py-2.5">Kirim Pesan Template</button>
                </form>
            </div>
            <div class="border border-gray-300 dark:border-gray-600 rounded-lg p-6 bg-white dark:bg-gray-800 shadow-md">
                <h2 class="text-xl font-bold mb-4 text-gray-800 dark:text-white">💬 Balas Pesan (Dalam 24 Jam)</h2>
                <form action="<?= base_url('admin/whatsapp-analyzer/balas-biasa') ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="mb-4">
                        <label for="nomor_tujuan_balas" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nomor HP Penerima</label>
                        <input type="text" name="nomor_tujuan_balas" id="nomor_tujuan_balas" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Gunakan format 628..." required>
                    </div>
                    <div class="mb-4">
                        <label for="isi_pesan_balas" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Isi Pesan Balasan</label>
                        <textarea name="isi_pesan_balas" id="isi_pesan_balas" rows="3" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Ketik balasan Anda di sini..." required></textarea>
                    </div>
                    <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 font-medium rounded-lg text-sm px-5 py-2.5">Kirim Balasan</button>
                </form>
            </div>
        </div>
    </div>
</main>
<?= $this->endSection() ?>