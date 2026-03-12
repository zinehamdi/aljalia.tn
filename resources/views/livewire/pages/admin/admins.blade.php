<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new class extends Component {
    public $search = '';

    public function promoteToAdmin($userId)
    {
        if (!Auth::user()->isSuperAdmin()) return;

        $user = User::findOrFail($userId);
        if ($user->isSuperAdmin()) return;

        $user->update(['role' => 'admin']);
    }

    public function demoteToUser($userId)
    {
        if (!Auth::user()->isSuperAdmin()) return;

        $user = User::findOrFail($userId);
        if ($user->isSuperAdmin()) return;
        if ($user->id === Auth::id()) return;

        $user->update(['role' => 'user']);
    }

    public function rendering($view)
    {
        $view->layout('layouts.admin');
    }

    public function with()
    {
        $admins = User::where('role', '!=', 'user')->get();

        $query = User::where('role', 'user');
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%");
            });
        }

        return [
            'admins' => $admins,
            'regularUsers' => $query->latest()->take(20)->get(),
        ];
    }
}; ?>

<div>
    <h1 class="text-2xl font-black text-gray-900 mb-8">🛡️ {{ __('Admin Management') }}</h1>

    <!-- Current Admins -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-8">
        <h3 class="font-bold text-gray-800 mb-4 text-lg">{{ __('Current Admins') }}</h3>
        <div class="space-y-3">
            @foreach($admins as $admin)
                <div class="flex items-center justify-between py-3 px-4 rounded-xl {{ $admin->isSuperAdmin() ? 'bg-red-50 border border-red-100' : 'bg-blue-50 border border-blue-100' }}">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full {{ $admin->isSuperAdmin() ? 'bg-red-200 text-red-700' : 'bg-blue-200 text-blue-700' }} flex items-center justify-center font-bold">
                            {{ mb_substr($admin->name, 0, 1) }}
                        </div>
                        <div>
                            <div class="font-bold text-gray-900">{{ $admin->name }}</div>
                            <div class="text-xs text-gray-500">{{ $admin->email }}</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-xs px-3 py-1 rounded-full font-bold
                            {{ $admin->isSuperAdmin() ? 'bg-red-200 text-red-800' : 'bg-blue-200 text-blue-800' }}">
                            {{ $admin->isSuperAdmin() ? '👑 SuperAdmin' : '🛡️ Admin' }}
                        </span>
                        @if(!$admin->isSuperAdmin() && $admin->id !== Auth::id())
                            <button wire:click="demoteToUser({{ $admin->id }})"
                                wire:confirm="{{ __('Remove admin privileges from this user?') }}"
                                class="text-xs px-3 py-1 rounded-full bg-gray-200 text-gray-700 hover:bg-red-100 hover:text-red-700 font-bold transition">
                                {{ __('Remove') }}
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Add Admin -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h3 class="font-bold text-gray-800 mb-4 text-lg">{{ __('Promote User to Admin') }}</h3>
        <div class="mb-4">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="{{ __('Search users by name or email...') }}"
                class="w-full rounded-xl border-gray-300 shadow-sm focus:border-aljalia-red focus:ring focus:ring-red-200 focus:ring-opacity-50 text-sm px-4 py-3 bg-gray-50">
        </div>

        @if($search)
            <div class="space-y-2">
                @forelse($regularUsers as $user)
                    <div class="flex items-center justify-between py-3 px-4 rounded-xl bg-gray-50 border border-gray-100">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-gray-200 text-gray-600 flex items-center justify-center font-bold text-sm">
                                {{ mb_substr($user->name, 0, 1) }}
                            </div>
                            <div>
                                <div class="font-bold text-sm text-gray-900">{{ $user->name }}</div>
                                <div class="text-[10px] text-gray-400">{{ $user->email }}</div>
                            </div>
                        </div>
                        <button wire:click="promoteToAdmin({{ $user->id }})"
                            wire:confirm="{{ __('Make this user an admin?') }}"
                            class="text-xs px-3 py-1.5 rounded-full bg-blue-500 text-white hover:bg-blue-600 font-bold transition shadow-sm">
                            🛡️ {{ __('Make Admin') }}
                        </button>
                    </div>
                @empty
                    <p class="text-sm text-gray-400 text-center py-4">{{ __('No users found') }}</p>
                @endforelse
            </div>
        @else
            <p class="text-sm text-gray-400 text-center py-4">{{ __('Search for a user to promote') }}</p>
        @endif
    </div>
</div>
