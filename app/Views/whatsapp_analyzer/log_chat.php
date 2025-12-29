<?= $this->extend('produk/layout') ?>

<?= $this->section('content') ?>
<main class="p-4 pt-20 sm:ml-16">
    <div class="p-4 rounded-lg w-full">
        <h1 class="text-3xl font-extrabold mb-8 text-gray-900 dark:text-white tracking-tight">
            Log Semua Percakapan
        </h1>
        <a href="<?= base_url('admin/chat-log/clear') ?>"
            onclick="return confirm('PERHATIAN: Ini akan menghapus SEMUA riwayat percakapan dan log pesan. Anda yakin?')"
            class="inline-flex items-center gap-2 text-white bg-red-600 hover:bg-red-700 font-semibold rounded-lg text-sm px-5 py-2.5">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
            </svg>
            Hapus Semua Log
        </a>

        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-300">
                    <tr>
                        <th scope="col" class="px-6 py-3">Tanggal</th>
                        <th scope="col" class="px-6 py-3">Waktu</th>
                        <th scope="col" class="px-6 py-3">Nomor Pengirim</th>
                        <th scope="col" class="px-6 py-3">Isi Pesan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($messages)): ?>
                        <tr>
                            <td colspan="4" class="text-center py-4">Belum ada pesan yang tercatat.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($messages as $msg): ?>
                            <?php
                            // Konversi Unix timestamp ke DateTime dengan zona waktu yang benar
                            $tanggal_wib = (new DateTime('@' . $msg['message_timestamp']))
                                ->setTimezone(new DateTimeZone('Asia/Jakarta'));
                            ?>
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                <td class="px-6 py-4">
                                    <?= $tanggal_wib->format('d M Y') ?>
                                </td>
                                <td class="px-6 py-4">
                                    <?= $tanggal_wib->format('H:i:s') ?>
                                </td>
                                <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                                    <?= esc($msg['sender_number']) ?>
                                </td>
                                <td class="px-6 py-4">
                                    <?= esc($msg['message_text']) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            <?= $pager->links() ?>
        </div>
    </div>
</main>
<?= $this->endSection() ?>