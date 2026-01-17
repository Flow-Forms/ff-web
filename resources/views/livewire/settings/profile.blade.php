<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;

new class extends Component
{
    public string $name = '';

    public string $email = '';

    public function mount(): void
    {
        $this->name = Auth::user()->name;
        $this->email = Auth::user()->email;
    }

    public function updateProfile(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore(Auth::id())],
        ]);

        $user = Auth::user();
        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        $this->dispatch('profile-updated');
    }

    public function resendVerification(): void
    {
        Auth::user()->sendEmailVerificationNotification();
        $this->dispatch('verification-sent');
    }
}; ?>

<flux:card class="space-y-6">
    <div>
        <flux:heading size="lg">{{ __('Profile Information') }}</flux:heading>
        <flux:subheading>{{ __("Update your account's profile information and email address.") }}</flux:subheading>
    </div>

    <form wire:submit="updateProfile" class="space-y-6">
        <flux:field>
            <flux:label>{{ __('Name') }}</flux:label>
            <flux:input wire:model="name" type="text" required autocomplete="name" />
            <flux:error name="name" />
        </flux:field>

        <flux:field>
            <flux:label>{{ __('Email') }}</flux:label>
            <flux:input wire:model="email" type="email" required autocomplete="email" />
            <flux:error name="email" />

            @if (Auth::user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! Auth::user()->hasVerifiedEmail())
                <div class="mt-2">
                    <flux:text class="text-sm">
                        {{ __('Your email address is unverified.') }}
                        <flux:link variant="subtle" wire:click.prevent="resendVerification" class="cursor-pointer">
                            {{ __('Click here to re-send the verification email.') }}
                        </flux:link>
                    </flux:text>

                    @if (session('verification-sent'))
                        <flux:text class="mt-2 text-sm text-green-600 dark:text-green-400">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </flux:text>
                    @endif
                </div>
            @endif
        </flux:field>

        <div class="flex items-center gap-4">
            <flux:button type="submit" variant="primary">{{ __('Save') }}</flux:button>

            <div x-data="{ shown: false }" x-on:profile-updated.window="shown = true; setTimeout(() => shown = false, 2000)">
                <flux:text x-show="shown" x-transition class="text-sm text-green-600 dark:text-green-400">
                    {{ __('Saved.') }}
                </flux:text>
            </div>
        </div>
    </form>
</flux:card>
