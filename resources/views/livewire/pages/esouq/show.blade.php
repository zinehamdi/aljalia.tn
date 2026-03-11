<?php

use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new class extends Component {
    public Product $product;

    public function mount(Product $product)
    {
        $this->product = $product->load(['user', 'city', 'country']);
    }

    public function rendering($view)
    {
        $view->layout('layouts.app');
    }

    public function toggleSold()
    {
        if ($this->product->user_id !== Auth::id()) {
            return;
        }
        $this->product->update(['is_sold' => !$this->product->is_sold]);
    }

    public function messageUser()
    {
        if ($this->product->user_id == Auth::id())
            return;

        $conversation = \App\Models\Conversation::findOrCreateBetween(
            Auth::id(),
            $this->product->user_id
        );

        return redirect()->route('messages.show', $conversation);
    }
}; ?>

<div>
    <x-slot name="header">
        <div class="flex items-center gap-3 w-full">
            <a href="{{ route('esouq.index') }}" wire:navigate class="text-white hover:text-red-200">
                <svg class="w-6 h-6 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <h2 class="font-bold text-lg {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} leading-tight truncate">
                {{ $product->name }}
            </h2>
        </div>
    </x-slot>

    <div class="pb-24">
        <!-- Product Image -->
        <div class="relative aspect-square bg-gray-100 w-full">
            @if($product->image_url)
                <img src="{{ asset('storage/' . $product->image_url) }}" class="w-full h-full object-cover">
            @else
                <div class="w-full h-full flex items-center justify-center text-6xl bg-gray-50">🛍️</div>
            @endif

            @if($product->is_sold)
                <div class="absolute inset-0 bg-black/40 flex items-center justify-center">
                    <span
                        class="bg-red-600 text-white font-black px-6 py-3 rounded-2xl text-xl {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} -rotate-12 shadow-2xl">
                        {{ __('Sold') }} ✅
                    </span>
                </div>
            @endif

            <!-- Condition Badge -->
            <div class="absolute top-4 {{ app()->getLocale() == 'ar' ? 'right-4' : 'left-4' }}">
                @if($product->condition == 'new')
                    <span class="bg-green-500 text-white text-xs font-bold px-2.5 py-1 rounded-lg shadow-lg">✨ {{ __('New') }}</span>
                @elseif($product->condition == 'like_new')
                    <span class="bg-blue-500 text-white text-xs font-bold px-2.5 py-1 rounded-lg shadow-lg">👍 {{ __('Like New') }}</span>
                @else
                    <span class="bg-amber-500 text-white text-xs font-bold px-2.5 py-1 rounded-lg shadow-lg">📦 {{ __('Used') }}</span>
                @endif
            </div>
        </div>

        <!-- Product Details -->
        <div class="px-5 py-5 bg-white" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
            <!-- Price -->
            <div class="flex items-center justify-between mb-4">
                @if($product->price)
                    <span class="text-2xl font-black text-aljalia-red">
                        {{ $product->price }} <span class="text-lg text-gray-400">{{ $product->currency }}</span>
                    </span>
                @else
                    <span class="text-lg font-bold text-gray-500 {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }}">
                        {{ __('Negotiable') }} 🤝
                    </span>
                @endif
            </div>

            <h1 class="text-xl font-bold text-gray-900 {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} mb-3 leading-snug">
                {{ $product->name }}
            </h1>

            <div class="text-gray-700 {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} text-md leading-relaxed whitespace-pre-line mb-6">
                {{ $product->description }}
            </div>

            <!-- Seller Info -->
            <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100 mb-4">
                <div class="flex items-center gap-3">
                    @if($product->user->avatar_url)
                        <img src="{{ asset('storage/' . $product->user->avatar_url) }}"
                            class="w-12 h-12 rounded-full object-cover border-2 border-white shadow-sm shrink-0">
                    @else
                        <div
                            class="w-12 h-12 rounded-full bg-gray-200 text-gray-600 flex items-center justify-center font-bold text-lg shrink-0">
                            {{ mb_substr($product->user->name, 0, 1) }}
                        </div>
                    @endif
                    <div>
                        <h4 class="font-bold text-gray-900 {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }}">
                            {{ $product->user->name }}</h4>
                        <p class="text-xs text-gray-500">
                            @if($product->city) {{ $product->city->name }}, @endif {{ $product->country->name }}
                            • {{ $product->created_at->diffForHumans() }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="px-5 py-3 bg-white border-t border-gray-100 sticky bottom-16 z-40">
            @if($product->user_id == Auth::id())
                <button wire:click="toggleSold"
                    class="w-full font-bold py-3 px-4 rounded-xl shadow-md transition-colors {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} text-lg flex justify-center items-center gap-2 {{ $product->is_sold ? 'bg-gray-200 text-gray-700 hover:bg-gray-300' : 'bg-green-600 text-white hover:bg-green-700' }}">
                    @if($product->is_sold)
                        {{ __('Mark as Available') }}
                    @else
                        {{ __('Mark as Sold') }} ✅
                    @endif
                </button>
            @else
                @if(!$product->is_sold)
                    <button wire:click="messageUser"
                        class="w-full bg-aljalia-red text-white font-bold py-3 px-4 rounded-xl shadow-md hover:bg-red-800 transition-colors {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} text-lg flex justify-center items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                            </path>
                        </svg>
                        {{ __('Contact Seller') }}
                    </button>
                @else
                    <div class="w-full bg-gray-100 text-gray-500 font-bold py-3 px-4 rounded-xl text-center {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} text-lg">
                        {{ __('This product has been sold') }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>
