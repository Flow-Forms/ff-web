<x-layouts.auth.card>
    <x-auth-header :title="__('Log in to your account')" :description="__('Enter your email and password below')" />

    <x-auth-session-status :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="flex flex-col gap-6">
        @csrf

        <flux:field>
            <flux:label>{{ __('Email address') }}</flux:label>
            <flux:input
                name="email"
                type="email"
                :value="old('email')"
                required
                autofocus
                autocomplete="email"
                placeholder="email@example.com"
            />
            @error('email')
                <flux:error>{{ $message }}</flux:error>
            @enderror
        </flux:field>

        <flux:field>
            <div class="flex items-center justify-between">
                <flux:label>{{ __('Password') }}</flux:label>
                @if (Route::has('password.request'))
                    <flux:link :href="route('password.request')" variant="subtle" class="text-sm">
                        {{ __('Forgot password?') }}
                    </flux:link>
                @endif
            </div>
            <flux:input
                name="password"
                type="password"
                required
                autocomplete="current-password"
                viewable
            />
            @error('password')
                <flux:error>{{ $message }}</flux:error>
            @enderror
        </flux:field>

        <flux:checkbox name="remember" :label="__('Remember me')" />

        <flux:button type="submit" variant="primary" class="w-full">
            {{ __('Log in') }}
        </flux:button>
    </form>

    @if (Route::has('register'))
        <div class="text-center text-sm text-zinc-600 dark:text-zinc-400">
            {{ __("Don't have an account?") }}
            <flux:link :href="route('register')" variant="subtle">{{ __('Sign up') }}</flux:link>
        </div>
    @endif
</x-layouts.auth.card>
