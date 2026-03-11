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

    public $categories = [];

    #[Validate('required')]
    public $category_id;

    #[Validate('required|min:5|max:100')]
    public $title;

    #[Validate('required|min:10')]
    public $content;

    #[Validate('required')]
    public $type = 'discussion';

    #[Validate('nullable|image|max:5120')] // Max 5MB
    public $image;

    public function mount()
    {
        $this->categories = Category::where('is_active', true)->orderBy('order')->get();

        if (request()->has('category_id')) {
            $this->category_id = request()->query('category_id');
        } else {
            $this->category_id = $this->categories->first()->id ?? null;
        }
    }

    public function rendering($view)
    {
        $view->layout('layouts.app');
    }

    public function createPost()
    {
        $this->validate();

        $user = Auth::user();
        $imageUrl = null;

        if ($this->image) {
            $moderator = app(ImageModerator::class);
            // Content Moderation check
            if ($moderator && !$moderator->isSafe($this->image->getRealPath())) {
                $this->addError('image', 'هذه الصورة تحتوي على محتوى غير لائق. يرجى احترام القوانين.');
                return;
            }

            $filename = hexdec(uniqid()) . '.jpg';
            $path = storage_path('app/public/posts/' . $filename);

            // Ensure directory exists
            if (!file_exists(storage_path('app/public/posts'))) {
                mkdir(storage_path('app/public/posts'), 0755, true);
            }

            // Optimize Image with Intervention v3
            $manager = new ImageManager(new Driver());
            $img = $manager->read($this->image->getRealPath());

            // Resize if wider than 1200px
            $img->scale(width: 1200);

            // Save as JPEG with 75% quality
            $img->toJpeg(75)->save($path);

            $imageUrl = 'posts/' . $filename;
        }

        $post = Post::create([
            'user_id' => $user->id,
            'category_id' => $this->category_id,
            'country_id' => $user->country_id,
            'city_id' => $user->city_id,
            'title' => $this->title,
            'content' => $this->content,
            'type' => $this->type,
            'image_url' => $imageUrl,
        ]);

        $category = Category::find($this->category_id);

        if ($category) {
            return redirect()->route('category.show', $category->slug);
        }

        return redirect()->route('dashboard');
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
            <h2 class="font-bold text-lg {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} leading-tight">
                {{ __('Write new post') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-6 px-4">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <form wire:submit.prevent="createPost" class="space-y-5">

                <div>
                    <label
                        class="block text-sm font-bold text-gray-700 {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} mb-1">{{ __('Select Category') }}</label>
                    <select wire:model="category_id"
                        class="w-full rounded-xl border-gray-300 shadow-sm focus:border-aljalia-red focus:ring focus:ring-red-200 focus:ring-opacity-50 {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} text-sm px-4 py-3 bg-gray-50">
                        <option value="">{{ __('Select to theme...') }}</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ __($category->name) }}</option>
                        @endforeach
                    </select>
                    @error('category_id') <span
                        class="text-xs text-red-500 mt-1 {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }}">{{ __('Please select category') }}</span>
                    @enderror
                </div>

                <div>
                    <label
                        class="block text-sm font-bold text-gray-700 {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} mb-1">{{ __('Post Type') }}</label>
                    <div class="grid grid-cols-3 gap-2">
                        <label class="cursor-pointer">
                            <input type="radio" wire:model="type" value="discussion" class="peer sr-only">
                            <div
                                class="text-center px-2 py-2 text-xs rounded-lg border-2 peer-checked:border-aljalia-red peer-checked:bg-red-50 peer-checked:text-aljalia-red font-bold {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} transition-all text-gray-500 border-gray-200">
                                {{ __('Discussion') }}
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" wire:model="type" value="guide" class="peer sr-only">
                            <div
                                class="text-center px-2 py-2 text-xs rounded-lg border-2 peer-checked:border-blue-500 peer-checked:bg-blue-50 peer-checked:text-blue-600 font-bold {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} transition-all text-gray-500 border-gray-200">
                                {{ __('Guide') }}
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" wire:model="type" value="help" class="peer sr-only">
                            <div
                                class="text-center px-2 py-2 text-xs rounded-lg border-2 peer-checked:border-amber-500 peer-checked:bg-amber-50 peer-checked:text-amber-600 font-bold {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} transition-all text-gray-500 border-gray-200">
                                {{ __('Help') }}
                            </div>
                        </label>
                    </div>
                </div>

                <div>
                    <label
                        class="block text-sm font-bold text-gray-700 {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} mb-1">{{ __('Title') }}</label>
                    <input type="text" wire:model="title" placeholder="{{ __('What do you want to talk about?') }}"
                        class="w-full rounded-xl border-gray-300 shadow-sm focus:border-aljalia-red focus:ring focus:ring-red-200 focus:ring-opacity-50 {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} text-sm px-4 py-3 bg-gray-50 text-gray-900 placeholder:text-gray-400">
                    @error('title') <span
                        class="text-xs text-red-500 mt-1 {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }}">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label
                        class="block text-sm font-bold text-gray-700 {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} mb-1">{{ __('Details') }}</label>
                    <textarea wire:model="content" rows="6"
                        placeholder="{{ __('Write details here. Be clear and help your community...') }}"
                        class="w-full rounded-xl border-gray-300 shadow-sm focus:border-aljalia-red focus:ring focus:ring-red-200 focus:ring-opacity-50 {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} text-sm px-4 py-3 bg-gray-50 text-gray-900 placeholder:text-gray-400 resize-none"></textarea>
                    @error('content') <span
                        class="text-xs text-red-500 mt-1 {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }}">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label
                        class="block text-sm font-bold text-gray-700 {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} mb-2">{{ __('Add Image (Optional)') }}</label>
                    <div class="relative group">
                        @if ($image)
                            <div
                                class="mb-3 relative rounded-2xl overflow-hidden border-2 border-dashed border-red-200 aspect-video">
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
                                    <svg class="w-8 h-8 mb-3 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0l-4 4m4-4v12"></path>
                                    </svg>
                                    <p
                                        class="mb-2 text-sm text-gray-500 {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }}">
                                        <span class="font-bold">{{ __('Click to add image') }}</span>
                                    </p>
                                    <p class="text-xs text-gray-400 {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }}">
                                        PNG, JPG {{ __('up to 5MB') }}</p>
                                </div>
                                <input type="file" wire:model="image" class="hidden" accept="image/*" />
                            </label>
                        @endif
                    </div>
                    <div wire:loading wire:target="image"
                        class="mt-2 text-[10px] text-aljalia-red {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} animate-pulse">
                        {{ __('Wait a moment...') }}</div>
                    @error('image') <span class="text-xs text-red-500 mt-1 font-arabic">{{ $message }}</span> @enderror
                </div>

                <div class="pt-4">
                    <button type="submit"
                        class="w-full bg-aljalia-red text-white font-bold py-3 px-4 rounded-xl shadow-md hover:bg-red-800 transition-colors {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} text-lg flex justify-center items-center gap-2">
                        <span wire:loading.remove wire:target="createPost">{{ __('Post Now') }}</span>
                        <span wire:loading wire:target="createPost">{{ __('Wait a moment...') }}</span>
                        <svg wire:loading.remove wire:target="createPost" class="w-5 h-5" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                        </svg>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>