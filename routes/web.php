<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::view('/', 'welcome');

Route::get('/lang/{locale}', function ($locale) {
    if (in_array($locale, ['ar', 'en', 'fr'])) {
        session()->put('locale', $locale);
    }
    return back();
})->name('locale.switch');

Route::middleware(['auth'])->group(function () {
    // Stage 1: "وصلنا" Onboarding Step
    Volt::route('/onboarding/country', 'pages.onboarding')
        ->name('onboarding.country');

    Route::middleware([\App\Http\Middleware\EnsureCountryIsSelected::class])->group(function () {
        
        Volt::route('dashboard', 'pages.dashboard')
            ->middleware(['verified'])
            ->name('dashboard');

        Volt::route('category/{category:slug}', 'pages.category.show')
            ->middleware(['verified'])
            ->name('category.show');

        Volt::route('posts/create', 'pages.posts.create')
            ->middleware(['verified'])
            ->name('posts.create');

        Volt::route('posts/{post}', 'pages.posts.show')
            ->middleware(['verified'])
            ->name('posts.show');

        Volt::route('places', 'pages.places.index')
            ->middleware(['verified'])
            ->name('places.index');

        Volt::route('places/create', 'pages.places.create')
            ->middleware(['verified'])
            ->name('places.create');

        Volt::route('esouq', 'pages.esouq.index')
            ->middleware(['verified'])
            ->name('esouq.index');

        Volt::route('esouq/create', 'pages.esouq.create')
            ->middleware(['verified'])
            ->name('esouq.create');

        Volt::route('esouq/{product}', 'pages.esouq.show')
            ->middleware(['verified'])
            ->name('esouq.show');

        Volt::route('messages', 'pages.messages.index')
            ->middleware(['verified'])
            ->name('messages.index');

        Volt::route('messages/{conversation}', 'pages.messages.show')
            ->middleware(['verified'])
            ->name('messages.show');

        Route::view('profile', 'profile')
            ->name('profile');
            
    });
});

require __DIR__.'/auth.php';
