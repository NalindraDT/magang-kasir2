<?= $this->extend('produk/layout') ?>

<?= $this->section('content') ?>
<main class="p-4 pt-20 sm:ml-16">
    <div class="p-4 rounded-lg w-full">

        <?php if (session()->getFlashdata('message')): ?>
            <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-100 dark:bg-gray-800 dark:text-green-400" role="alert">
                <?= session()->getFlashdata('message') ?>
            </div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-100 dark:bg-gray-800 dark:text-red-400" role="alert">
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>

        <h2 class="text-3xl font-extrabold mb-8 text-gray-900 dark:text-white tracking-tight">Manajemen Supplier</h2>
        <button type="button" data-modal-target="tambah-supplier-modal" data-modal-toggle="tambah-supplier-modal" class="inline-flex items-center gap-2 text-white bg-blue-600 hover:bg-blue-700 font-semibold rounded-lg text-sm px-5 py-2.5 mb-4">
            ➕ Tambah Supplier
        </button>
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-300">
                    <tr>
                        <th scope="col" class="px-6 py-3">No</th>
                        <th scope="col" class="px-6 py-3">Nama Supplier</th>
                        <th scope="col" class="px-6 py-3">Kontak</th>
                        <th scope="col" class="px-6 py-3">Alamat</th>
                        <th scope="col" class="px-6 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($restokers)): ?>
                        <tr>
                            <td colspan="4" class="text-center py-4 dark:text-gray-400">Belum ada data supplier.</td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1; ?>
                        <?php foreach ($restokers as $supplier): ?>
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                            <td class="px-6 py-4 font-semibold text-gray-900 dark:text-white"><?= $no++ ?></td>
                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white"><?= esc($supplier['nama_restoker']) ?></td>
                            <td class="px-6 py-4"><?= esc($supplier['kontak']) ?></td>
                            <td class="px-6 py-4"><?= esc($supplier['alamat']) ?></td>
                            <td class="px-6 py-4 text-center">
                                <button type="button" data-modal-target="edit-supplier-modal" data-modal-toggle="edit-supplier-modal" class="font-medium text-blue-600 dark:text-blue-500 hover:underline btn-edit-supplier"
                                    data-id="<?= $supplier['id_restoker'] ?>" data-nama="<?= esc($supplier['nama_restoker']) ?>"
                                    data-kontak="<?= esc($supplier['kontak']) ?>" data-alamat="<?= esc($supplier['alamat']) ?>">Edit</button>
                                <a href="<?= base_url('admin/restok/supplier/delete/' . $supplier['id_restoker']) ?>" class="font-medium text-red-600 dark:text-red-500 hover:underline ml-4" onclick="return confirm('Yakin ingin menghapus supplier ini?')">Hapus</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<div id="tambah-supplier-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-md max-h-full">
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Tambah Supplier Baru</h3>
                <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-toggle="tambah-supplier-modal">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                    </svg><span class="sr-only">Close modal</span>
                </button>
            </div>
            <form action="<?= base_url('admin/restok/supplier/create') ?>" method="POST" class="p-4 md:p-5">
                <div class="grid gap-4 mb-4">
                    <div>
                        <label for="nama_restoker" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama Supplier</label>
                        <input type="text" name="nama_restoker" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white" required>
                    </div>
                    <div>
                        <label for="kontak" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Kontak (Telp/Email)</label>
                        <input type="text" name="kontak" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white">
                    </div>
                    <div>
                        <label for="alamat" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Alamat</label>
                        <textarea name="alamat" rows="3" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white"></textarea>
                    </div>
                </div>
                <button type="submit" class="text-white inline-flex items-center bg-blue-700 hover:bg-blue-800 font-medium rounded-lg text-sm px-5 py-2.5">
                    <svg class="me-1 -ms-1 w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"></path>
                    </svg>
                    Tambah Supplier
                </button>
            </form>
        </div>
    </div>
</div>

<div id="edit-supplier-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-md max-h-full">
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Edit Data Supplier</h3>
                <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-toggle="edit-supplier-modal">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                    </svg><span class="sr-only">Close modal</span>
                </button>
            </div>
            <form id="form-edit-supplier" method="POST" class="p-4 md:p-5">
                <div class="grid gap-4 mb-4">
                    <div>
                        <label for="edit_nama_restoker" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama Supplier</label>
                        <input type="text" name="nama_restoker" id="edit_nama_restoker" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white" required>
                    </div>
                    <div>
                        <label for="edit_kontak" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Kontak (Telp/Email)</label>
                        <input type="text" name="kontak" id="edit_kontak" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white">
                    </div>
                    <div>
                        <label for="edit_alamat" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Alamat</label>
                        <textarea name="alamat" id="edit_alamat" rows="3" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white"></textarea>
                    </div>
                </div>
                <button type="submit" class="text-white inline-flex items-center bg-blue-700 hover:bg-blue-800 font-medium rounded-lg text-sm px-5 py-2.5">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z"></path>
                        <path fill-rule="evenodd" d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" clip-rule="evenodd"></path>
                    </svg>
                    Simpan Perubahan
                </button>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- Skrip untuk Modal Edit Supplier ---
        const editSupplierButtons = document.querySelectorAll('.btn-edit-supplier');
        const formEditSupplier = document.getElementById('form-edit-supplier');
        const editNama = document.getElementById('edit_nama_restoker');
        const editKontak = document.getElementById('edit_kontak');
        const editAlamat = document.getElementById('edit_alamat');

        editSupplierButtons.forEach(button => {
            button.addEventListener('click', function() {
                const id = this.dataset.id;
                const nama = this.dataset.nama;
                const kontak = this.dataset.kontak;
                const alamat = this.dataset.alamat;

                formEditSupplier.action = `<?= base_url('admin/restok/supplier/update/') ?>/${id}`;
                editNama.value = nama;
                editKontak.value = kontak;
                editAlamat.value = alamat;
            });
        });
    });
</script>
<?= $this->endSection() ?>