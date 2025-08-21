<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selamat Datang di Kasir Online</title>
    <link href="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.css" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        body {
            font-family: 'Inter', sans-serif;
        }
        .dark .bg-gray-100 {
            background-color: #1a202c;
        }
    </style>
</head>
<body class="bg-gray-100 dark:bg-gray-900 flex items-center justify-center h-screen">

    <div class="flex flex-col items-center justify-center p-8 bg-white dark:bg-gray-800 shadow-lg rounded-lg max-w-sm w-full">
        <svg class="w-20 h-20 mb-6 text-teal-500 dark:text-teal-400" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
        </svg>
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white mb-6">Selamat Datang</h1>

        <a href="<?= base_url('admin') ?>" class="w-full mb-4 px-6 py-3 text-white font-semibold rounded-lg text-center shadow-md hover:shadow-lg transition-shadow" style="background-color: #ef4444;">
            Admin
        </a>
        <a href="<?= base_url('pembeli') ?>" class="w-full px-6 py-3 text-white font-semibold rounded-lg text-center shadow-md hover:shadow-lg transition-shadow" style="background-color: #3b82f6;">
            Pembeli
        </a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
</body>
</html>
