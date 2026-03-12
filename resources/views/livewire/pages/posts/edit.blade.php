<?php

use App\Models\Category;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;
use Livewire\Attributes\Validate;
use Livewire\WithFileUploads;
use App\Services\ImageModerator;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

new class extends Component {
    use WithFileUploads;

    public Post $post;
    public $categories = [];

    #[Validate('required')]
    public $category_id;

    #[Validate('required|min:5|max:100')]
    public $title;

    #[Validate('required|min:10')]
    public $content;

    #[Validate('required')]
    public $type;

    #[Validate('nullable|image|max:5120')]
    public $newImage;

    public $currentImage;

    public function mount(Post $post)
    {
        // Only the owner can edit
        if ($post->user_id !== Auth::id()) {
            abort(403);
        }

        $this->post = $post;
        $this->category_id = $post->category_id;
        $this->title = $post->title;
        $this->content = $post->content;
        $this->type = $post->type;
        $this->currentImage = $post->image_url;

        $this->categories = Category::where('is_active', true)
            ->where('slug', '!=', 'e-souq')
            ->orderBy('order')
            ->get();
    }

    public function rendering($view)
    {
        $view->layout('layouts.app');
    }

    public function removeImage()
    {
        $this->currentImage = null;
    }

    public function updatePost()
    {
        $this->validate();

        $imageUrl = $this->currentImage;

        if ($this->newImage) {
            $moderator = app(ImageModerator::class);
            if ($moderator && !$moderator->isSafe($this->newImage->getRealPath())) {
                $this->addError('newImage', __('This image contains inappropriate content. Please respect the laws.'));
                return;
            }

            $filename = hexdec(uniqid()) . '.jpg';
            $path = storage_path('app/public/posts/' . $filename);

            if (!file_exists(storage_path('app/public/posts'))) {
                mkdir(storage_path('app/public/posts'), 0755, true);
            }

            $manager = new ImageManager(new Driver());
            $img = $manager->read($this->newImage->getRealPath());
            $img->scale(width: 1200);
            $img->toJpeg(75)->save($path);

            $imageUrl = 'posts/' . $filename;
        }

        $this->post->update([
            'category_id' => $this->category_id,
            'title' => $this->title,
            'content' => $this->content,
            'type' => $this->type,
            'image_url' => $imageUrl,
        ]);

        return redirect()->route('posts.show', $this->post);
    }
}; ?>

<div dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
    <x-slot name="header">
        <div class="flex items-center gap-3 w-full">
            <a href="{{ route('posts.show', $post) }}" wire:navigate class="text-white hover:text-red-200">
                <svg class="w-6 h-6 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <h2 class="font-bold text-lg {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} leading-tight">
                ✏️ {{ __('Edit Post') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-6 px-4 pb-24">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <form wire:submit.prevent="updatePost" class="space-y-5">

                <!-- Category -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} mb-1">{{ __('Category') }}</label>
                    <select wire:model="category_id"
                        class="w-full rounded-xl border-gray-300 shadow-sm focus:border-aljalia-red focus:ring focus:ring-red-200 focus:ring-opacity-50 {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} text-sm px-4 py-3 bg-gray-50 text-gray-900">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ __($cat->name) }}</option>
                        @endforeach
                    </select>
                    @error('category_id') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Title -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} mb-1">{{ __('Title') }}</label>
                    <input type="text" wire:model="title"
                        class="w-full rounded-xl border-gray-300 shadow-sm focus:border-aljalia-red focus:ring focus:ring-red-200 focus:ring-opacity-50 {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} text-sm px-4 py-3 bg-gray-50 text-gray-900">
                    @error('title') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Type -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} mb-2">{{ __('Post type') }}</label>
                    <div class="grid grid-cols-3 gap-2">
                        <label class="cursor-pointer">
                            <input type="radio" wire:model="type" value="discussion" class="peer sr-only">
                            <div class="text-center px-2 py-3 text-xs rounded-xl border-2 peer-checked:border-aljalia-red peer-checked:bg-red-50 peer-checked:text-aljalia-red font-bold {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} transition-all text-gray-500 border-gray-200">
                                💬 {{ __('Discussion') }}
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" wire:model="type" value="guide" class="peer sr-only">
                            <div class="text-center px-2 py-3 text-xs rounded-xl border-2 peer-checked:border-blue-500 peer-checked:bg-blue-50 peer-checked:text-blue-600 font-bold {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} transition-all text-gray-500 border-gray-200">
                                📘 {{ __('Guide') }}
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" wire:model="type" value="help" class="peer sr-only">
                            <div class="text-center px-2 py-3 text-xs rounded-xl border-2 peer-checked:border-amber-500 peer-checked:bg-amber-50 peer-checked:text-amber-600 font-bold {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} transition-all text-gray-500 border-gray-200">
                                🆘 {{ __('Help') }}
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Content -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} mb-1">{{ __('Content') }}</label>
                    <textarea wire:model="content" rows="6"
                        class="w-full rounded-xl border-gray-300 shadow-sm focus:border-aljalia-red focus:ring focus:ring-red-200 focus:ring-opacity-50 {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} text-sm px-4 py-3 bg-gray-50 text-gray-900 resize-none"></textarea>
                    @error('content') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Image -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} mb-2">{{ __('Image') }}</label>

                    @if($currentImage && !$newImage)
                        <div class="relative rounded-2xl overflow-hidden border border-gray-200 mb-3 max-w-[250px]">
                            <img src="{{ asset('storage/' . $currentImage) }}" class="w-full object-cover">
                            <button type="button" wire:click="removeImage"
                                class="absolute top-2 right-2 bg-red-600 text-white rounded-full p-1 shadow-lg hover:bg-red-700">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    @elseif($newImage)
                        <div class="relative rounded-2xl overflow-hidden border-2 border-dashed border-green-200 mb-3 max-w-[250px]">
                            <img src="{{ $newImage->temporaryUrl() }}" class="w-full object-cover">
                            <button type="button" wire:click="$set('newImage', null)"
                                class="absolute top-2 right-2 bg-red-600 text-white rounded-full p-1 shadow-lg hover:bg-red-700">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    @else
                        <label class="flex flex-col items-center justify-center w-full h-24 border-2 border-dashed border-gray-300 rounded-2xl cursor-pointer bg-gray-50 hover:bg-gray-100 transition-colors">
                            <span class="text-2xl mb-1">📸</span>
                            <p class="text-xs text-gray-500 {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }}">{{ __('Add image (optional)') }}</p>
                            <input type="file" wire:model="newImage" class="hidden" accept="image/*" />
                        </label>
                    @endif

                    <div wire:loading wire:target="newImage" class="mt-2 text-[10px] text-aljalia-red animate-pulse">
                        {{ __('Wait a moment...') }}
                    </div>
                    @error('newImage') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Submit -->
                <div class="pt-2">
                    <button type="submit"
                        class="w-full bg-aljalia-red text-white font-bold py-3 px-4 rounded-xl shadow-md hover:bg-red-800 transition-colors {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} text-lg flex justify-center items-center gap-2">
                        <span wire:loading.remove wire:target="updatePost">{{ __('Save Changes') }}</span>
                        <span wire:loading wire:target="updatePost">{{ __('Wait a moment...') }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
