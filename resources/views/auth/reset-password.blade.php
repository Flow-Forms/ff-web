<x-layouts.auth.card>
    <x-auth-header :title="__('Reset password')" :description="__('Enter your new password below')" />

    <form method="POST" action="{{ route('password.update') }}" class="flex flex-col gap-6">
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <flux:field>
            <flux:label>{{ __('Email address') }}</flux:label>
            <flux:input
                name="email"
                type="email"
                :value="old('email', $request->email)"
                required
                autofocus
                autocomplete="email"
            />
            @error('email')
                <flux:error>{{ $message }}</flux:error>
            @enderror
        </flux:field>

        <flux:field>
            <flux:label>{{ __('Password') }}</flux:label>
            <flux:input
                name="password"
                type="password"
                required
                autocomplete="new-password"
                viewable
            />
            @error('password')
                <flux:error>{{ $message }}</flux:error>
            @enderror
        </flux:field>

        <flux:field>
            <flux:label>{{ __('Confirm password') }}</flux:label>
            <flux:input
                name="password_confirmation"
                type="password"
                required
                autocomplete="new-password"
                viewable
            />
            @error('password_confirmation')
                <flux:error>{{ $message }}</flux:error>
            @enderror
        </flux:field>

        <flux:button type="submit" variant="primary" class="w-full">
            {{ __('Reset password') }}
        </flux:button>
    </form>
</x-layouts.auth.card>
