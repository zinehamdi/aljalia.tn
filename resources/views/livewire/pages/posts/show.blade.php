<?php

use App\Models\Post;
use App\Models\Comment;
use App\Models\Vote;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;
use Livewire\Attributes\Validate;

new class extends Component {
    public Post $post;

    #[Validate('required|min:2')]
    public $newComment;

    public function mount(Post $post)
    {
        $this->post = $post->load(['user', 'city', 'category', 'comments.user']);
    }

    public function rendering($view)
    {
        $view->layout('layouts.app');
    }

    public function addComment()
    {
        $this->validate();

        $this->post->comments()->create([
            'user_id' => Auth::id(),
            'content' => $this->newComment,
        ]);

        $this->newComment = '';
        $this->post->load('comments.user'); // Refresh comments
    }

    public function votePost($val)
    {
        $existingVote = $this->post->votes()->where('user_id', Auth::id())->first();
        if ($existingVote) {
            if ($existingVote->value == $val) {
                $existingVote->delete(); // Toggle off
            } else {
                $existingVote->update(['value' => $val]); // Change vote
            }
        } else {
            $this->post->votes()->create([
                'user_id' => Auth::id(),
                'value' => $val
            ]);
        }
    }

    public function voteComment($commentId, $val)
    {
        $comment = Comment::find($commentId);
        $existingVote = $comment->votes()->where('user_id', Auth::id())->first();
        if ($existingVote) {
            if ($existingVote->value == $val) {
                $existingVote->delete();
            } else {
                $existingVote->update(['value' => $val]);
            }
        } else {
            $comment->votes()->create([
                'user_id' => Auth::id(),
                'value' => $val
            ]);
        }
    }

    public function messageUser()
    {
        if ($this->post->user_id == Auth::id())
            return;

        $conversation = \App\Models\Conversation::findOrCreateBetween(
            Auth::id(),
            $this->post->user_id
        );

        return redirect()->route('messages.show', $conversation);
    }
}; ?>

<div>
    <x-slot name="header">
        <div class="flex items-center gap-3 w-full">
            <a href="javascript:history.back()" class="text-white hover:text-red-200">
                <svg class="w-6 h-6 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <div>
                <h2 class="font-bold text-lg {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} leading-tight">
                    {{ __($post->category->name) }}
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="pb-6">
        <!-- Post Content -->
        <div class="bg-white border-b border-gray-100 p-5 shadow-sm">
            <div class="flex gap-4">
                <!-- Voting Sidebar -->
                <div class="flex flex-col items-center pt-1 gap-1 w-10 shrink-0">
                    @php
                        $userVote = $post->votes()->where('user_id', Auth::id())->first()?->value;
                    @endphp
                    <button wire:click="votePost(1)"
                        class="w-8 h-8 flex items-center justify-center rounded-full transition-colors {{ $userVote == 1 ? 'bg-aljalia-red text-white' : 'text-gray-400 hover:bg-gray-100 hover:text-aljalia-red' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7">
                            </path>
                        </svg>
                    </button>
                    <span
                        class="font-bold text-sm {{ $post->score > 0 ? 'text-aljalia-red' : ($post->score < 0 ? 'text-blue-500' : 'text-gray-600') }}">
                        {{ $post->score }}
                    </span>
                    <button wire:click="votePost(-1)"
                        class="w-8 h-8 flex items-center justify-center rounded-full transition-colors {{ $userVote == -1 ? 'bg-blue-500 text-white' : 'text-gray-400 hover:bg-gray-100 hover:text-blue-500' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                            </path>
                        </svg>
                    </button>
                </div>

                <!-- Main Content -->
                <div class="flex-1 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
                    <div class="flex items-center gap-2 mb-3">
                        @if($post->user->avatar_url)
                            <img src="{{ asset('storage/' . $post->user->avatar_url) }}"
                                class="w-10 h-10 rounded-full object-cover border border-gray-100 shadow-sm shrink-0">
                        @else
                            <div
                                class="w-10 h-10 rounded-full bg-gray-200 text-gray-600 flex items-center justify-center font-bold text-lg">
                                {{ mb_substr($post->user->name, 0, 1) }}
                            </div>
                        @endif
                        <div class="{{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">
                            <h4 class="font-bold text-gray-900 {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }}">
                                {{ $post->user->name }}
                            </h4>
                            <span
                                class="text-[11px] text-gray-500 {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }}">{{ $post->created_at->diffForHumans() }}
                                @if($post->city) • {{ $post->city->name }} @endif</span>
                        </div>
                        @if($post->type == 'guide')
                            <span
                                class="mr-auto bg-blue-100 text-blue-800 text-[10px] font-bold px-2 py-1 rounded-sm uppercase tracking-wider {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }}">{{ __('Guide') }}</span>
                        @elseif($post->type == 'help')
                            <span
                                class="mr-auto bg-amber-100 text-amber-800 text-[10px] font-bold px-2 py-1 rounded-sm uppercase tracking-wider {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }}">{{ __('Help') }}</span>
                        @elseif($post->type == 'esouq' || $post->type == 'marketplace')
                            <span
                                class="mr-auto bg-green-100 text-green-800 text-[10px] font-bold px-2 py-1 rounded-sm uppercase tracking-wider {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }}">{{ __('E-Souq') }}</span>
                        @endif
                    </div>

                    <h1
                        class="text-xl font-bold text-gray-900 {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} mb-3 leading-snug">
                        {{ $post->title }}
                    </h1>

                    @if($post->image_url)
                        <div class="mb-4 rounded-2xl overflow-hidden border border-gray-100 shadow-sm">
                            <img src="{{ asset('storage/' . $post->image_url) }}" class="w-full object-cover max-h-96">
                        </div>
                    @endif
                    <div
                        class="text-gray-800 {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} text-md leading-relaxed whitespace-pre-line">
                        {{ $post->content }}
                    </div>
                </div>
            </div>
        </div>

        @if($post->user_id != Auth::id())
            <div class="px-5 py-3 bg-gray-50 border-b border-gray-100">
                <button wire:click="messageUser"
                    class="w-full bg-white text-gray-700 font-bold py-2.5 px-4 rounded-xl text-sm {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} shadow-sm border border-gray-200 hover:bg-gray-100 transition-colors flex justify-center items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                        </path>
                    </svg>
                    {{ __('Send message to') }} {{ explode(' ', $post->user->name)[0] }} {{ __('Private message') }}
                </button>
            </div>
        @endif

        <!-- Comments Section -->
        <div class="p-4 mt-2">
            <h3 class="font-bold text-gray-800 {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} mb-4 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">
                {{ __('Comments') }} ({{ $post->comments->count() }})
            </h3>

            <div class="space-y-4 mb-6">
                @forelse($post->comments as $comment)
                    <div wire:key="comment-{{ $comment->id }}"
                        class="bg-gray-50 border border-gray-100 rounded-2xl p-4 flex gap-3 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
                        @php
                            $userCommentVote = $comment->votes()->where('user_id', Auth::id())->first()?->value;
                        @endphp
                        <div class="flex flex-col items-center shrink-0 w-6 mt-1">
                            <button wire:click="voteComment({{ $comment->id }}, 1)"
                                class="{{ $userCommentVote == 1 ? 'text-aljalia-red' : 'text-gray-400 hover:text-aljalia-red' }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7">
                                    </path>
                                </svg>
                            </button>
                            <span class="text-xs font-bold text-gray-600 my-0.5">{{ $comment->score }}</span>
                            <button wire:click="voteComment({{ $comment->id }}, -1)"
                                class="{{ $userCommentVote == -1 ? 'text-blue-500' : 'text-gray-400 hover:text-blue-500' }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-1">
                                <span
                                    class="font-bold text-sm text-gray-900 {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }}">{{ $comment->user->name }}</span>
                                <span class="text-[10px] text-gray-500">{{ $comment->created_at->diffForHumans() }}</span>
                            </div>
                            <p
                                class="text-sm text-gray-800 {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} whitespace-pre-line leading-relaxed">
                                {{ $comment->content }}
                            </p>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-center {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} text-sm py-4">
                        {{ __('Be the first to comment!') }}
                    </p>
                @endforelse
            </div>

            <!-- Add Comment Form -->
            <form wire:submit.prevent="addComment"
                class="bg-white border {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }} border-gray-200 rounded-2xl p-4 shadow-sm sticky bottom-20 z-40">
                <textarea wire:model="newComment" rows="2"
                    placeholder="{{ __('Write your comment here... (Respect others)') }}"
                    class="w-full {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }} rounded-xl border-gray-300 shadow-sm focus:border-aljalia-red focus:ring focus:ring-red-200 focus:ring-opacity-50 {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} text-sm px-4 py-3 bg-gray-50 resize-none mb-3"></textarea>
                @error('newComment') <span
                    class="text-xs text-red-500 {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} block mb-2">{{ $message }}</span>
                @enderror
                <div class="flex {{ app()->getLocale() == 'ar' ? 'justify-start pr-2' : 'justify-end pl-2' }}">
                    <button type="submit"
                        class="bg-aljalia-red text-white font-bold py-2 px-6 rounded-xl shadow-md hover:bg-red-800 transition-colors {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} text-sm flex justify-center items-center gap-2">
                        <span wire:loading.remove wire:target="addComment">{{ __('Send') }}</span>
                        <span wire:loading wire:target="addComment">{{ __('Wait a moment...') }}</span>
                        <svg wire:loading.remove wire:target="addComment" class="w-4 h-4 {{ app()->getLocale() == 'ar' ? '' : 'rotate-180' }}" fill="none"
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