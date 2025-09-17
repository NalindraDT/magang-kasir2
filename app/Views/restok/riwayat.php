<?= $this->extend('produk/layout') ?>

<?= $this->section('content') ?>
<main class="p-4 pt-20 sm:ml-16">
    <div class="p-4 rounded-lg w-full">
        <div class="flex justify-between items-center mb-8 ml-8">
            <h1 class="text-3xl font-extrabold mb-8 text-gray-900 dark:text-white tracking-tight">
                Riwayat Restok per Produk
            </h1>
            <a href="<?= base_url('admin/restok/export') ?>"
                class="inline-flex items-center gap-2 text-white bg-green-600 hover:bg-green-700 font-semibold rounded-lg text-sm px-5 py-2.5">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                </svg>
                Export ke CSV
            </a>
        </div>
        <div class="relative overflow-x-auto shadow-xl sm:rounded-2xl mt-6 w-full max-w-6xl mx-auto">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-300">
                    <tr>
                        <th scope="col" class="px-6 py-3">No</th>
                        <th scope="col" class="px-6 py-3">Nama Produk</th>
                        <th scope="col" class="px-6 py-3">Total Dipesan</th>
                        <th scope="col" class="px-6 py-3">Total Diterima</th>
                        <th scope="col" class="px-6 py-3">Total Belum Diterima</th>
                        <th scope="col" class="px-6 py-3">Total Diretur</th>
                        <th scope="col" class="px-6 py-3 font-semibold">Jumlah Akhir</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($grouped_restok)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-4 dark:text-gray-400">Belum ada riwayat restok.</td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1; ?>
                        <?php foreach ($grouped_restok as $nama_produk => $data): ?>
                            <?php
                            // Kalkulasi untuk summary
                            $jumlahAkhir = $data['summary']['total_diterima'] - $data['summary']['total_retur'];
                            $totalBelumDiterima = $data['summary']['total_pesan'] - $data['summary']['total_diterima'];
                            ?>
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                <td class="px-6 py-4 font-semibold text-gray-900 dark:text-white"><?= $no ?></td>
                                <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                                    <button type="button" class="flex items-center justify-between w-full text-left font-medium" data-collapse-toggle="details-<?= $no ?>">
                                        <span><?= esc($nama_produk) ?> (<?= count($data['details']) ?> transaksi)</span>
                                        <svg class="w-3 h-3 rotate-180 shrink-0" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5 5 1 1 5" />
                                        </svg>
                                    </button>
                                </td>
                                <td class="px-6 py-4"><?= $data['summary']['total_pesan'] ?></td>
                                <td class="px-6 py-4"><?= $data['summary']['total_diterima'] ?></td>
                                <td class="px-6 py-4 text-orange-500 font-medium"><?= $totalBelumDiterima ?></td>
                                <td class="px-6 py-4"><?= $data['summary']['total_retur'] ?></td>
                                <td class="px-6 py-4 font-bold text-gray-900 dark:text-white"><?= $jumlahAkhir ?></td>
                            </tr>
                            <!-- Dropdown/Collapsible content -->
                            <tr>
                                <td colspan="7" class="p-0">
                                    <div id="details-<?= $no ?>" class="hidden bg-gray-50 dark:bg-gray-900">
                                        <div class="relative overflow-x-auto p-4">
                                            <h4 class="font-semibold mb-2 text-gray-700 dark:text-gray-300">Detail Transaksi untuk: <?= esc($nama_produk) ?></h4>
                                            <table class="w-full text-xs">
                                                <thead class="text-gray-600 dark:text-gray-400">
                                                    <tr>
                                                        <th class="px-4 py-2 text-left">Supplier</th>
                                                        <th class="px-4 py-2 text-left">Tgl Pesan</th>
                                                        <th class="px-4 py-2 text-left">Tgl Diterima</th>
                                                        <th class="px-4 py-2 text-left">Dipesan</th>
                                                        <th class="px-4 py-2 text-left">Diterima</th>
                                                        <th class="px-4 py-2 text-left">Diretur</th>
                                                        <th class="px-4 py-2 text-left">Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($data['details'] as $detail): ?>
                                                        <tr class="border-t border-gray-200 dark:border-gray-700">
                                                            <td class="px-4 py-2"><?= esc($detail['nama_restoker']) ?></td>
                                                            <td class="px-4 py-2"><?= (new DateTime($detail['tanggal_pesan'], new DateTimeZone('UTC')))->setTimezone(new DateTimeZone('Asia/Jakarta'))->format('d M Y') ?></td>
                                                            <td class="px-4 py-2"><?= $detail['tanggal_diterima'] ? (new DateTime($detail['tanggal_diterima'], new DateTimeZone('UTC')))->setTimezone(new DateTimeZone('Asia/Jakarta'))->format('d M Y') : '---' ?></td>
                                                            <td class="px-4 py-2"><?= $detail['jumlah_pesan'] ?></td>
                                                            <td class="px-4 py-2"><?= $detail['jumlah_diterima'] ?? 0 ?></td>
                                                            <td class="px-4 py-2"><?= $detail['jumlah_retur'] ?? 0 ?></td>
                                                            <td class="px-4 py-2"><?= esc($detail['status']) ?></td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <?php $no++; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>
<?= $this->endSection() ?>