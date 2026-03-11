<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use App\Services\ImageModerator;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

new class extends Component {
    use WithFileUploads;

    public string $name = '';
    public string $email = '';
    public $avatar;

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->name = Auth::user()->name;
        $this->email = Auth::user()->email;
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
            'avatar' => ['nullable', 'image', 'max:2048'],
        ]);

        $user->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        if ($this->avatar) {
            $moderator = app(ImageModerator::class);
            // Content Moderation check
            if ($moderator && !$moderator->isSafe($this->avatar->getRealPath())) {
                $this->addError('avatar', 'هذه الصورة تحتوي على محتوى غير لائق. يرجى احترام القوانين.');
                return;
            }

            $filename = hexdec(uniqid()) . '.jpg';
            $path = storage_path('app/public/avatars/' . $filename);

            if (!file_exists(storage_path('app/public/avatars'))) {
                mkdir(storage_path('app/public/avatars'), 0755, true);
            }

            // Optimize Avatar
            $manager = new ImageManager(new Driver());
            $img = $manager->read($this->avatar->getRealPath());
            $img->cover(300, 300); // 300x300 square crop
            $img->toJpeg(80)->save($path);

            $user->avatar_url = 'avatars/' . $filename;
        }

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        $this->dispatch('profile-updated', name: $user->name);
    }

    /**
     * Send an email verification notification to the current user.
     */
    public function sendVerification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));

            return;
        }

        $user->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }
}; ?>

<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }}">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }}">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form wire:submit="updateProfileInformation" class="mt-6 space-y-6">
        <div class="flex items-center gap-4 mb-4">
            <div class="relative">
                @if ($avatar)
                    <img src="{{ $avatar->temporaryUrl() }}"
                        class="w-20 h-20 rounded-full object-cover border-2 border-aljalia-red">
                @elseif(auth()->user()->avatar_url)
                    <img src="{{ asset('storage/' . auth()->user()->avatar_url) }}"
                        class="w-20 h-20 rounded-full object-cover border-2 border-gray-100">
                @else
                    <div
                        class="w-20 h-20 rounded-full bg-gray-200 flex items-center justify-center text-gray-500 font-bold text-2xl uppercase">
                        {{ mb_substr(auth()->user()->name, 0, 1) }}
                    </div>
                @endif

                <label for="avatar"
                    class="absolute bottom-0 right-0 bg-white rounded-full p-1.5 shadow-md border border-gray-200 cursor-pointer hover:bg-gray-50 transition-colors">
                    <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z">
                        </path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <input type="file" id="avatar" wire:model="avatar" class="hidden" accept="image/*">
                </label>
            </div>
            <div>
                <h4 class="font-bold text-sm {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }}">
                    {{ __('Your profile picture') }}</h4>
                <p class="text-xs text-gray-500 {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }}"> PNG, JPG
                    {{ __('Max 2MB') }}</p>
                @error('avatar') <span
                    class="text-xs text-red-600 {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }}">{{ $message }}</span>
                @enderror
                <div wire:loading wire:target="avatar"
                    class="text-[10px] text-aljalia-red {{ app()->getLocale() == 'ar' ? 'font-arabic' : '' }} animate-pulse mt-1">
                    {{ __('Wait a moment...') }}</div>
            </div>
        </div>

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input wire:model="name" id="name" name="name" type="text" class="mt-1 block w-full" required
                autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input wire:model="email" id="email" name="email" type="email" class="mt-1 block w-full" required
                autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !auth()->user()->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800">
                        {{ __('Your email address is unverified.') }}

                        <button wire:click.prevent="sendVerification"
                            class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            <x-action-message class="me-3" on="profile-updated">
                {{ __('Saved.') }}
            </x-action-message>
        </div>
    </form>
</section>