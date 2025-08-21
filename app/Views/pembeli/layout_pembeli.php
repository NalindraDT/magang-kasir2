<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembeli - Kasir Online</title>
    <link href="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.css" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
        }
    </style>
</head>
<body class="bg-gray-100 dark:bg-gray-900">
    <!-- Navbar sederhana untuk pembeli -->
    <nav class="bg-white dark:bg-gray-800 shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center">
                    <a href="/" class="flex-shrink-0 text-2xl font-bold text-gray-900 dark:text-white">
                        TOKO MAGANG
                    </a>
                </div>
                <div class="hidden md:block">
                    <a href="#" class="text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md px-3 py-2 text-sm font-medium">
                        Keranjang (<?= isset($jumlah_keranjang) ? $jumlah_keranjang : 0 ?>)
                    </a>
                </div>
            </div>
        </div>
    </nav>
    
    <!-- Konten Utama -->
    <div class="p-4">
        <?= $this->renderSection('content') ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
    <?= $this->renderSection('scripts') ?>
</body>
</html>
