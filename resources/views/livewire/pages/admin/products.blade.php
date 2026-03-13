<?php

use App\Models\Product;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public $search = '';

    public function updatedSearch() { $this->resetPage(); }

    public function deleteProduct($id) { Product::findOrFail($id)->delete(); }

    public function toggleSold($id)
    {
        $product = Product::findOrFail($id);
        $product->update(['is_sold' => !$product->is_sold]);
    }

    public function rendering($view) { $view->layout('layouts.admin'); }

    public function with()
    {
        $query = Product::with('user')->latest();
        if ($this->search) {
            $query->where('name', 'like', "%{$this->search}%");
        }
        return ['products' => $query->paginate(20)];
    }
}; ?>

<div>
    <h1 class="text-2xl font-black text-gray-900 mb-6">🛒 {{ __('Products') }}</h1>

    <div class="mb-6">
        <input type="text" wire:model.live.debounce.300ms="search" placeholder="{{ __('Search products...') }}"
            class="w-full md:w-96 rounded-xl border-gray-300 shadow-sm focus:border-aljalia-red focus:ring focus:ring-red-200 focus:ring-opacity-50 text-sm px-4 py-3 bg-white">
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-600 text-xs uppercase tracking-wider">
                    <tr>
                        <th class="px-4 py-3 text-left">{{ __('Product') }}</th>
                        <th class="px-4 py-3 text-left">{{ __('Seller') }}</th>
                        <th class="px-4 py-3 text-left">{{ __('Price') }}</th>
                        <th class="px-4 py-3 text-left">{{ __('Condition') }}</th>
                        <th class="px-4 py-3 text-left">{{ __('Status') }}</th>
                        <th class="px-4 py-3 text-center">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($products as $product)
                        <tr wire:key="product-{{ $product->id }}" class="hover:bg-gray-50/50 transition">
                            <td class="px-4 py-3 font-bold text-gray-900 max-w-[200px] truncate">{{ $product->name }}</td>
                            <td class="px-4 py-3 text-gray-500 text-xs">{{ $product->user->name }}</td>
                            <td class="px-4 py-3 text-sm font-bold {{ $product->price ? 'text-green-600' : 'text-gray-400' }}">
                                {{ $product->price ? $product->price . ' ' . $product->currency : __('Negotiable') }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-[10px] px-2 py-0.5 rounded-full font-bold
                                    {{ $product->condition == 'new' ? 'bg-green-100 text-green-700' : ($product->condition == 'like_new' ? 'bg-blue-100 text-blue-700' : 'bg-amber-100 text-amber-700') }}">
                                    {{ __($product->condition == 'new' ? 'New' : ($product->condition == 'like_new' ? 'Like New' : 'Used')) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <button wire:click="toggleSold({{ $product->id }})"
                                    class="text-[10px] px-2 py-0.5 rounded-full font-bold cursor-pointer transition
                                    {{ $product->is_sold ? 'bg-red-100 text-red-700 hover:bg-red-200' : 'bg-green-100 text-green-700 hover:bg-green-200' }}">
                                    {{ $product->is_sold ? __('Sold') : __('Available') }}
                                </button>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <button wire:click="deleteProduct({{ $product->id }})"
                                    wire:confirm="{{ __('Delete this product?') }}"
                                    class="text-red-500 hover:text-red-700 text-xs font-bold">🗑️</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-4">{{ $products->links() }}</div>
</div>
