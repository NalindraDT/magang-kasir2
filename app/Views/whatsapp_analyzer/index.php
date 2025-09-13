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
                <h2 class="text-xl font-bold mb-4 text-gray-800 dark:text-white">📊 Analisis Waktu Respons</h2>
                <form action="<?= base_url('admin/whatsapp-analyzer/proses') ?>" method="post" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <div class="grid gap-6">
                        <div>
                            <label for="nama_anda" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama Anda (Sesuai di File Chat)</label>
                            <input type="text" name="nama_anda" id="nama_anda" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Contoh: Nalindra DT" required>
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white" for="chatfile">Unggah File Chat (.txt)</label>
                            <input class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600" id="chatfile" name="chatfile" type="file" accept=".txt" required>
                        </div>
                         <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white" for="kontakfile">Unggah File Kontak (.csv)</label>
                            <input class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600" id="kontakfile" name="kontakfile" type="file" accept=".csv" required>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-300">File CSV harus berisi header 'Nama,NomorHP'.</p>
                        </div>
                    </div>
                    <button type="submit" class="mt-4 text-white bg-blue-700 hover:bg-blue-800 font-medium rounded-lg text-sm px-5 py-2.5">Analisis Sekarang</button>
                </form>
            </div>
        </div>
    </div>
</main>
<?= $this->endSection() ?>