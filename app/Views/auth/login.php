<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.css" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        body {
            font-family: 'Inter', sans-serif;
            background-color: #1a202c; /* Warna latar belakang gelap */
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
    </style>
</head>
<body class="bg-gray-900">
    <div class="flex flex-col items-center p-8 bg-white dark:bg-gray-800 shadow-lg rounded-lg w-full max-w-md text-center">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white mb-6">Login Admin</h1>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('message')): ?>
            <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400" role="alert">
                <?= session()->getFlashdata('message') ?>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('auth/manual_login') ?>" method="post" class="w-full mb-4">
            <div class="mb-4">
                <input type="text" name="username" placeholder="Username atau Email" required class="w-full px-4 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            </div>
            <div class="mb-4">
                <input type="password" name="password" placeholder="Password" required class="w-full px-4 py-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            </div>
            <button type="submit" class="w-full px-4 py-2 text-white font-semibold bg-blue-600 hover:bg-blue-700 transition-colors rounded-lg shadow-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                Login
            </button>
        </form>

        <p class="mb-4 text-gray-500 dark:text-gray-400">atau</p>
        <a href="<?= base_url('auth/google/login') ?>" class="inline-flex items-center justify-center w-full px-4 py-2 text-white font-semibold bg-red-600 hover:bg-red-700 transition-colors rounded-lg shadow-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
            <svg class="w-6 h-6 mr-2" fill="currentColor" viewBox="0 0 24 24">
                <path d="M22.5 12.1c0-.8-.1-1.6-.3-2.4H12v4.5h6.4c-.2 1.5-.9 2.8-2.1 3.7l3.9 3c2.3-2.1 3.6-5.2 3.6-8.8z" fill="#4285F4"/>
                <path d="M12 24c3.2 0 5.8-1.1 7.7-3l-3.9-3c-1.1.7-2.6 1.1-3.8 1.1-2.9 0-5.3-2-6.2-4.7L3.1 18.6c1.8 3.5 5.4 5.4 8.9 5.4z" fill="#34A853"/>
                <path d="M5.8 14.5c-.5-1.5-.5-3.1 0-4.6L3.1 7.3C1.2 10.8 1.2 14.2 3.1 17.7l2.7-3.2z" fill="#FBBC05"/>
                <path d="M12 4c1.8 0 3.3.6 4.5 1.7l3.4-3.4c-2.3-2.2-5.4-3.5-8.9-3.5-3.6 0-7.2 2-8.9 5.4l2.7 3.2C6.7 6 9.1 4 12 4z" fill="#EA4335"/>
            </svg>
            Login with Google
        </a>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
</body>
</html>