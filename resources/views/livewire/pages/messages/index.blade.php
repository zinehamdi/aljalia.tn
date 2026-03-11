<?php

use App\Models\Conversation;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new class extends Component {
    public function rendering($view)
    {
        $view->layout('layouts.app');
    }

    public function with()
    {
        $user = Auth::user();

        $conversations = $user->conversations()
            ->with(['userOne', 'userTwo', 'lastMessage.sender'])
            ->orderBy('last_message_at', 'desc')
            ->get();

        return [
            'conversations' => $conversations,
            'user' => $user,
        ];
    }
}; ?>

<div>
    <x-slot name="header">
        <div class="flex items-center gap-3 w-full">
            <div class="{{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">
                <h2 class="font-bold text-xl {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} leading-tight">
                    {{ __('Connect') }}
                </h2>
                <p class="text-xs text-red-100 {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }}">
                    {{ __('Your messages with Tunisians') }}</p>
            </div>
        </div>
    </x-slot>

    <div class="py-2">
        @forelse($conversations as $conversation)
            @php
                $user = Auth::user();
                $other = $conversation->otherUser($user->id);
                $lastMsg = $conversation->lastMessage;
                $unread = $conversation->unreadCountFor($user->id);
            @endphp
            <a wire:key="conversation-{{ $conversation->id }}" href="{{ route('messages.show', $conversation) }}"
                wire:navigate
                class="flex items-center gap-3 px-4 py-4 border-b border-gray-100 hover:bg-gray-50 transition-colors {{ $unread > 0 ? 'bg-red-50/50' : '' }} {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}"
                dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

                <!-- Avatar -->
                <div class="shrink-0">
                    @if($other->avatar_url)
                        <img src="{{ asset('storage/' . $other->avatar_url) }}"
                            class="w-12 h-12 rounded-full object-cover {{ $unread > 0 ? 'ring-2 ring-aljalia-red' : '' }}">
                    @else
                        <div
                            class="w-12 h-12 rounded-full bg-gray-200 text-gray-600 flex items-center justify-center font-bold text-lg {{ $unread > 0 ? 'ring-2 ring-aljalia-red' : '' }}">
                            {{ mb_substr($other->name, 0, 1) }}
                        </div>
                    @endif
                </div>

                <div class="flex-1 min-w-0 {{ app()->getLocale() == 'ar' ? 'pr-1' : 'pl-1' }}">
                    <div class="flex justify-between items-center mb-0.5">
                        <h4
                            class="font-bold text-gray-900 {{ app()->getLocale() == 'ar' ? 'font-arabic ml-2' : 'mr-2' }} text-sm truncate">
                            {{ $other->name }}</h4>
                        @if($lastMsg)
                            <span class="text-[10px] text-gray-400 shrink-0">{{ $lastMsg->created_at->diffForHumans() }}</span>
                        @endif
                    </div>
                    @if($lastMsg)
                        <p
                            class="text-xs text-gray-500 {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} truncate leading-relaxed">
                            @if($lastMsg->sender_id == $user->id)<span class="text-gray-400">{{ __('You') }}: </span>@endif
                            {{ $lastMsg->body }}
                        </p>
                    @else
                        <p class="text-xs text-gray-400 {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} italic">
                            {{ __('No messages yet') }}</p>
                    @endif
                </div>

                @if($unread > 0)
                    <span
                        class="bg-aljalia-red text-white text-[10px] font-bold w-5 h-5 rounded-full flex items-center justify-center shrink-0 {{ app()->getLocale() == 'ar' ? 'mr-2' : 'ml-2' }} shadow-sm">{{ $unread > 9 ? '9+' : $unread }}</span>
                @endif
            </a>
        @empty
            <div class="text-center py-20 px-6">
                <div class="text-6xl mb-6">💬</div>
                <h3 class="font-bold text-gray-700 {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} mb-3 text-xl">
                    {{ __('No messages') }}</h3>
                <p class="text-gray-500 text-sm {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} leading-relaxed">
                    {{ __('When you start chatting with someone in the neighborhood, the conversations will appear here. Go read a post and start a conversation!') }}
                </p>
            </div>
        @endforelse
    </div>
</div>