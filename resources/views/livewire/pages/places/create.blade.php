<?php

use App\Models\Place;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;
use Livewire\Attributes\Validate;

new class extends Component {
    #[Validate('required|min:3')]
    public $name;

    #[Validate('nullable|string')]
    public $description;

    #[Validate('required')]
    public $type = 'restaurant';

    #[Validate('nullable|string')]
    public $address;

    #[Validate('nullable|url')]
    public $map_link;

    public function rendering($view)
    {
        $view->layout('layouts.app');
    }

    public function createPlace()
    {
        $this->validate();

        $user = Auth::user();

        Place::create([
            'user_id' => $user->id,
            'country_id' => $user->country_id,
            'city_id' => $user->city_id,
            'name' => $this->name,
            'description' => $this->description,
            'type' => $this->type,
            'address' => $this->address,
            'map_link' => $this->map_link,
        ]);

        return redirect()->route('places.index');
    }
}; ?>

<div>
    <x-slot name="header">
        <div class="flex items-center gap-3 w-full">
            <a href="javascript:history.back()" class="text-white hover:text-red-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
            <h2 class="font-bold text-lg font-arabic leading-tight">
                زيد بلاصة تونسية
            </h2>
        </div>
    </x-slot>

    <div class="py-6 px-4 pb-24">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <p class="text-sm text-gray-500 font-arabic mb-4 pt-1 text-right">
                تعرف مطعم، قهوة، ولا شكون يقدم خدمات لولاد لبلاد؟ زيدو هوني باش غيرك يستفاد!
            </p>

            <form wire:submit.prevent="createPlace" class="space-y-5 mt-2">

                <div>
                    <label class="block text-sm font-bold text-gray-700 font-arabic mb-2 text-right">شنوة نوعها؟</label>
                    <div class="grid grid-cols-2 gap-2">
                        <label class="cursor-pointer">
                            <input type="radio" wire:model="type" value="restaurant" class="peer sr-only">
                            <div
                                class="text-center px-2 py-3 text-xs rounded-xl border-2 peer-checked:border-orange-500 peer-checked:bg-orange-50 peer-checked:text-orange-600 font-bold font-arabic transition-all text-gray-500 border-gray-200">
                                🍽️ مطعم تونسي
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" wire:model="type" value="cafe" class="peer sr-only">
                            <div
                                class="text-center px-2 py-3 text-xs rounded-xl border-2 peer-checked:border-amber-600 peer-checked:bg-amber-50 peer-checked:text-amber-700 font-bold font-arabic transition-all text-gray-500 border-gray-200">
                                ☕ قهوة وشيشة
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" wire:model="type" value="shop" class="peer sr-only">
                            <div
                                class="text-center px-2 py-3 text-xs rounded-xl border-2 peer-checked:border-blue-500 peer-checked:bg-blue-50 peer-checked:text-blue-600 font-bold font-arabic transition-all text-gray-500 border-gray-200">
                                🛒 عطرية / مواد
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" wire:model="type" value="service" class="peer sr-only">
                            <div
                                class="text-center px-2 py-3 text-xs rounded-xl border-2 peer-checked:border-purple-500 peer-checked:bg-purple-50 peer-checked:text-purple-600 font-bold font-arabic transition-all text-gray-500 border-gray-200">
                                💼 صنيعة / خدمة
                            </div>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 font-arabic mb-1 text-right">الاسم</label>
                    <input type="text" wire:model="name" placeholder="اسم البلاصة / الشخص"
                        class="w-full rounded-xl border-gray-300 shadow-sm focus:border-aljalia-red focus:ring focus:ring-red-200 focus:ring-opacity-50 font-arabic text-sm px-4 py-3 bg-gray-50 text-gray-900 placeholder:text-gray-400 text-right"
                        dir="rtl">
                    @error('name') <span
                    class="text-xs text-red-500 mt-1 font-arabic text-right block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 font-arabic mb-1 text-right">وصف صغير
                        (اختياري)</label>
                    <textarea wire:model="description" rows="2" placeholder="شنوة يبيع؟ وقتاش يحل؟"
                        class="w-full rounded-xl border-gray-300 shadow-sm focus:border-aljalia-red focus:ring focus:ring-red-200 focus:ring-opacity-50 font-arabic text-sm px-4 py-3 bg-gray-50 text-gray-900 placeholder:text-gray-400 resize-none text-right"
                        dir="rtl"></textarea>
                    @error('description') <span
                        class="text-xs text-red-500 mt-1 font-arabic text-right block">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 font-arabic mb-1 text-right">العنوان
                        (اختياري)</label>
                    <input type="text" wire:model="address" placeholder="الشارع، المنطقة..."
                        class="w-full rounded-xl border-gray-300 shadow-sm focus:border-aljalia-red focus:ring focus:ring-red-200 focus:ring-opacity-50 font-arabic text-sm px-4 py-3 bg-gray-50 text-gray-900 placeholder:text-gray-400 text-right"
                        dir="rtl">
                    @error('address') <span
                        class="text-xs text-red-500 mt-1 font-arabic text-right block">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 font-arabic mb-1 text-right">رابط Google Maps
                        (اختياري)</label>
                    <input type="url" dir="ltr" wire:model="map_link" placeholder="https://maps.google.com/..."
                        class="w-full rounded-xl border-gray-300 shadow-sm focus:border-aljalia-red focus:ring focus:ring-red-200 focus:ring-opacity-50 font-sans text-sm px-4 py-3 bg-gray-50 text-gray-900 placeholder:text-gray-400 text-left">
                    @error('map_link') <span
                        class="text-xs text-red-500 mt-1 font-arabic text-right block">{{ $message }}</span>
                    @enderror
                </div>

                <div class="pt-4">
                    <button type="submit"
                        class="w-full bg-aljalia-red text-white font-bold py-3 px-4 rounded-xl shadow-md hover:bg-red-800 transition-colors font-arabic text-lg flex justify-center items-center gap-2">
                        <span wire:loading.remove wire:target="createPlace">سجل البلاصة</span>
                        <span wire:loading wire:target="createPlace">لحظة برك...</span>
                        <svg wire:loading.remove wire:target="createPlace" class="w-5 h-5" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                            </path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>