<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public $search = '';

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function deleteUser($userId)
    {
        $user = User::findOrFail($userId);

        if ($user->id === Auth::id()) return;
        if ($user->isSuperAdmin()) return;

        $user->delete();
    }

    public function rendering($view)
    {
        $view->layout('layouts.admin');
    }

    public function with()
    {
        $query = User::with(['country', 'city'])->latest();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%");
            });
        }

        return [
            'users' => $query->paginate(20),
        ];
    }
}; ?>

<div>
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-black text-gray-900">👥 {{ __('Users') }}</h1>
        <span class="text-sm text-gray-500">{{ User::count() }} {{ __('total') }}</span>
    </div>

    <!-- Search -->
    <div class="mb-6">
        <input type="text" wire:model.live.debounce.300ms="search" placeholder="{{ __('Search by name or email...') }}"
            class="w-full md:w-96 rounded-xl border-gray-300 shadow-sm focus:border-aljalia-red focus:ring focus:ring-red-200 focus:ring-opacity-50 text-sm px-4 py-3 bg-white">
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-600 text-xs uppercase tracking-wider">
                    <tr>
                        <th class="px-4 py-3 text-left">{{ __('User') }}</th>
                        <th class="px-4 py-3 text-left">{{ __('Email') }}</th>
                        <th class="px-4 py-3 text-left">{{ __('Role') }}</th>
                        <th class="px-4 py-3 text-left">{{ __('Location') }}</th>
                        <th class="px-4 py-3 text-left">{{ __('Registered') }}</th>
                        <th class="px-4 py-3 text-center">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($users as $user)
                        <tr wire:key="user-{{ $user->id }}" class="hover:bg-gray-50/50 transition">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-full bg-gray-200 text-gray-600 flex items-center justify-center font-bold text-xs shrink-0">
                                        {{ mb_substr($user->name, 0, 1) }}
                                    </div>
                                    <span class="font-bold text-gray-900">{{ $user->name }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-gray-500 text-xs">{{ $user->email }}</td>
                            <td class="px-4 py-3">
                                <span class="text-[10px] px-2 py-0.5 rounded-full font-bold
                                    {{ $user->role == 'superadmin' ? 'bg-red-100 text-red-700' : ($user->role == 'admin' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-500') }}">
                                    {{ $user->role }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-500 text-xs">
                                {{ $user->country->name ?? '—' }}
                                @if($user->city) / {{ $user->city->name }} @endif
                            </td>
                            <td class="px-4 py-3 text-gray-400 text-xs">{{ $user->created_at->format('d/m/Y') }}</td>
                            <td class="px-4 py-3 text-center">
                                @if($user->id !== Auth::id() && !$user->isSuperAdmin())
                                    <button wire:click="deleteUser({{ $user->id }})"
                                        wire:confirm="{{ __('Are you sure you want to delete this user?') }}"
                                        class="text-red-500 hover:text-red-700 text-xs font-bold">
                                        🗑️
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $users->links() }}
    </div>
</div>
