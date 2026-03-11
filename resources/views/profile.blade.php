<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3 w-full">
            <div>
                <h2 class="font-bold text-xl font-arabic leading-tight">
                    بروفايلي
                </h2>
            </div>
            <div class="mr-auto">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="text-red-200 hover:text-white font-arabic text-sm font-bold flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                            </path>
                        </svg>
                        خروج
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    @php
        $user = auth()->user()->load(['country', 'city']);
        $postCount = $user->posts()->count();
        $commentCount = $user->comments()->count();
        $placeCount = $user->places()->count();
    @endphp

    <div class="py-6 px-4 pb-24">
        <!-- Profile Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 text-center mb-6">
            <div
                class="w-20 h-20 rounded-full bg-gradient-to-br from-aljalia-red to-red-700 text-white flex items-center justify-center font-bold text-3xl mx-auto mb-3 shadow-lg shadow-red-200">
                {{ mb_substr($user->name, 0, 1) }}
            </div>
            <h3 class="font-bold text-xl text-gray-900 font-arabic">{{ $user->name }}</h3>
            <p class="text-sm text-gray-500 font-arabic mt-1">{{ $user->email }}</p>

            <div class="flex items-center justify-center gap-2 mt-3">
                @if($user->country)
                    <span
                        class="bg-red-50 text-aljalia-red text-xs font-bold px-3 py-1 rounded-full border border-red-100 font-arabic">
                        🇹🇳 {{ $user->country->name }}
                    </span>
                @endif
                @if($user->city)
                    <span
                        class="bg-gray-50 text-gray-600 text-xs font-bold px-3 py-1 rounded-full border border-gray-200 font-arabic">
                        📍 {{ $user->city->name }}
                    </span>
                @endif
            </div>

            <div class="text-xs text-gray-400 font-arabic mt-3">
                عضو من {{ $user->created_at->translatedFormat('F Y') }}
            </div>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-3 gap-3 mb-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center">
                <span class="text-2xl font-black text-aljalia-red">{{ $postCount }}</span>
                <p class="text-xs text-gray-500 font-arabic mt-1">بوست</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center">
                <span class="text-2xl font-black text-blue-600">{{ $commentCount }}</span>
                <p class="text-xs text-gray-500 font-arabic mt-1">تعليق</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center">
                <span class="text-2xl font-black text-green-600">{{ $placeCount }}</span>
                <p class="text-xs text-gray-500 font-arabic mt-1">بلاصة</p>
            </div>
        </div>

        <!-- Settings Section -->
        <h3 class="text-gray-700 font-bold text-lg mb-3 font-arabic px-1">الإعدادات</h3>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden divide-y divide-gray-100">
            <!-- Edit Profile -->
            <div class="p-4">
                <div class="max-w-xl">
                    <livewire:profile.update-profile-information-form />
                </div>
            </div>

            <!-- Change Country -->
            <a href="{{ route('onboarding.country') }}"
                class="flex items-center justify-between p-4 hover:bg-gray-50 transition-colors">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-green-50 text-green-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-800 font-arabic text-sm">بدل البلاد والمدينة</h4>
                        <p class="text-xs text-gray-500 font-arabic">{{ $user->country->name ?? '' }}
                            {{ $user->city ? '- ' . $user->city->name : '' }}</p>
                    </div>
                </div>
                <svg class="w-5 h-5 text-gray-400 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
        </div>

        <!-- Security Section -->
        <h3 class="text-gray-700 font-bold text-lg mb-3 mt-6 font-arabic px-1">الأمان</h3>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden divide-y divide-gray-100">
            <div class="p-4">
                <div class="max-w-xl">
                    <livewire:profile.update-password-form />
                </div>
            </div>
        </div>

        <!-- Danger Zone -->
        <h3 class="text-red-600 font-bold text-lg mb-3 mt-6 font-arabic px-1">منطقة الخطر</h3>
        <div class="bg-white rounded-2xl shadow-sm border border-red-100 overflow-hidden">
            <div class="p-4">
                <div class="max-w-xl">
                    <livewire:profile.delete-user-form />
                </div>
            </div>
        </div>
    </div>
</x-app-layout>