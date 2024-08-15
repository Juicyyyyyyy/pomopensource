<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&family=Oswald:wght@700&display=swap" rel="stylesheet">

    <!-- Fontawesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Styles -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="text-white font-sans antialiased dark:bg-black dark:text-white/50 bg-cover bg-center" style="background-image: url('https://cdn.discordapp.com/attachments/780634443157078026/1273306879375704064/backiee-226955-landscape.jpg?ex=66be22f0&is=66bcd170&hm=48d015b13f4971ed1d9bd1d2c2e293c5393f82cbfb75d4aec97556c83e025934&');">
<div class="relative min-h-screen flex flex-col items-center justify-center selection:bg-[#FF2D20] selection:text-white">
    <div class="relative w-full max-w-2xl px-6 lg:max-w-7xl">

        <!-- Updated Header -->
        <header class="flex justify-between items-center py-6">
            <!-- Logo or Site Name -->
            <div class="flex items-center space-x-2">
                <i class="fas fa-check text-white w-6 h-6"></i>
                <h1 class="text-xl font-oswald font-bold">Pomopensource</h1>
            </div>

            <!-- Right-side Buttons -->
            <div class="flex space-x-3">
                <button class="flex items-center space-x-1 py-2 px-3 bg-white/10 text-white rounded-full hover:bg-white/20">
                    <i class="fas fa-flag w-4 h-4"></i>
                    <span class="text-sm font-inter">Report</span>
                </button>

                <button class="flex items-center space-x-1 py-2 px-3 bg-white/10 text-white rounded-full hover:bg-white/20">
                    <i class="fas fa-cog w-4 h-4"></i>
                    <span class="text-sm font-inter">Setting</span>
                </button>

                <button class="py-2 px-3 bg-white/10 text-white rounded-full hover:bg-white/20">
                    <i class="fas fa-user w-6 h-6"></i>
                </button>
            </div>
        </header>

        <main class="flex flex-col items-center justify-center text-white">
            <!-- Timer Mode Buttons -->
            <div class="flex space-x-4 mb-8">
                <button class="py-2 px-4 bg-white text-gray-800 font-semibold rounded-full shadow hover:bg-gray-100">
                    pomodoro
                </button>
                <button class="py-2 px-4 text-white font-semibold rounded-full border border-white hover:bg-white hover:text-gray-800">
                    short break
                </button>
                <button class="py-2 px-4 text-white font-semibold rounded-full border border-white hover:bg-white hover:text-gray-800">
                    long break
                </button>
            </div>

            <!-- Timer Display -->
            <div class="text-9xl font-oswald font-bold mb-8">
                25:00
            </div>

            <!-- Control Buttons -->
            <div class="flex space-x-4 mb-8">
                <button class="py-2 px-8 bg-white text-gray-800 font-semibold rounded-full shadow hover:bg-gray-100">
                    start
                </button>
                <button class="text-3xl">
                    <i class="fas fa-sync-alt"></i>
                </button>
                <button class="text-3xl">
                    <i class="fas fa-cog"></i>
                </button>
            </div>


            <!-- Add Project Section -->
            <div class="w-full max-w-lg py-8 px-6 bg-white/10 rounded-lg shadow-lg">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold font-oswald">Projects</h2>
                    <button class="p-2 rounded-full bg-white/20 hover:bg-white/30">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                        </svg>
                    </button>
                </div>
                <button class="w-full py-2 border-2 border-dashed border-white rounded-lg text-center text-sm font-inter font-semibold hover:bg-white/20">
                    + Add Project
                </button>
            </div>
        </main>

        <footer class="py-16 text-center text-sm text-black dark:text-white/70">

        </footer>
    </div>
</div>
</body>
</html>
