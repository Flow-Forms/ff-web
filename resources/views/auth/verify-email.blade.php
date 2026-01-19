<x-layouts.auth.card>
    <x-auth-header :title="__('Verify email')" :description="__('Please verify your email address by clicking the link we sent you.')" />

    @if (session('status') == 'verification-link-sent')
        <flux:callout variant="success" icon="check-circle">
            {{ __('A new verification link has been sent to your email address.') }}
        </flux:callout>
    @endif

    <div class="flex flex-col gap-4">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <flux:button type="submit" variant="primary" class="w-full">
                {{ __('Resend verification email') }}
            </flux:button>
        </form>

        <div class="flex items-center justify-center gap-4 text-sm">
            <flux:link :href="route('settings')" variant="subtle">
                {{ __('Edit Profile') }}
            </flux:link>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <flux:button type="submit" variant="ghost" size="sm">
                    {{ __('Log out') }}
                </flux:button>
            </form>
        </div>
    </div>
</x-layouts.auth.card>
