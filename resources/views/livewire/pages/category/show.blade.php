<?php

use App\Models\Category;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public Category $category;
    public $currentCityOnly = false;

    public function mount(Category $category)
    {
        $this->category = $category;
    }

    public function toggleFilter()
    {
        $this->currentCityOnly = !$this->currentCityOnly;
        $this->resetPage();
    }

    public function rendering($view)
    {
        $view->layout('layouts.app');
    }

    public function with()
    {
        $user = Auth::user();

        $query = Post::where('category_id', $this->category->id)
            ->where('country_id', $user->country_id)
            ->with(['user', 'city'])
            ->withCount([
                'comments',
                'votes as score' => function ($query) {
                    $query->select(\Illuminate\Support\Facades\DB::raw('sum(value)'));
                }
            ])
            ->latest();

        if ($this->currentCityOnly && $user->city_id) {
            $query->where('city_id', $user->city_id);
        }

        return [
            'posts' => $query->paginate(10),
            'user' => $user,
        ];
    }
}; ?>

<div>
    <x-slot name="header">
        <div class="flex items-center gap-3 w-full">
            <a href="{{ route('dashboard') }}" wire:navigate class="text-white hover:text-red-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
            <div>
                <h2 class="font-bold text-lg font-arabic leading-tight text-right">
                    {{ $category->name }}
                </h2>
                <p class="text-xs text-red-100 font-arabic text-right">
                    {{ Auth::user()->country->name }}
                    @if(Auth::user()->city) - {{ Auth::user()->city->name }} @endif
                </p>
            </div>
            <div class="mr-auto">
                <a href="{{ route('posts.create', ['category_id' => $category->id]) }}" wire:navigate
                    class="bg-white text-aljalia-red p-2 rounded-full shadow flex items-center justify-center transition-transform active:scale-90">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-4">
        @if(Auth::user()->city_id)
            <div
                class="px-4 mb-4 flex justify-between items-center bg-gray-50/50 p-2 rounded-xl mx-4 border border-gray-100">
                <div class="text-sm font-bold text-gray-700 font-arabic text-right">
                    @if($currentCityOnly)
                        فقط بوستات {{ Auth::user()->city->name }}
                    @else
                        بوستات كل {{ Auth::user()->country->name }}
                    @endif
                </div>
                <button wire:click="toggleFilter"
                    class="text-xs px-3 py-1.5 rounded-full font-arabic transition-colors border {{ $currentCityOnly ? 'bg-aljalia-red text-white border-aljalia-red' : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50' }}">
                    @if($currentCityOnly) كل البلاد @else مدينتي فقط @endif
                </button>
            </div>
        @endif

        <div class="space-y-3 px-4">
            @forelse($posts as $post)
                <a wire:key="post-{{ $post->id }}" href="{{ route('posts.show', $post) }}" wire:navigate
                    class="block bg-white rounded-xl shadow-sm border border-gray-100 p-4 active:scale-95 transition-transform text-right"
                    dir="rtl">
                    <div class="flex justify-between items-start mb-2">
                        <div class="flex items-center gap-2">
                            <div
                                class="w-8 h-8 rounded-full bg-gray-200 text-gray-500 flex items-center justify-center font-bold text-sm shrink-0">
                                {{ mb_substr($post->user->name, 0, 1) }}
                            </div>
                            <div class="text-right">
                                <h4 class="font-bold text-sm text-gray-900 font-arabic leading-tight">
                                    {{ $post->user->name }}</h4>
                                <span class="text-[10px] text-gray-500">{{ $post->created_at->diffForHumans() }}
                                    @if($post->city) • في {{ $post->city->name }} @endif</span>
                            </div>
                        </div>
                        @if($post->type == 'guide')
                            <span
                                class="bg-blue-100 text-blue-800 text-[10px] font-bold px-2 py-0.5 rounded uppercase tracking-wider font-arabic">دليل</span>
                        @elseif($post->type == 'help')
                            <span
                                class="bg-amber-100 text-amber-800 text-[10px] font-bold px-2 py-0.5 rounded uppercase tracking-wider font-arabic">مساعدة</span>
                        @elseif($post->type == 'esouq')
                            <span
                                class="bg-green-100 text-green-800 text-[10px] font-bold px-2 py-0.5 rounded uppercase tracking-wider font-arabic">إي-سوق</span>
                        @endif
                    </div>

                    <h3 class="font-bold text-gray-800 font-arabic mb-1 leading-tight text-lg">{{ $post->title }}</h3>
                    <p class="text-gray-600 text-sm font-arabic line-clamp-2 leading-relaxed opacity-90 mb-3">
                        {{ $post->content }}
                    </p>

                    <div class="flex gap-4 text-gray-500 pb-1 border-t border-gray-50 pt-3 mt-1">
                        <div class="flex items-center gap-1 text-xs font-bold text-aljalia-red">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5">
                                </path>
                            </svg>
                            {{ $post->score ?? 0 }}
                        </div>
                        <div class="flex items-center gap-1 text-xs">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z">
                                </path>
                            </svg>
                            {{ $post->comments_count }}
                        </div>
                    </div>
                </a>
            @empty
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 text-center mt-4">
                    <div class="text-4xl mb-4">👻</div>
                    <h3 class="font-bold text-gray-700 font-arabic mb-2">الضو مقصوص هوني...</h3>
                    <p class="text-gray-500 text-sm font-arabic mb-4">ما فمة حتى بوست في الحومة هذي حالياً. اكبس واهبطلنا
                        بوست!</p>
                    <a href="{{ route('posts.create', ['category_id' => $category->id]) }}" wire:navigate
                        class="inline-block bg-aljalia-red text-white py-2 px-6 rounded-xl font-bold font-arabic shadow-md transition-colors hover:bg-red-800">
                        أول واحد يكتب
                    </a>
                </div>
            @endforelse

            <div class="mt-4 pb-8">
                {{ $posts->links() }}
            </div>
        </div>
    </div>
</div>