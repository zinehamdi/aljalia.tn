<?php

use App\Models\Place;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new class extends Component {
    public $currentCityOnly = false;
    public $filterType = ''; // empty means all

    public function toggleFilter()
    {
        $this->currentCityOnly = !$this->currentCityOnly;
    }

    public function setType($type)
    {
        $this->filterType = $type;
    }

    public function rendering($view)
    {
        $view->layout('layouts.app');
    }

    public function with()
    {
        $user = Auth::user();

        $query = Place::where('country_id', $user->country_id)
            ->with(['city', 'user'])
            ->withCount([
                'votes as score' => function ($query) {
                    $query->select(\Illuminate\Support\Facades\DB::raw('sum(value)'));
                }
            ])
            ->orderByDesc('score')
            ->latest();

        if ($this->currentCityOnly && $user->city_id) {
            $query->where('city_id', $user->city_id);
        }

        if ($this->filterType) {
            $query->where('type', $this->filterType);
        }

        return [
            'places' => $query->get(),
            'user' => $user,
        ];
    }
}; ?>

<div>
    <x-slot name="header">
        <div class="flex items-center gap-3 w-full">
            <div class="{{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">
                <h2 class="font-bold text-xl {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} leading-tight">
                    {{ __('Tunisian Shops') }}
                </h2>
                <p class="text-xs text-red-100 {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }}">
                    {{ Auth::user()->country->name }} @if(Auth::user()->city) - {{ Auth::user()->city->name }} @endif
                </p>
            </div>
            <div class="{{ app()->getLocale() == 'ar' ? 'mr-auto' : 'ml-auto' }}">
                <a href="{{ route('places.create') }}" wire:navigate
                    class="bg-white text-aljalia-red py-1.5 px-3 rounded-xl shadow flex items-center justify-center font-bold text-sm {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} gap-1 transition-transform active:scale-90">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    {{ __('Add Place') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-4 px-4 pb-8">
        <!-- Filters -->
         <div class="mb-4 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
            <h3 class="font-bold text-gray-800 {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} mb-3">{{ __('What are you looking for?') }}</h3>

            <div class="flex gap-2 mb-4 overflow-x-auto pb-2 -mx-4 px-4 snap-x hide-scrollbar">
                <button wire:click="setType('')"
                    class="snap-start shrink-0 px-4 py-2 rounded-full font-bold {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} text-sm transition-colors border {{ $filterType == '' ? 'bg-aljalia-red text-white border-aljalia-red shadow-md' : 'bg-white text-gray-600 border-gray-200 hover:border-aljalia-red hover:text-aljalia-red' }}">
                    {{ __('All') }}
                </button>
                <button wire:click="setType('restaurant')"
                    class="snap-start shrink-0 px-4 py-2 rounded-full font-bold {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} text-sm transition-colors border {{ $filterType == 'restaurant' ? 'bg-orange-500 text-white border-orange-500 shadow-md' : 'bg-white text-gray-600 border-gray-200 hover:border-orange-500 hover:text-orange-500' }} flex items-center gap-1">
                    🍽️ {{ __('Tunisian Restaurant') }}
                </button>
                <button wire:click="setType('cafe')"
                    class="snap-start shrink-0 px-4 py-2 rounded-full font-bold {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} text-sm transition-colors border {{ $filterType == 'cafe' ? 'bg-amber-600 text-white border-amber-600 shadow-md' : 'bg-white text-gray-600 border-gray-200 hover:border-amber-600 hover:text-amber-600' }} flex items-center gap-1">
                    ☕ {{ __('Cafe & Shisha') }}
                </button>
                <button wire:click="setType('shop')"
                    class="snap-start shrink-0 px-4 py-2 rounded-full font-bold {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} text-sm transition-colors border {{ $filterType == 'shop' ? 'bg-blue-500 text-white border-blue-500 shadow-md' : 'bg-white text-gray-600 border-gray-200 hover:border-blue-500 hover:text-blue-500' }} flex items-center gap-1">
                    🛒 {{ __('Tunisian Grocery') }}
                </button>
                <button wire:click="setType('service')"
                    class="snap-start shrink-0 px-4 py-2 rounded-full font-bold {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} text-sm transition-colors border {{ $filterType == 'service' ? 'bg-purple-500 text-white border-purple-500 shadow-md' : 'bg-white text-gray-600 border-gray-200 hover:border-purple-500 hover:text-purple-500' }} flex items-center gap-1">
                    💼 {{ __('Pro / Service') }}
                </button>
            </div>

            @if(Auth::user()->city_id)
                <div class="flex justify-between items-center bg-gray-100 p-2 rounded-xl">
                    <div class="text-xs font-bold text-gray-600 {{ app()->getLocale() == 'ar' ? 'font-arabic mr-2' : 'ml-2' }}">
                        {{ __('Nearest Tunisian Place to You') }}
                    </div>
                    <button wire:click="toggleFilter"
                        class="text-xs px-3 py-1 rounded-lg {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} transition-colors border {{ $currentCityOnly ? 'bg-aljalia-red text-white border-aljalia-red' : 'bg-white text-gray-600 border-gray-300' }}">
                        @if($currentCityOnly) {{ __('All country places') }} @else {{ Auth::user()->city->name }} @endif
                    </button>
                </div>
            @endif
        </div>

        <!-- Places List -->
        <div class="space-y-4 pt-2 mt-4 border-t border-gray-200">
            @forelse($places as $place)
                <div wire:key="place-{{ $place->id }}"
                    class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden active:scale-[0.98] transition-transform {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}"
                    dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
                    <div class="p-4 flex gap-3">
                        <div class="w-14 h-14 rounded-xl flex items-center justify-center shrink-0 border border-gray-100 shadow-sm
                                     {{ $place->type == 'restaurant' ? 'bg-orange-50 text-orange-500' : '' }}
                                     {{ $place->type == 'cafe' ? 'bg-amber-50 text-amber-600' : '' }}
                                     {{ $place->type == 'shop' ? 'bg-blue-50 text-blue-500' : '' }}
                                     {{ $place->type == 'service' ? 'bg-purple-50 text-purple-500' : '' }}
                                     text-3xl shrink-0">
                            @if($place->type == 'restaurant') 🍽️
                            @elseif($place->type == 'cafe') ☕
                            @elseif($place->type == 'shop') 🛒
                            @elseif($place->type == 'service') 💼
                            @endif
                        </div>

                        <div class="flex-1">
                            <h3 class="font-bold text-gray-900 {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} text-lg leading-tight mb-1">{{ $place->name }}
                            </h3>
                            <p class="text-xs text-gray-500 {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} mb-1 flex items-center gap-1">
                                <svg class="w-3 h-3 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                    </path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                {{ $place->city ? $place->city->name : $place->country->name }} @if($place->address) -
                                {{ Str::limit($place->address, 30) }} @endif
                            </p>
                            @if($place->description)
                                <p class="text-sm text-gray-600 {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} line-clamp-2 leading-tight">
                                    {{ $place->description }}
                                </p>
                            @endif
                            <div class="mt-2 text-[10px] text-gray-400 {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }}">
                                {{ __('Added by') }}: {{ explode(' ', $place->user->name)[0] }}
                            </div>
                        </div>

                        <!-- Rating/Votes -->
                        <div class="flex flex-col items-center justify-center shrink-0 w-12 {{ app()->getLocale() == 'ar' ? 'border-r pr-2' : 'border-l pl-2' }} border-gray-100">
                            <span class="text-[9px] text-gray-500 {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} mb-0.5">{{ __('Rating') }}</span>
                            <span
                                class="font-bold text-lg {{ ($place->score ?? 0) > 0 ? 'text-green-600' : (($place->score ?? 0) < 0 ? 'text-red-500' : 'text-gray-800') }}">{{ $place->score ?? 0 }}</span>
                            <span class="text-[9px] text-gray-400">{{ __('vote') }}</span>
                        </div>
                    </div>

                    @if($place->map_link)
                        <div class="bg-gray-50 px-4 py-2.5 border-t border-gray-100">
                            <a href="{{ $place->map_link }}" target="_blank"
                                class="text-blue-600 font-bold text-sm {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} flex items-center justify-center gap-1 hover:text-blue-800 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7">
                                    </path>
                                </svg>
                                {{ __('See on map') }}
                            </a>
                        </div>
                    @endif
                </div>
            @empty
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 text-center mt-6">
                    <div class="text-4xl mb-4 text-gray-300">🏪</div>
                    <h3 class="font-bold text-gray-700 {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} mb-2">{{ __('The shop is closed now!') }}</h3>
                    <p class="text-gray-500 text-sm {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} mb-4">
                        {{ __('No places registered here yet. Do you know a Tunisian restaurant or grocery? Add it and help the community!') }}</p>
                    <a href="{{ route('places.create') }}" wire:navigate
                        class="inline-block bg-aljalia-red text-white py-2 px-8 rounded-xl font-bold {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} shadow-md hover:bg-red-800 transition-colors">
                        {{ __('Be the first to add') }}
                    </a>
                </div>
            @endforelse
        </div>
    </div>
</div>