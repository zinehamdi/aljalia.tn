<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Aljalia Admin | الجالية</title>
    <meta name="robots" content="noindex, nofollow">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="font-sans antialiased bg-gray-50 text-gray-900">
    <div class="min-h-screen">
        <!-- Admin Navbar -->
        <nav class="bg-gray-900 text-white shadow-xl sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center gap-6">
                        <a href="{{ route('admin.dashboard') }}" class="text-xl font-black tracking-tight">
                            <span class="text-red-500">ج</span> Admin
                        </a>
                        <div class="hidden md:flex items-center gap-1 text-sm">
                            <a href="{{ route('admin.dashboard') }}"
                                class="px-3 py-2 rounded-lg {{ request()->routeIs('admin.dashboard') ? 'bg-white/10 text-white' : 'text-gray-400 hover:text-white hover:bg-white/5' }} transition">
                                📊 {{ __('Dashboard') }}
                            </a>
                            <a href="{{ route('admin.users') }}"
                                class="px-3 py-2 rounded-lg {{ request()->routeIs('admin.users') ? 'bg-white/10 text-white' : 'text-gray-400 hover:text-white hover:bg-white/5' }} transition">
                                👥 {{ __('Users') }}
                            </a>
                            <a href="{{ route('admin.posts') }}"
                                class="px-3 py-2 rounded-lg {{ request()->routeIs('admin.posts') ? 'bg-white/10 text-white' : 'text-gray-400 hover:text-white hover:bg-white/5' }} transition">
                                📝 {{ __('Posts') }}
                            </a>
                            <a href="{{ route('admin.products') }}"
                                class="px-3 py-2 rounded-lg {{ request()->routeIs('admin.products') ? 'bg-white/10 text-white' : 'text-gray-400 hover:text-white hover:bg-white/5' }} transition">
                                🛒 {{ __('Products') }}
                            </a>
                            <a href="{{ route('admin.places') }}"
                                class="px-3 py-2 rounded-lg {{ request()->routeIs('admin.places') ? 'bg-white/10 text-white' : 'text-gray-400 hover:text-white hover:bg-white/5' }} transition">
                                📍 {{ __('Places') }}
                            </a>
                            @if(Auth::user()->isSuperAdmin())
                                <a href="{{ route('admin.admins') }}"
                                    class="px-3 py-2 rounded-lg {{ request()->routeIs('admin.admins') ? 'bg-white/10 text-white' : 'text-gray-400 hover:text-white hover:bg-white/5' }} transition">
                                    🛡️ {{ __('Admins') }}
                                </a>
                                <a href="{{ route('admin.settings') }}"
                                    class="px-3 py-2 rounded-lg {{ request()->routeIs('admin.settings') ? 'bg-white/10 text-white' : 'text-gray-400 hover:text-white hover:bg-white/5' }} transition">
                                    ⚙️ {{ __('Settings') }}
                                </a>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <a href="{{ route('dashboard') }}"
                            class="text-xs text-gray-400 hover:text-white transition px-3 py-1.5 rounded-lg border border-gray-700 hover:border-gray-500">
                            ← {{ __('Back to App') }}
                        </a>
                        <span class="text-xs text-gray-500">{{ Auth::user()->name }}</span>
                    </div>
                </div>
            </div>

            <!-- Mobile Nav -->
            <div class="md:hidden border-t border-gray-800 px-2 py-2 flex gap-1 overflow-x-auto text-xs">
                <a href="{{ route('admin.dashboard') }}"
                    class="px-3 py-1.5 rounded-lg whitespace-nowrap {{ request()->routeIs('admin.dashboard') ? 'bg-white/10 text-white' : 'text-gray-400' }}">📊</a>
                <a href="{{ route('admin.users') }}"
                    class="px-3 py-1.5 rounded-lg whitespace-nowrap {{ request()->routeIs('admin.users') ? 'bg-white/10 text-white' : 'text-gray-400' }}">👥</a>
                <a href="{{ route('admin.posts') }}"
                    class="px-3 py-1.5 rounded-lg whitespace-nowrap {{ request()->routeIs('admin.posts') ? 'bg-white/10 text-white' : 'text-gray-400' }}">📝</a>
                <a href="{{ route('admin.products') }}"
                    class="px-3 py-1.5 rounded-lg whitespace-nowrap {{ request()->routeIs('admin.products') ? 'bg-white/10 text-white' : 'text-gray-400' }}">🛒</a>
                <a href="{{ route('admin.places') }}"
                    class="px-3 py-1.5 rounded-lg whitespace-nowrap {{ request()->routeIs('admin.places') ? 'bg-white/10 text-white' : 'text-gray-400' }}">📍</a>
                @if(Auth::user()->isSuperAdmin())
                    <a href="{{ route('admin.admins') }}"
                        class="px-3 py-1.5 rounded-lg whitespace-nowrap {{ request()->routeIs('admin.admins') ? 'bg-white/10 text-white' : 'text-gray-400' }}">🛡️</a>
                    <a href="{{ route('admin.settings') }}"
                        class="px-3 py-1.5 rounded-lg whitespace-nowrap {{ request()->routeIs('admin.settings') ? 'bg-white/10 text-white' : 'text-gray-400' }}">⚙️</a>
                @endif
            </div>
        </nav>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            {{ $slot }}
        </main>
    </div>

    @livewireScripts
</body>

</html>
