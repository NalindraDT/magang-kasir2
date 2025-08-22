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
            background-color: #1a202c;
            /* Warna latar belakang gelap */
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
    </style>
</head>

<body class="bg-gray-900">
    <div class="flex flex-col items-center p-8 bg-white dark:bg-gray-800 shadow-2xl rounded-2xl w-full max-w-md text-center relative">

        <!-- Tombol kembali -->
        <a href="<?= base_url('/') ?>"
            class="absolute top-4 left-4 text-gray-500 hover:text-gray-900 dark:hover:text-white transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
        </a>

        <!-- Judul -->
        <h1 class="text-3xl font-extrabold text-gray-800 dark:text-white mb-6 mt-6">Login Admin</h1>

        <!-- Alert error -->
        <?php if (session()->getFlashdata('error')): ?>
            <div class="p-4 mb-6 text-sm text-red-800 rounded-lg bg-red-100 dark:bg-gray-700 dark:text-red-400 border border-red-300 dark:border-red-500 w-full text-left">
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>
        <!-- Alert sukses -->
        <?php if (session()->getFlashdata('message')): ?>
            <div class="p-4 mb-6 text-sm text-green-800 rounded-lg bg-green-100 dark:bg-gray-700 dark:text-green-400 border border-green-300 dark:border-green-500 w-full text-left">
                <?= session()->getFlashdata('message') ?>
            </div>
        <?php endif; ?>

        <!-- Form login -->
        <form action="<?= base_url('auth/manual_login') ?>" method="post" class="w-full space-y-4 mb-6">
            <!-- Username -->
            <div class="relative">
                <input type="text" name="username" placeholder="Username atau Email" required
                    class="w-full px-4 py-3 pl-10 border rounded-lg shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
                <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M5.121 17.804A9.935 9.935 0 0112 15c2.485 0 4.735.904 6.879 2.804M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </span>
            </div>
            <!-- Password -->
            <div class="relative">
                <input type="password" name="password" placeholder="Password" required
                    class="w-full px-4 py-3 pl-10 border rounded-lg shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
                <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 11c0-1.104-.896-2-2-2s-2 .896-2 2m4 0c0 1.104.896 2 2 2s2-.896 2-2m-6 4h4m6 0V9a2 2 0 00-2-2h-1V6a5 5 0 10-10 0v1H6a2 2 0 00-2 2v6a2 2 0 002 2h12a2 2 0 002-2z" />
                    </svg>
                </span>
            </div>
            <!-- Tombol login -->
            <button type="submit"
                class="w-full px-4 py-3 text-white font-semibold bg-blue-500 hover:bg-blue-600 transition-all duration-300 rounded-lg shadow-lg focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-2">
                Login
            </button>
        </form>

        <!-- Divider -->
        <p class="mb-4 text-gray-500 dark:text-gray-400">atau</p>

        <!-- Login Google -->
        <a href="<?= base_url('auth/google/login') ?>"
            class="inline-flex items-center justify-center w-full px-4 py-3 text-white font-semibold bg-red-500 hover:bg-red-600 transition-all duration-300 rounded-lg shadow-lg focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-offset-2">
            <svg class="w-6 h-6 mr-2" fill="currentColor" viewBox="0 0 24 24">
                <path d="M22.5 12.1c0-.8-.1-1.6-.3-2.4H12v4.5h6.4c-.2 1.5-.9 2.8-2.1 3.7l3.9 3c2.3-2.1 3.6-5.2 3.6-8.8z" fill="#4285F4" />
                <path d="M12 24c3.2 0 5.8-1.1 7.7-3l-3.9-3c-1.1.7-2.6 1.1-3.8 1.1-2.9 0-5.3-2-6.2-4.7L3.1 18.6c1.8 3.5 5.4 5.4 8.9 5.4z" fill="#34A853" />
                <path d="M5.8 14.5c-.5-1.5-.5-3.1 0-4.6L3.1 7.3C1.2 10.8 1.2 14.2 3.1 17.7l2.7-3.2z" fill="#FBBC05" />
                <path d="M12 4c1.8 0 3.3.6 4.5 1.7l3.4-3.4c-2.3-2.2-5.4-3.5-8.9-3.5-3.6 0-7.2 2-8.9 5.4l2.7 3.2C6.7 6 9.1 4 12 4z" fill="#EA4335" />
            </svg>
            Login dengan Google
        </a>

        <!-- Link daftar -->
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-6">
            Belum punya akun?
            <a href="<?= base_url('auth/register') ?>"
                class="text-blue-600 hover:underline dark:text-blue-400 font-medium">
                Daftar sekarang
            </a>
        </p>

    </div>
    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
</body>

</html>