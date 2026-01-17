<x-layouts.auth.card>
    <div x-data="{ recovery: false }">
        <div x-show="! recovery">
            <x-auth-header :title="__('Two-factor authentication')" :description="__('Enter the code from your authenticator app.')" />
        </div>

        <div x-cloak x-show="recovery">
            <x-auth-header :title="__('Two-factor authentication')" :description="__('Enter one of your emergency recovery codes.')" />
        </div>

        <form method="POST" action="{{ route('two-factor.login') }}" class="flex flex-col gap-6">
            @csrf

            <div x-show="! recovery">
                <flux:field>
                    <flux:label>{{ __('Authentication code') }}</flux:label>
                    <flux:input
                        name="code"
                        type="text"
                        inputmode="numeric"
                        autofocus
                        x-ref="code"
                        autocomplete="one-time-code"
                        placeholder="000000"
                    />
                    @error('code')
                        <flux:error>{{ $message }}</flux:error>
                    @enderror
                </flux:field>
            </div>

            <div x-cloak x-show="recovery">
                <flux:field>
                    <flux:label>{{ __('Recovery code') }}</flux:label>
                    <flux:input
                        name="recovery_code"
                        type="text"
                        x-ref="recovery_code"
                        autocomplete="one-time-code"
                    />
                    @error('recovery_code')
                        <flux:error>{{ $message }}</flux:error>
                    @enderror
                </flux:field>
            </div>

            <div class="flex items-center justify-between">
                <flux:link
                    variant="subtle"
                    class="text-sm cursor-pointer"
                    x-show="! recovery"
                    x-on:click="recovery = true; $nextTick(() => { $refs.recovery_code.focus() })"
                >
                    {{ __('Use a recovery code') }}
                </flux:link>

                <flux:link
                    variant="subtle"
                    class="text-sm cursor-pointer"
                    x-cloak
                    x-show="recovery"
                    x-on:click="recovery = false; $nextTick(() => { $refs.code.focus() })"
                >
                    {{ __('Use an authentication code') }}
                </flux:link>

                <flux:button type="submit" variant="primary">
                    {{ __('Log in') }}
                </flux:button>
            </div>
        </form>
    </div>
</x-layouts.auth.card>
