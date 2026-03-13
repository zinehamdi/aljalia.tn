<?php

use App\Models\Place;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public $search = '';

    public function updatedSearch() { $this->resetPage(); }

    public function deletePlace($id) { Place::findOrFail($id)->delete(); }

    public function rendering($view) { $view->layout('layouts.admin'); }

    public function with()
    {
        $query = Place::with('user')->latest();
        if ($this->search) {
            $query->where('name', 'like', "%{$this->search}%");
        }
        return ['places' => $query->paginate(20)];
    }
}; ?>

<div>
    <h1 class="text-2xl font-black text-gray-900 mb-6">📍 {{ __('Places') }}</h1>

    <div class="mb-6">
        <input type="text" wire:model.live.debounce.300ms="search" placeholder="{{ __('Search places...') }}"
            class="w-full md:w-96 rounded-xl border-gray-300 shadow-sm focus:border-aljalia-red focus:ring focus:ring-red-200 focus:ring-opacity-50 text-sm px-4 py-3 bg-white">
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-600 text-xs uppercase tracking-wider">
                    <tr>
                        <th class="px-4 py-3 text-left">{{ __('Name') }}</th>
                        <th class="px-4 py-3 text-left">{{ __('Type') }}</th>
                        <th class="px-4 py-3 text-left">{{ __('Added by') }}</th>
                        <th class="px-4 py-3 text-left">{{ __('Address') }}</th>
                        <th class="px-4 py-3 text-left">{{ __('Date') }}</th>
                        <th class="px-4 py-3 text-center">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($places as $place)
                        <tr wire:key="place-{{ $place->id }}" class="hover:bg-gray-50/50 transition">
                            <td class="px-4 py-3 font-bold text-gray-900">{{ $place->name }}</td>
                            <td class="px-4 py-3">
                                <span class="text-[10px] px-2 py-0.5 rounded-full font-bold
                                    {{ $place->type == 'restaurant' ? 'bg-orange-100 text-orange-700' : ($place->type == 'cafe' ? 'bg-amber-100 text-amber-700' : ($place->type == 'shop' ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700')) }}">
                                    {{ $place->type }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-500 text-xs">{{ $place->user->name }}</td>
                            <td class="px-4 py-3 text-gray-500 text-xs max-w-[150px] truncate">{{ $place->address ?? '—' }}</td>
                            <td class="px-4 py-3 text-gray-400 text-xs">{{ $place->created_at->format('d/m/Y') }}</td>
                            <td class="px-4 py-3 text-center">
                                <button wire:click="deletePlace({{ $place->id }})"
                                    wire:confirm="{{ __('Delete this place?') }}"
                                    class="text-red-500 hover:text-red-700 text-xs font-bold">🗑️</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-4">{{ $places->links() }}</div>
</div>
