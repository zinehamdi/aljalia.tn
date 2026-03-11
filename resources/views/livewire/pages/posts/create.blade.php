<?php

use App\Models\Category;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;
use Livewire\Attributes\Validate;

new class extends Component {
    public $categories = [];

    #[Validate('required')]
    public $category_id;

    #[Validate('required|min:5|max:100')]
    public $title;

    #[Validate('required|min:10')]
    public $content;

    #[Validate('required')]
    public $type = 'discussion';

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
        \Illuminate\Support\Facades\Log::info('Attempting to create post', [
            'category_id' => $this->category_id,
            'title' => $this->title,
            'type' => $this->type,
        ]);

        try {
            $this->validate();
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Illuminate\Support\Facades\Log::error('Validation failed', $e->errors());
            throw $e;
        }

        $user = Auth::user();

        $post = Post::create([
            'user_id' => $user->id,
            'category_id' => $this->category_id,
            'country_id' => $user->country_id,
            'city_id' => $user->city_id,
            'title' => $this->title,
            'content' => $this->content,
            'type' => $this->type,
        ]);

        \Illuminate\Support\Facades\Log::info('Post created successfully', ['id' => $post->id]);

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
            <h2 class="font-bold text-lg font-arabic leading-tight">
                اكتب بوست جديد
            </h2>
        </div>
    </x-slot>

    <div class="py-6 px-4">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <form wire:submit.prevent="createPost" class="space-y-5">

                <div>
                    <label class="block text-sm font-bold text-gray-700 font-arabic mb-1">اختار الموضوع</label>
                    <select wire:model="category_id"
                        class="w-full rounded-xl border-gray-300 shadow-sm focus:border-aljalia-red focus:ring focus:ring-red-200 focus:ring-opacity-50 font-arabic text-sm px-4 py-3 bg-gray-50">
                        <option value="">اختار الموضوع...</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id') <span class="text-xs text-red-500 mt-1 font-arabic">يرجى اختيار الموضوع</span>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 font-arabic mb-1">نوع البوست</label>
                    <div class="grid grid-cols-3 gap-2">
                        <label class="cursor-pointer">
                            <input type="radio" wire:model="type" value="discussion" class="peer sr-only">
                            <div
                                class="text-center px-2 py-2 text-xs rounded-lg border-2 peer-checked:border-aljalia-red peer-checked:bg-red-50 peer-checked:text-aljalia-red font-bold font-arabic transition-all text-gray-500 border-gray-200">
                                نقاش / سؤال
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" wire:model="type" value="guide" class="peer sr-only">
                            <div
                                class="text-center px-2 py-2 text-xs rounded-lg border-2 peer-checked:border-blue-500 peer-checked:bg-blue-50 peer-checked:text-blue-600 font-bold font-arabic transition-all text-gray-500 border-gray-200">
                                دليل / نصيحة
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" wire:model="type" value="help" class="peer sr-only">
                            <div
                                class="text-center px-2 py-2 text-xs rounded-lg border-2 peer-checked:border-amber-500 peer-checked:bg-amber-50 peer-checked:text-amber-600 font-bold font-arabic transition-all text-gray-500 border-gray-200">
                                طلب فزعة
                            </div>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 font-arabic mb-1">العنوان</label>
                    <input type="text" wire:model="title" placeholder="على شنوة تحب تحكي؟"
                        class="w-full rounded-xl border-gray-300 shadow-sm focus:border-aljalia-red focus:ring focus:ring-red-200 focus:ring-opacity-50 font-arabic text-sm px-4 py-3 bg-gray-50 text-gray-900 placeholder:text-gray-400">
                    @error('title') <span class="text-xs text-red-500 mt-1 font-arabic">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 font-arabic mb-1">التفاصيل</label>
                    <textarea wire:model="content" rows="6" placeholder="اكتب التفاصيل هوني. خليك واضح وعاون خوتك..."
                        class="w-full rounded-xl border-gray-300 shadow-sm focus:border-aljalia-red focus:ring focus:ring-red-200 focus:ring-opacity-50 font-arabic text-sm px-4 py-3 bg-gray-50 text-gray-900 placeholder:text-gray-400 resize-none"></textarea>
                    @error('content') <span class="text-xs text-red-500 mt-1 font-arabic">{{ $message }}</span>
                    @enderror
                </div>

                <div class="pt-4">
                    <button type="submit"
                        class="w-full bg-aljalia-red text-white font-bold py-3 px-4 rounded-xl shadow-md hover:bg-red-800 transition-colors font-arabic text-lg flex justify-center items-center gap-2">
                        <span wire:loading.remove wire:target="createPost">انشر البوست</span>
                        <span wire:loading wire:target="createPost">لحظة برك...</span>
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