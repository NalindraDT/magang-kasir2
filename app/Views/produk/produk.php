<?= $this->extend('produk/layout') ?>

<?= $this->section('content') ?>
<main class="p-4 pt-20 sm:ml-16 flex flex-col items-center">
    <div class="p-4 rounded-lg max-w-full w-full">
        <h1 class="text-3xl font-extrabold mb-8 text-gray-900 dark:text-white tracking-tight">
            📦 Manajemen Produk
        </h1>

        <!-- Pesan Status -->
        <?php if (session()->getFlashdata('message')): ?>
            <div class="p-4 mb-6 text-sm font-medium text-green-800 rounded-lg bg-green-100 border border-green-300 dark:bg-gray-800 dark:text-green-400 dark:border-green-600 shadow">
                ✅ <?= session()->getFlashdata('message') ?>
            </div>
        <?php endif; ?>

        <!-- Tombol Tambah Produk -->
        <a href="<?= base_url('admin/produk/tambah') ?>"
            class="inline-flex items-center gap-2 text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 font-semibold rounded-lg text-sm px-5 py-2.5 shadow transition dark:bg-blue-500 dark:hover:bg-blue-600 dark:focus:ring-blue-700">
            ➕ Tambah Produk
        </a>

        <!-- Tabel Produk -->
        <div class="relative overflow-x-auto shadow-xl sm:rounded-2xl mt-8 w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
            <table class="w-full text-sm text-left text-gray-600 dark:text-gray-300">
                <thead class="text-xs text-gray-700 uppercase bg-gradient-to-r from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-800 dark:text-gray-300">
                    <tr>
                        <th scope="col" class="px-6 py-4">No</th>
                        <th scope="col" class="px-6 py-4">Nama Produk</th>
                        <th scope="col" class="px-6 py-4">Harga</th>
                        <th scope="col" class="px-6 py-4">Stok</th>
                        <th scope="col" class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody id="produk-table-body">
                    <?php if (empty($produks)): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-6 text-center text-gray-500 dark:text-gray-400 italic">
                                Belum ada produk 📂
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1; ?>
                        <?php foreach ($produks as $produk): ?>
                            <tr class="border-b even:bg-gray-50 odd:bg-white dark:even:bg-gray-700 dark:odd:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-600 transition">
                                <td class="px-6 py-4 font-semibold text-gray-900 dark:text-white"><?= $no++ ?></td>
                                <td class="px-6 py-4"><?= $produk['nama_produk'] ?></td>
                                <td class="px-6 py-4 font-medium">Rp <?= number_format($produk['harga'], 0, ',', '.') ?></td>
                                <td class="px-6 py-4">
                                    <?php if ($produk['stok'] > 10): ?>
                                        <span class="px-2 py-1 text-xs font-bold rounded-full bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300">
                                            <?= $produk['stok'] ?> Tersedia
                                        </span>
                                    <?php elseif ($produk['stok'] > 0): ?>
                                        <span class="px-2 py-1 text-xs font-bold rounded-full bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300">
                                            <?= $produk['stok'] ?> Terbatas
                                        </span>
                                    <?php else: ?>
                                        <span class="px-2 py-1 text-xs font-bold rounded-full bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300">
                                            Habis
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 flex justify-center gap-3">
                                    <!-- Tombol Edit -->
                                    <a href="<?= base_url('admin/produk/edit/' . $produk['id_produk']) ?>"
                                        class="inline-flex items-center justify-center p-2 rounded-full bg-blue-100 hover:bg-blue-200 dark:bg-blue-800 dark:hover:bg-blue-700 text-blue-600 dark:text-blue-300 transition"
                                        title="Edit Produk">
                                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M5 20h14v2H5v-2zm13.7-11.3l-2.4-2.4a1 1 0 0 0-1.4 0L7 14.2V17h2.8l8.9-8.9a1 1 0 0 0 0-1.4z" />
                                        </svg>
                                    </a>
                                    <!-- Tombol Hapus -->
                                    <a href="<?= base_url('admin/produk/hapus/' . $produk['id_produk']) ?>"
                                        class="inline-flex items-center justify-center p-2 rounded-full bg-red-100 hover:bg-red-200 dark:bg-red-800 dark:hover:bg-red-700 text-red-600 dark:text-red-300 transition"
                                        onclick="return confirm('Apakah Anda yakin ingin menghapus produk ini?')"
                                        title="Hapus Produk">
                                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                            <path fill-rule="evenodd" d="M8.586 2.586A2 2 0 0110 2h4a2 2 0 012 2v2h3a1 1 0 010 2h-1v12a2 2 0 01-2 2H7a2 2 0 01-2-2V8H4a1 1 0 010-2h3V4a2 2 0 011.586-1.414zM8 6h8V4h-2a2 2 0 11-4 0H8v2zm2 5a1 1 0 011 1v5a1 1 0 11-2 0v-5a1 1 0 011-1zm4 0a1 1 0 011 1v5a1 1 0 11-2 0v-5a1 1 0 011-1z" clip-rule="evenodd" />
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div id="pagination" class="flex justify-center items-center gap-2 mt-6"></div>
    </div>
</main>

<!-- Script Pagination -->
<script>
    document.addEventListener("DOMContentLoaded", () => {
        const rowsPerPage = 5;
        const tableBody = document.getElementById("produk-table-body");
        const rows = tableBody.querySelectorAll("tr");
        const pagination = document.getElementById("pagination");

        let currentPage = 1;
        const totalPages = Math.ceil(rows.length / rowsPerPage);

        function showPage(page) {
            rows.forEach((row, index) => {
                row.style.display =
                    index >= (page - 1) * rowsPerPage && index < page * rowsPerPage
                        ? ""
                        : "none";
            });
        }

        function renderPagination() {
            pagination.innerHTML = "";

            if (totalPages <= 1) return;

            // Tombol Prev
            const prevBtn = document.createElement("button");
            prevBtn.innerText = "‹ Prev";
            prevBtn.className =
                "px-3 py-1 rounded border border-gray-600 text-gray-300 hover:bg-gray-700 disabled:opacity-50 disabled:text-gray-500";
            prevBtn.disabled = currentPage === 1;
            prevBtn.onclick = () => {
                if (currentPage > 1) {
                    currentPage--;
                    updateTable();
                }
            };
            pagination.appendChild(prevBtn);

            // Tombol halaman
            for (let i = 1; i <= totalPages; i++) {
                const pageBtn = document.createElement("button");
                pageBtn.innerText = i;
                pageBtn.className =
                    "px-3 py-1 rounded border border-gray-600 " +
                    (i === currentPage
                        ? "bg-blue-600 text-white"
                        : "text-gray-300 hover:bg-gray-700");
                pageBtn.onclick = () => {
                    currentPage = i;
                    updateTable();
                };
                pagination.appendChild(pageBtn);
            }

            // Tombol Next
            const nextBtn = document.createElement("button");
            nextBtn.innerText = "Next ›";
            nextBtn.className =
                "px-3 py-1 rounded border border-gray-600 text-gray-300 hover:bg-gray-700 disabled:opacity-50 disabled:text-gray-500";
            nextBtn.disabled = currentPage === totalPages;
            nextBtn.onclick = () => {
                if (currentPage < totalPages) {
                    currentPage++;
                    updateTable();
                }
            };
            pagination.appendChild(nextBtn);
        }

        function updateTable() {
            showPage(currentPage);
            renderPagination();
        }

        updateTable();
    });
</script>

<?= $this->endSection() ?>
