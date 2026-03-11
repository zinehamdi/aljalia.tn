<?php

use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;
use Livewire\Attributes\Validate;

new class extends Component {
    public Conversation $conversation;
    public $otherUser;

    #[Validate('required|min:1')]
    public $newMessage = '';

    public function mount(Conversation $conversation)
    {
        $userId = Auth::id();
        $this->conversation = $conversation;

        // Security: only allow users who are part of this conversation
        if ($conversation->user_one_id != $userId && $conversation->user_two_id != $userId) {
            abort(403);
        }

        $this->otherUser = $conversation->otherUser($userId);

        // Mark messages as read
        $conversation->messages()
            ->where('sender_id', '!=', $userId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    public function sendMessage()
    {
        $this->validate();

        $this->conversation->messages()->create([
            'sender_id' => Auth::id(),
            'body' => $this->newMessage,
        ]);

        $this->conversation->update(['last_message_at' => now()]);

        $this->newMessage = '';

        // Reload conversation messages
        $this->conversation->load('messages.sender');
    }

    public function rendering($view)
    {
        $view->layout('layouts.app');
    }

    public function with()
    {
        return [
            'messages' => $this->conversation->messages()->with('sender')->oldest()->get(),
            'currentUserId' => Auth::id(),
        ];
    }
}; ?>

<div class="flex flex-col h-[calc(100vh-140px)]"
    x-data="{ scrollToBottom() { $nextTick(() => { const container = document.getElementById('chat-messages'); if(container) container.scrollTop = container.scrollHeight; }) } }"
    x-init="scrollToBottom()">
    <x-slot name="header">
        <div class="flex items-center gap-3 w-full">
            <a href="{{ route('messages.index') }}" wire:navigate class="text-white hover:text-red-200">
                <svg class="w-6 h-6 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <div
                class="w-10 h-10 rounded-full bg-white/20 text-white flex items-center justify-center font-bold text-lg shrink-0 overflow-hidden shadow-inner">
                @if($otherUser->avatar_url)
                    <img src="{{ asset('storage/' . $otherUser->avatar_url) }}" class="w-full h-full object-cover">
                @else
                    {{ mb_substr($otherUser->name, 0, 1) }}
                @endif
            </div>
            <div class="{{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">
                <h2 class="font-bold text-lg {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} leading-tight">
                    {{ $otherUser->name }}
                </h2>
                @if($otherUser->city)
                    <p
                        class="text-[11px] text-red-100 {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} truncate max-w-[150px] opacity-90">
                        {{ $otherUser->city->name }}, {{ $otherUser->country->name }}
                    </p>
                @endif
            </div>
        </div>
    </x-slot>

    <!-- Messages list -->
    <div wire:poll.5s class="flex-1 overflow-y-auto px-4 py-4 space-y-3 bg-gray-50/50" id="chat-messages"
        dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
        @forelse($messages as $message)
            @if($message->sender_id == $currentUserId)
                <!-- My message -->
                <div wire:key="msg-{{ $message->id }}"
                    class="flex {{ app()->getLocale() == 'ar' ? 'justify-start' : 'justify-end' }}">
                    <div
                        class="bg-aljalia-red text-white rounded-2xl rounded-tr-sm px-4 py-2.5 max-w-[80%] shadow-sm relative group">
                        <p
                            class="text-sm {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} whitespace-pre-line leading-relaxed {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">
                            {{ $message->body }}
                        </p>
                        <div
                            class="text-[9px] text-red-100/80 mt-1 flex items-center {{ app()->getLocale() == 'ar' ? 'justify-end' : 'justify-start' }} gap-1">
                            {{ $message->created_at->format('H:i') }}
                            @if($message->read_at)
                                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            @endif
                        </div>
                    </div>
                </div>
            @else
                <!-- Their message -->
                <div wire:key="msg-{{ $message->id }}"
                    class="flex {{ app()->getLocale() == 'ar' ? 'justify-end' : 'justify-start' }}">
                    <div
                        class="bg-white text-gray-800 border border-gray-100 rounded-2xl rounded-tl-sm px-4 py-2.5 max-w-[80%] shadow-sm">
                        <p
                            class="text-sm {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} whitespace-pre-line leading-relaxed {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">
                            {{ $message->body }}
                        </p>
                        <div
                            class="text-[9px] text-gray-400 mt-1 {{ app()->getLocale() == 'ar' ? 'text-left' : 'text-right' }}">
                            {{ $message->created_at->format('H:i') }}</div>
                    </div>
                </div>
            @endif
        @empty
            <div class="text-center py-20 bg-white rounded-2xl mx-2 border border-dashed border-gray-200 mt-4">
                <div class="text-5xl mb-4">👋</div>
                <p class="text-gray-500 text-sm {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} px-6">
                    {{ __('Start a conversation with') }} {{ $otherUser->name }}!</p>
            </div>
        @endforelse
    </div>

    <!-- Message input -->
    <div
        class="border-t border-gray-100 bg-white p-3 pb-safe-offset-4 sticky bottom-0 z-50 shadow-[0_-4px_10px_rgba(0,0,0,0.02)]">
        <form wire:submit.prevent="sendMessage" class="flex gap-2 items-end max-w-lg mx-auto"
            dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
            <textarea wire:model="newMessage" rows="1" placeholder="{{ __('Write your message...') }}"
                class="flex-1 rounded-2xl border-gray-200 shadow-sm focus:border-aljalia-red focus:ring focus:ring-red-100 focus:ring-opacity-50 {{ app()->getLocale() == 'ar' ? 'font-arabic text-right' : 'text-left' }} text-sm px-4 py-3 bg-gray-50 resize-none max-h-32 transition-all"
                x-data x-on:input="$el.style.height = 'auto'; $el.style.height = Math.min($el.scrollHeight, 128) + 'px'"
                @keydown.enter.prevent="if(!$event.shiftKey) { $wire.sendMessage(); $el.style.height = 'auto'; scrollToBottom(); }"></textarea>
            <button type="submit"
                class="bg-aljalia-red text-white w-12 h-12 rounded-full shadow-lg hover:bg-red-800 transition-all flex items-center justify-center shrink-0 active:scale-90">
                <svg wire:loading.remove wire:target="sendMessage"
                    class="w-6 h-6 {{ app()->getLocale() == 'ar' ? 'rotate-180' : '' }}" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                </svg>
                <div wire:loading wire:target="sendMessage"
                    class="animate-spin w-5 h-5 border-2 border-white/20 border-t-white rounded-full"></div>
            </button>
        </form>
    </div>
</div>