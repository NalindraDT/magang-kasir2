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

        <h2 class="text-3xl font-extrabold mb-8 text-gray-900 dark:text-white tracking-tight">Manajemen Kategori</h2>
        
        <div class="mb-8 p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Tambah Kategori Baru</h3>
            <form action="<?= base_url('admin/kategori/create') ?>" method="POST" class="flex items-center gap-4">
                <input type="text" name="nama_kategori" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white" placeholder="Nama kategori baru" required>
                <button type="submit" class="text-white bg-blue-600 hover:bg-blue-700 font-semibold rounded-lg text-sm px-5 py-2.5">Tambah</button>
            </form>
        </div>

        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-300">
                    <tr>
                        <th scope="col" class="px-6 py-3">No</th>
                        <th scope="col" class="px-6 py-3">Nama Kategori</th>
                        <th scope="col" class="px-6 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($kategori)): ?>
                        <tr>
                            <td colspan="3" class="text-center py-4 dark:text-gray-400">Belum ada data kategori.</td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1; ?>
                        <?php foreach ($kategori as $kat): ?>
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                            <td class="px-6 py-4 font-semibold text-gray-900 dark:text-white"><?= $no++ ?></td>
                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white"><?= esc($kat['nama_kategori']) ?></td>
                            <td class="px-6 py-4 text-center">
                                <button type="button" data-modal-target="edit-kategori-modal" data-modal-toggle="edit-kategori-modal" class="font-medium text-blue-600 dark:text-blue-500 hover:underline btn-edit-kategori"
                                    data-id="<?= $kat['id_kategori'] ?>" data-nama="<?= esc($kat['nama_kategori']) ?>">Edit</button>
                                <a href="<?= base_url('admin/kategori/delete/' . $kat['id_kategori']) ?>" class="font-medium text-red-600 dark:text-red-500 hover:underline ml-4" onclick="return confirm('Yakin ingin menghapus kategori ini?')">Hapus</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<div id="edit-kategori-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-md max-h-full">
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Edit Nama Kategori</h3>
                <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-toggle="edit-kategori-modal">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" /></svg><span class="sr-only">Close modal</span>
                </button>
            </div>
            <form id="form-edit-kategori" method="POST" class="p-4 md:p-5">
                <input type="text" name="nama_kategori" id="edit_nama_kategori" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500" required>
                <button type="submit" class="mt-4 text-white w-full bg-blue-700 hover:bg-blue-800 font-medium rounded-lg text-sm px-5 py-2.5">Simpan Perubahan</button>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const editKategoriButtons = document.querySelectorAll('.btn-edit-kategori');
        const formEditKategori = document.getElementById('form-edit-kategori');
        const editNamaInput = document.getElementById('edit_nama_kategori');

        editKategoriButtons.forEach(button => {
            button.addEventListener('click', function() {
                const id = this.dataset.id;
                const nama = this.dataset.nama;

                formEditKategori.action = `<?= base_url('admin/kategori/update/') ?>/${id}`;
                editNamaInput.value = nama;
            });
        });
    });
</script>
<?= $this->endSection() ?>