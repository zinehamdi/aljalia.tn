<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'الجالية.tn') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&family=Tajawal:wght@400;500;700&display=swap"
        rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased text-gray-900 bg-gray-50 flex justify-center">
    <!-- Main Mobile Container -->
    <div
        class="w-full max-w-md min-h-screen bg-gray-50 flex flex-col sm:justify-center items-center pt-6 sm:pt-0 pb-12 relative overflow-x-hidden">
        <div class="mt-8 mb-4">
            <a href="/" wire:navigate class="flex flex-col items-center">
                <!-- Custom Logo or Text for Aljalia -->
                <div class="w-20 h-20 bg-aljalia-red rounded-full flex items-center justify-center shadow-lg mb-2">
                    <span class="text-white font-bold text-3xl font-arabic">ج</span>
                </div>
                <h1 class="text-2xl font-bold text-aljalia-red font-arabic tracking-tight">الجالية.tn</h1>
            </a>
        </div>

        <div
            class="w-full sm:max-w-md mt-2 px-6 py-6 bg-white shadow-xl overflow-hidden sm:rounded-2xl border border-gray-100">
            {{ $slot }}
        </div>
    </div>
</body>

</html>