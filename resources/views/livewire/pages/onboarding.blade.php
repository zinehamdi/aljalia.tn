<?php

use App\Models\City;
use App\Models\Country;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;
use Livewire\Attributes\Layout;

new class extends Component {
    public $step = 'continent'; // continent, country, city
    public $continents = [
        ['id' => 'europe', 'name' => 'Europe', 'icon' => '🌍'],
        ['id' => 'gulf', 'name' => 'Gulf & Middle East', 'icon' => '🕌'],
        ['id' => 'north_america', 'name' => 'North America', 'icon' => '🏔️'],
    ];

    public $selectedContinent = null;
    public $selectedCountry = null;
    public $selectedCity = null;

    public function selectContinent($continentId)
    {
        $this->selectedContinent = $continentId;
        $this->step = 'country';
    }

    public function selectCountry($countryId)
    {
        $this->selectedCountry = $countryId;
        $this->selectedCity = null;

        $citiesCount = City::where('country_id', $countryId)->count();
        if ($citiesCount > 0) {
            $this->step = 'city';
        } else {
            $this->saveLocation();
        }
    }

    public function selectCity($cityId)
    {
        $this->selectedCity = $cityId;
        $this->saveLocation();
    }

    public function saveLocation()
    {
        if (!$this->selectedCountry) {
            return;
        }

        $user = Auth::user();
        $user->country_id = $this->selectedCountry;
        $user->city_id = $this->selectedCity;
        $user->save();

        return redirect()->route('dashboard');
    }

    public function rendering($view)
    {
        $view->layout('layouts.guest');
    }

    public function goBack()
    {
        if ($this->step === 'city') {
            $this->step = 'country';
            $this->selectedCity = null;
        } elseif ($this->step === 'country') {
            $this->step = 'continent';
            $this->selectedCountry = null;
        }
    }

    public function with()
    {
        return [
            'countries' => $this->selectedContinent
                ? Country::where('continent', $this->selectedContinent)->where('is_active', true)->get()
                : [],
            'cities' => $this->selectedCountry
                ? City::where('country_id', $this->selectedCountry)->get()
                : [],
        ];
    }
}; ?>

<div class="py-12 w-full" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
    <div class="max-w-md mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
            <div class="p-8 text-gray-900 flex flex-col items-center">

                <!-- Progress / Back -->
                @if($step !== 'continent')
                    <div class="w-full flex {{ app()->getLocale() == 'ar' ? 'justify-start' : 'justify-end' }} mb-4">
                        <button wire:click="goBack"
                            class="text-gray-400 hover:text-aljalia-red flex items-center gap-1 text-sm {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }}">
                            <svg class="w-4 h-4 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7">
                                </path>
                            </svg>
                            {{ __('Back') }}
                        </button>
                    </div>
                @endif

                <div class="text-center mb-10">
                    <div
                        class="w-20 h-20 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-4 border-2 border-white shadow-sm">
                        <span class="text-aljalia-red font-black text-3xl">ج</span>
                    </div>
                    <h1
                        class="text-3xl font-black text-gray-900 mb-2 {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} tracking-tight">
                        {{ __('Welcome!') }}</h1>
                    <p class="text-gray-500 {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} text-md">
                        @if($step === 'continent')
                            {{ __('In which continent do you live?') }}
                        @elseif($step === 'country')
                            {{ __('Select country') }}
                        @else
                            {{ __('Select your city') }}
                        @endif
                    </p>
                </div>

                <!-- Step 1: Continent Selection -->
                @if($step === 'continent')
                    <div class="space-y-4 w-full">
                        @foreach($continents as $continent)
                            <button wire:key="continent-{{ $continent['id'] }}"
                                wire:click="selectContinent('{{ $continent['id'] }}')"
                                class="w-full flex items-center justify-between p-5 border-2 border-gray-100 rounded-2xl hover:border-aljalia-red hover:bg-red-50 transition-all group shadow-sm">
                                <div class="flex items-center gap-4">
                                    <span
                                        class="text-3xl group-hover:scale-110 transition-transform">{{ $continent['icon'] }}</span>
                                    <span
                                        class="font-bold text-gray-700 {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} text-lg">{{ __($continent['name']) }}</span>
                                </div>
                                <svg class="w-5 h-5 text-gray-300 group-hover:text-aljalia-red rtl:rotate-180" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7">
                                    </path>
                                </svg>
                            </button>
                        @endforeach
                    </div>
                @endif

                <!-- Step 2: Country Selection -->
                @if($step === 'country')
                    <div class="grid grid-cols-2 gap-4 w-full">
                        @foreach($countries as $country)
                            <button wire:key="country-{{ $country->id }}" wire:click="selectCountry({{ $country->id }})"
                                class="flex flex-col items-center justify-center p-6 border-2 border-gray-100 rounded-2xl hover:border-aljalia-red hover:bg-red-50 transition-all group shadow-sm">
                                <span
                                    class="text-5xl mb-3 group-hover:scale-110 transition-transform shadow-sm rounded-full">{{ $country->icon }}</span>
                                <span
                                    class="font-bold text-gray-800 {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }}">{{ $country->name }}</span>
                            </button>
                        @endforeach
                    </div>
                @endif

                <!-- Step 3: City Selection -->
                @if($step === 'city')
                    <div class="w-full space-y-4">
                        <div class="grid grid-cols-2 gap-3">
                            @forelse($cities as $city)
                                <button wire:key="city-{{ $city->id }}" wire:click="selectCity({{ $city->id }})"
                                    class="p-4 border-2 border-gray-100 rounded-xl text-center font-bold {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} text-gray-700 hover:border-aljalia-red hover:bg-red-50 transition-all shadow-sm">
                                    {{ $city->name }}
                                </button>
                            @empty
                                <div
                                    class="col-span-2 text-center py-4 text-gray-400 {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }}">
                                    {{ __('No cities added yet.') }}
                                </div>
                            @endforelse
                        </div>

                        <button wire:click="saveLocation"
                            class="w-full mt-6 py-4 bg-gray-100 text-gray-500 rounded-2xl font-bold {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} hover:bg-gray-200 transition-colors uppercase tracking-wider text-sm">
                            {{ __('Skip and choose later') }}
                        </button>
                    </div>
                @endif

                <div class="mt-12 text-center">
                    <p class="text-[11px] text-gray-400 {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }}">
                        {{ __('Aljalia.tn - Your guide and family abroad') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>