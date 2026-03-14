<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- SEO Meta Tags -->
    <title>@yield('title', 'الجالية - Aljalia') | {{ __('Aljalia.tn - Your guide and family abroad') }}</title>
    <meta name="description" content="{{ __('Tunisians platform abroad, ask, answer, sell, buy... and meet your country brothers in your new neighborhood.') }}">
    <meta name="keywords" content="Aljalia, Jalia, aljalia.tn, الجالية, جالية, الجالية التونسية, تونسيون بالخارج, Tunisiens à l'étranger, Tunisians abroad, expats, Tunisia">
    <meta name="author" content="Aljalia.tn">
    <meta name="robots" content="index, follow">

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
        href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&family=Tajawal:wght@400;500;700&display=swap"
        rel="stylesheet">

    <!-- Icons (FontAwesome or similar, using Lucide for now via CDN or similar if needed. For now assume icons are passed as text or SVG) -->

    <!-- PWA & Mobile Optimization -->
    <link rel="manifest" href="/manifest.json">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Aljalia">
    <link rel="apple-touch-icon" href="https://ui-avatars.com/api/?name=Aljalia&background=e11d48&color=fff&size=180">
    <meta name="theme-color" content="#e11d48">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased text-gray-900 bg-gray-50 flex justify-center">
    <!-- Main Mobile Container -->
    <div class="w-full max-w-md min-h-screen bg-white relative shadow-2xl flex flex-col overflow-x-hidden">

        <!-- Top App Bar -->
        @if (isset($header))
            <header class="bg-aljalia-red text-white shadow-md sticky top-0 z-50">
                <div class="py-4 px-4 flex items-center justify-between">
                    <div class="flex-1">
                        {{ $header }}
                    </div>

                    <!-- Language Switcher -->
                    <div class="flex items-center gap-2 mr-2">
                        <a href="{{ route('locale.switch', 'ar') }}"
                            class="text-[10px] font-bold px-1.5 py-1 rounded {{ app()->getLocale() == 'ar' ? 'bg-white text-aljalia-red' : 'bg-red-800/30 text-white hover:bg-red-800/50' }}">AR</a>
                        <a href="{{ route('locale.switch', 'fr') }}"
                            class="text-[10px] font-bold px-1.5 py-1 rounded {{ app()->getLocale() == 'fr' ? 'bg-white text-aljalia-red' : 'bg-red-800/30 text-white hover:bg-red-800/50' }}">FR</a>
                        <a href="{{ route('locale.switch', 'en') }}"
                            class="text-[10px] font-bold px-1.5 py-1 rounded {{ app()->getLocale() == 'en' ? 'bg-white text-aljalia-red' : 'bg-red-800/30 text-white hover:bg-red-800/50' }}">EN</a>
                    </div>
                </div>
            </header>
        @endif

        <!-- Page Content -->
        <main class="flex-1 pb-20"> <!-- pb-20 to leave space for bottom nav -->
            {{ $slot }}
        </main>

        <!-- Bottom Navigation -->
        @auth
            <nav
                class="fixed bottom-0 w-full max-w-md bg-white border-t border-gray-200 flex justify-around items-center py-2 z-50 pb-safe">
                <a href="{{ route('dashboard') }}" wire:navigate
                    class="flex flex-col items-center justify-center w-1/4 {{ request()->routeIs('dashboard') || request()->routeIs('category.*') || request()->routeIs('posts.*') ? 'text-aljalia-red' : 'text-gray-500 hover:text-aljalia-red' }} transition-colors">
                    <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                        </path>
                    </svg>
                    <span
                        class="text-[10px] font-bold {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }}">{{ __('Neighborhood') }}</span>
                </a>
                <a href="{{ route('messages.index') }}" wire:navigate
                    class="flex flex-col items-center justify-center w-1/4 relative {{ request()->routeIs('messages.*') ? 'text-aljalia-red' : 'text-gray-500 hover:text-aljalia-red' }} transition-colors">
                    @php $unread = auth()->user()->totalUnreadMessages(); @endphp
                    @if($unread > 0)
                        <span
                            class="absolute -top-1 right-2 bg-aljalia-red text-white text-[9px] font-bold w-4 h-4 rounded-full flex items-center justify-center">{{ $unread > 9 ? '9+' : $unread }}</span>
                    @endif
                    <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z">
                        </path>
                    </svg>
                    <span
                        class="text-[10px] font-bold {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }}">{{ __('Connect') }}</span>
                </a>
                <a href="{{ route('places.index') }}" wire:navigate
                    class="flex flex-col items-center justify-center w-1/4 {{ request()->routeIs('places.*') ? 'text-aljalia-red' : 'text-gray-500 hover:text-aljalia-red' }} transition-colors">
                    <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z">
                        </path>
                    </svg>
                    <span
                        class="text-[10px] font-bold {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }}">{{ __('Shop') }}</span>
                </a>
                <a href="{{ route('profile') }}" wire:navigate
                    class="flex flex-col items-center justify-center w-1/4 {{ request()->routeIs('profile') ? 'text-aljalia-red' : 'text-gray-500 hover:text-aljalia-red' }} transition-colors">
                    @if(auth()->user()->avatar_url)
                        <img src="{{ asset('storage/' . auth()->user()->avatar_url) }}"
                            class="w-6 h-6 rounded-full object-cover mb-1 border {{ request()->routeIs('profile') ? 'border-aljalia-red' : 'border-gray-300' }}">
                    @else
                        <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    @endif
                    <span
                        class="text-[10px] font-bold {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }}">{{ __('Profile') }}</span>
                </a>
            </nav>
        @endauth
        <!-- PWA Install Prompt -->
        <div id="pwa-install-banner" class="hidden fixed bottom-24 left-4 right-4 z-[60] max-w-md mx-auto">
            <div
                class="bg-white border border-red-100 rounded-2xl shadow-2xl p-4 flex items-center justify-between gap-3 animate-bounce">
                <div class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 bg-aljalia-red rounded-xl flex items-center justify-center text-white shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-sm {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }}">
                            {{ __('Install App') }}</h4>
                        <p class="text-[10px] text-gray-500 {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }}">
                            {{ __('Inside neighborhood faster') }}</p>
                    </div>
                </div>
                <div class="flex gap-2">
                    <button id="pwa-install-btn"
                        class="bg-aljalia-red text-white text-xs font-bold px-4 py-2 rounded-lg {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} whitespace-nowrap">{{ __('Install') }}</button>
                    <button onclick="document.getElementById('pwa-install-banner').remove()" class="text-gray-400 p-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- iOS Install Tip -->
        <div id="ios-install-tip" class="hidden fixed bottom-24 left-4 right-4 z-[60] max-w-md mx-auto">
            <div class="bg-white border border-blue-100 rounded-2xl shadow-2xl p-4 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}"
                dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
                <div class="flex items-center gap-3 justify-between mb-2">
                    <button onclick="document.getElementById('ios-install-tip').remove()" class="text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                    <h4 class="font-bold text-sm {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }}">
                        {{ __('Install App') }} (iPhone)</h4>
                </div>
                <p class="text-xs text-gray-600 {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} leading-relaxed">
                    {!! __('ios_install_tip') !!}
                </p>
            </div>
        </div>

        <script>
            let deferredPrompt;
            const installBanner = document.getElementById('pwa-install-banner');
            const installBtn = document.getElementById('pwa-install-btn');
            const iosTip = document.getElementById('ios-install-tip');

            // Check if it's iOS
            const isIos = () => {
                const userAgent = window.navigator.userAgent.toLowerCase();
                return /iphone|ipad|ipod/.test(userAgent);
            }

            // Check if already in standalone mode
            const isInStandaloneMode = () => ('standalone' in window.navigator) && (window.navigator.standalone) || window.matchMedia('(display-mode: standalone)').matches;

            window.addEventListener('beforeinstallprompt', (e) => {
                // Prevent Chrome 67 and earlier from automatically showing the prompt
                e.preventDefault();
                // Stash the event so it can be triggered later.
                deferredPrompt = e;
                // Update UI to notify the user they can add to home screen
                if (!isInStandaloneMode()) {
                    installBanner.classList.remove('hidden');
                }
            });

            installBtn.addEventListener('click', (e) => {
                // hide our install banner
                installBanner.classList.add('hidden');
                // Show the prompt
                deferredPrompt.prompt();
                // Wait for the user to respond to the prompt
                deferredPrompt.userChoice.then((choiceResult) => {
                    if (choiceResult.outcome === 'accepted') {
                        console.log('User accepted the install prompt');
                    }
                    deferredPrompt = null;
                });
            });

            // Show iOS tip if on iOS and not in standalone mode
            window.addEventListener('load', () => {
                if (isIos() && !isInStandaloneMode()) {
                    setTimeout(() => {
                        iosTip.classList.remove('hidden');
                    }, 3000);
                }
            });
        </script>
</body>

</html>