<?php

use App\Models\Post;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public $search = '';

    public function updatedSearch() { $this->resetPage(); }

    public function deletePost($postId)
    {
        Post::findOrFail($postId)->delete();
    }

    public function rendering($view) { $view->layout('layouts.admin'); }

    public function with()
    {
        $query = Post::with(['user', 'category'])->latest();
        if ($this->search) {
            $query->where('title', 'like', "%{$this->search}%");
        }
        return ['posts' => $query->paginate(20)];
    }
}; ?>

<div>
    <h1 class="text-2xl font-black text-gray-900 mb-6">📝 {{ __('Posts') }}</h1>

    <div class="mb-6">
        <input type="text" wire:model.live.debounce.300ms="search" placeholder="{{ __('Search posts...') }}"
            class="w-full md:w-96 rounded-xl border-gray-300 shadow-sm focus:border-aljalia-red focus:ring focus:ring-red-200 focus:ring-opacity-50 text-sm px-4 py-3 bg-white">
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-600 text-xs uppercase tracking-wider">
                    <tr>
                        <th class="px-4 py-3 text-left">{{ __('Title') }}</th>
                        <th class="px-4 py-3 text-left">{{ __('Author') }}</th>
                        <th class="px-4 py-3 text-left">{{ __('Category') }}</th>
                        <th class="px-4 py-3 text-left">{{ __('Type') }}</th>
                        <th class="px-4 py-3 text-left">{{ __('Date') }}</th>
                        <th class="px-4 py-3 text-center">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($posts as $post)
                        <tr wire:key="post-{{ $post->id }}" class="hover:bg-gray-50/50 transition">
                            <td class="px-4 py-3 font-bold text-gray-900 max-w-[200px] truncate">{{ $post->title }}</td>
                            <td class="px-4 py-3 text-gray-500 text-xs">{{ $post->user->name }}</td>
                            <td class="px-4 py-3 text-xs text-gray-500">{{ $post->category->name ?? '—' }}</td>
                            <td class="px-4 py-3">
                                <span class="text-[10px] px-2 py-0.5 rounded-full font-bold
                                    {{ $post->type == 'guide' ? 'bg-blue-100 text-blue-700' : ($post->type == 'help' ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 text-gray-500') }}">
                                    {{ $post->type }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-400 text-xs">{{ $post->created_at->format('d/m/Y') }}</td>
                            <td class="px-4 py-3 text-center">
                                <button wire:click="deletePost({{ $post->id }})"
                                    wire:confirm="{{ __('Delete this post?') }}"
                                    class="text-red-500 hover:text-red-700 text-xs font-bold">🗑️</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-4">{{ $posts->links() }}</div>
</div>
