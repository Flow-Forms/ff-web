<x-layouts.auth.card>
    <x-auth-header :title="__('Create an account')" :description="__('Enter your details below to get started')" />

    <form method="POST" action="{{ route('register') }}" class="flex flex-col gap-6">
        @csrf

        <flux:field>
            <flux:label>{{ __('Name') }}</flux:label>
            <flux:input
                name="name"
                type="text"
                :value="old('name')"
                required
                autofocus
                autocomplete="name"
                placeholder="Full name"
            />
            @error('name')
                <flux:error>{{ $message }}</flux:error>
            @enderror
        </flux:field>

        <flux:field>
            <flux:label>{{ __('Email address') }}</flux:label>
            <flux:input
                name="email"
                type="email"
                :value="old('email')"
                required
                autocomplete="email"
                placeholder="email@example.com"
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

        @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
            <flux:field>
                <flux:checkbox name="terms" required>
                    {!! __('I agree to the :terms_of_service and :privacy_policy', [
                        'terms_of_service' => '<a target="_blank" href="'.route('terms.show').'" class="underline text-sm hover:text-zinc-900 dark:hover:text-zinc-100">'.__('Terms of Service').'</a>',
                        'privacy_policy' => '<a target="_blank" href="'.route('policy.show').'" class="underline text-sm hover:text-zinc-900 dark:hover:text-zinc-100">'.__('Privacy Policy').'</a>',
                    ]) !!}
                </flux:checkbox>
                @error('terms')
                    <flux:error>{{ $message }}</flux:error>
                @enderror
            </flux:field>
        @endif

        <flux:button type="submit" variant="primary" class="w-full">
            {{ __('Create account') }}
        </flux:button>
    </form>

    <div class="text-center text-sm text-zinc-600 dark:text-zinc-400">
        {{ __('Already have an account?') }}
        <flux:link :href="route('login')" variant="subtle">{{ __('Log in') }}</flux:link>
    </div>
</x-layouts.auth.card>
