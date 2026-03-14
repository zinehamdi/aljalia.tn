<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new class extends Component {
    public User $user;

    public function mount(User $user)
    {
        $this->user = $user->load(['country', 'city', 'posts']);
    }

    public function rendering($view)
    {
        $view->layout('layouts.app');
    }

    public function with()
    {
        return [
            'posts' => $this->user->posts()->latest()->paginate(10),
        ];
    }

    public function messageUser()
    {
        if ($this->user->id == Auth::id())
            return;

        $conversation = \App\Models\Conversation::findOrCreateBetween(
            Auth::id(),
            $this->user->id
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
                    {{ __('Profile') }}: {{ $user->name }}
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="pb-6">
        <!-- User Header Info -->
        <div class="bg-gradient-to-br from-aljalia-red to-red-900 border-b border-gray-100 p-6 shadow-sm overflow-hidden relative">
            <div class="flex items-center gap-4 relative z-10 {{ app()->getLocale() == 'ar' ? 'flex-row-reverse' : '' }}">
                @if($user->avatar_url)
                    <img src="{{ asset('storage/' . $user->avatar_url) }}"
                        class="w-20 h-20 rounded-full object-cover border-4 border-white shadow-lg shrink-0">
                @else
                    <div
                        class="w-20 h-20 rounded-full bg-white text-aljalia-red flex items-center justify-center font-bold text-3xl shadow-lg shrink-0">
                        {{ mb_substr($user->name, 0, 1) }}
                    </div>
                @endif
                <div class="text-white {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">
                    <h1 class="text-2xl font-black {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }}">
                        {{ $user->name }}
                        @if($user->isSuperAdmin())
                            <span class="inline-block align-middle ml-1"><svg class="w-5 h-5 text-yellow-300 inline" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1.323l3.954 1.582 1.599-.8a1 1 0 01.894 1.79l-1.233.616 1.738 5.42a1 1 0 01-.285 1.05A3.989 3.989 0 0115 15a3.989 3.989 0 01-2.667-1.019 1 1 0 01-.285-1.05l1.715-5.349L11 6.477V16h2a1 1 0 110 2H7a1 1 0 110-2h2V6.477L6.237 7.582l1.715 5.349a1 1 0 01-.285 1.05A3.989 3.989 0 015 15a3.989 3.989 0 01-2.667-1.019 1 1 0 01-.285-1.05l1.738-5.42-1.233-.617a1 1 0 01.894-1.788l1.599.799L9 4.323V3a1 1 0 011-1z" clip-rule="evenodd"></path></svg></span>
                        @endif
                    </h1>
                    <p class="text-sm opacity-90 mt-1 {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }}">
                        📍 {{ $user->country?->name }} @if($user->city) - {{ $user->city->name }} @endif
                    </p>
                    <p class="text-xs opacity-75 mt-1 {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }}">
                        {{ __('Joined') }} {{ $user->created_at->diffForHumans() }}
                    </p>
                </div>
            </div>
            <!-- Decorative circle -->
            <div class="absolute -bottom-10 -right-10 w-32 h-32 bg-white opacity-10 rounded-full blur-2xl"></div>
        </div>

        @if(Auth::id() !== $user->id)
        <!-- Action Buttons -->
        <div class="px-5 py-4 bg-white border-b border-gray-100 shadow-sm flex flex-col gap-3">
            <button wire:click="messageUser"
                class="w-full bg-aljalia-red text-white font-bold py-3 px-4 rounded-xl text-md {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} shadow-md border hover:bg-red-800 transition-colors flex justify-center items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                    </path>
                </svg>
                {{ __('Send message to') }} {{ explode(' ', $user->name)[0] }}
            </button>
        </div>
        @endif

        <div class="px-4 mt-6">
            <h3 class="font-bold text-gray-800 text-lg {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} mb-4 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">
                {{ __('Recent Posts') }} ({{ $user->posts()->count() }})
            </h3>

            <div class="space-y-4">
                @forelse($posts as $post)
                    <a wire:key="post-{{ $post->id }}" href="{{ route('posts.show', $post) }}" wire:navigate
                        class="block bg-white rounded-xl shadow-sm border border-gray-100 p-4 active:scale-95 transition-transform {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}"
                        dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
                        
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="font-bold text-gray-800 {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} mb-1 leading-tight text-md">
                                {{ $post->title }}
                            </h3>
                            @if($post->type == 'guide')
                                <span class="bg-blue-100 text-blue-800 text-[10px] font-bold px-2 py-0.5 rounded uppercase tracking-wider shrink-0 {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }}">{{ __('Guide') }}</span>
                            @elseif($post->type == 'help')
                                <span class="bg-amber-100 text-amber-800 text-[10px] font-bold px-2 py-0.5 rounded uppercase tracking-wider shrink-0 {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }}">{{ __('Help') }}</span>
                            @elseif($post->type == 'esouq' || $post->type == 'marketplace')
                                <span class="bg-green-100 text-green-800 text-[10px] font-bold px-2 py-0.5 rounded uppercase tracking-wider shrink-0 {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }}">{{ __('E-Souq') }}</span>
                            @endif
                        </div>

                        <p class="text-[11px] text-gray-400 mb-2 {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }}">
                           {{ $post->created_at->diffForHumans() }} • {{ $post->category?->name }} 
                        </p>

                        <p class="text-gray-600 text-sm {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} line-clamp-2 overflow-hidden leading-relaxed opacity-90 mb-3">
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
                                {{ $post->comments()->count() }}
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="bg-gray-50 rounded-xl p-8 text-center mt-4">
                        <div class="text-4xl mb-4">📭</div>
                        <p class="text-gray-500 text-sm {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }}">
                            {{ __('No posts published yet.') }}
                        </p>
                    </div>
                @endforelse

                <div class="mt-4 pb-8">
                    {{ $posts->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
