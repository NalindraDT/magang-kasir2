<?= $this->extend('produk/layout') ?>

<?= $this->section('content') ?>
<main class="p-4 pt-20 sm:ml-16">
    <div class="p-4 rounded-lg w-full">
        <h1 class="text-3xl font-extrabold mb-8 text-gray-900 dark:text-white tracking-tight">
            Hasil Analisis Respons
        </h1>

        <h2 class="text-xl font-bold mb-4 text-gray-800 dark:text-white">Ringkasan Rata-rata Respons</h2>
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg mb-8">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-300">
                    <tr>
                        <th scope="col" class="px-6 py-3">Tipe Respons</th>
                        <th scope="col" class="px-6 py-3">Nomor HP</th>
                        <th scope="col" class="px-6 py-3">Rata-rata (Detik)</th>
                        <th scope="col" class="px-6 py-3">Rata-rata (JJ:MM:DD)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($ringkasan as $item): ?>
                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                        <td class="px-6 py-4 font-medium text-gray-900 dark:text-white"><?= esc($item['Tipe Respons']) ?></td>
                        <td class="px-6 py-4"><?= esc($item['NomorHP']) ?></td>
                        <td class="px-6 py-4"><?= esc($item['Rata-rata Respons (Detik)']) ?></td>
                        <td class="px-6 py-4"><?= esc($item['Rata-rata Respons (JJ:MM:DD)']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <h2 class="text-xl font-bold mb-4 text-gray-800 dark:text-white">Rincian Semua Respons (Urut Waktu)</h2>
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-300">
                    <tr>
                        <th scope="col" class="px-6 py-3">Waktu Kejadian</th>
                        <th scope="col" class="px-6 py-3">Tipe Respons</th>
                        <th scope="col" class="px-6 py-3">Waktu Respons (Detik)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rincian as $item): ?>
                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                        <td class="px-6 py-4"><?= $item['Waktu Kejadian']->format('Y-m-d H:i') ?></td>
                        <td class="px-6 py-4 font-medium text-gray-900 dark:text-white"><?= esc($item['Tipe Respons']) ?></td>
                        <td class="px-6 py-4"><?= esc($item['Waktu Respons (Detik)']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
         <div class="mt-8">
            <a href="<?= site_url('/admin/whatsapp-analyzer') ?>" class="text-white bg-blue-700 hover:bg-blue-800 font-medium rounded-lg text-sm px-5 py-2.5">
                Analisis File Lain
            </a>
        </div>
    </div>
</main>
<?= $this->endSection() ?>