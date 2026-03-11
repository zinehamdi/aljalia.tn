<?php

use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new class extends Component {
    public $categories = [];

    public function mount()
    {
        $this->categories = Category::where('is_active', true)->orderBy('order')->get();
    }

    public function rendering($view)
    {
        $view->layout('layouts.app');
    }

    public function with()
    {
        $user = Auth::user();
        return [
            'user' => $user,
            'recentPosts' => \App\Models\Post::where('country_id', $user->country_id)
                ->with(['user', 'city'])
                ->latest()
                ->take(5)
                ->get(),
        ];
    }
}; ?>

<div>
    <x-slot name="header">
        <div class="flex items-center gap-3 w-full">
            <div class="flex-shrink-0">
                @if(Auth::user()->avatar_url)
                    <img src="{{ asset('storage/' . Auth::user()->avatar_url) }}"
                        class="w-12 h-12 rounded-full border-2 border-white shadow-md object-cover">
                @else
                    <div
                        class="w-12 h-12 rounded-full bg-white text-aljalia-red shadow flex shrink-0 items-center justify-center font-bold text-lg">
                        {{ mb_substr(Auth::user()->name, 0, 1) }}
                    </div>
                @endif
            </div>
            <div>
                <h2
                    class="font-bold text-lg {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} leading-tight {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">
                    {{ __('Neighborhood') }} - {{ Auth::user()->country->name ?? 'تونس' }}
                    @if(Auth::user()->city)
                        <span class="text-sm font-normal opacity-90">({{ Auth::user()->city->name }})</span>
                    @endif
                </h2>
                <p class="text-xs text-red-100 {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">
                    {{ __('Welcome') }}, {{ explode(' ', Auth::user()->name)[0] }}!
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-6 px-4">
        <!-- Quick Call to Action -->
        <div class="bg-gradient-to-r from-red-600 to-red-800 rounded-2xl p-5 mb-8 shadow-lg text-white">
            <h3 class="font-bold text-xl mb-1 {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">
                {{ __('Have a question or want to help?') }}
            </h3>
            <p class="text-sm opacity-90 mb-4 {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">
                {{ __('Ask or share your experience') }}
            </p>
            <a href="{{ route('posts.create') }}" wire:navigate
                class="bg-white text-aljalia-red font-bold px-4 py-2 rounded-lg text-sm w-full {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} shadow hover:bg-gray-50 flex justify-center items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                {{ __('Write new post') }}
            </a>
        </div>

        <!-- The Grid (الحومة) -->
        <h3
            class="text-gray-700 font-bold text-lg mb-4 {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} px-1 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">
            {{ __("What's happening in the neighborhood?") }}
        </h3>

        <div class="grid grid-cols-2 gap-4">
            @foreach($categories as $category)
                <a wire:key="category-{{ $category->id }}" href="{{ route('category.show', $category->slug) }}"
                    wire:navigate
                    class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex flex-col items-center text-center hover:border-aljalia-red transition relative overflow-hidden group">
                    <!-- Decorative Circle -->
                    <div
                        class="absolute -right-4 -top-4 w-12 h-12 bg-red-50 rounded-full group-hover:bg-red-100 transition">
                    </div>

                    <div
                        class="w-12 h-12 mb-3 bg-red-50 rounded-full flex items-center justify-center text-aljalia-red border border-red-100 group-hover:scale-110 transition z-10">
                        @if($category->icon == 'utensils')
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                                </path>
                            </svg>
                        @elseif($category->icon == 'users')
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                </path>
                            </svg>
                        @elseif($category->icon == 'coffee')
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0">
                                </path>
                            </svg>
                        @elseif($category->icon == 'file-text')
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
@elseif($category->icon == 'car')
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 17H4a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v7a2 2 0 01-2 2h-1" />
                            </svg>
                        @elseif($category->icon == 'life-buoy')
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z">
                                </path>
                            </svg>
                        @elseif($category->icon == 'shopping-cart')
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z">
                                </path>
                            </svg>
                        @endif
                    </div>

                    <h4
                        class="font-bold text-gray-800 text-[13px] {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} z-10 leading-snug">
                        {{ __($category->name) }}
                    </h4>
                </a>
            @endforeach
        </div>

        <!-- Recent Posts Section -->
        <div class="mt-8">
            <h3
                class="text-gray-700 font-bold text-lg mb-4 {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} px-1 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">
                {{ __("Recent in the Neighborhood") }}
            </h3>

            <div class="space-y-4">
                @foreach($recentPosts as $post)
                    <a wire:key="recent-post-{{ $post->id }}" href="{{ route('posts.show', $post) }}" wire:navigate
                        class="block bg-white rounded-xl shadow-sm border border-gray-100 p-4 active:scale-95 transition-transform {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}"
                        dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
                        <div class="flex justify-between items-start mb-3">
                            <div class="flex items-center gap-2">
                                @if($post->user->avatar_url)
                                    <img src="{{ asset('storage/' . $post->user->avatar_url) }}"
                                        class="w-10 h-10 rounded-full object-cover border border-gray-100 shadow-sm shrink-0">
                                @else
                                    <div
                                        class="w-10 h-10 rounded-full bg-gray-100 text-gray-400 flex items-center justify-center font-bold text-sm shrink-0 border border-gray-100">
                                        {{ mb_substr($post->user->name, 0, 1) }}
                                    </div>
                                @endif
                                <div class="{{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">
                                    <h4 class="font-bold text-sm text-gray-900 {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} leading-tight">
                                        {{ $post->user->name }}</h4>
                                    <span class="text-[10px] text-gray-500">{{ $post->created_at->diffForHumans() }}
                                        @if($post->city) • {{ $post->city->name }} @endif</span>
                                </div>
                            </div>
                            <span
                                class="bg-gray-100 text-gray-600 text-[10px] font-bold px-2 py-0.5 rounded uppercase tracking-wider {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }}">
                                {{ __(ucfirst($post->type)) }}
                            </span>
                        </div>

                        <h3 class="font-bold text-gray-800 {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} mb-1 leading-tight text-lg">{{ $post->title }}</h3>
                        
                        @if($post->image_url)
                            <div class="my-3 rounded-xl overflow-hidden border border-gray-50 h-32 w-full">
                                <img src="{{ asset('storage/' . $post->image_url) }}" class="w-full h-full object-cover">
                            </div>
                        @endif

                        <p class="text-gray-600 text-sm {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} line-clamp-2 leading-relaxed opacity-90">
                            {{ $post->content }}
                        </p>
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Safety Warning -->
        <div class="mt-8 bg-blue-50 border border-blue-100 rounded-xl p-4 flex items-start gap-3">
            <div class="text-blue-500 mt-1">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div>
                <h4 class="font-bold text-blue-900 text-sm {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">{{ __('Always take care!') }}</h4>
                <p class="text-xs text-blue-700 mt-1 {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} leading-relaxed {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">
                    {{ __('The advice here comes from the experiences of Tunisians and is not necessarily official legal information. Always check the consulate website for more confirmation!') }}
                </p>
            </div>
        </div>
    </div>
</div>