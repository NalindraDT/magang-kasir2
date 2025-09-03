<?= $this->extend('produk/layout') ?>

<?= $this->section('content') ?>
<main class="p-4 pt-20 sm:ml-8">
    <div class="p-4 rounded-lg min-h-screen">
        <div class="flex justify-between items-center mb-8 ml-8">
            <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">
                Riwayat Transaksi
            </h1>
            <a href="<?= base_url('admin/transaksi/export') ?>"
                class="inline-flex items-center gap-2 text-white bg-green-600 hover:bg-green-700 font-semibold rounded-lg text-sm px-5 py-2.5">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                </svg>
                Ekspor ke CSV
            </a>
        </div>

        <div class="relative overflow-x-auto shadow-xl sm:rounded-2xl mt-6 w-full max-w-6xl mx-auto ml-8 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
            <table class="w-full text-sm text-left text-gray-600 dark:text-gray-300">
                <thead class="text-xs text-gray-700 uppercase bg-gradient-to-r from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-800 dark:text-gray-300">
                    <tr>
                        <th scope="col" class="px-6 py-4">ID Pesanan</th>
                        <th scope="col" class="px-6 py-4">Jumlah Item</th>
                        <th scope="col" class="px-6 py-4">Total Bayar</th>
                        <th scope="col" class="px-6 py-4">Status Transaksi</th>
                        <th scope="col" class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($transaksis)): ?>
                        <tr class="odd:bg-gray-900 even:bg-gray-800 text-white">
                            <td colspan="5" class="px-6 py-6 text-center italic">
                                Belum ada riwayat transaksi 📂
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php
                        // Grouping transaksi berdasarkan id_pesanan
                        $grouped_transaksis = [];
                        foreach ($transaksis as $transaksi) {
                            $id_pesanan = $transaksi['id_pesanan'] ?? null;
                            if ($id_pesanan) {
                                if (!isset($grouped_transaksis[$id_pesanan])) {
                                    $grouped_transaksis[$id_pesanan] = [];
                                }
                                $grouped_transaksis[$id_pesanan][] = $transaksi;
                            }
                        }

                        // Pagination
                        $perPage = 5;
                        $totalPages = ceil(count($grouped_transaksis) / $perPage);
                        $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                        if ($currentPage < 1) $currentPage = 1;
                        if ($totalPages < 1) $totalPages = 1; // guard
                        if ($currentPage > $totalPages) $currentPage = $totalPages;

                        $start = ($currentPage - 1) * $perPage;
                        $paged_transaksis = array_slice($grouped_transaksis, $start, $perPage, true);
                        ?>

                        <?php foreach ($paged_transaksis as $id_pesanan => $items): ?>
                            <?php
                            $total_pesanan = 0;
                            $status_array = array_column($items, 'status');
                            $all_success = !in_array('Pending', $status_array) && !in_array('Refund', $status_array);
                            $has_refund = in_array('Refund', $status_array);
                            $has_pending = in_array('Pending', $status_array);

                            foreach ($items as $item) {
                                if ($item['status'] === 'Sukses') {
                                    $total_pesanan += $item['total_harga'];
                                }
                            }
                            ?>
                            <tr class="odd:bg-gray-900 even:bg-gray-800 text-white hover:bg-gray-700 transition-colors">
                                <td class="px-6 py-4 font-semibold">
                                    <span class="px-3 py-1 text-sm rounded-lg bg-blue-600 text-white"><?= $id_pesanan ?></span>
                                </td>
                                <td class="px-6 py-4"><?= count($items) ?> item</td>
                                <td class="px-6 py-4 font-medium">Rp <?= number_format($total_pesanan, 0, ',', '.') ?></td>
                                <td class="px-6 py-4">
                                    <?php if ($has_pending): ?>
                                        <span class="px-2 py-1 text-xs font-bold rounded-full bg-gray-100 text-gray-700 dark:bg-gray-900 dark:text-gray-300">Pending</span>
                                    <?php elseif ($all_success): ?>
                                        <span class="px-2 py-1 text-xs font-bold rounded-full bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300">Sukses</span>
                                    <?php elseif ($has_refund): ?>
                                        <span class="px-2 py-1 text-xs font-bold rounded-full bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300">Sebagian Refund</span>
                                    <?php else: ?>
                                        <span class="px-2 py-1 text-xs font-bold rounded-full bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300">Full Refund</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <button type="button" data-modal-target="detail-modal" data-modal-toggle="detail-modal"
                                        class="text-white bg-indigo-600 hover:bg-indigo-700 font-medium rounded-lg text-sm px-4 py-2"
                                        onclick="showDetail(<?= htmlspecialchars(json_encode($items), ENT_QUOTES, 'UTF-8') ?>, <?= $id_pesanan ?>)">
                                        Detail
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if (!empty($transaksis) && $totalPages > 1): ?>
            <div class="flex justify-center mt-6 space-x-2">
                <?php if ($currentPage > 1): ?>
                    <a href="?page=<?= $currentPage - 1 ?>" class="px-3 py-1 rounded border border-gray-600 text-gray-300 hover:bg-gray-700">‹ Prev</a>
                <?php else: ?>
                    <span class="px-3 py-1 rounded border border-gray-600 text-gray-500 cursor-not-allowed">‹ Prev</span>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="?page=<?= $i ?>" class="px-3 py-1 rounded border border-gray-600 <?= $i == $currentPage ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-700' ?>"><?= $i ?></a>
                <?php endfor; ?>

                <?php if ($currentPage < $totalPages): ?>
                    <a href="?page=<?= $currentPage + 1 ?>" class="px-3 py-1 rounded border border-gray-600 text-gray-300 hover:bg-gray-700">Next ›</a>
                <?php else: ?>
                    <span class="px-3 py-1 rounded border border-gray-600 text-gray-500 cursor-not-allowed">Next ›</span>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</main>

<div id="detail-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-2xl max-h-full">
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                    Detail Pesanan #<span id="modal-id-pesanan"></span>
                </h3>
                <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="detail-modal">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>
            <div class="p-4 md:p-5 space-y-4">
                <div class="relative overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="px-6 py-3">Produk</th>
                                <th scope="col" class="px-6 py-3">Kuantitas</th>
                                <th scope="col" class="px-6 py-3">Total</th>
                                <th scope="col" class="px-6 py-3">Status</th>
                                <th scope="col" class="px-6 py-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="modal-table-body">
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="flex items-center justify-end p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600">
                <a id="modal-cetak-button" href="#" target="_blank" class="text-white bg-green-600 hover:bg-green-700 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Cetak Nota</a>
            </div>
        </div>
    </div>
</div>

<script>
    function showDetail(items, id_pesanan) {
        const modalIdPesanan = document.getElementById('modal-id-pesanan');
        const modalTableBody = document.getElementById('modal-table-body');
        const modalCetakButton = document.getElementById('modal-cetak-button');

        // Set ID Pesanan di header modal
        modalIdPesanan.textContent = id_pesanan;

        // Set link tombol cetak
        modalCetakButton.href = `<?= base_url('admin/transaksi/cetak/') ?>${id_pesanan}`;

        // Kosongkan isi tabel sebelumnya
        modalTableBody.innerHTML = '';

        // Isi tabel dengan item-item transaksi
        items.forEach(item => {
            const row = document.createElement('tr');
            row.className = 'bg-white border-b dark:bg-gray-800 dark:border-gray-700';

            const statusClass = item.status === 'Sukses' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' :
                item.status === 'Refund' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300' :
                'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300';

            let deleteButtonHtml = '';
            if (item.status === 'Sukses' || item.status === 'Refund') {
                deleteButtonHtml = `
                <a href="<?= base_url('admin/transaksi/hapus/') ?>${item.id_detail}?page=<?= $_GET['page'] ?? 1 ?>"
                   onclick="return confirm('Apakah Anda yakin ingin menghapus item ini?')"
                   class="font-medium text-red-600 dark:text-red-500 hover:underline">
                   Hapus
                </a>`;
            } else {
                deleteButtonHtml = `<span class="text-gray-400 dark:text-gray-500 cursor-not-allowed">Hapus</span>`;
            }

            row.innerHTML = `
            <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">${item.nama_produk}</td>
            <td class="px-6 py-4">${item.kuantitas}</td>
            <td class="px-6 py-4">Rp ${Number(item.total_harga).toLocaleString('id-ID')}</td>
            <td class="px-6 py-4"><span class="text-xs font-medium me-2 px-2.5 py-0.5 rounded ${statusClass}">${item.status}</span></td>
            <td class="px-6 py-4">${deleteButtonHtml}</td>
        `;

            modalTableBody.appendChild(row);
        });
    }
</script>

<?= $this->endSection() ?>