<flux:card class="space-y-6">
    <div>
        <flux:heading size="lg">{{ __('Two Factor Authentication') }}</flux:heading>
        <flux:subheading>{{ __('Add additional security to your account using two factor authentication.') }}</flux:subheading>
    </div>

    <div class="space-y-4">
        <div class="flex items-center gap-3">
            @if ($this->enabled)
                <flux:badge color="green" size="sm">{{ __('Enabled') }}</flux:badge>
            @else
                <flux:badge color="zinc" size="sm">{{ __('Disabled') }}</flux:badge>
            @endif

            <flux:text class="text-sm">
                @if ($this->enabled)
                    @if ($showingConfirmation)
                        {{ __('Finish enabling two factor authentication.') }}
                    @else
                        {{ __('You have enabled two factor authentication.') }}
                    @endif
                @else
                    {{ __('You have not enabled two factor authentication.') }}
                @endif
            </flux:text>
        </div>

        <flux:text class="text-sm text-zinc-600 dark:text-zinc-400">
            {{ __("When two factor authentication is enabled, you will be prompted for a secure, random token during authentication. You may retrieve this token from your phone's Google Authenticator application.") }}
        </flux:text>

        @if ($this->enabled)
            @if ($showingQrCode)
                <div class="space-y-4">
                    <flux:text class="text-sm font-medium">
                        @if ($showingConfirmation)
                            {{ __("To finish enabling two factor authentication, scan the following QR code using your phone's authenticator application or enter the setup key and provide the generated OTP code.") }}
                        @else
                            {{ __("Two factor authentication is now enabled. Scan the following QR code using your phone's authenticator application or enter the setup key.") }}
                        @endif
                    </flux:text>

                    <div class="p-4 inline-block bg-white rounded-lg">
                        {!! $this->user->twoFactorQrCodeSvg() !!}
                    </div>

                    <div class="p-3 bg-zinc-100 dark:bg-zinc-800 rounded-lg">
                        <flux:text class="text-sm font-medium">
                            {{ __('Setup Key') }}: <code class="font-mono">{{ decrypt($this->user->two_factor_secret) }}</code>
                        </flux:text>
                    </div>

                    @if ($showingConfirmation)
                        <flux:field class="max-w-xs">
                            <flux:label>{{ __('Code') }}</flux:label>
                            <flux:input
                                wire:model="code"
                                type="text"
                                inputmode="numeric"
                                autofocus
                                autocomplete="one-time-code"
                                wire:keydown.enter="confirmTwoFactorAuthentication"
                                placeholder="000000"
                            />
                            @error('code')
                                <flux:error>{{ $message }}</flux:error>
                            @enderror
                        </flux:field>
                    @endif
                </div>
            @endif

            @if ($showingRecoveryCodes)
                <div class="space-y-4">
                    <flux:text class="text-sm font-medium">
                        {{ __('Store these recovery codes in a secure password manager. They can be used to recover access to your account if your two factor authentication device is lost.') }}
                    </flux:text>

                    <div class="p-4 bg-zinc-100 dark:bg-zinc-800 rounded-lg font-mono text-sm grid gap-1">
                        @foreach (json_decode(decrypt($this->user->two_factor_recovery_codes), true) as $code)
                            <div>{{ $code }}</div>
                        @endforeach
                    </div>
                </div>
            @endif
        @endif

        <div class="flex flex-wrap gap-3 pt-2">
            @if (! $this->enabled)
                <x-confirms-password wire:then="enableTwoFactorAuthentication">
                    <flux:button variant="primary" wire:loading.attr="disabled">
                        {{ __('Enable') }}
                    </flux:button>
                </x-confirms-password>
            @else
                @if ($showingRecoveryCodes)
                    <x-confirms-password wire:then="regenerateRecoveryCodes">
                        <flux:button variant="ghost">
                            {{ __('Regenerate Recovery Codes') }}
                        </flux:button>
                    </x-confirms-password>
                @elseif ($showingConfirmation)
                    <x-confirms-password wire:then="confirmTwoFactorAuthentication">
                        <flux:button variant="primary" wire:loading.attr="disabled">
                            {{ __('Confirm') }}
                        </flux:button>
                    </x-confirms-password>
                @else
                    <x-confirms-password wire:then="showRecoveryCodes">
                        <flux:button variant="ghost">
                            {{ __('Show Recovery Codes') }}
                        </flux:button>
                    </x-confirms-password>
                @endif

                @if ($showingConfirmation)
                    <x-confirms-password wire:then="disableTwoFactorAuthentication">
                        <flux:button variant="ghost" wire:loading.attr="disabled">
                            {{ __('Cancel') }}
                        </flux:button>
                    </x-confirms-password>
                @else
                    <x-confirms-password wire:then="disableTwoFactorAuthentication">
                        <flux:button variant="danger" wire:loading.attr="disabled">
                            {{ __('Disable') }}
                        </flux:button>
                    </x-confirms-password>
                @endif
            @endif
        </div>
    </div>
</flux:card>
