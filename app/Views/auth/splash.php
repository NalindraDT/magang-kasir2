<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="refresh" content="5;url=<?= base_url('admin') ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Berhasil</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
        .loading-spinner {
            border: 4px solid rgba(255, 255, 255, 0.3);
            border-top: 4px solid #ffffff;
            border-radius: 50%;
            width: 48px;
            height: 48px;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body class="bg-gray-900">
    <div class="flex flex-col items-center p-8 bg-white dark:bg-gray-800 shadow-2xl rounded-2xl w-full max-w-sm text-center relative border border-gray-200 dark:border-gray-700">
        <div class="loading-spinner mb-4"></div>
        <h1 class="text-3xl font-extrabold text-gray-800 dark:text-white mb-2 tracking-tight">Login Berhasil!</h1>
        <p class="text-gray-500 dark:text-gray-400 text-sm">Anda akan diarahkan ke dasbor dalam 5 detik...</p>
    </div>
</body>
</html>