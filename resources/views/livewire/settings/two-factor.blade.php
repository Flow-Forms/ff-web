<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Actions\ConfirmTwoFactorAuthentication;
use Laravel\Fortify\Actions\DisableTwoFactorAuthentication;
use Laravel\Fortify\Actions\EnableTwoFactorAuthentication;
use Laravel\Fortify\Actions\GenerateNewRecoveryCodes;
use Livewire\Component;

new class extends Component
{
    public bool $showingQrCode = false;

    public bool $showingConfirmation = false;

    public bool $showingRecoveryCodes = false;

    public string $code = '';

    public string $password = '';

    public bool $confirmingDisable = false;

    public function getEnabledProperty(): bool
    {
        return ! empty(Auth::user()->two_factor_secret);
    }

    public function getConfirmedProperty(): bool
    {
        return ! empty(Auth::user()->two_factor_confirmed_at);
    }

    public function enableTwoFactor(): void
    {
        $this->validatePassword();

        app(EnableTwoFactorAuthentication::class)(Auth::user());

        $this->showingQrCode = true;
        $this->showingConfirmation = true;
        $this->password = '';
    }

    public function confirmTwoFactor(): void
    {
        $this->validate(['code' => ['required', 'string', 'regex:/^\d{6}$/']]);

        try {
            app(ConfirmTwoFactorAuthentication::class)(Auth::user(), $this->code);
        } catch (\Exception $e) {
            throw ValidationException::withMessages([
                'code' => [__('The provided two factor authentication code was invalid.')],
            ]);
        }

        $this->showingQrCode = false;
        $this->showingConfirmation = false;
        $this->showingRecoveryCodes = true;
        $this->code = '';
    }

    public function showRecoveryCodes(): void
    {
        $this->validatePassword();
        $this->showingRecoveryCodes = true;
        $this->password = '';
    }

    public function regenerateRecoveryCodes(): void
    {
        $this->validatePassword();
        app(GenerateNewRecoveryCodes::class)(Auth::user());
        $this->showingRecoveryCodes = true;
        $this->password = '';
    }

    public function disableTwoFactor(): void
    {
        $this->validatePassword();
        app(DisableTwoFactorAuthentication::class)(Auth::user());

        $this->showingQrCode = false;
        $this->showingConfirmation = false;
        $this->showingRecoveryCodes = false;
        $this->confirmingDisable = false;
        $this->password = '';
    }

    protected function validatePassword(): void
    {
        $this->validate(['password' => ['required', 'string']]);

        if (! Hash::check($this->password, Auth::user()->password)) {
            throw ValidationException::withMessages([
                'password' => [__('This password does not match our records.')],
            ]);
        }
    }
}; ?>

<flux:card class="space-y-6">
    <div>
        <flux:heading size="lg">{{ __('Two Factor Authentication') }}</flux:heading>
        <flux:subheading>{{ __('Add additional security to your account using two factor authentication.') }}</flux:subheading>
    </div>

    <div class="space-y-4">
        <div class="flex items-center gap-3">
            @if ($this->enabled && $this->confirmed)
                <flux:badge color="green" size="sm">{{ __('Enabled') }}</flux:badge>
            @elseif ($this->enabled)
                <flux:badge color="amber" size="sm">{{ __('Pending') }}</flux:badge>
            @else
                <flux:badge color="zinc" size="sm">{{ __('Disabled') }}</flux:badge>
            @endif
        </div>

        <flux:text class="text-sm text-zinc-600 dark:text-zinc-400">
            {{ __("When two factor authentication is enabled, you will be prompted for a secure, random token during authentication.") }}
        </flux:text>

        @if ($this->enabled)
            @if ($showingQrCode)
                <div class="space-y-4">
                    <flux:text class="text-sm font-medium">
                        @if ($showingConfirmation)
                            {{ __("Scan the QR code with your authenticator app and enter the code to confirm.") }}
                        @else
                            {{ __("Two factor authentication is enabled. Scan the QR code with your authenticator app.") }}
                        @endif
                    </flux:text>

                    <div class="p-4 inline-block bg-white rounded-lg">
                        {!! Auth::user()->twoFactorQrCodeSvg() !!}
                    </div>

                    <div class="p-3 bg-zinc-100 dark:bg-zinc-800 rounded-lg">
                        <flux:text class="text-sm font-medium">
                            {{ __('Setup Key') }}: <code class="font-mono">{{ decrypt(Auth::user()->two_factor_secret) }}</code>
                        </flux:text>
                    </div>

                    @if ($showingConfirmation)
                        <flux:field class="max-w-xs">
                            <flux:label>{{ __('Code') }}</flux:label>
                            <flux:input wire:model="code" type="text" inputmode="numeric" autofocus autocomplete="one-time-code" wire:keydown.enter="confirmTwoFactor" placeholder="000000" />
                            <flux:error name="code" />
                        </flux:field>
                    @endif
                </div>
            @endif

            @if ($showingRecoveryCodes)
                <div class="space-y-4">
                    <flux:text class="text-sm font-medium">
                        {{ __('Store these recovery codes in a secure password manager.') }}
                    </flux:text>

                    <div class="p-4 bg-zinc-100 dark:bg-zinc-800 rounded-lg font-mono text-sm grid gap-1">
                        @foreach (json_decode(decrypt(Auth::user()->two_factor_recovery_codes), true) as $code)
                            <div>{{ $code }}</div>
                        @endforeach
                    </div>
                </div>
            @endif
        @endif

        {{-- Action Buttons --}}
        <div class="flex flex-wrap gap-3 pt-2">
            @if (! $this->enabled)
                {{-- Enable Button with Password --}}
                <div x-data="{ open: false }">
                    <flux:button variant="primary" x-on:click="open = true">{{ __('Enable') }}</flux:button>

                    <flux:modal x-model="open" class="max-w-md">
                        <flux:heading size="lg">{{ __('Confirm Password') }}</flux:heading>
                        <flux:text class="mt-2">{{ __('Please confirm your password to enable two-factor authentication.') }}</flux:text>

                        <flux:field class="mt-4">
                            <flux:label>{{ __('Password') }}</flux:label>
                            <flux:input wire:model="password" type="password" viewable />
                            <flux:error name="password" />
                        </flux:field>

                        <div class="mt-6 flex justify-end gap-3">
                            <flux:button variant="ghost" x-on:click="open = false">{{ __('Cancel') }}</flux:button>
                            <flux:button variant="primary" wire:click="enableTwoFactor" x-on:click="open = false">{{ __('Enable') }}</flux:button>
                        </div>
                    </flux:modal>
                </div>
            @else
                @if ($showingConfirmation)
                    <flux:button variant="primary" wire:click="confirmTwoFactor">{{ __('Confirm') }}</flux:button>
                    <flux:button variant="ghost" wire:click="$set('showingConfirmation', false)">{{ __('Cancel') }}</flux:button>
                @elseif ($showingRecoveryCodes)
                    {{-- Regenerate with password confirmation --}}
                    <div x-data="{ open: false }">
                        <flux:button variant="ghost" x-on:click="open = true">{{ __('Regenerate Recovery Codes') }}</flux:button>

                        <flux:modal x-model="open" class="max-w-md">
                            <flux:heading size="lg">{{ __('Confirm Password') }}</flux:heading>
                            <flux:field class="mt-4">
                                <flux:label>{{ __('Password') }}</flux:label>
                                <flux:input wire:model="password" type="password" viewable />
                                <flux:error name="password" />
                            </flux:field>
                            <div class="mt-6 flex justify-end gap-3">
                                <flux:button variant="ghost" x-on:click="open = false">{{ __('Cancel') }}</flux:button>
                                <flux:button variant="primary" wire:click="regenerateRecoveryCodes" x-on:click="open = false">{{ __('Regenerate') }}</flux:button>
                            </div>
                        </flux:modal>
                    </div>

                    <flux:button variant="ghost" wire:click="$set('showingRecoveryCodes', false)">{{ __('Close') }}</flux:button>
                @else
                    {{-- Show Recovery Codes with password --}}
                    <div x-data="{ open: false }">
                        <flux:button variant="ghost" x-on:click="open = true">{{ __('Show Recovery Codes') }}</flux:button>

                        <flux:modal x-model="open" class="max-w-md">
                            <flux:heading size="lg">{{ __('Confirm Password') }}</flux:heading>
                            <flux:field class="mt-4">
                                <flux:label>{{ __('Password') }}</flux:label>
                                <flux:input wire:model="password" type="password" viewable />
                                <flux:error name="password" />
                            </flux:field>
                            <div class="mt-6 flex justify-end gap-3">
                                <flux:button variant="ghost" x-on:click="open = false">{{ __('Cancel') }}</flux:button>
                                <flux:button variant="primary" wire:click="showRecoveryCodes" x-on:click="open = false">{{ __('Show') }}</flux:button>
                            </div>
                        </flux:modal>
                    </div>
                @endif

                {{-- Disable with password confirmation --}}
                <div x-data="{ open: false }">
                    <flux:button variant="danger" x-on:click="open = true">{{ __('Disable') }}</flux:button>

                    <flux:modal x-model="open" class="max-w-md">
                        <flux:heading size="lg">{{ __('Disable Two-Factor Authentication') }}</flux:heading>
                        <flux:text class="mt-2">{{ __('Please confirm your password to disable two-factor authentication.') }}</flux:text>

                        <flux:field class="mt-4">
                            <flux:label>{{ __('Password') }}</flux:label>
                            <flux:input wire:model="password" type="password" viewable />
                            <flux:error name="password" />
                        </flux:field>

                        <div class="mt-6 flex justify-end gap-3">
                            <flux:button variant="ghost" x-on:click="open = false">{{ __('Cancel') }}</flux:button>
                            <flux:button variant="danger" wire:click="disableTwoFactor" x-on:click="open = false">{{ __('Disable') }}</flux:button>
                        </div>
                    </flux:modal>
                </div>
            @endif
        </div>
    </div>
</flux:card>
