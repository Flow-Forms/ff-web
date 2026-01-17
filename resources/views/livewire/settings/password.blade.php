<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Volt\Component;

new class extends Component
{
    public string $current_password = '';

    public string $password = '';

    public string $password_confirmation = '';

    public function updatePassword(): void
    {
        $validated = $this->validate([
            'current_password' => ['required', 'string', 'current_password:web'],
            'password' => ['required', 'string', Password::default(), 'confirmed'],
        ]);

        Auth::user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        $this->reset(['current_password', 'password', 'password_confirmation']);

        $this->dispatch('password-updated');
    }
}; ?>

<flux:card class="space-y-6">
    <div>
        <flux:heading size="lg">{{ __('Update Password') }}</flux:heading>
        <flux:subheading>{{ __('Ensure your account is using a long, random password to stay secure.') }}</flux:subheading>
    </div>

    <form wire:submit="updatePassword" class="space-y-6">
        <flux:field>
            <flux:label>{{ __('Current Password') }}</flux:label>
            <flux:input wire:model="current_password" type="password" autocomplete="current-password" viewable />
            <flux:error name="current_password" />
        </flux:field>

        <flux:field>
            <flux:label>{{ __('New Password') }}</flux:label>
            <flux:input wire:model="password" type="password" autocomplete="new-password" viewable />
            <flux:error name="password" />
        </flux:field>

        <flux:field>
            <flux:label>{{ __('Confirm Password') }}</flux:label>
            <flux:input wire:model="password_confirmation" type="password" autocomplete="new-password" viewable />
            <flux:error name="password_confirmation" />
        </flux:field>

        <div class="flex items-center gap-4">
            <flux:button type="submit" variant="primary">{{ __('Save') }}</flux:button>

            <div x-data="{ shown: false }" x-on:password-updated.window="shown = true; setTimeout(() => shown = false, 2000)">
                <flux:text x-show="shown" x-transition class="text-sm text-green-600 dark:text-green-400">
                    {{ __('Saved.') }}
                </flux:text>
            </div>
        </div>
    </form>
</flux:card>
