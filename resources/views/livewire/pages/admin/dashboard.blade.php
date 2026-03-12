<?php

use App\Models\User;
use App\Models\Post;
use App\Models\Product;
use App\Models\Place;
use App\Models\Comment;
use Livewire\Volt\Component;

new class extends Component {
    public function rendering($view)
    {
        $view->layout('layouts.admin');
    }

    public function with()
    {
        return [
            'totalUsers' => User::count(),
            'totalPosts' => Post::count(),
            'totalProducts' => Product::count(),
            'totalPlaces' => Place::count(),
            'totalComments' => Comment::count(),
            'recentUsers' => User::latest()->take(5)->get(),
            'recentPosts' => Post::with('user')->latest()->take(5)->get(),
            'todayUsers' => User::whereDate('created_at', today())->count(),
            'todayPosts' => Post::whereDate('created_at', today())->count(),
        ];
    }
}; ?>

<div>
    <h1 class="text-2xl font-black text-gray-900 mb-8">📊 {{ __('Admin Dashboard') }}</h1>

    <!-- Stats Grid -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-8">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <div class="text-3xl font-black text-aljalia-red">{{ number_format($totalUsers) }}</div>
            <div class="text-sm text-gray-500 font-bold mt-1">👥 {{ __('Users') }}</div>
            <div class="text-[10px] text-green-600 font-bold mt-2">+{{ $todayUsers }} {{ __('today') }}</div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <div class="text-3xl font-black text-blue-600">{{ number_format($totalPosts) }}</div>
            <div class="text-sm text-gray-500 font-bold mt-1">📝 {{ __('Posts') }}</div>
            <div class="text-[10px] text-green-600 font-bold mt-2">+{{ $todayPosts }} {{ __('today') }}</div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <div class="text-3xl font-black text-green-600">{{ number_format($totalProducts) }}</div>
            <div class="text-sm text-gray-500 font-bold mt-1">🛒 {{ __('Products') }}</div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <div class="text-3xl font-black text-purple-600">{{ number_format($totalPlaces) }}</div>
            <div class="text-sm text-gray-500 font-bold mt-1">📍 {{ __('Places') }}</div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <div class="text-3xl font-black text-amber-600">{{ number_format($totalComments) }}</div>
            <div class="text-sm text-gray-500 font-bold mt-1">💬 {{ __('Comments') }}</div>
        </div>
    </div>

    <div class="grid md:grid-cols-2 gap-6">
        <!-- Recent Users -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-bold text-gray-800 mb-4">👥 {{ __('Recent Users') }}</h3>
            <div class="space-y-3">
                @foreach($recentUsers as $user)
                    <div class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-gray-200 text-gray-600 flex items-center justify-center font-bold text-sm">
                                {{ mb_substr($user->name, 0, 1) }}
                            </div>
                            <div>
                                <div class="font-bold text-sm text-gray-900">{{ $user->name }}</div>
                                <div class="text-[10px] text-gray-400">{{ $user->email }}</div>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-[10px] px-2 py-0.5 rounded-full font-bold
                                {{ $user->role == 'superadmin' ? 'bg-red-100 text-red-700' : ($user->role == 'admin' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-500') }}">
                                {{ $user->role }}
                            </span>
                            <div class="text-[10px] text-gray-400 mt-1">{{ $user->created_at->diffForHumans() }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Recent Posts -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-bold text-gray-800 mb-4">📝 {{ __('Recent Posts') }}</h3>
            <div class="space-y-3">
                @foreach($recentPosts as $post)
                    <div class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
                        <div class="min-w-0 flex-1">
                            <div class="font-bold text-sm text-gray-900 truncate">{{ $post->title }}</div>
                            <div class="text-[10px] text-gray-400">{{ __('by') }} {{ $post->user->name }}</div>
                        </div>
                        <div class="text-[10px] text-gray-400 shrink-0 ml-3">{{ $post->created_at->diffForHumans() }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
