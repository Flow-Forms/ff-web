<flux:card class="space-y-6">
    <div>
        <flux:heading size="lg">{{ __('Browser Sessions') }}</flux:heading>
        <flux:subheading>{{ __('Manage and log out your active sessions on other browsers and devices.') }}</flux:subheading>
    </div>

    <flux:text class="text-sm text-zinc-600 dark:text-zinc-400">
        {{ __('If necessary, you may log out of all of your other browser sessions across all of your devices. Some of your recent sessions are listed below; however, this list may not be exhaustive. If you feel your account has been compromised, you should also update your password.') }}
    </flux:text>

    @if (count($this->sessions) > 0)
        <div class="space-y-4">
            @foreach ($this->sessions as $session)
                <div class="flex items-center gap-3">
                    <div class="text-zinc-500">
                        @if ($session->agent->isDesktop())
                            <flux:icon.computer-desktop class="size-8" />
                        @else
                            <flux:icon.device-phone-mobile class="size-8" />
                        @endif
                    </div>

                    <div>
                        <flux:text class="text-sm">
                            {{ $session->agent->platform() ? $session->agent->platform() : __('Unknown') }} - {{ $session->agent->browser() ? $session->agent->browser() : __('Unknown') }}
                        </flux:text>

                        <flux:text class="text-xs text-zinc-500">
                            {{ $session->ip_address }},
                            @if ($session->is_current_device)
                                <span class="text-green-500 font-semibold">{{ __('This device') }}</span>
                            @else
                                {{ __('Last active') }} {{ $session->last_active }}
                            @endif
                        </flux:text>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <div class="flex items-center gap-4">
        <flux:button variant="primary" wire:click="confirmLogout" wire:loading.attr="disabled">
            {{ __('Log Out Other Browser Sessions') }}
        </flux:button>

        <x-action-message on="loggedOut">
            <flux:text class="text-sm text-green-600 dark:text-green-400">{{ __('Done.') }}</flux:text>
        </x-action-message>
    </div>

    <flux:modal wire:model.live="confirmingLogout" class="max-w-lg">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('Log Out Other Browser Sessions') }}</flux:heading>
                <flux:text class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">
                    {{ __('Please enter your password to confirm you would like to log out of your other browser sessions across all of your devices.') }}
                </flux:text>
            </div>

            <flux:field
                x-data="{}"
                x-on:confirming-logout-other-browser-sessions.window="setTimeout(() => $refs.password.focus(), 250)"
            >
                <flux:label>{{ __('Password') }}</flux:label>
                <flux:input
                    x-ref="password"
                    wire:model="password"
                    type="password"
                    autocomplete="current-password"
                    placeholder="{{ __('Password') }}"
                    wire:keydown.enter="logoutOtherBrowserSessions"
                    viewable
                />
                @error('password')
                    <flux:error>{{ $message }}</flux:error>
                @enderror
            </flux:field>

            <div class="flex justify-end gap-3">
                <flux:button variant="ghost" wire:click="$toggle('confirmingLogout')" wire:loading.attr="disabled">
                    {{ __('Cancel') }}
                </flux:button>

                <flux:button variant="primary" wire:click="logoutOtherBrowserSessions" wire:loading.attr="disabled">
                    {{ __('Log Out Other Browser Sessions') }}
                </flux:button>
            </div>
        </div>
    </flux:modal>
</flux:card>
