<x-layouts.auth.card>
    <x-auth-header :title="__('Forgot password')" :description="__('Enter your email to receive a reset link')" />

    <x-auth-session-status :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="flex flex-col gap-6">
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
            <flux:error name="email" />
        </flux:field>

        <flux:button type="submit" variant="primary" class="w-full">
            {{ __('Email password reset link') }}
        </flux:button>
    </form>

    <div class="text-center text-sm text-zinc-600 dark:text-zinc-400">
        {{ __('Or, return to') }}
        <flux:link :href="route('login')" variant="subtle">{{ __('log in') }}</flux:link>
    </div>
</x-layouts.auth.card>
