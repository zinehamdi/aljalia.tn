<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    
    <!-- SEO Meta Tags -->
    <title>الجالية - Aljalia | {{ __('Aljalia.tn - Your guide and family abroad') }}</title>
    <meta name="description" content="{{ __('Tunisians platform abroad, ask, answer, sell, buy... and meet your country brothers in your new neighborhood.') }}">
    <meta name="keywords" content="Aljalia, Jalia, aljalia.tn, الجالية, جالية, الجالية التونسية, تونسيون بالخارج, Tunisiens à l'étranger, Tunisians abroad, expats, Tunisia">
    <meta name="author" content="Aljalia.tn">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ url('/') }}">
    
    <!-- Open Graph (Facebook/LinkedIn) -->
    <meta property="og:title" content="الجالية - Aljalia | {{ __('Aljalia.tn - Your guide and family abroad') }}">
    <meta property="og:description" content="{{ __('Tunisians platform abroad, ask, answer, sell, buy... and meet your country brothers in your new neighborhood.') }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url('/') }}">
    <meta property="og:image" content="{{ asset('social-logo.svg') }}">
    
    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="الجالية - Aljalia | {{ __('Aljalia.tn - Your guide and family abroad') }}">
    <meta name="twitter:description" content="{{ __('Tunisians platform abroad, ask, answer, sell, buy... and meet your country brothers in your new neighborhood.') }}">
    <meta name="twitter:image" content="{{ asset('social-logo.svg') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;900&family=Tajawal:wght@400;500;700;900&display=swap"
        rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-gray-50 flex justify-center">
    <div class="w-full max-w-md min-h-screen bg-white relative shadow-2xl flex flex-col overflow-hidden">

        <!-- Background decoration -->
        <div class="absolute top-0 right-0 w-full h-96 bg-aljalia-red rounded-br-[100px] rounded-bl-[40px] z-0 shadow-lg"
            style="background: linear-gradient(135deg, #C8102E 0%, #a00d25 100%);"></div>
        <div class="absolute -top-20 -right-20 w-64 h-64 bg-white opacity-10 rounded-full blur-2xl z-0"></div>
        <div class="absolute top-20 -left-10 w-40 h-40 bg-white opacity-10 rounded-full blur-xl z-0"></div>

        <main class="flex-1 flex flex-col justify-center items-center px-6 relative z-10 pt-10 pb-20">
            <!-- Logo Section -->
            <div
                class="w-32 h-32 bg-white rounded-full shadow-2xl flex items-center justify-center mb-8 border-4 border-white">
                <div class="w-28 h-28 bg-red-50 rounded-full flex items-center justify-center">
                    <span class="text-aljalia-red font-arabic font-black text-5xl">ج</span>
                </div>
            </div>

            <h1 class="text-4xl font-black text-white {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} mb-3 text-center drop-shadow-md tracking-tight">
                الجالية<span class="text-red-200">.tn</span>
            </h1>
            <p class="text-red-100 {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} text-lg text-center mb-12 drop-shadow">
                {{ __('Aljalia.tn - Your guide and family abroad') }}
            </p>

            <div class="w-full bg-white rounded-3xl shadow-xl p-8 border border-gray-100">
                <h2 class="text-gray-800 text-xl font-bold {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} mb-2 text-center">
                    {{ __('Welcome to your home!') }}
                </h2>
                <p class="text-gray-500 text-sm {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} mb-8 text-center leading-relaxed">
                    {{ __('Tunisians platform abroad, ask, answer, sell, buy... and meet your country brothers in your new neighborhood.') }}
                </p>

                <div class="space-y-4">
                    @auth
                        <a href="{{ route('dashboard') }}"
                            class="w-full bg-aljalia-red text-white font-bold py-4 px-4 rounded-2xl shadow-lg shadow-red-200 hover:bg-red-800 hover:-translate-y-1 transition-all duration-200 {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} text-lg flex justify-center items-center gap-2">
                            {{ __('Enter neighborhood directly') }}
                            <svg class="w-5 h-5 rtl:scale-x-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                        </a>
                    @else
                        <a href="{{ route('register') }}"
                            class="w-full bg-aljalia-red text-white font-bold py-4 px-4 rounded-2xl shadow-lg shadow-red-200 hover:bg-red-800 hover:-translate-y-1 transition-all duration-200 {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} text-lg flex justify-center items-center gap-2 group">
                            {{ __('New Account') }}
                            <svg class="w-5 h-5 rtl:group-hover:-translate-x-1 transition-transform" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                        </a>

                        <div class="relative flex py-2 items-center">
                            <div class="flex-grow border-t border-gray-200"></div>
                            <span class="flex-shrink-0 mx-4 text-gray-400 text-xs {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }}">
                                {{ __('Already have an account?') }}
                            </span>
                            <div class="flex-grow border-t border-gray-200"></div>
                        </div>

                        <a href="{{ route('login') }}"
                            class="w-full bg-gray-50 text-gray-800 border-2 border-gray-200 font-bold py-4 px-4 rounded-2xl hover:bg-gray-100 hover:border-gray-300 transition-all {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} text-lg flex justify-center items-center">
                            {{ __('Login') }}
                        </a>
                    @endauth
                </div>
            </div>

            <div class="mt-8 text-center text-gray-400 text-[10px] {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }}">
                &copy; {{ date('Y') }} {{ config('app.name') }}. {{ __('All rights reserved.') }}
            </div>
        </main>
    </div>
</body>

</html>