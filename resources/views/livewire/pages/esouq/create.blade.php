<?php

use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;
use Livewire\Attributes\Validate;
use Livewire\WithFileUploads;
use App\Services\ImageModerator;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

new class extends Component {
    use WithFileUploads;

    #[Validate('required|min:3|max:100')]
    public $name;

    #[Validate('required|min:10')]
    public $description;

    #[Validate('nullable|numeric|min:0')]
    public $price;

    public $currency = 'EUR';

    #[Validate('required')]
    public $condition = 'new';

    #[Validate('nullable|image|max:5120')]
    public $image;

    public function rendering($view)
    {
        $view->layout('layouts.app');
    }

    public function createProduct()
    {
        $this->validate();

        $user = Auth::user();
        $imageUrl = null;

        if ($this->image) {
            $moderator = app(ImageModerator::class);
            if ($moderator && !$moderator->isSafe($this->image->getRealPath())) {
                $this->addError('image', __('This image contains inappropriate content. Please respect the laws.'));
                return;
            }

            $filename = hexdec(uniqid()) . '.jpg';
            $path = storage_path('app/public/products/' . $filename);

            if (!file_exists(storage_path('app/public/products'))) {
                mkdir(storage_path('app/public/products'), 0755, true);
            }

            $manager = new ImageManager(new Driver());
            $img = $manager->read($this->image->getRealPath());
            $img->scale(width: 1200);
            $img->toJpeg(75)->save($path);

            $imageUrl = 'products/' . $filename;
        }

        Product::create([
            'user_id' => $user->id,
            'country_id' => $user->country_id,
            'city_id' => $user->city_id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price ?: null,
            'currency' => $this->currency,
            'condition' => $this->condition,
            'image_url' => $imageUrl,
        ]);

        return redirect()->route('esouq.index');
    }
}; ?>

<div dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
    <x-slot name="header">
        <div class="flex items-center gap-3 w-full">
            <a href="{{ route('esouq.index') }}" wire:navigate class="text-white hover:text-red-200">
                <svg class="w-6 h-6 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <h2 class="font-bold text-lg {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} leading-tight">
                {{ __('Add Product') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-6 px-4 pb-24">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <p class="text-sm text-gray-500 {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} mb-4 pt-1 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">
                {{ __('Want to sell something? Add your product and other Tunisians in your area can see it!') }}
            </p>

            <form wire:submit.prevent="createProduct" class="space-y-5 mt-2">

                <!-- Product Image -->
                <div>
                    <label
                        class="block text-sm font-bold text-gray-700 {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} mb-2">{{ __('Product Photo') }}</label>
                    <div class="relative group">
                        @if ($image)
                            <div
                                class="mb-3 relative rounded-2xl overflow-hidden border-2 border-dashed border-green-200 aspect-square max-w-[200px]">
                                <img src="{{ $image->temporaryUrl() }}" class="w-full h-full object-cover">
                                <button type="button" wire:click="$set('image', null)"
                                    class="absolute top-2 right-2 bg-red-600 text-white rounded-full p-1 shadow-lg hover:bg-red-700">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        @else
                            <label
                                class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-gray-300 rounded-2xl cursor-pointer bg-gray-50 hover:bg-gray-100 transition-colors">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    <span class="text-3xl mb-2">📸</span>
                                    <p class="mb-1 text-sm text-gray-500 {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }}">
                                        <span class="font-bold">{{ __('Add product photo') }}</span>
                                    </p>
                                    <p class="text-xs text-gray-400">PNG, JPG {{ __('up to 5MB') }}</p>
                                </div>
                                <input type="file" wire:model="image" class="hidden" accept="image/*" />
                            </label>
                        @endif
                    </div>
                    <div wire:loading wire:target="image"
                        class="mt-2 text-[10px] text-aljalia-red {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} animate-pulse">
                        {{ __('Wait a moment...') }}</div>
                    @error('image') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Product Name -->
                <div>
                    <label
                        class="block text-sm font-bold text-gray-700 {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} mb-1">{{ __('Product Name') }}</label>
                    <input type="text" wire:model="name" placeholder="{{ __('What are you selling?') }}"
                        class="w-full rounded-xl border-gray-300 shadow-sm focus:border-aljalia-red focus:ring focus:ring-red-200 focus:ring-opacity-50 {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} text-sm px-4 py-3 bg-gray-50 text-gray-900 placeholder:text-gray-400">
                    @error('name') <span
                        class="text-xs text-red-500 mt-1 {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} block">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Description -->
                <div>
                    <label
                        class="block text-sm font-bold text-gray-700 {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} mb-1">{{ __('Details') }}</label>
                    <textarea wire:model="description" rows="3"
                        placeholder="{{ __('Describe your product, condition, origin...') }}"
                        class="w-full rounded-xl border-gray-300 shadow-sm focus:border-aljalia-red focus:ring focus:ring-red-200 focus:ring-opacity-50 {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} text-sm px-4 py-3 bg-gray-50 text-gray-900 placeholder:text-gray-400 resize-none"></textarea>
                    @error('description') <span
                        class="text-xs text-red-500 mt-1 {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} block">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Condition -->
                <div>
                    <label
                        class="block text-sm font-bold text-gray-700 {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} mb-2">{{ __('Condition') }}</label>
                    <div class="grid grid-cols-3 gap-2">
                        <label class="cursor-pointer">
                            <input type="radio" wire:model="condition" value="new" class="peer sr-only">
                            <div
                                class="text-center px-2 py-3 text-xs rounded-xl border-2 peer-checked:border-green-500 peer-checked:bg-green-50 peer-checked:text-green-600 font-bold {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} transition-all text-gray-500 border-gray-200">
                                ✨ {{ __('New') }}
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" wire:model="condition" value="like_new" class="peer sr-only">
                            <div
                                class="text-center px-2 py-3 text-xs rounded-xl border-2 peer-checked:border-blue-500 peer-checked:bg-blue-50 peer-checked:text-blue-600 font-bold {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} transition-all text-gray-500 border-gray-200">
                                👍 {{ __('Like New') }}
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" wire:model="condition" value="used" class="peer sr-only">
                            <div
                                class="text-center px-2 py-3 text-xs rounded-xl border-2 peer-checked:border-amber-500 peer-checked:bg-amber-50 peer-checked:text-amber-600 font-bold {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} transition-all text-gray-500 border-gray-200">
                                📦 {{ __('Used') }}
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Price -->
                <div>
                    <label
                        class="block text-sm font-bold text-gray-700 {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} mb-1">{{ __('Price (Optional)') }}</label>
                    <p class="text-[11px] text-gray-400 {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} mb-2">{{ __('Leave empty if negotiable') }}</p>
                    <div class="flex gap-2">
                        <input type="number" wire:model="price" placeholder="0.00" step="0.01" min="0"
                            class="flex-1 rounded-xl border-gray-300 shadow-sm focus:border-aljalia-red focus:ring focus:ring-red-200 focus:ring-opacity-50 text-sm px-4 py-3 bg-gray-50 text-gray-900 placeholder:text-gray-400 text-left"
                            dir="ltr">
                        <select wire:model="currency"
                            class="rounded-xl border-gray-300 shadow-sm focus:border-aljalia-red focus:ring focus:ring-red-200 focus:ring-opacity-50 text-sm px-3 py-3 bg-gray-50 text-gray-900 w-24">
                            <option value="EUR">EUR €</option>
                            <option value="USD">USD $</option>
                            <option value="GBP">GBP £</option>
                            <option value="CAD">CAD $</option>
                            <option value="SAR">SAR ر.س</option>
                            <option value="AED">AED د.إ</option>
                            <option value="TND">TND د.ت</option>
                        </select>
                    </div>
                    @error('price') <span
                        class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <div class="pt-4">
                    <button type="submit"
                        class="w-full bg-green-600 text-white font-bold py-3 px-4 rounded-xl shadow-md hover:bg-green-700 transition-colors {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} text-lg flex justify-center items-center gap-2">
                        <span wire:loading.remove wire:target="createProduct">{{ __('Publish Product') }}</span>
                        <span wire:loading wire:target="createProduct">{{ __('Wait a moment...') }}</span>
                        <svg wire:loading.remove wire:target="createProduct" class="w-5 h-5" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z">
                            </path>
                        </svg>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
