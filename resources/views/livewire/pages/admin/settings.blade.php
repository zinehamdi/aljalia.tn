<?php

use App\Models\Setting;
use Livewire\Volt\Component;

new class extends Component {
    public $newUserNotificationEmail = '';

    public function mount()
    {
        $this->newUserNotificationEmail = Setting::where('key', 'new_user_notification_email')->value('value') ?? '';
    }

    public function saveSettings()
    {
        $this->validate([
            'newUserNotificationEmail' => 'nullable|email',
        ]);

        Setting::updateOrCreate(
            ['key' => 'new_user_notification_email'],
            ['value' => $this->newUserNotificationEmail]
        );

        session()->flash('success', __('Settings saved successfully.'));
    }

    public function rendering($view)
    {
        $view->layout('layouts.admin');
    }
}; ?>

<div>
    <h1 class="text-2xl font-black text-gray-900 mb-8">⚙️ {{ __('Settings') }}</h1>

    @if (session()->has('success'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl shadow-sm text-sm font-bold flex items-center gap-2">
            ✅ {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 max-w-2xl">
        <h3 class="font-bold text-gray-800 mb-6 text-lg border-b border-gray-100 pb-3">{{ __('Notifications') }}</h3>
        
        <form wire:submit="saveSettings" class="space-y-6">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">
                    {{ __('New User Notification Email') }}
                </label>
                <div class="flex flex-col gap-1">
                    <input type="email" wire:model="newUserNotificationEmail" placeholder="admin@example.com"
                        class="w-full rounded-xl border-gray-300 shadow-sm focus:border-aljalia-red focus:ring focus:ring-red-200 focus:ring-opacity-50 text-sm px-4 py-3 bg-gray-50">
                    <span class="text-xs text-gray-500">{{ __('Leave empty to disable new user email notifications.') }}</span>
                </div>
                @error('newUserNotificationEmail') <span class="text-xs text-red-500 font-bold mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div class="pt-4 flex justify-end">
                <button type="submit" class="bg-aljalia-red text-white font-bold py-2.5 px-6 rounded-xl shadow-md hover:bg-red-800 transition-colors text-sm flex items-center gap-2">
                    <span wire:loading.remove wire:target="saveSettings">💾 {{ __('Save Settings') }}</span>
                    <span wire:loading wire:target="saveSettings">⏳ {{ __('Saving...') }}</span>
                </button>
            </div>
        </form>
    </div>
</div>
