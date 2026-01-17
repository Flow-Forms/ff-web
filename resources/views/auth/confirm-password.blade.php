<x-layouts.auth.card>
    <x-auth-header :title="__('Confirm password')" :description="__('This is a secure area. Please confirm your password to continue.')" />

    <form method="POST" action="{{ route('password.confirm') }}" class="flex flex-col gap-6">
        @csrf

        <flux:field>
            <flux:label>{{ __('Password') }}</flux:label>
            <flux:input
                name="password"
                type="password"
                required
                autofocus
                autocomplete="current-password"
                viewable
            />
            <flux:error name="password" />
        </flux:field>

        <flux:button type="submit" variant="primary" class="w-full">
            {{ __('Confirm') }}
        </flux:button>
    </form>
</x-layouts.auth.card>
