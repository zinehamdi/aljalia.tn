<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3 w-full">
            <div>
                <h2 class="font-bold text-xl {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} leading-tight">
                    {{ __('My Profile') }}
                </h2>
            </div>
            <div class="mr-auto">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="text-red-200 hover:text-white {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} text-sm font-bold flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                            </path>
                        </svg>
                        {{ __('Logout') }}
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
        $myPosts = $user->posts()->with(['category', 'city'])->latest()->get();
    @endphp

    <div class="py-6 px-4 pb-24" x-data="{ tab: 'posts' }">
        <!-- Profile Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 text-center mb-6">
            <div class="relative w-24 h-24 mx-auto mb-4">
                @if($user->avatar_url)
                    <img src="{{ asset('storage/' . $user->avatar_url) }}" 
                         class="w-24 h-24 rounded-full object-cover border-2 border-aljalia-red shadow-lg">
                @else
                    <div class="w-24 h-24 rounded-full bg-gradient-to-br from-aljalia-red to-red-700 text-white flex items-center justify-center font-bold text-3xl shadow-lg shadow-red-200">
                        {{ mb_substr($user->name, 0, 1) }}
                    </div>
                @endif
                
                <button @click="tab = 'settings'" class="absolute -bottom-1 -right-1 bg-white p-2 rounded-full shadow-md border border-gray-100 text-gray-600 hover:text-aljalia-red transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </button>
            </div>

            <h3 class="font-bold text-xl text-gray-900 {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }}">{{ $user->name }}</h3>
            <p class="text-sm text-gray-500 {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} mt-1">{{ $user->email }}</p>

            <div class="flex items-center justify-center gap-2 mt-4">
                @if($user->country)
                    <span class="bg-red-50 text-aljalia-red text-[11px] font-bold px-3 py-1 rounded-full border border-red-100 {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }}">
                        🇹🇳 {{ $user->country->name }}
                    </span>
                @endif
                @if($user->city)
                    <span class="bg-gray-50 text-gray-600 text-[11px] font-bold px-3 py-1 rounded-full border border-gray-200 {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }}">
                        📍 {{ $user->city->name }}
                    </span>
                @endif
            </div>

            <div class="text-[10px] text-gray-400 {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} mt-4 uppercase tracking-wider">
                {{ __('Member since') }} {{ $user->created_at->translatedFormat('F Y') }}
            </div>
        </div>

        <!-- Stats Bar -->
        <div class="grid grid-cols-3 gap-3 mb-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-3 text-center">
                <span class="text-xl font-black text-aljalia-red block leading-none">{{ $postCount }}</span>
                <p class="text-[10px] text-gray-500 {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} mt-1 uppercase">{{ __('Posts') }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-3 text-center">
                <span class="text-xl font-black text-blue-600 block leading-none">{{ $commentCount }}</span>
                <p class="text-[10px] text-gray-500 {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} mt-1 uppercase">{{ __('Comments') }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-3 text-center">
                <span class="text-xl font-black text-green-600 block leading-none">{{ $placeCount }}</span>
                <p class="text-[10px] text-gray-500 {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} mt-1 uppercase">{{ __('Places') }}</p>
            </div>
        </div>

        <!-- Tabs -->
        <div class="flex bg-gray-100 p-1 rounded-xl mb-6">
            <button @click="tab = 'posts'" 
                    :class="tab === 'posts' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500'"
                    class="flex-1 py-2 text-sm font-bold rounded-lg transition-all {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }}">
                {{ __('My posts') }}
            </button>
            <button @click="tab = 'settings'" 
                    :class="tab === 'settings' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500'"
                    class="flex-1 py-2 text-sm font-bold rounded-lg transition-all {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }}">
                {{ __('Settings') }}
            </button>
        </div>

        <!-- Tab Content: Posts -->
        <div x-show="tab === 'posts'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-4">
            @if($myPosts->isEmpty())
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-10 text-center">
                    <div class="text-4xl mb-4">📝</div>
                    <p class="text-gray-500 {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} text-sm">{{ __('You haven\'t posted anything in the neighborhood yet.') }}</p>
                    <a href="{{ route('posts.create') }}" wire:navigate class="mt-4 inline-block bg-aljalia-red text-white py-2 px-6 rounded-xl font-bold {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} shadow-sm">
                        {{ __('Write new post') }}
                    </a>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($myPosts as $post)
                        <a wire:key="post-{{ $post->id }}" href="{{ route('posts.show', $post) }}" wire:navigate
                           class="block bg-white rounded-xl shadow-sm border border-gray-100 p-4 active:scale-95 transition-transform text-right"
                           dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
                            <div class="flex justify-between items-start mb-2">
                                <span class="text-[10px] text-gray-400 font-medium">{{ $post->created_at->diffForHumans() }}</span>
                                <span class="bg-gray-100 text-gray-600 text-[10px] font-bold px-2 py-0.5 rounded uppercase tracking-wider {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }}">
                                    {{ __($post->category->name) }}
                                </span>
                            </div>
                            <h4 class="font-bold text-gray-800 {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} leading-tight">{{ $post->title }}</h4>
                            
                            @if($post->image_url)
                                <div class="mt-3 rounded-lg overflow-hidden border border-gray-50 h-24 w-full">
                                    <img src="{{ asset('storage/' . $post->image_url) }}" class="w-full h-full object-cover">
                                </div>
                            @endif
                        </a>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Tab Content: Settings -->
        <div x-show="tab === 'settings'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-4" style="display: none;">
            <div class="space-y-6">
                <!-- Edit Profile -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                    <livewire:profile.update-profile-information-form />
                </div>

                <!-- Change Country/City -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden divide-y divide-gray-100">
                    <a href="{{ route('onboarding.country') }}"
                        class="flex items-center justify-between p-5 hover:bg-gray-50 transition-colors">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-full bg-red-50 text-aljalia-red flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                    </path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-800 {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} text-sm">{{ __('Change Country & City') }}</h4>
                                <p class="text-[11px] text-gray-500 {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }}">
                                    {{ $user->country->name ?? '' }} {{ $user->city ? '- ' . $user->city->name : '' }}
                                </p>
                            </div>
                        </div>
                        <svg class="w-5 h-5 text-gray-400 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </a>
                </div>

                <!-- Security -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                    <h3 class="text-gray-800 font-bold mb-4 {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} text-right">{{ __('Security') }}</h3>
                    <livewire:profile.update-password-form />
                </div>

                <!-- Danger Zone -->
                <div class="bg-white rounded-2xl shadow-sm border border-red-100 p-5">
                    <h3 class="text-red-600 font-bold mb-4 {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} text-right">{{ __('Danger Zone') }}</h3>
                    <livewire:profile.delete-user-form />
                </div>
            </div>
        </div>
    </div>
</x-app-layout>