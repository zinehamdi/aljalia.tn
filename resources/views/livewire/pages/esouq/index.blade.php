<?php

use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public $currentCityOnly = false;
    public $showSoldItems = false;

    public function toggleFilter()
    {
        $this->currentCityOnly = !$this->currentCityOnly;
        $this->resetPage();
    }

    public function toggleSold()
    {
        $this->showSoldItems = !$this->showSoldItems;
        $this->resetPage();
    }

    public function rendering($view)
    {
        $view->layout('layouts.app');
    }

    public function with()
    {
        $user = Auth::user();

        $query = Product::where('country_id', $user->country_id)
            ->with(['user', 'city'])
            ->latest();

        if ($this->currentCityOnly && $user->city_id) {
            $query->where('city_id', $user->city_id);
        }

        if (!$this->showSoldItems) {
            $query->where('is_sold', false);
        }

        return [
            'products' => $query->paginate(12),
            'user' => $user,
        ];
    }
}; ?>

<div>
    <x-slot name="header">
        <div class="flex items-center gap-3 w-full">
            <a href="{{ route('dashboard') }}" wire:navigate class="text-white hover:text-red-200 shrink-0">
                <svg class="w-6 h-6 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <div class="flex-1 min-w-0 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">
                <h2
                    class="font-bold text-lg {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} leading-tight truncate">
                    {{ __('E-Souq') }} 🛒
                </h2>
                <p class="text-xs text-red-100 {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} truncate">
                    {{ __('Buy and sell between Tunisians') }}
                </p>
            </div>
            <div class="shrink-0">
                <a href="{{ route('esouq.create') }}" wire:navigate
                    class="bg-white text-aljalia-red p-2 rounded-full shadow flex items-center justify-center transition-transform active:scale-90">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4">
                        </path>
                    </svg>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-4 px-4">
        <!-- Filters -->
        <div class="flex gap-2 mb-4" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
            @if(Auth::user()->city_id)
                <button wire:click="toggleFilter"
                    class="text-xs px-3 py-1.5 rounded-full {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} transition-colors border {{ $currentCityOnly ? 'bg-aljalia-red text-white border-aljalia-red' : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50' }}">
                    @if($currentCityOnly) {{ __('All country') }} @else {{ __('My city only') }} @endif
                </button>
            @endif
        </div>

        <!-- Products Grid -->
        <div class="grid grid-cols-2 gap-3">
            @forelse($products as $product)
                <a wire:key="product-{{ $product->id }}" href="{{ route('esouq.show', $product) }}" wire:navigate
                    class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden active:scale-[0.97] transition-transform {{ $product->is_sold ? 'opacity-60' : '' }}">

                    <!-- Product Image -->
                    <div class="relative aspect-square bg-gray-100">
                        @if($product->image_url)
                            <img src="{{ asset('storage/' . $product->image_url) }}"
                                class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-4xl">🛍️</div>
                        @endif

                        @if($product->is_sold)
                            <div
                                class="absolute inset-0 bg-black/40 flex items-center justify-center">
                                <span
                                    class="bg-red-600 text-white font-bold px-3 py-1 rounded-lg text-xs {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} -rotate-12 shadow-lg">
                                    {{ __('Sold') }} ✅
                                </span>
                            </div>
                        @endif

                        <!-- Condition Badge -->
                        <div class="absolute top-2 {{ app()->getLocale() == 'ar' ? 'right-2' : 'left-2' }}">
                            @if($product->condition == 'new')
                                <span class="bg-green-500 text-white text-[9px] font-bold px-1.5 py-0.5 rounded-md shadow">{{ __('New') }}</span>
                            @elseif($product->condition == 'like_new')
                                <span class="bg-blue-500 text-white text-[9px] font-bold px-1.5 py-0.5 rounded-md shadow">{{ __('Like New') }}</span>
                            @else
                                <span class="bg-amber-500 text-white text-[9px] font-bold px-1.5 py-0.5 rounded-md shadow">{{ __('Used') }}</span>
                            @endif
                        </div>
                    </div>

                    <!-- Product Info -->
                    <div class="p-3" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
                        <h3 class="font-bold text-sm text-gray-900 {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} line-clamp-1 leading-tight mb-1">
                            {{ $product->name }}
                        </h3>
                        <p class="text-[11px] text-gray-500 {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} line-clamp-1 mb-2">
                            {{ $product->description }}
                        </p>

                        <div class="flex items-center justify-between">
                            @if($product->price)
                                <span class="text-aljalia-red font-black text-sm">
                                    {{ $product->price }} {{ $product->currency }}
                                </span>
                            @else
                                <span class="text-gray-400 text-xs font-bold {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }}">
                                    {{ __('Negotiable') }}
                                </span>
                            @endif
                        </div>

                        <div class="flex items-center gap-1 mt-2 pt-2 border-t border-gray-50">
                            <div class="w-5 h-5 rounded-full bg-gray-200 text-gray-500 flex items-center justify-center font-bold text-[9px] shrink-0">
                                {{ mb_substr($product->user->name, 0, 1) }}
                            </div>
                            <span class="text-[10px] text-gray-400 truncate">{{ $product->user->name }}</span>
                            <span class="text-[9px] text-gray-300 mx-1">•</span>
                            <span class="text-[9px] text-gray-400">{{ $product->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                </a>
            @empty
                <div class="col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 p-8 text-center mt-4">
                    <div class="text-5xl mb-4">🛒</div>
                    <h3 class="font-bold text-gray-700 {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} mb-2">
                        {{ __('The souq is empty!') }}
                    </h3>
                    <p class="text-gray-500 text-sm {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} mb-4 opacity-80">
                        {{ __('Be the first to sell something! Tunisian products, used items... everything is welcome.') }}
                    </p>
                    <a href="{{ route('esouq.create') }}" wire:navigate
                        class="inline-block bg-aljalia-red text-white py-2 px-6 rounded-xl font-bold {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} shadow-md transition-colors hover:bg-red-800">
                        {{ __('Add Product') }}
                    </a>
                </div>
            @endforelse
        </div>

        <div class="mt-4 pb-8">
            {{ $products->links() }}
        </div>
    </div>
</div>
