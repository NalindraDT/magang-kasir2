<?= $this->extend('produk/layout') ?>

<?= $this->section('content') ?>
<main class="p-4 pt-20 sm:ml-16">
    <div class="p-4 rounded-lg w-full">
        <h1 class="text-3xl font-extrabold mb-8 text-gray-900 dark:text-white tracking-tight">
            Laporan Waktu Respons WhatsApp
        </h1>
        <a href="<?= base_url('admin/whatsapp-report/clear') ?>"
            onclick="return confirm('Apakah Anda yakin ingin menghapus semua riwayat waktu respons? Tindakan ini tidak dapat diurungkan.')"
            class="inline-flex items-center gap-2 text-white bg-red-600 hover:bg-red-700 font-semibold rounded-lg text-sm px-5 py-2.5">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
            </svg>
            Hapus Semua Data
        </a>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="bg-gradient-to-r from-blue-500 to-indigo-600 text-white p-6 rounded-2xl shadow-lg">
                <p class="text-sm font-medium opacity-80">RATA-RATA RESPON OPERATOR</p>
                <h2 class="text-3xl font-bold mt-1"><?= round($avg_operator_response, 2) ?> detik</h2>
            </div>
            <div class="bg-gradient-to-r from-green-500 to-teal-600 text-white p-6 rounded-2xl shadow-lg">
                <p class="text-sm font-medium opacity-80">RATA-RATA RESPON KLIEN</p>
                <h2 class="text-3xl font-bold mt-1"><?= round($avg_client_response, 2) ?> detik</h2>
            </div>
        </div>

        <h2 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white">Log Respons Terakhir</h2>
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-300">
                    <tr>
                        <th scope="col" class="px-6 py-3">Waktu Tercatat</th>
                        <th scope="col" class="px-6 py-3">Nomor Klien</th>
                        <th scope="col" class="px-6 py-3">Arah Respons</th>
                        <th scope="col" class="px-6 py-3">Waktu Respons (detik)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($responses)): ?>
                        <tr>
                            <td colspan="4" class="text-center py-4">Belum ada data respons.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($responses as $item): ?>
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                <td class="px-6 py-4">
                                    <?= (new DateTime($item['created_at'], new DateTimeZone('UTC')))->setTimezone(new DateTimeZone('Asia/Jakarta'))->format('d M Y, H:i:s') ?>
                                </td>
                                <td class="px-6 py-4 font-medium text-gray-900 dark:text-white"><?= esc($item['conversation_id']) ?></td>
                                <td class="px-6 py-4">
                                    <?php if ($item['response_direction'] == 'operator_to_client'): ?>
                                        <span class="px-2 py-1 text-xs font-bold rounded-full bg-blue-100 text-blue-800">Operator → Klien</span>
                                    <?php else: ?>
                                        <span class="px-2 py-1 text-xs font-bold rounded-full bg-green-100 text-green-800">Klien → Operator</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4"><?= esc($item['response_time_seconds']) ?> detik</td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>
<?= $this->endSection() ?>