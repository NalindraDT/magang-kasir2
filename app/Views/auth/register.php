<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.css" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

        body {
            font-family: 'Inter', sans-serif;
            background-color: #1a202c;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
    </style>
</head>

<body class="bg-gray-900">
    <div class="flex flex-col items-center p-8 bg-white dark:bg-gray-800 shadow-2xl rounded-2xl w-full max-w-md text-center">
        <h1 class="text-3xl font-extrabold text-gray-800 dark:text-white mb-6">Registrasi Akun Admin</h1>

        <?php if (session()->getFlashdata('errors')): ?>
            <div class="p-4 mb-6 text-sm text-red-800 rounded-lg bg-red-100 dark:bg-gray-700 dark:text-red-400 border border-red-300 dark:border-red-500 w-full text-left">
                <ul class="list-disc list-inside space-y-1">
                    <?php foreach (session()->getFlashdata('errors') as $error): ?>
                        <li><?= esc($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('auth/register/create') ?>" method="post" class="w-full space-y-4">
            <!-- Username -->
            <div class="relative">
                <input type="text" name="username" placeholder="Username" required
                    class="w-full px-4 py-3 pl-10 border rounded-lg shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
                <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M5.121 17.804A9.935 9.935 0 0112 15c2.485 0 4.735.904 6.879 2.804M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </span>
            </div>

            <!-- Email -->
            <div class="relative">
                <input type="email" name="email" placeholder="Email" required
                    class="w-full px-4 py-3 pl-10 border rounded-lg shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
                <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M16 12H8m0 0l4-4m-4 4l4 4m8-8v8a2 2 0 01-2 2H6a2 2 0 01-2-2V8a2 2 0 012-2h12a2 2 0 012 2z" />
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

            <!-- Confirm Password -->
            <div class="relative">
                <input type="password" name="pass_confirm" placeholder="Konfirmasi Password" required
                    class="w-full px-4 py-3 pl-10 border rounded-lg shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
                <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-4.586a1 1 0 00-.293-.707l-7.414-7.414a1 1 0 00-1.414 0L4.293 13.707A1 1 0 004 14.414V19a2 2 0 002 2z" />
                    </svg>
                </span>
            </div>

            <!-- Recaptcha -->
            <div class="flex justify-center">
                <div class="g-recaptcha" data-sitekey="<?= getenv('recaptcha.sitekey') ?>"></div>
            </div>

            <!-- Button -->
            <button type="submit"
                class="w-full mt-2 px-4 py-3 text-white font-semibold bg-blue-500 hover:bg-blue-600 transition-all duration-300 rounded-lg shadow-lg focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-2">
                Daftar
            </button>
        </form>

        <p class="text-sm text-gray-500 dark:text-gray-400 mt-6">
            Sudah punya akun?
            <a href="<?= base_url('auth/login') ?>" class="text-blue-600 hover:underline dark:text-blue-400 font-medium">Kembali ke halaman login</a>
        </p>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
</body>

</html>