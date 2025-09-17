<?= $this->extend('produk/layout') ?>

<?= $this->section('content') ?>
<main class="p-4 pt-20 sm:ml-16">
    <div class="p-4 rounded-lg w-full">
        <h1 class="text-3xl font-extrabold mb-8 text-gray-900 dark:text-white tracking-tight">
            Log Semua Percakapan
        </h1>

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
                        <tr><td colspan="4" class="text-center py-4">Belum ada pesan yang tercatat.</td></tr>
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